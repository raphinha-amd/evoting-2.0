#!make
include .env

IMAGE_VERSION ?= latest
DOCKER_REGISTRY ?= registry.linkbee.id/evoting

export IMAGE_VERSION
export DOCKER_REGISTRY

ps:
	@if [ "$(APP_ENV)" = "local" ]; then \
		docker compose -f docker-compose.local.yaml -p ${DOCKER_NAME} ps; \
	else \
		docker compose -f docker-compose.production.yaml -p ${DOCKER_NAME} ps; \
	fi

serve:
	@if [ "$(APP_ENV)" = "local" ]; then \
		docker compose -f docker-compose.local.yaml -p ${DOCKER_NAME} up -d; \
	else \
		docker compose -f docker-compose.production.yaml -p ${DOCKER_NAME} up -d; \
	fi

serve-with-util:
	@if [ "$(APP_ENV)" = "local" ]; then \
		docker compose -f docker-compose.local.yaml -f docker-compose.util.yaml -p ${DOCKER_NAME} up -d; \
	else \
		docker compose -f docker-compose.production.yaml -f docker-compose.util.yaml -p ${DOCKER_NAME} up -d; \
	fi

down:
	@if [ "$(APP_ENV)" = "local" ]; then \
		docker compose -f docker-compose.local.yaml -f docker-compose.util.yaml -p ${DOCKER_NAME} down; \
	else \
		docker compose -f docker-compose.production.yaml -f docker-compose.util.yaml -p ${DOCKER_NAME} down; \
	fi

shell:
	@if [ "$(APP_ENV)" = "local" ]; then \
		docker compose -f docker-compose.local.yaml -p ${DOCKER_NAME} exec -it web sh; \
	else \
		docker compose -f docker-compose.production.yaml -p ${DOCKER_NAME} exec -it fpm sh; \
	fi

docker-build-composer:
	docker build --build-arg IMAGE_VERSION=${IMAGE_VERSION} -f ops/${APP_ENV}/composer.Dockerfile -t evoting/composer:${IMAGE_VERSION} .

docker-build-frontend:
	docker build --build-arg IMAGE_VERSION=${IMAGE_VERSION} -f ops/${APP_ENV}/frontend.Dockerfile -t evoting/frontend:${IMAGE_VERSION} .

docker-build-cli:
	docker build --build-arg IMAGE_VERSION=${IMAGE_VERSION} -f ops/${APP_ENV}/cli.Dockerfile -t evoting/cli:${IMAGE_VERSION} .

docker-build-fpm-prod:
	docker build --build-arg IMAGE_VERSION=${IMAGE_VERSION} -f ops/${APP_ENV}/fpm.Dockerfile -t evoting/fpm:${IMAGE_VERSION} .

docker-build-web:
	docker build --build-arg IMAGE_VERSION=${IMAGE_VERSION} -f ops/${APP_ENV}/web.Dockerfile -t evoting/web:${IMAGE_VERSION} .

docker-build-local: docker-build-composer docker-build-frontend docker-build-cli docker-build-web
docker-build-prod: docker-build-composer docker-build-frontend docker-build-cli docker-build-fpm-prod docker-build-web

docker-push-composer:
	docker tag evoting/composer:${IMAGE_VERSION} ${DOCKER_REGISTRY}/composer:${IMAGE_VERSION}
	docker push ${DOCKER_REGISTRY}/composer:${IMAGE_VERSION}

docker-push-frontend:
	docker tag evoting/frontend:${IMAGE_VERSION} ${DOCKER_REGISTRY}/frontend:${IMAGE_VERSION}
	docker push ${DOCKER_REGISTRY}/frontend:${IMAGE_VERSION}


docker-push-cli:
	docker tag evoting/cli:${IMAGE_VERSION} ${DOCKER_REGISTRY}/cli:${IMAGE_VERSION}
	docker push ${DOCKER_REGISTRY}/cli:${IMAGE_VERSION}

docker-push-fpm-prod:
	docker tag evoting/fpm:${IMAGE_VERSION} ${DOCKER_REGISTRY}/fpm:${IMAGE_VERSION}
	docker push ${DOCKER_REGISTRY}/fpm:${IMAGE_VERSION}

docker-push-web:
	docker tag evoting/web:${IMAGE_VERSION} ${DOCKER_REGISTRY}/web:${IMAGE_VERSION}
	docker push ${DOCKER_REGISTRY}/web:${IMAGE_VERSION}

docker-push-local: docker-push-composer docker-push-frontend docker-push-cli docker-push-web
docker-push-prod: docker-push-composer docker-push-frontend docker-push-cli docker-push-fpm-prod docker-push-web
