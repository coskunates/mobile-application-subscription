docker-compose -f docker/docker-compose.yml down -v
docker-compose -f docker/docker-compose.yml up -d --build
docker exec -ti sa_php sh -c "cd api && composer update && cp .env.example .env && php artisan migrate:refresh --seed"
docker exec -ti sa_php sh -c "cd mock && composer update && cp .env.example .env"
docker exec -ti sa_php sh -c "supervisord -c /etc/supervisord.conf"
docker exec -ti sa_php sh -c "crond -l 2 -d 8"