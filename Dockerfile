FROM php:8.2-cli

WORKDIR /var/www/html

COPY ./src .

CMD ["php", "-S", "0.0.0.0:8000", "-t", "."]
