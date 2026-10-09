#!/usr/bin/env node
/**
 * Скриншоты локального стенда — инструмент разработки, не часть сайта.
 *
 *     tools/shot.mjs 1440                        # /tmp/shot-1440.png
 *     tools/shot.mjs 1280 1440 1600 1920 390m    # пять ширин, один хром
 *     tools/shot.mjs --url /services/audit/obyazatelnyiy/ --out /tmp/A- 1440
 *     tools/shot.mjs --eval "document.querySelector('[data-promo-next]').click()" 1440
 *     tools/shot.mjs --expect 404 --url /net-takoi-stranicy/ 1440   # страница 404
 *
 * Гоняет headless Chrome по DevTools Protocol через `--remote-debugging-pipe`
 * (fd 3/4, сообщения JSON через `\0`). Зависимостей нет.
 *
 * Стенд — `TAXLAB_BASE` (по умолчанию http://localhost:8088): так же снимаются
 * полосы tools/lane.sh — `TAXLAB_BASE=http://localhost:8092 tools/shot.mjs 1440`.
 * Полный URL в `--url` базу перекрывает.
 *
 * ⚠️ Почему pipe, а не WebSocket: встроенный WebSocket Node (undici) рвёт
 * соединение на входящем сообщении больше 4 МиБ (жёсткий
 * kDefaultMaxDecompressedSize в permessage-deflate), и делает это молча.
 * Full-PNG длинной страницы с догруженными картинками весит >3 МБ (base64
 * >4 МиБ) — на WebSocket `--full` висел до watchdog на 5 из 14 страниц
 * эталона. Не возвращай WebSocket и `--remote-debugging-port`.
 *
 * ⚠️ Зачем не `chrome --screenshot`: с `--virtual-time-budget` он на десктопной
 * вёрстке этого сайта пишет PNG за 6 секунд и после этого НЕ ВЫХОДИТ НИКОГДА.
 * Проверено: 1440 + бюджет 6000/12000 — висит вечно; без бюджета или на 390 px
 * — выходит за 5 с. Кто именно держит виртуальное время, не установлено:
 * раньше здесь было написано «бесконечный таймер виджета qoopler», но замер
 * таймеров и rAF в простое (аудит 09.10.2026) показал, что qoopler их не
 * создаёт, — вероятнее Метрика или Venyoo. Одна сессия 7 сентября потеряла на
 * этом 19 минут: пять вызовов сгорели в таймаутах Bash по 120–400 с, хотя
 * картинки лежали на диске через 6 секунд, а поверх этого агент ждал их
 * слепыми `sleep 90/100/180`. Не возвращай `--screenshot` и
 * `--virtual-time-budget`, даже если кажется, что так короче.
 */

import { spawn, spawnSync } from "node:child_process";
import { mkdirSync, readdirSync, rmSync, writeFileSync } from "node:fs";
import { dirname } from "node:path";

const CHROME = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome";
const STAND = (process.env.TAXLAB_BASE || "http://localhost:8088").replace(/\/+$/, "");
const UA_IPHONE = "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) " +
  "AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1";

// ⚠️ Профиль — СВОЙ на прогон (грабли из dutch-exam/tools/shot.mjs: при общем
// профиле/порте новый прогон цепляется к хрому прошлого, если тот не успел
// умереть, и снимает чужое состояние). С pipe порта нет вовсе.
const PROFILE = `/tmp/taxlab-shot-profile-${process.pid}`;

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
const die = (msg) => { process.stderr.write(`shot.mjs: ${msg}\n`); process.exit(1); };

// ── аргументы ────────────────────────────────────────────────────────────────
const opt = { url: "/", out: "/tmp/shot-", height: 1200, wait: 400, full: false, eval: null, expect: 0 };
const widths = [];
for (let i = 2; i < process.argv.length; i++) {
  const a = process.argv[i];
  if (a === "--full") opt.full = true;
  else if (a === "--url") opt.url = process.argv[++i];
  else if (a === "--out") opt.out = process.argv[++i];
  else if (a === "--height") opt.height = Number(process.argv[++i]);
  else if (a === "--wait") opt.wait = Number(process.argv[++i]);
  else if (a === "--eval") opt.eval = process.argv[++i];
  else if (a === "--expect") opt.expect = Number(process.argv[++i]);
  else if (/^\d+m?$/.test(a)) widths.push({ w: parseInt(a, 10), mobile: a.endsWith("m") });
  else die(`не понял аргумент «${a}»`);
}
if (!widths.length) die("не задана ни одна ширина: tools/shot.mjs 1440 [1920 390m …]");
if (!opt.url) die("--url без значения");
const url = opt.url.startsWith("http") ? opt.url : STAND + opt.url;

