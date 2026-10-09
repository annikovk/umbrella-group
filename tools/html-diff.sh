#!/usr/bin/env bash
# Нормализованный HTML-дифф стенда «до/после» (спека 1, §4.3) — инструмент
# разработки, не часть сайта.
#
#     tools/html-diff.sh snapshot <dir>          # 301 URL × desktop/iPhone → <dir>
#     tools/html-diff.sh diff <A> <B> [out]      # сравнить два снимка
#     tools/html-diff.sh normalize <file.html>   # показать, что видит diff
#
# Снимок: для каждого пути из tools/html-urls.txt (или файла URLS=…) два curl —
# desktop UA и iPhone UA (тот же, что у tools/shot.mjs; по нему срабатывает
# wp_is_mobile) — в <dir>/<slug>.<desktop|iphone>.html, заголовки ответа рядом
# в .headers, сводка «путь, UA, HTTP-код, байты» в <dir>/_index.tsv. Сохраняется
# сырой HTML: нормализация делается при diff и может меняться без пересъёмки.
#
# Стенд — TAXLAB_BASE (по умолчанию http://localhost:8088), полоса tools/lane.sh:
#     TAXLAB_BASE=http://localhost:8092 tools/html-diff.sh snapshot /tmp/html-ws-2
# Параллельность — JOBS (по умолчанию 4; стенд на одной машине, больше не надо).
# Подмножество путей: URLS=/tmp/my-urls.txt tools/html-diff.sh snapshot /tmp/x
#
# Нормализация перед сравнением (всё, что меняется от запроса к запросу при
# неизменном коде, — проверено двумя снимками подряд):
#  - `?ver=…` / `&ver=…` у ассетов;
#  - nonce: `_wpnonce`, `"nonce":"…"`, `data-nonce`, `*_nonce` в JS-объектах
#    (на стенде анонимно их сейчас нет, правило — на случай кэша/форм);
#  - `<meta name="ip">` (REMOTE_ADDR), комментарий кэша WP Rocket;
#  - значения cookie в разметку не попадают: curl ходит без cookie, поэтому
#    атрибуция UTM и A/B-cookie в HTML всегда «первого визита»;
#  - случайные id Flatsome (`section_…`, `row-…`, `col-…`, `banner-…`,
#    `image_…`, `text-box-…`, `gap-…` …: в 3.13 это rand()) и uniqid()
#    (13 hex, якоря плиток клиентов) — заменяются на N по порядку появления, так
#    что ссылка `href="#id"` на соседний `id` по-прежнему сравнивается;
#  - порядок терминов, который сервер отдаёт случайно: табы-фильтры
#    клиентов/отзывов (кнопки сортируются по name), классы отраслей у карточек
#    (классы в class="" сортируются везде), `articleSection` в schema.org AIOSEO;
#  - адрес стенда (`TAXLAB_BASE` из _base.txt снимка, также \/-экранированный,
#    %-кодированный и без схемы) → {BASE}/{HOST}: полоса сравнима с основным;
#  - пробелы между тегами схлопываются, после каждого `>` — перенос строки,
#    чтобы diff был построчным.
# Отличия, оставшиеся после этого, — настоящие.
#
# Diff печатает по каждому файлу «одинаково / N строк отличий / нет в B /
# HTTP-код разный», в [out] кладёт <slug>.<ua>.diff (unified, 3 строки контекста),
# в конце — итог. Код выхода 1, если отличия есть.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
export TAXLAB_BASE="${TAXLAB_BASE:-http://localhost:8088}"
URLS="${URLS:-$ROOT/tools/html-urls.txt}"
JOBS="${JOBS:-4}"
UA_DESKTOP="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36"
UA_IPHONE="Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1"
export UA_DESKTOP UA_IPHONE

# slug: путь без крайних «/», «/» → «-», прочее не [A-Za-z0-9._-] → «_»;
# длинные (%-кодированная кириллица) — обрезка + 8 символов md5, чтобы имя
# файла влезло и не совпало с соседним. Главная = home.
slug_py='
import sys, re, hashlib
for line in sys.stdin:
    p = line.rstrip("\n")
    s = p.strip("/")
    t = re.sub(r"[^A-Za-z0-9._-]", "_", s.replace("/", "-")) or "home"
    if s and (t != s.replace("/", "-") or len(t) > 100):   # что-то заменили — не склеить с соседом
        t = t[:90] + "~" + hashlib.md5(p.encode()).hexdigest()[:8]
    s = t
    print(p + "\t" + s)
'

