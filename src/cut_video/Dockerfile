FROM php:7.4-apache
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y ffmpeg
COPY cut_video.php /var/www/html/
RUN chown -R www-data:www-data /var/www/html
RUN a2enmod rewrite