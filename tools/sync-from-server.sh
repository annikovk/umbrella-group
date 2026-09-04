#!/usr/bin/env bash
# Обновляет локальное зеркало httpdocs/ по живому сайту. Источник истины —
# сервер: контент и настройки правят там (Plesk, wp-admin), сюда только
# стягиваем. Обратной заливки в этом скрипте нет намеренно.
set -euo pipefail

SRC="annikov.com:/var/www/vhosts/taxlab.ru/httpdocs/"
DST="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/httpdocs/"

# Файлы принадлежат юзеру taxlabru, а ssh-юзер под ним не ходит — читаем через
# sudo на дальней стороне (он passwordless). Флаги — под openrsync из macOS:
# ни --info=progress2, ни -a (chown без root всё равно не сработает) он не берёт.
rsync -rlptDz --delete --rsync-path="sudo rsync" "$SRC" "$DST"

# Четыре файла в uploads различаются только регистром имени — на APFS они
# схлопываются в один. Вторые копии живут в _case-conflicts/, см. README там.