fetch_one() {   # $1=путь $2=slug $3=ua-имя $4=dir
  local ua; [[ "$3" == iphone ]] && ua="$UA_IPHONE" || ua="$UA_DESKTOP"
  local f="$4/$2.$3"
  local code
  code=$(curl -s -m 90 -A "$ua" -D "$f.headers" -o "$f.html" -w '%{http_code}' \
         "$TAXLAB_BASE$1" || echo 000)
  printf '%s\t%s\t%s\t%s\n' "$1" "$3" "$code" "$(wc -c < "$f.html" | tr -d ' ')"
}
export -f fetch_one

snapshot() {
  local dir="${1:?usage: $0 snapshot <dir>}"
  mkdir -p "$dir"
  [[ -f "$URLS" ]] || { echo "нет списка путей: $URLS" >&2; exit 2; }
  curl -s -o /dev/null -m 30 "$TAXLAB_BASE/" || { echo "стенд не отвечает: $TAXLAB_BASE" >&2; exit 2; }
  local t0=$SECONDS
  echo "стенд: $TAXLAB_BASE, путей: $(grep -c . "$URLS"), JOBS=$JOBS → $dir"
  grep -v '^[[:space:]]*$' "$URLS" | python3 -c "$slug_py" \
    | while IFS=$'\t' read -r p s; do printf '%s\t%s\tdesktop\n%s\t%s\tiphone\n' "$p" "$s" "$p" "$s"; done \
    | tr '\t\n' '\0\0' | xargs -0 -n 3 -P "$JOBS" bash -c 'fetch_one "$0" "$1" "$2" "'"$dir"'"' \
    | sort > "$dir/_index.tsv"
  printf '%s\n' "$TAXLAB_BASE" > "$dir/_base.txt"
  echo "готово за $((SECONDS - t0)) с: $(wc -l < "$dir/_index.tsv") файлов; коды:" \
       "$(cut -f3 "$dir/_index.tsv" | sort | uniq -c | tr -s ' ' | tr '\n' ',' | sed 's/,$//')"
}

# Нормализатор и сравнение — на Python (в системе есть python3, зависимостей нет).
read -r -d '' NORM_PY <<'PY' || true
import re, sys, os, difflib

def base_of(d):
    try: return open(os.path.join(d, "_base.txt")).read().strip()
    except OSError: return None

# Случайный id Flatsome (в 3.13 это rand()) там, где это id: в атрибуте
# id/for/aria-*, в href="#…" и в CSS-селекторе «#…». У префиксов шорткодов
# Flatsome (`section_`, `row-`, `col-`, `banner-`, `image_`, `text-box-`, `gap-`,
# `slider-`, `stack-` …) — любой длины: rand() изредка даёт и `row-2281`; у
# прочих «слово-<6+ цифр>», чтобы не трогать `menu-item-123`, `post-45`, `text-2`.
_ID_CTX = r"(?:(?<=id=\")|(?<=id=')|(?<=#)|(?<=for=\")|(?<=-controls=\")|(?<=-labelledby=\"))"
FLATSOME_ID = re.compile(
    _ID_CTX + r"((?:section_|row-|col-|banner-grid-|banner-|image_|text-box-|gap-|slider-|"
    r"stack-|ux-video-|map-|timer-|cell-|portfolio-|gallery-|tabgroup-|accordion-)"
    r"|[a-z][a-z0-9]*(?:[-_][a-z0-9]+)*?[-_](?=\d{6,}\b))(\d+)\b")
UNIQID = re.compile(r"(?<![0-9a-f])[0-9a-f]{13}(?![0-9a-f])")

def sort_filter_tabs(m):
    block = m.group(0)
    btns = re.findall(r'<button class="btn-filter".*?</button>', block, re.S)
    if not btns or not re.search(r"Все (клиенты|отзывы)", block): return block
    head = [b for b in btns if 'name="all"' in b]
    rest = sorted((b for b in btns if 'name="all"' not in b),
                  key=lambda b: re.search(r'name="([^"]*)"', b).group(1))
    it = iter(head + rest)
    return re.sub(r'<button class="btn-filter".*?</button>', lambda _: next(it), block, flags=re.S)

