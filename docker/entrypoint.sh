#!/bin/bash
set -e

cd /var/www/html

mkdir -p /data/db
if [ ! -f /data/db/.initialized ]; then
  mongod --dbpath /data/db --bind_ip 127.0.0.1 --port 27017 --fork --logpath /var/log/mongodb.log
  for i in $(seq 1 30); do
    if mongosh --quiet --eval "db.runCommand({ ping: 1 })" >/dev/null 2>&1; then
      break
    fi
    sleep 1
  done

  if [ -z "$APP_KEY" ]; then
    export APP_KEY="base64:$(openssl rand -base64 32)"
  fi

  php artisan key:generate --force --no-interaction 2>/dev/null || true
  php artisan migrate --force --no-interaction
  php artisan storage:link --force 2>/dev/null || true
  php artisan db:seed --force --no-interaction 2>/dev/null || true
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache

  mongod --dbpath /data/db --shutdown 2>/dev/null || true
  touch /data/db/.initialized
fi

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
