#!/bin/sh
set -eu
RENDER_PORT="${PORT:-10000}"
sed -ri "s/^Listen [0-9]+$/Listen ${RENDER_PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${RENDER_PORT}>/" /etc/apache2/sites-available/000-default.conf
php lava migration run
exec apache2-foreground
