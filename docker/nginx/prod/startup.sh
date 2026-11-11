#!/usr/bin/env bash
set -euo pipefail
: "${APP_DOMAIN:?Set APP_DOMAIN}"
if [ ! -f "/etc/letsencrypt/live/${APP_DOMAIN}/fullchain.pem" ]; then certbot certonly --standalone --non-interactive --agree-tos --email "$NGINX_SSL_EMAIL" --domain "$APP_DOMAIN"; fi
envsubst '${APP_DOMAIN}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf
(while true; do sleep 12h; certbot renew --webroot --webroot-path /var/www/certbot --quiet --deploy-hook "nginx -s reload"; done) &
exec nginx -g 'daemon off;'
