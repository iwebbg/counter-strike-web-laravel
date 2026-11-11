FROM nginx:1.29-bookworm
RUN apt-get update && apt-get install -y --no-install-recommends certbot cron gettext-base && rm -rf /var/lib/apt/lists/* /etc/nginx/conf.d/default.conf
COPY docker/nginx/prod/default.conf.template /etc/nginx/templates/default.conf.template
COPY docker/nginx/prod/startup.sh /usr/local/bin/fragzone-startup
COPY public /var/www/html/public
RUN chmod +x /usr/local/bin/fragzone-startup
CMD ["/usr/local/bin/fragzone-startup"]
