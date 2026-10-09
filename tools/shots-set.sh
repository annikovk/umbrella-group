#!/usr/bin/env bash
# Визуальный эталон стенда для попиксельного сравнения «до/после» рефакторинга.
#
#     tools/shots-set.sh [outdir]                 # снять набор (по умолчанию /tmp/taxlab-shots)
#     tools/shots-set.sh diff <dirA> <dirB> <out> # сравнить два набора
#
# Снимает каждую страницу из PAGES целиком (--full) на 1440 и 390m через
# tools/shot.mjs, файлы — <outdir>/<slug>-1440.png и <outdir>/<slug>-390m.png
# (slug — путь через «-», главная = home). Ширины можно переопределить:
# WIDTHS="1280 1440 390m" tools/shots-set.sh /tmp/x
# Одну страницу: PAGES_ONLY="/faq/" tools/shots-set.sh /tmp/x
#
# Стабилизация (выражение STABILIZE ниже, гоняется через --eval перед кадром):
#  - анимации, transition, caret выключены стилем *{…!important};
#  - страница прокручивается до низа шагами (два прохода) — срабатывают
#    lazy-load (data-src flatsome, loading="lazy", lazy-фоны секций), потом
#    ждём decode всех <img> и всех url() из computed background-image,
#    document.fonts.ready и возвращаемся на верх;
#  - внешние <img> (flamp.ru, пиксели vk.com) подменяются прозрачным
#    квадратом: flamp отвечает 403 части клиентов, и логотип в отзывах то
#    грузится, то нет — с другой высотой карточки;
#  - loading=lazy → eager до прокрутки, data-srcset flatsome подставляется
#    вместе с data-src, битые (404) картинки прячутся visibility:hidden —
#    их список печатается в отчёте прогона («broken»);
#  - слайдеры (flickity, swiper) ставятся на паузу и откатываются на 1-й слайд;
#  - видео — на паузу, кадр 0;
#  - внешние виджеты, которые тянутся из интернета и рисуются по-разному
#    от прогона к прогону (jivo, marquiz, qoopler, recaptcha-бейдж, iframe'ы
#    карт/rutube/GTM), прячутся visibility:hidden — место в вёрстке они
#    сохраняют, так что сдвиги вокруг них diff всё равно поймает (видео
#    rutube на главной поэтому — белый прямоугольник); плашка cookie
#    убирается совсем — иначе она закрывает CTA первого экрана;
#
# Diff: python3 + Pillow (numpy/cv2/ImageMagick на машине нет, Pillow хватает).
# Печатает % отличающихся пикселей по каждой паре и кладёт <out>/<name>.png —
# красная маска отличий поверх обесцвеченного кадра A. Разная высота страниц
# — сама по себе находка: печатается, сравнение идёт по общей части,
# «лишний» хвост целиком считается отличием.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
# ⚠️ tools/shot.mjs ходит в хром через встроенный WebSocket Node (undici), а тот
# рвёт соединение на входящем сообщении больше 4 МиБ (жёсткий
# kDefaultMaxDecompressedSize в permessage-deflate). Full-PNG длинной страницы
# с догруженными картинками весит >3 МБ (base64 >4 МиБ) — и shot.mjs молча висит
# до своего watchdog в 120 с. Пока shot.mjs не переведён на
# --remote-debugging-pipe, подставляй исправленную копию: SHOT=/путь/shot-pipe.mjs.
SHOT="${SHOT:-$ROOT/tools/shot.mjs}"

PAGES=(
  /
  /services/
  /services/bukhgalterskie-uslugi/
  /services/licensing/
  /services/licensing/roznichnaya-torgovlya-alkogolem/
  /services/register-elimination/likvidatsiya-ooo/
  /services/services-le/predstavitelstvo-v-arbitrazhnom-sude/
  /blog/
  /blog/smena-direktora-v-ooo/
  /price/ceny-na-buhgalterskie-uslugi/
  /faq/
  /about/
  /about/contacts/
  /akcii/
)
read -r -a WIDTHS_ARR <<< "${WIDTHS:-1440 390m}"

slug() {
  local s="${1#/}"; s="${s%/}"
  [[ -z "$s" ]] && { echo home; return; }
  echo "${s//\//-}"
}

