COMPONENT := pluggitmonitoring
CONTAINER := php84
IMAGES ?= false
APP_ROOT := /app/monitoring

all: dev nodev

dev:
	@docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml up -d --build

enter:
	@docker exec -ti ${COMPONENT}_${CONTAINER}_1 /bin/sh

nodev:
	@docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml rm -fa > /dev/null
ifeq ($(IMAGES),true)
	@docker rmi ${COMPONENT}_${CONTAINER}
endif

deps:
	@docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml exec -T ${CONTAINER} \
	 composer install --no-interaction

test: unit

unit:
	@docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml exec -T ${CONTAINER} \
	 ${APP_ROOT}/ops/scripts/unit.sh

ps: status
status:
	@docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml ps

logs:
	@docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml logs

tag: # List last tag for this repo
	@git tag -l | sort -r |head -1

restart: nodev dev
