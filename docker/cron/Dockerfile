FROM php:8.1-fpm

# install main dependencies
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    libzip-dev \
    curl \
  && rm -rf /var/lib/apt/lists/* \
  # install sub dependencies
  && rm /etc/apt/preferences.d/no-debian-php \
  && apt-get update -yqq && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install zip && \
  # install Composer
   (curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer)

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
        install-php-extensions amqp

# Add crontab file in the cron directory
ADD crontab /etc/cron.d/parse-news-cron

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/parse-news-cron

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

#Install Cron
RUN apt-get update
RUN apt-get -y install cron


# Run the command on container startup
CMD cron && tail -f /var/log/cron.log
