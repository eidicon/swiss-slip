composer-install:
	docker run --rm --interactive --tty \
    --volume $(shell pwd):/app \
    --user $(id -u):$(id -g) \
    composer install

composer-update:
	docker run --rm --interactive --tty \
    --volume $(shell pwd):/app \
    --user $(id -u):$(id -g) \
    composer update

composer-php-version:
	docker run --rm --interactive --tty \
    --volume $(shell pwd):/app \
    --user $(id -u):$(id -g) \
	composer php -v

composer:
	docker run --rm --interactive --tty \
    --volume $(shell pwd):/app \
    --user $(id -u):$(id -g) \
	composer ${COMMAND}	