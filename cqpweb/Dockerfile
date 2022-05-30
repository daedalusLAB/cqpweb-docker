FROM ubuntu:20.04

RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt install -y \
    apache2 subversion php build-essential r-base libapache2-mod-php php-mysql php-gd sendmail mysql-client php-mbstring \
    && svn co http://svn.code.sf.net/p/cwb/code/cwb/trunk cwb && \
    svn co http://svn.code.sf.net/p/cwb/code/perl/trunk cwb-perl && \
    mkdir -p /data/corpora/cqpweb/upload /data/corpora/cqpweb/tmp /data/corpora/cqpweb/corpora /data/corpora/cqpweb/registry && \
    chown -R www-data /data/corpora 
    
WORKDIR /cwb
RUN ./install-scripts/install-linux && make clean && make all && make install && ldconfig

WORKDIR /cwb-perl/CWB
RUN perl Makefile.PL --config /usr/local/bin/cwb-config && make && make test && make install 

COPY php.ini /etc/php/7.4/apache2/php.ini

RUN svn co http://svn.code.sf.net/p/cwb/code/gui/cqpweb/trunk /var/www/html/CQPweb && chown -R www-data /var/www/html/CQPweb 

COPY config.php /var/www/html/CQPweb/lib/config.php
COPY autosetup.php /var/www/html/CQPweb/bin/autosetup.php


WORKDIR /
EXPOSE 80

COPY entrypoint.sh /root/entrypoint.sh

ENTRYPOINT [ "/root/entrypoint.sh" ]