def normalize(html, base=None):
    if base:   # снимки с разных портов (полоса vs основной стенд) сравнимы
        from urllib.parse import quote
        for form in (base, base.replace("/", "\\/"), quote(base, safe=""), quote(base, safe="").lower()):
            html = html.replace(form, "{BASE}")
        host = base.split("://", 1)[-1]          # //localhost:8088/… без схемы
        for form in (host, quote(host, safe=""), quote(host, safe="").lower()):
            html = html.replace(form, "{HOST}")
    html = re.sub(r"([?&](?:#038;|amp;)?)ver=[^\"'&\s>]*", r"\1ver=V", html)
    html = re.sub(r'(name="_wpnonce"\s+value=")[^"]*', r"\1NONCE", html)
    html = re.sub(r'((?:["\']|\b)[A-Za-z_]*nonce["\']?\s*[:=]\s*["\']?)[0-9a-f]{8,12}', r"\1NONCE", html, flags=re.I)
    html = re.sub(r'(data-[a-z-]*nonce=")[^"]*', r"\1NONCE", html)
    html = re.sub(r'<meta name="ip"[^>]*>', "", html)
    html = re.sub(r"<!--\s*This website is like a Rocket.*?-->", "", html, flags=re.S)
    html = re.sub(r"<!--\s*(Rocket|WP Rocket|Performance optimized|Page generated|Cached).*?-->", "", html, flags=re.S | re.I)
    html = re.sub(r'(<div class="toolbar">)(.*?)(</div>)', sort_filter_tabs, html, flags=re.S)
    # Тот же случайный порядок терминов — в разметке schema.org от AIOSEO.
    html = re.sub(r'("articleSection":")([^"]*)"', lambda m: m.group(1) + ", ".join(sorted(m.group(2).split(", "))) + '"', html)
    # Случайные id → N по порядку первого появления (связи id↔href сохраняются).
    seen = {}
    def ren(key):
        if key not in seen: seen[key] = str(len(seen) + 1)
        return seen[key]
    html = FLATSOME_ID.sub(lambda m: m.group(1) + "R" + ren(m.group(2)), html)
    html = UNIQID.sub(lambda m: "U" + ren(m.group(0)), html)
    # Порядок классов (отрасли клиентов/отзывов приходят в случайном порядке
    # вместе с табами) на вёрстку не влияет — сортируем.
    html = re.sub(r'class="([^"]*)"', lambda m: 'class="' + " ".join(sorted(m.group(1).split())) + '"', html)
    html = re.sub(r">\s+<", "><", html)
    html = re.sub(r"[ \t]+", " ", html)
    return html.replace(">", ">\n")

def main():
    cmd = sys.argv[1]
    if cmd == "normalize":
        sys.stdout.write(normalize(open(sys.argv[2], encoding="utf-8", errors="replace").read()))
        return 0
    a_dir, b_dir, out = sys.argv[2], sys.argv[3], (sys.argv[4] if len(sys.argv) > 4 else None)
    if out: os.makedirs(out, exist_ok=True)
    ba, bb = base_of(a_dir), base_of(b_dir)
    def index(d):
        r = {}
        try:
            for line in open(os.path.join(d, "_index.tsv")):
                p, ua, code, size = line.rstrip("\n").split("\t")
                r[(p, ua)] = code
        except OSError: pass
        return r
    ia, ib = index(a_dir), index(b_dir)
    names = sorted(n for n in os.listdir(a_dir) if n.endswith(".html"))
    same = diff_files = missing = 0
    total_lines = 0
    print(f"{'файл':<72} {'итог':>20}")
    for n in names:
        pb = os.path.join(b_dir, n)
        if not os.path.exists(pb):
            print(f"{n:<72} {'нет в B':>20}"); missing += 1; continue
        A = normalize(open(os.path.join(a_dir, n), encoding="utf-8", errors="replace").read(), ba).splitlines()
        B = normalize(open(pb, encoding="utf-8", errors="replace").read(), bb).splitlines()
        if A == B:
            same += 1; continue
        d = list(difflib.unified_diff(A, B, f"A/{n}", f"B/{n}", n=3, lineterm=""))
        changed = sum(1 for l in d if l[:1] in "+-" and not l.startswith(("+++", "---")))
        total_lines += changed; diff_files += 1
        print(f"{n:<72} {str(changed) + ' строк':>20}")
        if out:
            with open(os.path.join(out, n[:-5] + ".diff"), "w") as f: f.write("\n".join(d) + "\n")
    codes = [(k, ia[k], ib.get(k)) for k in sorted(ia) if ia[k] != ib.get(k)]
    for (p, ua), ca, cb in codes:
        print(f"HTTP-код разный: {p} [{ua}] {ca} → {cb}")
    print(f"итого: {len(names)} файлов, одинаковых {same}, с отличиями {diff_files} "
          f"({total_lines} строк), нет в B {missing}, разных кодов {len(codes)}")
    return 1 if (diff_files or missing or codes) else 0

sys.exit(main())
PY

case "${1:-}" in
  snapshot)  snapshot "${2:-}" ;;
  diff)      [[ $# -ge 3 ]] || { echo "usage: $0 diff <A> <B> [out]" >&2; exit 2; }
             python3 -c "$NORM_PY" diff "$2" "$3" "${4:-}" ;;
  normalize) [[ $# -eq 2 ]] || { echo "usage: $0 normalize <file.html>" >&2; exit 2; }
             python3 -c "$NORM_PY" normalize "$2" ;;
  *) sed -n '2,40p' "$0"; exit 1 ;;
esac
