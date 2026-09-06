#!/usr/bin/env bash
# Локальная копия taxlab.ru в Docker: PHP 8.3 + Apache и MariaDB 10.11, код из
# зеркала httpdocs/, база — дамп с прода. Конфигурация стенда — в docker/.
#
#   tools/local-site.sh            поднять и открыть в браузере
#   tools/local-site.sh fresh      всё заново: файлы с сервера + свежая база
#   tools/local-site.sh help       остальные команды
#
# Одностороннее правило проекта в силе: стенд только читает прод. Ничего из
# этого скрипта на сервер не заливается.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DOCKER_DIR="$ROOT/docker"
ENV_FILE="$DOCKER_DIR/.env"
DUMP_DIR="$ROOT/.local-dev/db"
KEEP_DUMPS=3
SSH_HOST="annikov.com"
REMOTE_DB="wp_taxlab"

OPEN_BROWSER=1
# В сессии из Telegram открывать браузер бессмысленно — человека нет у машины.
[[ -n "${CLAUDE_TG_DAEMON:-}" ]] && OPEN_BROWSER=0

say()  { printf '\033[1;36m==>\033[0m %s\n' "$*"; }
ok()   { printf '\033[1;32m  ✓\033[0m %s\n' "$*"; }
warn() { printf '\033[1;33m  !\033[0m %s\n' "$*" >&2; }
die()  { printf '\033[1;31m  ✗\033[0m %s\n' "$*" >&2; exit 1; }

usage() {
	cat <<'TXT'
tools/local-site.sh [команда] [опции]

  up                 (по умолчанию) поднять стенд; пустую базу заполнит дампом
  fresh              зеркало файлов + свежий дамп прода + импорт + поднять
  sync               только обновить зеркало файлов (sync-from-server.sh)
  db-pull            только снять свежий дамп прода в .local-dev/db/
  db-import [файл]   залить дамп (по умолчанию последний из .local-dev/db/)
  admin [login] [pw] создать/переустановить локального админа (по умолч. local/local)
  status             что запущено, адрес, версия WP, размер базы
  logs [-f]          логи контейнеров
  shell              bash внутри web-контейнера
  wp <args...>       wp-cli внутри контейнера
  db                 клиент mariadb к локальной базе
  open               открыть сайт в браузере
  down               остановить контейнеры (база остаётся)
  reset              остановить и снести базу вместе с volume
  rebuild            пересобрать образ web

  --no-open          не открывать браузер
  --fresh-db         (с up) перезалить базу из последнего дампа
TXT
}

# ---------------------------------------------------------------- окружение ---

prepare_env() {
	[[ -d "$ROOT/httpdocs" ]] || die "нет зеркала httpdocs/ — сначала tools/sync-from-server.sh"

	if [[ ! -f "$ENV_FILE" ]]; then
		say "первый запуск: создаю docker/.env из .env.example"
		sed -e "s/^HOST_UID=.*/HOST_UID=$(id -u)/" \
		    -e "s/^HOST_GID=.*/HOST_GID=$(id -g)/" \
		    "$DOCKER_DIR/.env.example" > "$ENV_FILE"
		ok "docker/.env создан (uid $(id -u), gid $(id -g))"
	fi

	set -a
	# shellcheck disable=SC1090
	source "$ENV_FILE"
	set +a
	: "${HTTP_PORT:=8088}" "${DB_PORT:=3307}" "${DB_NAME:=wp_taxlab}" "${DB_ROOT_PASSWORD:=local}"
	SITE_URL="http://localhost:$HTTP_PORT"

	docker info >/dev/null 2>&1 || die "демон Docker не отвечает — запусти Docker Desktop"
}

dc() { docker compose --project-directory "$DOCKER_DIR" -f "$DOCKER_DIR/compose.yml" --env-file "$ENV_FILE" "$@"; }

# mariadb внутри контейнера db. Пароль передаём переменной, а не флагом, чтобы
# не ловить предупреждение клиента в каждый вывод.
db_sql() { dc exec -T -e MYSQL_PWD="$DB_ROOT_PASSWORD" db mariadb -uroot -N -B -e "$1"; }

# wp-cli в контейнере. display_errors гасим: в CLI буферизации нет даже с
# php.ini (PHP её там жёстко выключает), поэтому лишний вывод из functions.php
# темы порождает «headers already sent» и валится прямо в stdout, вперемешку с
# данными. Предупреждения остаются в stderr, там им и место.
wp_cli() { dc exec -e WP_CLI_PHP_ARGS="-d display_errors=0" "$@"; }

port_taken_by_other() {
	local port="$1"
	lsof -nP -iTCP:"$port" -sTCP:LISTEN >/dev/null 2>&1 || return 1

	# Порт занят. Если его держит web-контейнер стенда — это норма, стенд просто
	# уже запущен. Спрашиваем у самого compose: по выводу lsof не разобрать, там
	# имя процесса урезано до «com.docke» и от чужого docker-проекта не отличить.
	[[ "$(dc port web 80 2>/dev/null)" == *":$port" ]] && return 1
	return 0
}

# ------------------------------------------------------------------- дампы ----