# Выражение для --eval: возвращает Promise, shot.mjs его дожидается.
read -r -d '' STABILIZE <<'JS' || true
(async () => {
  const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
  const css = document.createElement("style");
  css.id = "shots-set-stabilize";
  css.textContent = `
    *, *::before, *::after {
      animation: none !important; animation-delay: 0s !important;
      transition: none !important; caret-color: transparent !important;
      scroll-behavior: auto !important;
    }
    iframe, jdiv, #jivo-iframe-container, [id^="jivo"], [class*="jivo"],
    [class*="marquiz"], [id*="marquiz"], [class*="qoopler"], [id*="qoopler"],
    [class*="leadia"], [id*="leadia"], [class^="pv_"],
    .grecaptcha-badge { visibility: hidden !important; }
    /* Плашка cookie (свежий профиль хрома → видна всегда) стоит position:fixed
       и в full-кадре закрывает CTA первого экрана и рейтинги — прячем, чтобы
       регрессии под ней не терялись. */
    .cookie-notice { display: none !important; }
  `;
  document.head.appendChild(css);

  // Внешние картинки (flamp.ru отдаёт 403 части IP/UA — логотип то есть, то
  // битый значок с alt и другой высотой) подменяем прозрачным квадратом до
  // загрузки: в эталоне их нет, зато нет и случайного шума. Пиксели vk.com
  // (position:fixed за экраном) это тоже гасит.
  const BLANK = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'/%3E";
  const external = (u) => /^https?:/.test(u) && !u.startsWith(location.origin);
  const neuter = () => document.querySelectorAll("img").forEach((img) => {
    const u = img.dataset.src || img.getAttribute("src") || "";
    if (external(u)) { img.removeAttribute("data-src"); img.removeAttribute("srcset"); img.src = BLANK; }
  });
  neuter();
  // loading=lazy → eager сразу: иначе картинка вне экрана (или в скрытом
  // табе) грузится, когда повезёт, и 404-значок/высота меняются от прогона
  // к прогону.
  document.querySelectorAll("img[loading=lazy]").forEach((img) => (img.loading = "eager"));

  // Прокрутка до низа (дважды) — будит lazy-load и IntersectionObserver'ы.
  const step = Math.max(200, Math.floor(innerHeight * 0.8));
  for (let pass = 0; pass < 2; pass++) {
    for (let y = 0; y < document.documentElement.scrollHeight; y += step) {
      scrollTo(0, y);
      await sleep(pass ? 30 : 120);
    }
    scrollTo(0, document.documentElement.scrollHeight);
    await sleep(400);
  }

  // flatsome lazy-load: если что-то осталось с data-src — подставить вручную.
  neuter();
  // srcset тоже: иначе flatsome чуть позже ставит data-srcset, браузер
  // перевыбирает кандидата и картинка доезжает уже после кадра (так шумели
  // карточки /blog/ на 390m).
  document.querySelectorAll("img[data-src]").forEach((img) => {
    if (!img.getAttribute("src") || img.getAttribute("src").startsWith("data:")) {
      if (img.dataset.srcset) img.srcset = img.dataset.srcset;
      img.src = img.dataset.src;
    }
  });

  // Слайдеры: пауза и первый слайд.
  if (window.jQuery) {
    jQuery(".flickity-enabled").each(function () {
      const f = jQuery(this).data("flickity");
      if (f) { f.pausePlayer && f.pausePlayer(); f.stopPlayer && f.stopPlayer(); f.select(0, false, true); }
    });
  }
  document.querySelectorAll(".swiper, .swiper-container").forEach((el) => {
    const s = el.swiper;
    if (s) { s.autoplay && s.autoplay.stop(); s.slideTo(0, 0, false); }
  });
  document.querySelectorAll("video").forEach((v) => { try { v.pause(); v.currentTime = 0; } catch {} });

  // Ждём <img>, CSS-фоны и шрифты (с потолком — битая картинка не вешает прогон).
  // Обработчики через addEventListener: img.onload= затёр бы обработчики темы.
  const imgs = [...document.images].map((img) => img.complete
    ? img.decode?.().catch(() => {})
    : new Promise((r) => { img.addEventListener("load", r, { once: true }); img.addEventListener("error", r, { once: true }); }));
  const bgUrls = new Set();
  for (const el of document.querySelectorAll("*")) {
    for (const m of getComputedStyle(el).backgroundImage.matchAll(/url\("?([^")]+)"?\)/g)) {
      if (!m[1].startsWith("data:")) bgUrls.add(m[1]);
    }
  }
  const bgs = [...bgUrls].map((u) => { const i = new Image(); i.src = u; return i.decode().catch(() => {}); });
  await Promise.race([Promise.all([...imgs, ...bgs]), sleep(10000)]);
  await Promise.race([document.fonts.ready, sleep(5000)]);
  // Второй круг: flatsome/слайдеры могли за это время переставить src/srcset.
  await sleep(300);
  await Promise.race([Promise.all([...document.images].filter((i) => !i.complete).map((img) =>
    new Promise((r) => { img.addEventListener("load", r, { once: true }); img.addEventListener("error", r, { once: true }); }))), sleep(5000)]);
  await Promise.all([...document.images].filter((i) => i.complete && i.naturalWidth).map((i) => i.decode().catch(() => {})));

  // decoding=sync: при full-кадре хром растрирует всю длинную страницу разом,
  // и картинку с decoding="async" (так WP ставит по умолчанию) далеко от
  // вьюпорта иногда рисует пустой — баннер акции на /services/licensing/ 390m
  // пропадал примерно в каждом 4-м прогоне.
  for (const i of document.images) i.decoding = "sync";

  // Битые картинки (404 в зеркале) прячем: значок «broken image» рисуется
  // только если браузер успел попытаться — источник шума. Место остаётся.
  const broken = [...document.images].filter((i) => i.complete && !i.naturalWidth && !i.src.startsWith("data:"));
  broken.forEach((i) => (i.style.visibility = "hidden"));

  scrollTo(0, 0);
  await sleep(600);
  return { h: document.documentElement.scrollHeight,
           pending: [...document.images].filter((i) => !i.complete).length,
           broken: [...new Set(broken.map((i) => i.currentSrc.replace(location.origin, "")))],
           bg: bgUrls.size };
})()
JS

