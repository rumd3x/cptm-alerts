FROM php:cli
LABEL maintainer="edmurcardoso@gmail.com"

RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install --no-install-recommends --assume-yes unzip libicu-dev cron git
RUN docker-php-ext-install intl
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /usr/src

WORKDIR /usr/src
RUN composer install --no-dev --optimize-autoloader
RUN (crontab -l ; echo "* * * * * /usr/local/bin/php /usr/src/run.php >> /proc/1/fd/1 2>/proc/1/fd/2") | crontab
RUN service cron restart
RUN service cron reload
ENTRYPOINT cron -f -L 7