// ── уборка за прошлыми прогонами ─────────────────────────────────────────────
// Прогон, убитый по таймауту или Ctrl-C, оставляет хром с профилем в /tmp:
// именно так на машине скопилось 9 процессов и 2.1 ГБ. Профиль назван по pid,
// так что мёртвого владельца видно сразу.
function sweepLeftovers() {
  for (const name of readdirSync("/tmp").filter((n) => n.startsWith("taxlab-shot-profile-"))) {
    const pid = Number(name.slice("taxlab-shot-profile-".length));
    if (!pid || pid === process.pid) continue;
    try { process.kill(pid, 0); continue; } catch { /* владелец мёртв — метём */ }
    spawn("pkill", ["-9", "-f", `user-data-dir=/tmp/${name}`], { stdio: "ignore" });
    rmSync(`/tmp/${name}`, { recursive: true, force: true });
  }
}

// ── запуск браузера ──────────────────────────────────────────────────────────
let child = null;

// Профиль убираем с повторами: хром догрызает свои файлы уже после SIGTERM, и
// первый `rm` ловит ENOTEMPTY. Не убрался — не беда, следующий прогон подметёт.
function rmProfile() {
  const opts = { recursive: true, force: true, maxRetries: 8, retryDelay: 100 };
  try { rmSync(PROFILE, opts); } catch {}
}

/** Штатный выход: даём хрому закрыться сам, потом чистим профиль. */
async function cleanup() {
  if (child) { try { child.kill("SIGTERM"); } catch {} child = null; }
  await sleep(400);
  rmProfile();
}

// Аварийный выход — падение, Ctrl-C, вышел watchdog. Тут ждать нечего: SIGKILL
// браузеру и хелперам (их видно по профилю в командной строке), иначе процессы
// переживут прогон. Та же дырка есть в dutch-exam/tools/shot.mjs: его `catch`
// глушит скрипт, но не браузер — оттуда на машине хромы недельной давности.
process.on("exit", () => {
  if (child) {
    try { process.kill(child.pid, "SIGKILL"); } catch {}
    spawnSync("pkill", ["-9", "-f", `user-data-dir=${PROFILE}`]);
    child = null;
  }
  rmProfile();
});
for (const sig of ["SIGINT", "SIGTERM", "SIGHUP"]) process.on(sig, () => process.exit(1));

// CDP через --remote-debugging-pipe: хром читает команды из fd 3 и пишет
// ответы в fd 4, каждое сообщение — JSON с нулевым байтом в конце. Ответ
// приходит кусками произвольной длины, поэтому собираем буфер до `\0`.
function launch() {
  child = spawn(CHROME, [
    "--headless=new", "--disable-gpu", "--hide-scrollbars",
    "--no-first-run", "--no-default-browser-check", "--disable-extensions",
    `--user-data-dir=${PROFILE}`, "--remote-debugging-pipe", "about:blank",
  ], { stdio: ["ignore", "ignore", "ignore", "pipe", "pipe"] });
  child.on("error", (e) => die(`Chrome не запустился: ${e.message}`));
  return child;
}

function connect(proc) {
  const out = proc.stdio[3], inp = proc.stdio[4];
  const waiting = new Map();
  const events = new Map();
  let id = 0, buf = [], closed = null;
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
  // Хром умер или закрыл канал — все ожидающие вызовы падают сразу, а не
  // висят до watchdog.
  const onClose = (why) => {
    if (closed) return;
    closed = new Error(`CDP-канал закрыт (${why})`);
    for (const { fail } of waiting.values()) fail(closed);
    waiting.clear();
  };
  inp.on("close", () => onClose("хром закрыл fd 4"));
  inp.on("error", (e) => onClose(e.message));
  out.on("error", (e) => onClose(e.message));
  proc.on("exit", (code, sig) => onClose(`хром вышел: ${sig || code}`));
  const send = (method, params = {}, sessionId) => new Promise((ok, fail) => {
    if (closed) return fail(closed);
    const n = ++id;
    waiting.set(n, { ok, fail });
    out.write(JSON.stringify({ id: n, method, params, sessionId }) + "\0");
  });
  // Событие с потолком: ждём загрузку, но если страница тянет внешний скрипт
  // (GTM, шрифты Google, cdnjs) — снимаем что есть, а не висим.
  const once = (method, ms) => new Promise((ok) => {
    const list = events.get(method) || events.set(method, []).get(method);
    list.push(ok);
    setTimeout(ok, ms);
  });
  return { send, once };
}

