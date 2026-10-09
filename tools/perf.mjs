#!/usr/bin/env node
/**
 * Замер производительности локального стенда Lighthouse'ом — инструмент
 * разработки, не часть сайта.
 *
 *     tools/perf.mjs --out /tmp/perf-before                 # 8 страниц × 2 × 3 прогона
 *     tools/perf.mjs --out /tmp/perf-after --runs 5 / /blog/
 *     tools/perf.mjs --out /tmp/perf-prod --base https://taxlab.ru --runs 1 /
 *     TAXLAB_BASE=http://localhost:8092 tools/perf.mjs --out /tmp/perf-ws-2   # полоса
 *
 * Для каждого пути — mobile (дефолт Lighthouse) и desktop (`--preset=desktop`),
 * категории performance, accessibility, best-practices, seo. Результат в --out:
 *
 *     raw/<slug>.<mobile|desktop>.<n>.json   сырые отчёты Lighthouse
 *     summary.json                           медианы + все прогоны + opportunities
 *     summary.md                             та же таблица для чтения глазами
 *
 * Медиана берётся по каждой метрике отдельно (как делает PSI/LHCI): 4 оценки,
 * FCP, LCP, TBT, CLS, SI, вес страницы, число запросов, размер DOM. Список
 * opportunities/diagnostics — из прогона с медианной оценкой performance.
 *
 * Зависимостей нет, кроме `npx lighthouse@12` (кэш npx, первый запуск качает).
 *
 * ⚠️ Прогоны СТРОГО последовательно и каждый в своём процессе с жёстким
 * таймаутом (180 с). Параллельные Lighthouse'ы делят CPU и врут в TBT/LCP в
 * разы. А таймаут нужен потому, что хром на десктопной вёрстке этого сайта
 * умеет не завершаться (см. шапку tools/shot.mjs) — зависший прогон не должен
 * съесть весь замер.
 *
 * ⚠️ Убить по таймауту одну группу процессов мало: chrome-launcher запускает
 * хром с `detached: true`, он живёт в своей группе и переживает SIGKILL
 * lighthouse. Поэтому каждому хрому дан флаг-метка `--taxlab-perf-run=<pid>-<i>`
 * (хром игнорирует незнакомые ключи), и после прогона/таймаута добиваем его
 * `pkill -f` по метке. Без этого за ночь копятся десятки хромов.
 */

import { spawn, spawnSync } from "node:child_process";
import { existsSync, mkdirSync, readFileSync, writeFileSync } from "node:fs";
import { join } from "node:path";

const CHROME = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome";
const TIMEOUT_MS = 180_000;
const CATEGORIES = ["performance", "accessibility", "best-practices", "seo"];
const DEFAULT_PATHS = [
  "/",
  "/services/",
  "/services/bukhgalterskie-uslugi/",
  "/services/licensing/roznichnaya-torgovlya-alkogolem/",
  "/blog/",
  "/blog/smena-direktora-v-ooo/",
  "/price/ceny-na-buhgalterskie-uslugi/",
  "/about/contacts/",
];
const FORMS = ["mobile", "desktop"];

const die = (msg) => { process.stderr.write(`perf.mjs: ${msg}\n`); process.exit(1); };
const log = (msg) => process.stdout.write(`[${new Date().toTimeString().slice(0, 8)}] ${msg}\n`);

// ── аргументы ────────────────────────────────────────────────────────────────
// База: --base, иначе TAXLAB_BASE (полосы tools/lane.sh), иначе основной стенд.
const opt = { out: null, runs: 3, base: (process.env.TAXLAB_BASE || "http://localhost:8088").replace(/\/+$/, "") };
const paths = [];
for (let i = 2; i < process.argv.length; i++) {
  const a = process.argv[i];
  if (a === "--out") opt.out = process.argv[++i];
  else if (a === "--runs") opt.runs = Number(process.argv[++i]);
  else if (a === "--base") opt.base = process.argv[++i]?.replace(/\/+$/, "");
  else if (a.startsWith("/")) paths.push(a);
  else die(`не понял аргумент «${a}» (пути начинаются с «/»)`);
}
if (!opt.out) die("не задан --out <dir>");
if (!Number.isInteger(opt.runs) || opt.runs < 1) die("--runs должен быть целым ≥ 1");
if (!opt.base) die("--base без значения");
if (!existsSync(CHROME)) die(`нет хрома: ${CHROME}`);
if (!paths.length) paths.push(...DEFAULT_PATHS);

const RAW = join(opt.out, "raw");
mkdirSync(RAW, { recursive: true });

const slug = (p) => p.replace(/^\/|\/$/g, "").replace(/[^a-z0-9а-я_-]+/gi, "_") || "home";

