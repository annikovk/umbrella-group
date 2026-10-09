#!/usr/bin/env bash
# Полосы стенда: ветка темы в своём git worktree + свой web-контейнер на своём порту.
#
#     tools/lane.sh up ws-2 8092     # worktree .lanes/ws-2 (ветка ws-2 от HEAD) + http://localhost:8092
#     tools/lane.sh down ws-2        # остановить контейнер (worktree и ветка остаются)
#     tools/lane.sh rm ws-2          # контейнер + worktree (ветку не трогает)
#     tools/lane.sh ls
#
# Основной стенд (tools/local-site.sh) должен быть поднят: полоса ходит в его базу.
# База ОБЩАЯ — полоса, которая пишет в БД, ломает картинку всем остальным.
#
# Видео темы (assets/Umbrella_2_1.mp4, 149 МБ) не в git — в worktree оно клонируется
# через APFS (cp -c): мгновенно и без места на диске.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
THEME_REL="httpdocs/wp-content/themes/umbrella-group"
cmd="${1:-}"; name="${2:-}"

lane_env() {
  export LANE_NAME="taxlab-lane-$name" HOST_UID="$(id -u)" HOST_GID="$(id -g)"
  export LANE_THEME="$ROOT/.lanes/$name/$THEME_REL" LANE_PORT="${port:-0}"
}

case "$cmd" in
  up)
    port="${3:?порт}"
    [ -d "$ROOT/.lanes/$name" ] || {
      if git -C "$ROOT" show-ref --quiet "refs/heads/$name"; then
        git -C "$ROOT" worktree add -q "$ROOT/.lanes/$name" "$name"
      else
        git -C "$ROOT" worktree add -q -b "$name" "$ROOT/.lanes/$name" HEAD
      fi
    }
    mp4="$THEME_REL/assets/Umbrella_2_1.mp4"
    [ -f "$ROOT/.lanes/$name/$mp4" ] || cp -c "$ROOT/$mp4" "$ROOT/.lanes/$name/$mp4"
    lane_env
    docker compose -f "$ROOT/docker/lane.yml" up -d --wait web >/dev/null
    for _ in $(seq 60); do
      code=$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:$port/" || true)
      [ "$code" = 200 ] && break; sleep 1
    done
    echo "полоса $name: http://localhost:$port  worktree: .lanes/$name  (HTTP $code)"
    ;;
  down|rm)
    lane_env
    docker compose -f "$ROOT/docker/lane.yml" down >/dev/null 2>&1 || true
    if [ "$cmd" = rm ] && [ -d "$ROOT/.lanes/$name" ]; then
      git -C "$ROOT" worktree remove --force "$ROOT/.lanes/$name"
    fi
    echo "полоса $name: $cmd"
    ;;
  ls)
    docker ps --filter "name=taxlab-lane-" --format '{{.Names}}\t{{.Ports}}'
    git -C "$ROOT" worktree list
    ;;
  *) sed -n '2,13p' "$0"; exit 1 ;;
esac
