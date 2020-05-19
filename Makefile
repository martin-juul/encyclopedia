APP_NAME=martinjuul/encyclopedia
PWD=$(pwd)

build:
	docker build --tag ${APP_NAME} .

dev-infra:
	docker-compose -f ./docker-compose.dev.yml up --detach --remove-orphans

queue-work:
	php -dxdebug.gc_stats_output_dir=${PWD} -dxdebug.gc_stats_enable=true artisan queue:work --timeout=0 --memory=1024
