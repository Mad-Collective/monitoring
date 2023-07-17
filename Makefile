COMPONENT := pluggitmonitoring
CONTAINER := php80
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

test: unit

deps:
	@composer install --no-interaction

unit:
	@${APP_ROOT}/ops/scripts/unit.sh

ps: status
status:
	@docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml ps

logs:
	@docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml logs

tag: # List last tag for this repo
	@git tag -l | sort -r |head -1

restart: nodev dev
