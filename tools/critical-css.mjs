#!/usr/bin/env node
/**
 * Критический CSS первого экрана для страниц стенда — инструмент разработки.
 *
 *     tools/critical-css.mjs --out httpdocs/wp-content/themes/umbrella-group/assets/css/critical /services/bukhgalterskie-uslugi/obsluzhivanie/
 *
 * Для каждой страницы: headless Chrome с полным CSS на ширинах 390 (телефон),
 * 768 и 1440; набор элементов первого экрана (видимые, пересекающие 1,3 высоты
 * окна, плюс скрытые CSS-ом элементы, чей родитель виден на первом экране —
 * чтобы правила вида .mfp-hide{display:none} попали в критический CSS). Из
 * каждого <link rel=stylesheet> (в <head> и стилей блоков в конце <body>) берутся правила, селектор которых
 * (без :hover/::before и т.п.) находит хоть один такой элемент, с сохранением
 * @media/@supports и порядка; @font-face и @keyframes — всегда. url() —
 * от корня сайта. Результат: <out>/page-<ID>.json = {"handle": "css", …}
 * (handle — id ссылки без -css), его подставляет inc/perf.php.
 *
 * Стенд — TAXLAB_BASE (по умолчанию http://localhost:8088). Перегенерировать
 * после правки CSS темы/Flatsome на этих страницах: устаревший критический CSS
 * даёт кратковременную разницу первого кадра, полный CSS всё равно грузится.
 */
import { spawn, spawnSync } from "node:child_process";
import { mkdirSync, writeFileSync } from "node:fs";

const CHROME = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome";
const BASE = (process.env.TAXLAB_BASE || "http://localhost:8088").replace(/\/+$/, "");
const args = process.argv.slice(2);
let out = null; const paths = [];
for (let i = 0; i < args.length; i++) { if (args[i] === "--out") out = args[++i]; else paths.push(args[i]); }
if (!out || !paths.length) { console.error("usage: critical-css.mjs --out DIR path..."); process.exit(1); }
mkdirSync(out, { recursive: true });
const VIEWPORTS = [{ w: 390, h: 844, mobile: true }, { w: 768, h: 1024, mobile: true }, { w: 1440, h: 900, mobile: false }];
const UA_IPHONE = "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1";

const PROFILE = `/tmp/ugcrit-${process.pid}`;
const child = spawn(CHROME, ["--headless=new", "--disable-gpu", "--hide-scrollbars", "--no-first-run", `--user-data-dir=${PROFILE}`, "--remote-debugging-pipe", "about:blank"], { stdio: ["ignore", "ignore", "ignore", "pipe", "pipe"] });
const pout = child.stdio[3], pin = child.stdio[4]; const waiting = new Map(); const handlers = []; let id = 0, buf = [];
pin.on("data", (c) => { let s = 0, i; while ((i = c.indexOf(0, s)) !== -1) { buf.push(c.subarray(s, i)); const m = JSON.parse(Buffer.concat(buf).toString()); buf = []; s = i + 1; if (m.id && waiting.has(m.id)) { const w = waiting.get(m.id); waiting.delete(m.id); m.error ? w.fail(new Error(m.error.message)) : w.ok(m.result); } else if (m.method) handlers.forEach((h) => h(m)); } if (s < c.length) buf.push(c.subarray(s)); });
const send = (method, params = {}, sessionId) => new Promise((ok, fail) => { const n = ++id; waiting.set(n, { ok, fail }); pout.write(JSON.stringify({ id: n, method, params, sessionId }) + "\0"); });
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
const finish = (code) => { try { child.kill("SIGKILL"); } catch {} spawnSync("pkill", ["-9", "-f", `user-data-dir=${PROFILE}`]); spawnSync("rm", ["-rf", PROFILE]); process.exit(code); };
setTimeout(() => { console.error("watchdog"); finish(2); }, 60000 * paths.length + 30000).unref();

