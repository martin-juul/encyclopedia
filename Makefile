APP_NAME=martinjuul/encyclopedia

build:
	docker build --tag ${APP_NAME} .

dev-infra:
	docker-compose -f ./docker-compose.dev.yml up --detach --remove-orphans
