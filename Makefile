clear-cache:
	docker exec symfony-api rm -rf /app/var/cache/*
	docker exec symfony-api php bin/console cache:clear

composer-update:
	docker exec symfony-api composer update

create-project:
	composer create-project symfony/skeleton symfony-api

docker-bash:
	docker run --rm -it symfony-api/dev /bin/bash

docker-build:
	docker build --no-cache . -t symfony-api/dev

docker-delete:
	docker image rm symfony-api/dev

docker-network:
	docker network create -d bridge symfony-bridge

docker-run:
	docker run -it -p 8000:8000 --name symfony-api -v `pwd`:/app --network=symfony-bridge symfony-api/dev

docker-stop:
	docker stop symfony-api
	docker rm symfony-api

docker-compose-up:
	docker-compose -f docker-compose.yml -f docker-compose.override.yml up -d

heroku-git:
	heroku git:remote -a oleksiivelychkosymfonyapi

git-push:
	git push heroku main

list-routes:
	php bin/console debug:router

make-entity:
	php bin/console make:entity

# make make-migration table_name=users
make-migration:
	docker exec symfony-api php bin/console doctrine:migration:diff --filter-expression=/${table_name}/

make-migrations:
	docker exec symfony-api php bin/console make:migration
	docker cp symfony-api:/app/migrations/. migrations

# make migration-op migration=Version20220522093420 op=[up|down]
migration-op:
	docker exec symfony-api php bin/console doctrine:migrations:execute 'DoctrineMigrations\${migration}' --${op} --no-interaction -vvv

heroku-set-app-secret:
	heroku config:set APP_SECRET=$(php -r 'echo bin2hex(random_bytes(16));')

run-migrations:
	docker exec symfony-api php bin/console doctrine:migrations:migrate --no-interaction