// выполняется в странице: {handle: [ruleKey…]} по текущему окну, и тексты правил
const EXTRACT = String.raw`(() => {
  const H = innerHeight * 1.3;
  const shown = (el) => { const cs = getComputedStyle(el); return cs.display !== 'none' && el.getClientRects().length > 0; };
  const inFold = (el) => { for (const r of el.getClientRects()) { if (r.bottom > -5 && r.top < H) return true; } return false; };
  const set = new Set([document.documentElement, document.body]);
  const walk = (el, parentIn) => {
    for (const c of el.children) {
      if (c.tagName === 'SCRIPT' || c.tagName === 'STYLE' || c.tagName === 'LINK' || c.tagName === 'TEMPLATE') continue;
      const vis = shown(c);
      if (!vis) { if (parentIn) set.add(c); continue; }   // скрытый корень — его правило скрытия нужно
      const fold = inFold(c) || getComputedStyle(c).position === 'fixed';
      if (fold) set.add(c);
      // display:contents и обёртки без своих прямоугольников — смотрим детей всегда
      walk(c, fold || parentIn && !c.getClientRects().length);
    }
  };
  walk(document.body, true);
  // абсолютный элемент ниже первого экрана, у которого правило position попало
  // в критический CSS через общий класс (.fill, .section-bg), без position:relative
  // своего контейнера растянется на всю страницу — контейнеры добавляем всегда
  for (const el of document.body.querySelectorAll('*')) {
    if (getComputedStyle(el).position !== 'absolute') continue;
    let p = el.parentElement;
    while (p && p !== document.body && getComputedStyle(p).position === 'static') p = p.parentElement;
    if (p) set.add(p);
  }
  const DYN = /::?(before|after|placeholder|selection|marker|first-line|first-letter|backdrop|-webkit-[\w-]+|-moz-[\w-]+|-ms-[\w-]+)|:(hover|focus-visible|focus-within|focus|active|visited|target|checked|disabled|enabled|invalid|valid|required|optional|placeholder-shown|autofill|-webkit-autofill|indeterminate)\b(\([^)]*\))?/g;
  const splitSel = (s) => { const out = []; let d = 0, cur = ''; for (const ch of s) { if (ch === '(') d++; if (ch === ')') d--; if (ch === ',' && d === 0) { out.push(cur); cur = ''; } else cur += ch; } out.push(cur); return out; };
  const hit = (sel) => { let s = sel.replace(DYN, '').trim(); if (!s || /[>+~]$/.test(s)) s = (s.replace(/[>+~]\s*$/, '').trim()) || '*'; try { for (const el of document.querySelectorAll(s)) if (set.has(el)) return true; return false; } catch (e) { return true; } };
  const abs = (css, base) => css.replace(/url\(\s*(['"]?)([^'")]+)\1\s*\)/g, (m, q, u) => { if (/^(data:|#)/.test(u)) return m; try { const x = new URL(u, base); return 'url("' + (x.origin === location.origin ? x.pathname + x.search : x.href) + '")'; } catch (e) { return m; } });
  const res = {};
  for (const link of document.querySelectorAll('link[rel=stylesheet][id]')) {
    if (link.closest('noscript') || /-tail-css$/.test(link.id)) continue;   // якоря каскада в конце <body> (ug-main-tail, ug-header-site-tail…) — копии хэндлов из <head>
    const sheet = link.sheet; if (!sheet) continue;
    const handle = link.id.replace(/-css$/, '');
    const keep = {};
    const visit = (rules, path) => {
      Array.from(rules).forEach((r, i) => {
        const key = path + '.' + i;
        if (r instanceof CSSStyleRule) { if (splitSel(r.selectorText).some(hit)) keep[key] = abs(r.cssText, sheet.href); }
        else if (r instanceof CSSFontFaceRule || r instanceof CSSKeyframesRule) keep[key] = abs(r.cssText, sheet.href);
        else if (r instanceof CSSMediaRule || r instanceof CSSSupportsRule) {
          keep[key + '#open'] = (r instanceof CSSMediaRule ? '@media ' + r.conditionText : '@supports ' + r.conditionText) + '{';
          visit(r.cssRules, key);
          keep[key + '#close'] = '}';
        }
      });
    };
    try { visit(sheet.cssRules, 'r'); } catch (e) { keep['error'] = '/* ' + e.message + ' */'; }
    res[handle] = keep;
  }
  return res;
})()`;

const { targetId } = await send("Target.createTarget", { url: "about:blank" });
const { sessionId } = await send("Target.attachToTarget", { targetId, flatten: true });
const call = (m, p) => send(m, p, sessionId);
let loaded = false; handlers.push((m) => { if (m.method === "Page.loadEventFired") loaded = true; });
await call("Page.enable"); await call("Runtime.enable");

const order = (a, b) => { const pa = a.replace(/#(open|close)$/, (m, x) => (x === 'open' ? '.-1' : '.99999')).split('.').slice(1).map(Number); const pb = b.replace(/#(open|close)$/, (m, x) => (x === 'open' ? '.-1' : '.99999')).split('.').slice(1).map(Number); for (let i = 0; i < Math.max(pa.length, pb.length); i++) { const x = pa[i] ?? -2, y = pb[i] ?? -2; if (x !== y) return x - y; } return 0; };

for (const path of paths) {
  const url = BASE + path;
  const union = {};
  for (const vp of VIEWPORTS) {
    await call("Emulation.setDeviceMetricsOverride", { width: vp.w, height: vp.h, deviceScaleFactor: 1, mobile: vp.mobile });
    await call("Emulation.setTouchEmulationEnabled", { enabled: vp.mobile, maxTouchPoints: vp.mobile ? 5 : 1 });
    await call("Emulation.setUserAgentOverride", { userAgent: vp.mobile ? UA_IPHONE : "" });
    loaded = false; await call("Page.navigate", { url });
    for (let i = 0; i < 200 && !loaded; i++) await sleep(100);
    await sleep(1200);
    const r = await call("Runtime.evaluate", { expression: EXTRACT, returnByValue: true });
    if (r.exceptionDetails) { console.error(path, r.exceptionDetails.exception?.description); finish(1); }
    for (const [h, keep] of Object.entries(r.result.value)) Object.assign(union[h] ||= {}, keep);
  }
  const pid = (await call("Runtime.evaluate", { expression: "(document.body.className.match(/page-id-(\\d+)/)||[])[1]||''", returnByValue: true })).result.value;
  if (!pid) { console.error(path, "нет page-id"); continue; }
  const result = {};
  let bytes = 0;
  for (const [h, keep] of Object.entries(union)) {
    const keys = Object.keys(keep).sort(order);
    // пустые @media{} (обёртка без совпавших правил) — выбросить
    const parts = []; for (const k of keys) parts.push([k, keep[k]]);
    let css = ""; const stack = [];
    for (const [k, v] of parts) {
      if (k.endsWith("#open")) { stack.push({ head: v, body: "" }); continue; }
      if (k.endsWith("#close")) { const top = stack.pop(); const s = top.body ? top.head + top.body + "}" : ""; if (stack.length) stack[stack.length - 1].body += s; else css += s; continue; }
      if (stack.length) stack[stack.length - 1].body += v; else css += v;
    }
    result[h] = css; bytes += css.length;
  }
  const file = `${out}/page-${pid}.json`;
  writeFileSync(file, JSON.stringify(result, null, 0));
  console.log(`${path} → ${file} ${(bytes / 1024).toFixed(1)} КБ (${Object.keys(result).length} файлов)`);
}
finish(0);
