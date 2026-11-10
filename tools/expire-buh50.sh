#!/usr/bin/env bash
# Снять акцию «−50% на первый месяц бухгалтерского обслуживания» после 31.12.2026:
# страницу акции (akcii 31402, /akcii/buhgalterskoe-obsluzhivanie-skidka-50/) — в черновик,
# сбросить кэш WP Rocket и Redis, проверить главную и страницу акции.
#
#   tools/expire-buh50.sh            по расписанию: до 2027-01-01 (время сайта) молчит;
#                                    уже снято и проверено — тоже молчит
#   tools/expire-buh50.sh --dry-run  ничего не меняет, печатает, что сделал бы и что на сайте сейчас
#
# Слайд главной, всплывающее окно и блок [buh50_promo] прячутся сами по дате (PHP:
# 'until' в new-main-first-screen.php, ug_buh50_active() в promo_buh50.php; JS —
# data-until). Страницы, закэшированные WP Rocket до полуночи, освежает сброс кэша здесь.
#
# Запускает крон tg-manager (.tg-manager/schedule.json, задача «Снять акцию −50%»):
# cron по времени машины (CET) «5,35 18-23 31 12 *» = 00:05…05:35 1 января по
# Новосибирску (+07:00) — повторы на случай, если ssh-агент откажет. Дата
# проверяется по часам сервера (wp_date), поэтому раньше полуночи на сайте ничего
# не произойдёт. После успешного снятия задачу удалить из .tg-manager/schedule.json.
set -euo pipefail

DRY=0
[[ "${1:-}" == "--dry-run" ]] && DRY=1

POST=31402
SLUG=/akcii/buhgalterskoe-obsluzhivanie-skidka-50/
START=2027-01-01

ssh -o BatchMode=yes -o ConnectTimeout=20 annikov.com bash -s -- "$DRY" "$POST" "$SLUG" "$START" <<'REMOTE'
set -uo pipefail
DRY=$1 POST=$2 SLUG=$3 START=$4
WP="sudo -u taxlabru /opt/plesk/php/8.3/bin/php /usr/local/bin/wp --path=/var/www/vhosts/taxlab.ru/httpdocs"
CURL="curl -sk --max-time 30 --connect-to taxlab.ru:443:127.0.0.1:7081"

today=$($WP eval 'echo wp_date("Y-m-d");' 2>/dev/null)
status=$($WP post get "$POST" --field=post_status 2>/dev/null || echo "нет записи")
slide=$($CURL https://taxlab.ru/ | grep -c 'promo-slide-buh50' || true)
code=$($CURL -o /dev/null -w '%{http_code}' "https://taxlab.ru$SLUG")

report() {
    echo "Дата на сайте: $today; запись $POST: $(${WP} post get "$POST" --field=post_status 2>/dev/null)"
    echo "Главная: слайд акции $( [[ $1 -eq 0 ]] && echo 'не выводится' || echo 'ВЫВОДИТСЯ'); страница акции: HTTP $2"
}

if [[ $DRY -eq 1 ]]; then
    echo "[dry-run] дата на сайте $today, порог $START"
    echo "[dry-run] запись $POST: $status; слайд на главной: $slide; страница акции: HTTP $code"
    if [[ "$today" < "$START" ]]; then
        echo "[dry-run] рано — по расписанию ничего бы не делал"
    else
        echo "[dry-run] срок наступил — перевёл бы $POST в draft и сбросил кэш"
    fi
    exit 0
fi

# Ещё не время: молча выходим (крон повторяется ежегодно).
[[ "$today" < "$START" ]] && exit 0

# Уже снято и сайт в порядке — молчим, чтобы повторы не шумели в топике.
if [[ "$status" == "draft" && "$slide" -eq 0 && "$code" != "200" ]]; then
    exit 0
fi

if [[ "$status" == "publish" ]]; then
    $WP post update "$POST" --post_status=draft >/dev/null 2>&1 || { echo "❌ не удалось перевести $POST в черновик"; exit 1; }
fi
$WP eval 'rocket_clean_minify(); rocket_clean_domain(); wp_cache_flush();' >/dev/null 2>&1 || { echo "❌ сброс кэша не прошёл"; exit 1; }

slide=$($CURL https://taxlab.ru/ | grep -c 'promo-slide-buh50' || true)
code=$($CURL -o /dev/null -w '%{http_code}' "https://taxlab.ru$SLUG")
if [[ "$slide" -eq 0 && "$code" != "200" ]]; then
    echo "✅ Акция −50% снята: страница акции в черновике, кэш сброшен."
    report "$slide" "$code"
    echo "Задачу «Снять акцию −50%» можно удалить из .tg-manager/schedule.json."
else
    echo "⚠️ Акция снята не полностью"
    report "$slide" "$code"
    exit 1
fi
REMOTE
