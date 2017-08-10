PROJECT_DIR := ${PWD}
IMAGE_NAME = kejwmen-phpunit_listeners
DOCKER_RUN_COMMAND = run -it --rm -v ${PROJECT_DIR}:/app

all: | build composer-install test

build:
	docker build -t $(IMAGE_NAME) .

### COMPOSER

DOCKER_RUN_COMPOSER = $(DOCKER_RUN_COMMAND) --entrypoint=composer

composer-install:
	docker $(DOCKER_RUN_COMPOSER) $(IMAGE_NAME) install

composer-update:
	docker $(DOCKER_RUN_COMPOSER) $(IMAGE_NAME) update

composer:
	docker $(DOCKER_RUN_COMPOSER) $(IMAGE_NAME) ${argument}

### QA

phpstan:
	docker $(DOCKER_RUN_COMMAND) --entrypoint=vendor/bin/phpstan $(IMAGE_NAME) analyse src --level 7

test: test-with-phpdbg

test-with-phpdbg:
	docker $(DOCKER_RUN_COMMAND) --entrypoint="/usr/local/bin/phpdbg" $(IMAGE_NAME) -qrr vendor/bin/phpunit

shell:
	docker $(DOCKER_RUN_COMMAND) --entrypoint="/bin/sh" $(IMAGE_NAME)