take() {
  local out="${1:-/tmp/taxlab-shots}"
  mkdir -p "$out"
  local pages=("${PAGES[@]}")
  [[ -n "${PAGES_ONLY:-}" ]] && read -r -a pages <<< "$PAGES_ONLY"
  local t0=$SECONDS fail=0
  for p in "${pages[@]}"; do
    local s; s="$(slug "$p")"
    printf '%-60s ' "$p"
    if node "$SHOT" --full --wait 800 --url "$p" --out "$out/$s-" --eval "$STABILIZE" \
         "${WIDTHS_ARR[@]}" >/dev/null 2>"$out/.$s.log"; then
      grep -o '{"h".*' "$out/.$s.log" | tr '\n' ' '; echo
    else
      echo "FAIL: $(tail -1 "$out/.$s.log")"; fail=1
    fi
  done
  echo "готово за $((SECONDS - t0)) с → $out"
  return $fail
}

diff_sets() {
  local a="$1" b="$2" out="$3"
  mkdir -p "$out"
  python3 - "$a" "$b" "$out" <<'PY'
import sys, os
from PIL import Image, ImageChops, ImageOps
Image.MAX_IMAGE_PIXELS = None
a_dir, b_dir, out = sys.argv[1:4]
names = sorted(n for n in os.listdir(a_dir) if n.endswith(".png"))
worst = 0.0
print(f"{'файл':<62} {'размер A':>12} {'размер B':>12} {'отлич.%':>8}")
for n in names:
    pb = os.path.join(b_dir, n)
    if not os.path.exists(pb):
        print(f"{n:<62} нет в B"); worst = 100; continue
    A = Image.open(os.path.join(a_dir, n)).convert("RGB")
    B = Image.open(pb).convert("RGB")
    w, h = max(A.width, B.width), max(A.height, B.height)
    cw, ch = min(A.width, B.width), min(A.height, B.height)
    d = ImageChops.difference(A.crop((0, 0, cw, ch)), B.crop((0, 0, cw, ch)))
    # Пиксель «отличается», если хоть один канал разошёлся больше чем на 8/255
    # (антиалиасинг/субпиксель не считаем).
    r, g, bb = d.split()
    thr = lambda ch_: ch_.point(lambda v: 255 if v > 8 else 0)
    mask_c = ImageChops.lighter(ImageChops.lighter(thr(r), thr(g)), thr(bb))
    mask = Image.new("L", (w, h), 255)          # хвост за пределами общей части — отличие
    mask.paste(mask_c, (0, 0))
    diff_px = mask.histogram()[255]
    pct = 100.0 * diff_px / (w * h)
    worst = max(worst, pct)
    size_a, size_b = f"{A.width}x{A.height}", f"{B.width}x{B.height}"
    flag = "" if A.size == B.size else "  ← разная высота"
    print(f"{n:<62} {size_a:>12} {size_b:>12} {pct:>8.4f}{flag}")
    if diff_px:
        base = ImageOps.grayscale(A).convert("RGB")
        canvas = Image.new("RGB", (w, h), (255, 255, 255)); canvas.paste(base, (0, 0))
        canvas = Image.blend(canvas, Image.new("RGB", (w, h), (255, 255, 255)), 0.6)
        red = Image.new("RGB", (w, h), (255, 0, 0))
        canvas.paste(red, (0, 0), mask)
        canvas.save(os.path.join(out, n))
        # Строки с отличиями — чтобы сразу видеть, где искать.
        bbox = mask.getbbox()
        if bbox: print(f"{'':<62} область отличий y={bbox[1]}..{bbox[3]} x={bbox[0]}..{bbox[2]}")
print(f"максимум: {worst:.4f}%")
PY
}

case "${1:-}" in
  diff) [[ $# -eq 4 ]] || { echo "usage: $0 diff <dirA> <dirB> <outdir>" >&2; exit 2; }
        diff_sets "$2" "$3" "$4" ;;
  -h|--help) sed -n '2,30p' "$0" ;;
  *) take "${1:-}" ;;
esac
