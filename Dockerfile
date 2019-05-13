FROM php:7-cli
LABEL maintainer="edmurcardoso@gmail.com"

RUN apt-get update
RUN apt-get install --no-install-recommends --assume-yes --fix-missing unzip libicu-dev cron git dos2unix
RUN docker-php-ext-install intl
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /usr/src

WORKDIR /usr/src
RUN dos2unix generate_env.sh
RUN chmod +x generate_env.sh
RUN composer install --no-dev --optimize-autoloader
RUN (crontab -l ; echo "* * * * * /usr/local/bin/php /usr/src/run.php >> /usr/src/Storage/Logs/app.log 2>&1") | crontab
RUN service cron restart
RUN service cron reload
ENTRYPOINT ./generate_env.sh && cron -f -L 8 & tail -f /usr/src/Storage/Logs/app.log
