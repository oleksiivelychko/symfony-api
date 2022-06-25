phpexec := php bin/console
dockerexec := docker exec symfony_api
dockerexecphp := $(dockerexec) $(phpexec)

create-project:
	composer create-project symfony/skeleton symfony-api

docker-clear-cache:
	$(dockerexec) rm -rf /app/var/cache/*
	$(dockerexecphp) cache:clear

docker-bash:
	docker run --rm -it symfony_api /bin/bash

docker-build:
	docker build --no-cache . -t symfony-api/dev

docker-delete-image:
	docker image rm symfony-api/dev

docker-create-network:
	docker network create -d bridge symfony-bridge

docker-run:
	docker run -it -p 8000:8000 --name symfony_api -v `pwd`:/app --network=symfony-bridge symfony-api/dev

docker-force-stop:
	docker stop symfony_api
	docker rm symfony_api

docker-compose-up:
	docker-compose -f docker-compose.yml -f docker-compose.override.yml up -d

# make doctrine-make-migration table=name
doctrine-make-migration:
	$(dockerexecphp) doctrine:migration:diff --filter-expression=/${table}/

doctrine-init-test-db:
	$(dockerexecphp) --env=test doctrine:database:create
	$(dockerexecphp) --env=test doctrine:schema:create

doctrine-load-test-fixtures:
	$(dockerexecphp) --env=test doctrine:fixtures:load --no-interaction

# make doctrine-migration-exec migration=Version20220522093420 op=[up|down]
doctrine-migration-exec:
	$(dockerexecphp) doctrine:migrations:execute 'DoctrineMigrations\${migration}' --${op} --no-interaction -vvv

doctrine-run-migrations:
	$(dockerexecphp) doctrine:migrations:migrate --no-interaction

heroku-git:
	heroku git:remote -a oleksiivelychkosymfonyapi

heroku-set-app-secret:
	heroku config:set APP_SECRET=$(php -r 'echo bin2hex(random_bytes(16));')

git-push:
	git push heroku main

symfony-list-routes:
	$(phpexec) debug:router

symfony-make-entity:
	$(phpexec) make:entity

symfony-make-fixture:
	$(phpexec) make:fixtures

symfony-make-migrations:
	$(dockerexecphp) make:migration
	docker cp symfony_api:/app/migrations/. migrations

psql-connect:
	psql -h 127.0.0.1 -p 5432 -U symfony -d symfony-api