// ── один прогон ──────────────────────────────────────────────────────────────
let seq = 0;
function runOnce(url, form, outFile) {
  const mark = `${process.pid}-${++seq}`;
  const args = [
    "-y", "lighthouse@12", url,
    "--output=json", `--output-path=${outFile}`,
    `--only-categories=${CATEGORIES.join(",")}`,
    `--chrome-flags=--headless=new --taxlab-perf-run=${mark}`,
    "--quiet",
  ];
  if (form === "desktop") args.push("--preset=desktop");

  return new Promise((resolve) => {
    const t0 = Date.now();
    const child = spawn("npx", args, {
      env: { ...process.env, CHROME_PATH: CHROME },
      stdio: ["ignore", "ignore", "pipe"],
      detached: true, // своя группа — чтобы по таймауту убить npx+node разом
    });
    let stderr = "";
    child.stderr.on("data", (d) => { stderr = (stderr + d).slice(-4000); });
    let timedOut = false;
    const timer = setTimeout(() => {
      timedOut = true;
      try { process.kill(-child.pid, "SIGKILL"); } catch { /* уже умер */ }
    }, TIMEOUT_MS);
    child.on("close", (code) => {
      clearTimeout(timer);
      // хром в своей группе — добиваем по метке (см. шапку)
      spawnSync("pkill", ["-9", "-f", `taxlab-perf-run=${mark}`], { stdio: "ignore" });
      const secs = ((Date.now() - t0) / 1000).toFixed(1);
      if (timedOut) return resolve({ ok: false, secs, error: `таймаут ${TIMEOUT_MS / 1000} с` });
      if (code !== 0 || !existsSync(outFile)) {
        return resolve({ ok: false, secs, error: `код ${code}: ${stderr.trim().split("\n").slice(-3).join(" | ")}` });
      }
      try {
        const lhr = JSON.parse(readFileSync(outFile, "utf8"));
        if (lhr.runtimeError) return resolve({ ok: false, secs, error: `${lhr.runtimeError.code}: ${lhr.runtimeError.message}` });
        resolve({ ok: true, secs, lhr });
      } catch (e) {
        resolve({ ok: false, secs, error: `битый JSON: ${e.message}` });
      }
    });
  });
}

// ── извлечение метрик ────────────────────────────────────────────────────────
function metrics(lhr) {
  const a = lhr.audits;
  const num = (id) => a[id]?.numericValue ?? null;
  const score = (id) => (lhr.categories[id]?.score == null ? null : Math.round(lhr.categories[id].score * 100));
  return {
    performance: score("performance"),
    accessibility: score("accessibility"),
    bestPractices: score("best-practices"),
    seo: score("seo"),
    fcp: num("first-contentful-paint"),
    lcp: num("largest-contentful-paint"),
    tbt: num("total-blocking-time"),
    cls: num("cumulative-layout-shift"),
    si: num("speed-index"),
    bytes: num("total-byte-weight"),
    requests: a["network-requests"]?.details?.items?.length ?? null,
    // в 12.x старый аудит dom-size ещё есть; на случай его выпиливания — insight
    dom: num("dom-size") ?? a["dom-size-insight"]?.details?.items?.[0]?.value ?? null,
  };
}

// Opportunities (с оценкой экономии) и проваленные диагностики performance.
function opportunities(lhr) {
  const refs = lhr.categories.performance?.auditRefs ?? [];
  const out = [];
  for (const ref of refs) {
    const au = lhr.audits[ref.id];
    if (!au || au.scoreDisplayMode === "notApplicable" || au.scoreDisplayMode === "manual") continue;
    if (ref.group === "metrics" || ref.group === "hidden") continue;
    if (au.score === null && au.scoreDisplayMode !== "informative") continue;
    if (au.score !== null && au.score >= 0.9) continue;
    const ms = au.metricSavings ?? {};
    out.push({
      id: ref.id,
      title: au.title,
      displayValue: au.displayValue ?? "",
      score: au.score,
      savingsMs: au.details?.overallSavingsMs ?? ms.LCP ?? ms.FCP ?? ms.TBT ?? null,
      savingsBytes: au.details?.overallSavingsBytes ?? null,
      items: (au.details?.items ?? []).slice(0, 8).map((it) => ({
        url: it.url ?? it.node?.selector ?? it.source?.url ?? it.entity ?? it.label ?? undefined,
        totalBytes: it.totalBytes ?? it.transferSize ?? undefined,
        wastedBytes: it.wastedBytes ?? undefined,
        wastedMs: it.wastedMs ?? undefined,
        duration: it.duration ?? it.blockingTime ?? undefined,
      })),
    });
  }
  return out.sort((x, y) => (y.savingsMs ?? 0) - (x.savingsMs ?? 0) || (y.savingsBytes ?? 0) - (x.savingsBytes ?? 0));
}

const median = (xs) => {
  const v = xs.filter((x) => x != null).sort((p, q) => p - q);
  if (!v.length) return null;
  const m = Math.floor(v.length / 2);
  return v.length % 2 ? v[m] : (v[m - 1] + v[m]) / 2;
};

