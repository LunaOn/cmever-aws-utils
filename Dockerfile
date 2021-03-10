# docker build -t registry.lunaon.net:5000/cmever-aws-utils-app:latest .
# docker run -d --name cmever-aws-utils-test-app -p 80:80 registry.lunaon.net:5000/cmever-aws-utils-app:latest
FROM registry.lunaon.net:5000/php-composer-nginx:php73-v1.3
COPY --chown=nginx:nginx . /var/www/html/public