latest_dump() {
	[[ -e "$DUMP_DIR/latest.sql.gz" ]] && { printf '%s\n' "$DUMP_DIR/latest.sql.gz"; return 0; }
	local newest
	newest="$(ls -t "$DUMP_DIR"/*.sql.gz 2>/dev/null | head -1 || true)"
	[[ -n "$newest" ]] && printf '%s\n' "$newest"
}

# Имя файла, на который смотрит дамп: latest.sql.gz — симлинк, а в отчёте
# полезнее видеть дату. readlink -f на macOS есть не во всех версиях, поэтому
# разыменовываем сами.
dump_label() {
	local dump
	dump="$(latest_dump || true)"
	[[ -n "$dump" ]] || { printf 'нет\n'; return; }
	if [[ -L "$dump" ]]; then
		basename "$(readlink "$dump")"
	else
		basename "$dump"
	fi
}

db_pull() {
	mkdir -p "$DUMP_DIR"
	local stamp target
	stamp="$(date +%Y%m%d-%H%M)"
	target="$DUMP_DIR/${REMOTE_DB}-${stamp}.sql.gz"

	say "снимаю дамп базы с прода (~220 МБ, в gzip ≈40 МБ)"
	warn "ssh к $SSH_HOST идёт через Telegram-брокер — может ждать подтверждения"

	# --single-transaction: все 71 таблица InnoDB, живой сайт не блокируется.
	# utf8mb4 в соединении — в базе смешаны utf8mb3 и utf8mb4 таблицы, и только
	# так данные читаются без потерь.
	if ! ssh "$SSH_HOST" "sudo mariadb-dump \
			--single-transaction --quick --no-tablespaces \
			--default-character-set=utf8mb4 \
			$REMOTE_DB | gzip -1" > "$target"; then
		rm -f "$target"
		die "дамп не снялся"
	fi

	gzip -t "$target" 2>/dev/null || { rm -f "$target"; die "дамп битый (gzip -t не прошёл)"; }
	gzip -dc "$target" | tail -5 | grep -q 'Dump completed' \
		|| { rm -f "$target"; die "дамп обрезан — в конце нет «Dump completed»"; }

	ln -sfn "$(basename "$target")" "$DUMP_DIR/latest.sql.gz"
	ok "дамп готов: ${target#"$ROOT"/} ($(du -h "$target" | cut -f1))"

	# Держим только последние KEEP_DUMPS — каждый под 40 МБ.
	ls -t "$DUMP_DIR"/*.sql.gz 2>/dev/null | grep -v '/latest.sql.gz$' | tail -n +$((KEEP_DUMPS + 1)) \
		| while read -r old; do rm -f "$old"; done
}

db_import() {
	local dump="${1:-}"
	[[ -z "$dump" ]] && dump="$(latest_dump || true)"
	[[ -n "$dump" && -e "$dump" ]] || die "нет дампа — сделай tools/local-site.sh db-pull"

	dc up -d db
	wait_for_db

	say "заливаю $(dump_label) — это 1–3 минуты"
	# Пересоздаём базу в utf8mb3_general_ci: ровно как на проде, иначе
	# таблицы без явной коллации приедут в utf8mb4 и sql_mode-мелочи разойдутся.
	db_sql "DROP DATABASE IF EXISTS \`$DB_NAME\`;
	        CREATE DATABASE \`$DB_NAME\` CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci;" >/dev/null

	if command -v pv >/dev/null 2>&1; then
		pv "$dump" | gzip -dc | dc exec -T -e MYSQL_PWD="$DB_ROOT_PASSWORD" db \
			mariadb -uroot --default-character-set=utf8mb4 "$DB_NAME"
	else
		gzip -dc "$dump" | dc exec -T -e MYSQL_PWD="$DB_ROOT_PASSWORD" db \
			mariadb -uroot --default-character-set=utf8mb4 "$DB_NAME"
	fi

	local tables
	tables="$(db_sql "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME'")"
	[[ "${tables:-0}" -gt 10 ]] || die "после импорта в базе всего ${tables:-0} таблиц — что-то не так"
	ok "база залита: $tables таблиц"
}

wait_for_db() {
	local i
	for i in $(seq 1 60); do
		dc exec -T db healthcheck.sh --connect >/dev/null 2>&1 && return 0
		sleep 2
	done
	die "MariaDB не поднялась за две минуты — смотри tools/local-site.sh logs"
}

db_table_count() {
	db_sql "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME'" 2>/dev/null || echo 0
}

# --------------------------------------------------------------------- стенд ---

wait_for_site() {
	local i code
	for i in $(seq 1 60); do
		code="$(curl -s -o /dev/null -w '%{http_code}' --max-time 20 "$SITE_URL/" || true)"
		[[ "$code" == "200" ]] && return 0
		sleep 2
	done
	warn "сайт отвечает кодом ${code:-—}, а не 200. Логи: tools/local-site.sh logs"
	return 1
}

open_site() {
	[[ "$OPEN_BROWSER" == "1" ]] || return 0
	command -v open >/dev/null 2>&1 && open "$SITE_URL"
}

cmd_up() {
	local fresh_db="${1:-0}"

	if port_taken_by_other "$HTTP_PORT"; then
		die "порт $HTTP_PORT занят чужим процессом — поправь HTTP_PORT в docker/.env"
	fi

	say "поднимаю контейнеры (первая сборка образа — 2–4 минуты)"
	dc up -d --build
	wait_for_db

	local tables
	tables="$(db_table_count)"
	if [[ "$fresh_db" == "1" ]]; then
		db_import
	elif [[ "${tables:-0}" -lt 10 ]]; then
		say "база пустая — беру дамп с прода"
		[[ -n "$(latest_dump || true)" ]] || db_pull
		db_import
	else
		ok "база на месте ($tables таблиц), импорт не нужен"
	fi

	if wait_for_site; then
		ok "сайт поднят: $SITE_URL"
	fi
	printf '\n  сайт:    %s\n  админка: %s/wp-admin/\n  база:    127.0.0.1:%s (root/%s, %s)\n\n' \
		"$SITE_URL" "$SITE_URL" "$DB_PORT" "$DB_ROOT_PASSWORD" "$DB_NAME"
	open_site
}

cmd_admin() {
	local login="${1:-local}" pass="${2:-local}"
	say "локальный админ $login/$pass"
	if wp_cli -T web wp --allow-root user get "$login" >/dev/null 2>&1; then
		wp_cli -T web wp --allow-root user update "$login" --user_pass="$pass" --role=administrator >/dev/null 2>&1
	else
		wp_cli -T web wp --allow-root user create "$login" "$login@taxlab.local" \
			--role=administrator --user_pass="$pass" >/dev/null 2>&1
	fi
	ok "войти: $SITE_URL/wp-admin/ — $login / $pass"
}

cmd_status() {
	dc ps
	local tables size wp
	tables="$(db_table_count)"
	size="$(db_sql "SELECT IFNULL(ROUND(SUM(data_length+index_length)/1024/1024,1),0) FROM information_schema.tables WHERE table_schema='$DB_NAME'" 2>/dev/null || echo '?')"
	wp="$(grep -o "wp_version = '[^']*'" "$ROOT/httpdocs/wp-includes/version.php" 2>/dev/null | cut -d"'" -f2 || echo '?')"
	printf '\n  адрес:      %s\n  WordPress:  %s (из зеркала)\n  база:       %s таблиц, %s МБ\n  дамп:       %s\n\n' \
		"$SITE_URL" "$wp" "$tables" "$size" "$(dump_label)"
}

# ---------------------------------------------------------------------- CLI ---

CMD=""
FRESH_DB=0
ARGS=()
while [[ $# -gt 0 ]]; do
	case "$1" in
		--no-open)  OPEN_BROWSER=0 ;;
		--fresh-db) FRESH_DB=1 ;;
		-h|--help|help) usage; exit 0 ;;
		# Всё, что идёт после этих команд, — их собственные аргументы: там
		# бывают свои флаги (wp --field=name, logs -f), и разбирать их нельзя.
		wp|logs|db|shell)
			CMD="$1"; shift; ARGS=("$@"); break ;;
		up|fresh|sync|db-pull|db-import|admin|status|open|down|reset|rebuild)
			[[ -z "$CMD" ]] || die "две команды сразу: $CMD и $1"
			CMD="$1" ;;
		# Остальное — позиционные аргументы команды (файл дампа, логин админа).
		# Флаги, объявленные выше, работают и до команды, и после неё.
		*)
			[[ -n "$CMD" ]] || die "неизвестная команда: $1 (tools/local-site.sh help)"
			ARGS+=("$1") ;;
	esac
	shift
done
CMD="${CMD:-up}"

case "$CMD" in
	up)     prepare_env; cmd_up "$FRESH_DB" ;;
	fresh)
		prepare_env
		say "обновляю зеркало файлов с прода"
		"$ROOT/tools/sync-from-server.sh"
		ok "зеркало обновлено"
		db_pull
		cmd_up 1
		;;
	sync)      "$ROOT/tools/sync-from-server.sh" ;;
	db-pull)   prepare_env; db_pull ;;
	db-import) prepare_env; db_import "${ARGS[0]:-}" ;;
	admin)     prepare_env; cmd_admin "${ARGS[0]:-local}" "${ARGS[1]:-local}" ;;
	status)    prepare_env; cmd_status ;;
	logs)      prepare_env; if [[ ${#ARGS[@]} -gt 0 ]]; then dc logs "${ARGS[@]}"; else dc logs --tail=100; fi ;;
	shell)     prepare_env; dc exec web bash ;;
	wp)        prepare_env; wp_cli web wp --allow-root "${ARGS[@]}" ;;
	db)        prepare_env; dc exec -e MYSQL_PWD="$DB_ROOT_PASSWORD" db mariadb -uroot "$DB_NAME" ;;
	open)      prepare_env; OPEN_BROWSER=1; open_site ;;
	down)      prepare_env; dc down ;;
	reset)     prepare_env; dc down -v; ok "контейнеры и база снесены, файлы зеркала целы" ;;
	rebuild)   prepare_env; dc build --no-cache web; dc up -d ;;
esac
