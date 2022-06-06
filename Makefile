create-project:
	composer create-project symfony/skeleton symfony-api

docker-clear-cache:
	docker exec symfony-api rm -rf /app/var/cache/*
	docker exec symfony-api php bin/console cache:clear

docker-bash:
	docker run --rm -it symfony-api/dev /bin/bash

docker-build:
	docker build --no-cache . -t symfony-api/dev

docker-delete-image:
	docker image rm symfony-api/dev

docker-create-network:
	docker network create -d bridge symfony-bridge

docker-run:
	docker run -it -p 8000:8000 --name symfony-api -v `pwd`:/app --network=symfony-bridge symfony-api/dev

docker-force-stop:
	docker stop symfony-api
	docker rm symfony-api

docker-compose-up:
	docker-compose -f docker-compose.yml -f docker-compose.override.yml up -d

# make doctrine-make-migration table=name
doctrine-make-migration:
	docker exec symfony-api php bin/console doctrine:migration:diff --filter-expression=/${table}/

doctrine-init-test-db:
	docker exec symfony-api php bin/console --env=test doctrine:database:create
	docker exec symfony-api php bin/console --env=test doctrine:schema:create

doctrine-load-fixtures:
	php bin/console --env=test doctrine:fixtures:load --no-interaction

# make doctrine-migration-exec migration=Version20220522093420 op=[up|down]
doctrine-migration-exec:
	docker exec symfony-api php bin/console doctrine:migrations:execute 'DoctrineMigrations\${migration}' --${op} --no-interaction -vvv

doctrine-run-migrations:
	docker exec symfony-api php bin/console doctrine:migrations:migrate --no-interaction

heroku-git:
	heroku git:remote -a oleksiivelychkosymfonyapi

heroku-set-app-secret:
	heroku config:set APP_SECRET=$(php -r 'echo bin2hex(random_bytes(16));')

git-push:
	git push heroku main

symfony-list-routes:
	php bin/console debug:router

symfony-make-entity:
	php bin/console make:entity

symfony-make-fixture:
	php bin/console make:fixtures

symfony-make-migrations:
	docker exec symfony-api php bin/console make:migration
	docker cp symfony-api:/app/migrations/. migrations

psql-connect:
	psql -h 127.0.0.1 -p 5432 -U symfony -d symfony-api