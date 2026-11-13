#!/usr/bin/env node
/**
 * PDF-карточки организаций для «Скачать карточку» на /rekvizity/ (page 27388).
 *
 *     tools/rekvizity-cards.mjs                  # данные — page 27388 локального стенда
 *     tools/rekvizity-cards.mjs --prod           # данные — page 27388 прода (ssh annikov.com)
 *     tools/rekvizity-cards.mjs content.txt      # данные — файл с контентом страницы
 *     tools/rekvizity-cards.mjs --out /tmp/x/    # куда писать (по умолчанию — в тему)
 *
 * Единственный источник реквизитов — шорткоды [ug_rekv_org …] в контенте
 * страницы (разметка — inc/rekvizity.php). Скрипт разбирает их, собирает
 * HTML-карточку A4 (логотип из шапки сайта, шрифты темы — Inter и PT Serif)
 * и печатает её headless Chrome в PDF (CDP Page.printToPDF через
 * --remote-debugging-pipe, как tools/shot.mjs). Текст в PDF настоящий —
 * выделяется и копируется, шрифты встроены подмножеством.
 *
 * Пишет assets/docs/rekvizity-<id>.pdf в теме — по одному на [ug_rekv_org],
 * <id> — атрибут id. Шорткод ставит кнопку «Скачать карточку» только тем
 * организациям, у которых такой файл есть.
 *
 * Реквизиты поменялись — порядок такой:
 *   1. правим [ug_rekv_org] в контенте page 27388 (на стенде или на проде);
 *   2. tools/rekvizity-cards.mjs (или --prod, если правили сразу на проде);
 *   3. заливаем новые PDF на сервер в ту же папку темы, сверяем md5,
 *      сбрасываем кэш (rocket_clean_domain) — ?ver у ссылки берётся из filemtime.
 * Новая организация — новый [ug_rekv_org] + прогон скрипта: без PDF у неё
 * просто не будет кнопки карточки.
 *
 * ⚠️ Не печатай через `chrome --print-to-pdf` / `--screenshot` с
 * `--virtual-time-budget`: см. шапку tools/shot.mjs — хром не выходит.
 */
import { spawn, spawnSync } from "node:child_process";
import { existsSync, mkdirSync, readFileSync, rmSync, statSync, writeFileSync } from "node:fs";
import { dirname, join, resolve } from "node:path";
import { fileURLToPath, pathToFileURL } from "node:url";

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), "..");
const THEME = join(ROOT, "httpdocs/wp-content/themes/umbrella-group");
const LOGO = join(ROOT, "httpdocs/wp-content/uploads/logo-3.png"); // логотип шапки (header-site.php)
const CHROME = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome";
const PAGE_ID = 27388;
const PROFILE = `/tmp/taxlab-cards-profile-${process.pid}`;

// Контакты в подвале карточки — как в шапке сайта.
const CONTACTS = [
  ["Телефон", "+7 (383) 373-17-17"],
  ["Почта", "contact@taxlab.ru"],
  ["Сайт", "taxlab.ru"],
];

// Порядок и подписи — как ug_rekv_fields() в inc/rekvizity.php.
const GROUPS = [
  ["Сведения об организации", [
    ["inn", "ИНН"], ["kpp", "КПП"], ["ogrn", "ОГРН"],
    ["director", "Генеральный директор"], ["contact", "Контактное лицо"],
    ["address", "Юридический адрес"], ["postal", "Почтовый адрес"],
  ]],
  ["Банковские реквизиты", [
    ["bank", "Банк"], ["account", "Расчётный счёт"], ["corr", "Корреспондентский счёт"], ["bik", "БИК"],
  ]],
];

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
const die = (msg) => { process.stderr.write(`rekvizity-cards.mjs: ${msg}\n`); process.exit(1); };

// ── аргументы и контент ──────────────────────────────────────────────────────
let source = "local", file = null, outDir = join(THEME, "assets/docs");
for (let i = 2; i < process.argv.length; i++) {
  const a = process.argv[i];
  if (a === "--prod") source = "prod";
  else if (a === "--out") outDir = resolve(process.argv[++i] || die("--out без значения"));
  else if (!a.startsWith("--")) { source = "file"; file = a; }
  else die(`не понял аргумент «${a}»`);
}

