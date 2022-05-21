create-project:
	composer create-project symfony/skeleton symfony-api

dock-bash:
	docker run --rm -it symfony-api/dev /bin/bash

docker-build:
	docker build . -t symfony-api/dev

docker-delete:
	docker image rm symfony-api/dev

docker-run:
	docker run -it -p 8000:8000 symfony-api/dev

make-entity:
	php bin/console make:entity