// ── прогон ───────────────────────────────────────────────────────────────────
async function main() {
  sweepLeftovers();
  const t0 = Date.now();

  // Стенд проверяем до запуска браузера. Иначе на потушенном docker'е или в
  // опечатке слага получается PNG страницы ошибки — и выглядит он как обычный
  // снимок, то есть врёт молча.
  const probe = await fetch(url).catch((e) => die(`стенд не отвечает (${url}): ${e.message}`));
  probe.body?.cancel();
  // --expect 404 — для снимка страницы «не найдено»: тогда врёт как раз 200.
  if (opt.expect ? probe.status !== opt.expect : probe.status >= 400) {
    die(`${url} отдаёт HTTP ${probe.status}${opt.expect ? `, ждали ${opt.expect}` : ""} — снимать нечего`);
  }
  const cdp = connect(launch());

  const { targetId } = await cdp.send("Target.createTarget", { url: "about:blank" });
  const { sessionId } = await cdp.send("Target.attachToTarget", { targetId, flatten: true });
  const call = (m, p) => cdp.send(m, p, sessionId);
  await call("Page.enable");

  for (const { w, mobile } of widths) {
    await call("Emulation.setDeviceMetricsOverride", {
      width: w, height: opt.height, deviceScaleFactor: 1, mobile,
    });
    // maxTouchPoints обязателен и только 1..16 — нулём CDP отвечает ошибкой.
    await call("Emulation.setTouchEmulationEnabled", {
      enabled: mobile, maxTouchPoints: mobile ? 5 : 1,
    });
    await call("Emulation.setUserAgentOverride", { userAgent: mobile ? UA_IPHONE : "" });
    // Перезаход, а не просто смена ширины: JS темы (flatsome) раскладывает
    // слайдеры и меню на load и по смене viewport сам не пересобирается.
    // Тёплый кеш делает это дешёвым — ~0.5 с на ширину.
    await call("Page.navigate", { url });
    await cdp.once("Page.loadEventFired", 15000);
    await sleep(opt.wait);

    // Доводим страницу до нужного состояния перед кадром: щёлкнуть стрелку
    // слайдера, раскрыть аккордеон, переключить таб. Выражение гоняется на
    // каждой ширине — перезаход сбрасывает состояние.
    if (opt.eval) {
      const r = await call("Runtime.evaluate", {
        expression: opt.eval, awaitPromise: true, returnByValue: true,
      });
      if (r.exceptionDetails) {
        die(`--eval упал: ${r.exceptionDetails.exception?.description || r.exceptionDetails.text}`);
      }
      // Значение выражения печатаем: тем же вызовом удобно снять замеры со
      // страницы, а не только довести её до нужного состояния.
      if (r.result && r.result.value !== undefined) {
        process.stderr.write(`--eval → ${JSON.stringify(r.result.value)}\n`);
      }
      await sleep(opt.wait);
    }

    // Кадр режем клипом, а не высотой окна: результат детерминированный и не
    // зависит от того, куда доскроллил предыдущий заход.
    let clip = { x: 0, y: 0, width: w, height: opt.height, scale: 1 };
    if (opt.full) {
      const { cssContentSize } = await call("Page.getLayoutMetrics");
      clip = { x: 0, y: 0, width: w, height: Math.ceil(cssContentSize.height), scale: 1 };
    }
    const { data } = await call("Page.captureScreenshot", {
      format: "png", clip, captureBeyondViewport: true,
    });
    const file = `${opt.out}${w}${mobile ? "m" : ""}.png`;
    mkdirSync(dirname(file), { recursive: true });
    writeFileSync(file, Buffer.from(data, "base64"));
    process.stdout.write(`${file}\n`);
  }

  await cleanup();
  const sec = ((Date.now() - t0) / 1000).toFixed(1);
  process.stderr.write(`${widths.length} снимк(ов) за ${sec} с\n`);
  process.exit(0);
}

// Общий потолок: что бы ни случилось внутри, прогон не переживёт двух минут —
// вызывающий агент не должен снова сидеть в таймауте Bash.
const watchdog = setTimeout(() => die("прогон не уложился в 120 с"), 120_000);
watchdog.unref();

main().catch((e) => die(e.stack || String(e)));