function readContent() {
  if (source === "file") return readFileSync(file, "utf8");
  const r = source === "prod"
    ? spawnSync("ssh", ["annikov.com",
        `sudo -u taxlabru /opt/plesk/php/8.3/bin/php /usr/local/bin/wp --path=/var/www/vhosts/taxlab.ru/httpdocs post get ${PAGE_ID} --field=post_content`],
        { encoding: "utf8", maxBuffer: 1 << 24 })
    : spawnSync(join(ROOT, "tools/local-site.sh"), ["wp", "post", "get", String(PAGE_ID), "--field=post_content"],
        { encoding: "utf8", maxBuffer: 1 << 24 });
  if (r.status !== 0) die(`не прочитал контент page ${PAGE_ID} (${source}): ${(r.stderr || "").trim()}`);
  return r.stdout;
}

const decode = (s) => s
  .replace(/&laquo;/g, "«").replace(/&raquo;/g, "»").replace(/&quot;/g, '"').replace(/&#0?39;/g, "'")
  .replace(/&nbsp;/g, " ").replace(/&#(\d+);/g, (_, n) => String.fromCharCode(Number(n))).replace(/&amp;/g, "&");

/** [ug_rekv_org …] → [{id, name, full, inn, …}] — как ug_rekv_parse(). */
function parse(content) {
  const orgs = [];
  for (const m of content.matchAll(/\[ug_rekv_org\b([^\]]*)\]/gu)) {
    const org = {};
    for (const a of m[1].matchAll(/([a-z_]+)\s*=\s*"([^"]*)"/gu)) {
      org[a[1]] = decode(a[2]).replace(/<[^>]*>/g, "").replace(/\s+/gu, " ").trim();
    }
    if (!org.name) continue;
    org.id = (org.id || `org-${orgs.length + 1}`).toLowerCase().replace(/[^a-z0-9-]+/g, "-");
    orgs.push(org);
  }
  return orgs;
}

// ── HTML карточки ────────────────────────────────────────────────────────────
const esc = (s) => String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
const font = (fam, weight, f) => `@font-face{font-family:"${fam}";font-weight:${weight};src:url("${pathToFileURL(join(THEME, "fonts", f)).href}") format("woff2");}`;

function html(org) {
  const groups = GROUPS.map(([title, fields]) => {
    const rows = fields.filter(([k]) => org[k]).map(([k, label]) =>
      `<tr><th>${esc(label)}</th><td>${esc(org[k])}</td></tr>`).join("");
    return rows ? `<h2>${esc(title)}</h2><table>${rows}</table>` : "";
  }).join("");
  const contacts = CONTACTS.map(([k, v]) => `<span><i>${esc(k)}</i>${esc(v)}</span>`).join("");
  return `<!doctype html><html lang="ru"><head><meta charset="utf-8">
<title>Реквизиты ${esc(org.name)}</title>
<style>
${font("Inter", 400, "Inter-Regular.woff2")}${font("Inter", 400, "Inter-Regular-ext.woff2")}
${font("Inter", 500, "Inter-Medium.woff2")}${font("Inter", 600, "Inter-SemiBold.woff2")}
${font("PT Serif", 700, "pt-serif/PTSerif-Bold-cyrillic.woff2")}${font("PT Serif", 700, "pt-serif/PTSerif-Bold-latin.woff2")}
@page { size: A4; margin: 0; }
* { box-sizing: border-box; }
html, body { margin: 0; }
body { font-family: "Inter", sans-serif; color: #1a1a1a; font-size: 10.5pt; line-height: 1.4; }
.page { position: relative; width: 210mm; height: 297mm; padding: 16mm 18mm 0; overflow: hidden; }
.top { display: flex; align-items: center; justify-content: space-between; padding-bottom: 7mm; border-bottom: 1.2pt solid #ec1c23; }
.top img { display: block; height: 11mm; width: auto; }
.top .kind { font-size: 9pt; font-weight: 500; letter-spacing: .06em; text-transform: uppercase; color: #8a8a8a; }
h1 { margin: 11mm 0 2mm; font-family: "PT Serif", serif; font-size: 24pt; font-weight: 700; line-height: 1.15; }
.full { margin: 0 0 9mm; font-size: 11pt; color: #555; }
h2 { margin: 7mm 0 2mm; font-family: "PT Serif", serif; font-size: 14pt; font-weight: 700; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 2.6mm 0; border-bottom: .6pt solid #e3e3e3; text-align: left; vertical-align: top; }
tr:first-child th, tr:first-child td { border-top: .6pt solid #e3e3e3; }
th { width: 58mm; padding-right: 6mm; font-weight: 400; color: #6b6b6b; }
td { font-weight: 500; }
.foot { position: absolute; right: 18mm; bottom: 14mm; left: 18mm; display: flex; gap: 10mm; padding-top: 4mm; border-top: .6pt solid #e3e3e3; font-size: 9.5pt; }
.foot i { display: block; font-style: normal; font-size: 8pt; color: #8a8a8a; }
</style></head><body><div class="page">
<div class="top"><img src="${pathToFileURL(LOGO).href}" alt="Umbrella Group"><span class="kind">Карточка организации</span></div>
<h1>${esc(org.name)}</h1>
${org.full ? `<p class="full">${esc(org.full)}</p>` : ""}
${groups}
<div class="foot">${contacts}</div>
</div></body></html>`;
}