// ── главный цикл ─────────────────────────────────────────────────────────────
const total = paths.length * FORMS.length * opt.runs;
log(`${paths.length} путей × ${FORMS.length} × ${opt.runs} = ${total} прогонов, база ${opt.base}, вывод ${opt.out}`);
const pages = [];
let done = 0;
const tStart = Date.now();

for (const p of paths) {
  for (const form of FORMS) {
    const runs = [];
    for (let n = 1; n <= opt.runs; n++) {
      const file = join(RAW, `${slug(p)}.${form}.${n}.json`);
      let r = await runOnce(opt.base + p, form, file);
      if (!r.ok) { // один повтор: случайный сбой хрома не должен дырявить таблицу
        log(`  ${p} ${form} #${n}: ${r.error} — повтор`);
        r = await runOnce(opt.base + p, form, file);
      }
      done++;
      if (r.ok) {
        const m = metrics(r.lhr);
        runs.push({ n, file, secs: Number(r.secs), metrics: m, opps: opportunities(r.lhr) });
        log(`${done}/${total} ${p} ${form} #${n}: perf ${m.performance} LCP ${Math.round(m.lcp)} мс TBT ${Math.round(m.tbt)} мс (${r.secs} с)`);
      } else {
        runs.push({ n, file: null, secs: Number(r.secs), error: r.error });
        log(`${done}/${total} ${p} ${form} #${n}: ОШИБКА ${r.error}`);
      }
    }
    const good = runs.filter((r) => r.metrics);
    const keys = good.length ? Object.keys(good[0].metrics) : [];
    const med = Object.fromEntries(keys.map((k) => [k, median(good.map((r) => r.metrics[k]))]));
    // прогон, ближайший к медиане по performance, — его opportunities показываем
    const rep = good.slice().sort((x, y) =>
      Math.abs(x.metrics.performance - med.performance) - Math.abs(y.metrics.performance - med.performance))[0];
    pages.push({
      path: p, form, okRuns: good.length, median: med,
      representative: rep?.file ?? null,
      opportunities: rep?.opps ?? [],
      runs: runs.map(({ opps, ...r }) => r),
    });
  }
}

// ── отчёт ────────────────────────────────────────────────────────────────────
const summary = {
  generatedAt: new Date().toISOString(),
  base: opt.base, runs: opt.runs, lighthouse: "12",
  durationMin: Number(((Date.now() - tStart) / 60000).toFixed(1)),
  pages,
};
writeFileSync(join(opt.out, "summary.json"), JSON.stringify(summary, null, 2));

const f = {
  ms: (v) => (v == null ? "—" : v >= 1000 ? `${(v / 1000).toFixed(1)} с` : `${Math.round(v)} мс`),
  kb: (v) => (v == null ? "—" : `${Math.round(v / 1024)} КБ`),
  n: (v) => (v == null ? "—" : String(Math.round(v))),
  cls: (v) => (v == null ? "—" : v.toFixed(3)),
};
const md = [
  `# Lighthouse: ${opt.base}`,
  "",
  `${summary.generatedAt} · Lighthouse 12 · медиана из ${opt.runs} прогонов · ${summary.durationMin} мин`,
  "",
  "| Путь | Форм-фактор | Perf | A11y | BP | SEO | FCP | LCP | TBT | CLS | SI | Вес | Запросы | DOM | Прогоны |",
  "|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|",
  ...pages.map(({ path, form, median: m, okRuns }) =>
    `| ${path} | ${form} | ${f.n(m.performance)} | ${f.n(m.accessibility)} | ${f.n(m.bestPractices)} | ${f.n(m.seo)} | ` +
    `${f.ms(m.fcp)} | ${f.ms(m.lcp)} | ${f.ms(m.tbt)} | ${f.cls(m.cls)} | ${f.ms(m.si)} | ${f.kb(m.bytes)} | ` +
    `${f.n(m.requests)} | ${f.n(m.dom)} | ${okRuns}/${opt.runs} |`),
  "",
  "## Opportunities и проваленные диагностики (медианный прогон)",
  "",
];
for (const pg of pages) {
  md.push(`### ${pg.path} — ${pg.form}`, "");
  for (const o of pg.opportunities.slice(0, 10)) {
    const sav = [o.savingsMs ? `−${f.ms(o.savingsMs)}` : "", o.savingsBytes ? `−${f.kb(o.savingsBytes)}` : ""].filter(Boolean).join(", ");
    md.push(`- **${o.title}**${o.displayValue ? ` — ${o.displayValue}` : ""}${sav ? ` (${sav})` : ""} \`${o.id}\``);
  }
  md.push("");
}
writeFileSync(join(opt.out, "summary.md"), md.join("\n"));
log(`готово за ${summary.durationMin} мин → ${join(opt.out, "summary.md")}`);
const failed = pages.reduce((s, p) => s + (opt.runs - p.okRuns), 0);
if (failed) { log(`упавших прогонов: ${failed}`); process.exitCode = 2; }
