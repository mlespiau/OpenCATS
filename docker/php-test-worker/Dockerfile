FROM php:5.6.28-cli
RUN echo " deb http://http.debian.net/debian wheezy-backports main" > /etc/apt/sources.list.d/backports.list
RUN apt-get update
RUN apt-get install -y php-pear gearman-job-server php5-gearman vim bash libgearman-dev apt-transport-https ca-certificates
RUN ln -s /etc/php5/mods-available/gearman.ini /usr/local/etc/php/conf.d/gearman.ini
RUN ln -s /usr/lib/php5/20131226/gearman.so /usr/local/lib/php/extensions/no-debug-non-zts-20131226/gearman.so
RUN apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D
RUN echo "  deb https://apt.dockerproject.org/repo debian-jessie main" >  /etc/apt/sources.list.d/docker.list
RUN apt-get update
RUN apt-cache policy docker-engine
RUN apt-get install -y docker-engine
RUN curl -L "https://github.com/docker/compose/releases/download/1.8.1/docker-compose-$(uname -s)-$(uname -m)" > /usr/local/bin/docker-compose 
RUN chmod +x /usr/local/bin/docker-compose
RUN apt-get install -y wget
ENV DOCKERIZE_VERSION v0.2.0
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz
ENTRYPOINT ["php", "/var/www/public/test/testWorker.php"]