// ── хром по pipe (как tools/shot.mjs) ────────────────────────────────────────
let child = null;
process.on("exit", () => {
  if (child) { try { process.kill(child.pid, "SIGKILL"); } catch {} spawnSync("pkill", ["-9", "-f", `user-data-dir=${PROFILE}`]); }
  try { rmSync(PROFILE, { recursive: true, force: true, maxRetries: 8, retryDelay: 100 }); } catch {}
});
for (const sig of ["SIGINT", "SIGTERM", "SIGHUP"]) process.on(sig, () => process.exit(1));
setTimeout(() => die("прогон не уложился в 90 с"), 90_000).unref();

function connect(proc) {
  const out = proc.stdio[3], inp = proc.stdio[4];
  const waiting = new Map(), events = new Map();
  let id = 0, buf = [];
  inp.on("data", (chunk) => {
    let start = 0, i;
    while ((i = chunk.indexOf(0, start)) !== -1) {
      buf.push(chunk.subarray(start, i));
      const msg = JSON.parse(Buffer.concat(buf).toString("utf8"));
      buf = []; start = i + 1;
      if (msg.id && waiting.has(msg.id)) {
        const { ok, fail } = waiting.get(msg.id);
        waiting.delete(msg.id);
        msg.error ? fail(new Error(msg.error.message)) : ok(msg.result);
      } else if (msg.method) {
        (events.get(msg.method) || []).splice(0).forEach((fn) => fn(msg.params));
      }
    }
    if (start < chunk.length) buf.push(chunk.subarray(start));
  });
  proc.on("exit", (code, sig) => { for (const { fail } of waiting.values()) fail(new Error(`хром вышел: ${sig || code}`)); });
  const send = (method, params = {}, sessionId) => new Promise((ok, fail) => {
    const n = ++id;
    waiting.set(n, { ok, fail });
    out.write(JSON.stringify({ id: n, method, params, sessionId }) + "\0");
  });
  const once = (method, ms) => new Promise((ok) => {
    (events.get(method) || events.set(method, []).get(method)).push(ok);
    setTimeout(ok, ms);
  });
  return { send, once };
}

async function main() {
  const orgs = parse(readContent());
  if (!orgs.length) die(`в контенте нет [ug_rekv_org] (${source})`);
  if (!existsSync(LOGO)) die(`нет логотипа ${LOGO}`);
  mkdirSync(outDir, { recursive: true });

  child = spawn(CHROME, [
    "--headless=new", "--disable-gpu", "--no-first-run", "--no-default-browser-check", "--disable-extensions",
    "--allow-file-access-from-files", `--user-data-dir=${PROFILE}`, "--remote-debugging-pipe", "about:blank",
  ], { stdio: ["ignore", "ignore", "ignore", "pipe", "pipe"] });
  child.on("error", (e) => die(`Chrome не запустился: ${e.message}`));
  const cdp = connect(child);
  const { targetId } = await cdp.send("Target.createTarget", { url: "about:blank" });
  const { sessionId } = await cdp.send("Target.attachToTarget", { targetId, flatten: true });
  const call = (m, p) => cdp.send(m, p, sessionId);
  await call("Page.enable");

  const tmp = `/tmp/taxlab-cards-${process.pid}.html`;
  for (const org of orgs) {
    writeFileSync(tmp, html(org));
    const loaded = cdp.once("Page.loadEventFired", 10000);
    await call("Page.navigate", { url: pathToFileURL(tmp).href });
    await loaded;
    await call("Runtime.evaluate", { expression: "document.fonts.ready.then(() => document.fonts.size)", awaitPromise: true });
    await sleep(150);
    const { data } = await call("Page.printToPDF", {
      printBackground: true, preferCSSPageSize: true,
      marginTop: 0, marginBottom: 0, marginLeft: 0, marginRight: 0,
    });
    const dest = join(outDir, `rekvizity-${org.id}.pdf`);
    writeFileSync(dest, Buffer.from(data, "base64"));
    process.stdout.write(`${dest}  ${(statSync(dest).size / 1024).toFixed(0)} КБ  ${org.name}\n`);
  }
  rmSync(tmp, { force: true });
  try { child.kill("SIGTERM"); } catch {}
  await sleep(300);
  child = null;
  process.exit(0);
}

main().catch((e) => die(e.stack || String(e)));
