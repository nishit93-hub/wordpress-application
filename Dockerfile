FROM wordpress:php7.1-apache
COPY code/* /usr/src/wordpress
EXPOSE 80
