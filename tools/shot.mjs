#!/usr/bin/env node
/**
 * Скриншоты локального стенда — инструмент разработки, не часть сайта.
 *
 *     tools/shot.mjs 1440                        # /tmp/shot-1440.png
 *     tools/shot.mjs 1280 1440 1600 1920 390m    # пять ширин, один хром
 *     tools/shot.mjs --url /services/audit/obyazatelnyiy/ --out /tmp/A- 1440
 *     tools/shot.mjs --eval "document.querySelector('[data-promo-next]').click()" 1440
 *
 * Гоняет headless Chrome по DevTools Protocol. Зависимостей нет — WebSocket
 * в Node встроенный.
 *
 * ⚠️ Зачем не `chrome --screenshot`: с `--virtual-time-budget` он на десктопной
 * вёрстке этого сайта пишет PNG за 6 секунд и после этого НЕ ВЫХОДИТ НИКОГДА.
 * Проверено: 1440 + бюджет 6000/12000 — висит вечно; без бюджета или на 390 px
 * — выходит за 5 с. На 390 не висит потому, что бесконечный таймер держит
 * десктопный элемент (виджет qoopler), а на телефонной вёрстке его нет: пока
 * таймеры не кончились, виртуальное время не истекает и браузер не завершается.
 * Одна сессия 7 сентября потеряла на этом 19 минут: пять вызовов сгорели в
 * таймаутах Bash по 120–400 с, хотя картинки лежали на диске через 6 секунд, а
 * поверх этого агент ждал их слепыми `sleep 90/100/180`. Не возвращай
 * `--screenshot` и `--virtual-time-budget`, даже если кажется, что так короче.
 */

import { spawn, spawnSync } from "node:child_process";
import { mkdirSync, readdirSync, rmSync, writeFileSync } from "node:fs";
import { dirname } from "node:path";

const CHROME = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome";
const STAND = "http://localhost:8088";
const UA_IPHONE = "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) " +
  "AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1";

// ⚠️ Порт и профиль — СВОИ на прогон (грабли из dutch-exam/tools/shot.mjs: при
// общем порте новый прогон цепляется к хрому прошлого, если тот не успел умереть,
// и снимает чужое состояние).
const PORT = 9300 + (process.pid % 190);
const PROFILE = `/tmp/taxlab-shot-profile-${process.pid}`;

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
const die = (msg) => { process.stderr.write(`shot.mjs: ${msg}\n`); process.exit(1); };

// ── аргументы ────────────────────────────────────────────────────────────────
const opt = { url: "/", out: "/tmp/shot-", height: 1200, wait: 400, full: false, eval: null };
const widths = [];
for (let i = 2; i < process.argv.length; i++) {
  const a = process.argv[i];
  if (a === "--full") opt.full = true;
  else if (a === "--url") opt.url = process.argv[++i];
  else if (a === "--out") opt.out = process.argv[++i];
  else if (a === "--height") opt.height = Number(process.argv[++i]);
  else if (a === "--wait") opt.wait = Number(process.argv[++i]);
  else if (a === "--eval") opt.eval = process.argv[++i];
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

async function launch() {
  child = spawn(CHROME, [
    "--headless=new", "--disable-gpu", "--hide-scrollbars",
    "--no-first-run", "--no-default-browser-check", "--disable-extensions",
    `--user-data-dir=${PROFILE}`, `--remote-debugging-port=${PORT}`, "about:blank",
  ], { stdio: "ignore" });
  for (let i = 0; i < 80; i++) {              // ~1 с в норме, потолок 20 с
    try {
      const r = await fetch(`http://127.0.0.1:${PORT}/json/version`);
      return (await r.json()).webSocketDebuggerUrl;
    } catch { await sleep(250); }
  }
  die("Chrome не поднял отладочный порт за 20 с");
}

function connect(wsUrl) {
  const ws = new WebSocket(wsUrl);
  const waiting = new Map();
  const events = new Map();
  let id = 0;
  ws.addEventListener("message", (m) => {
    const msg = JSON.parse(m.data);
    if (msg.id && waiting.has(msg.id)) {
      const { ok, fail } = waiting.get(msg.id);
      waiting.delete(msg.id);
      msg.error ? fail(new Error(msg.error.message)) : ok(msg.result);
    } else if (msg.method) {
      (events.get(msg.method) || []).splice(0).forEach((fn) => fn(msg.params));
    }
  });
  const ready = new Promise((ok, fail) => {
    ws.addEventListener("open", ok);
    ws.addEventListener("error", () => fail(new Error("не подключился к CDP")));
  });
  const send = (method, params = {}, sessionId) => new Promise((ok, fail) => {
    const n = ++id;
    waiting.set(n, { ok, fail });
    ws.send(JSON.stringify({ id: n, method, params, sessionId }));
  });
  // Событие с потолком: ждём загрузку, но если страница тянет внешний скрипт
  // (GTM, шрифты Google, cdnjs) — снимаем что есть, а не висим.
  const once = (method, ms) => new Promise((ok) => {
    const list = events.get(method) || events.set(method, []).get(method);
    list.push(ok);
    setTimeout(ok, ms);
  });
  return { ready, send, once, close: () => ws.close() };
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
  if (probe.status >= 400) die(`${url} отдаёт HTTP ${probe.status} — снимать нечего`);
  const cdp = connect(await launch());
  await cdp.ready;

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

  cdp.close();
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
