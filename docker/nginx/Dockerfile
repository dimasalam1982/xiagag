FROM nginx:latest

ENV TZ 'Europe/Moscow'
RUN mkdir -p /etc/nginx/sites-available \
    && rm -rf  /etc/nginx/conf.d/default.conf
ADD docker/nginx/config/*.conf /etc/nginx/conf.d/

EXPOSE 80 443
