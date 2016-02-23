config_files := \
	config/ccouch-repos.lst \
	config/email-transport.json

generated_resources := \
	vendor

build_resources := ${generated_resources} ${config_files}

runtime_resources := \
	config/email-transport.json \
	vendor

resources := ${build_resources} ${runtime_resources}

default: runtime-resources run-tests

.DELETE_ON_ERROR:

.PHONY: \
	build-resources \
	clean \
	default \
	everything \
	fix-data-permissions \
	realclean \
	redeploy \
	resources \
	runtime-resources \
	run-tests \
	run-unit-tests \
	run-web-server \

build-resources: ${build_resources}
runtime-resources: ${runtime_resources}
resources: ${resources}

clean:
	rm -rf ${generated_resources}
realclean:
	rm -rf ${generated_resources} ${config_files}

vendor: composer.lock
	composer install
	touch "$@"

${config_files}: %: | %.example
	cp "$|" "$@"

# If composer.lock doesn't exist at all,
# this will 'composer install' for the first time.
# After that, it's up to you to 'composer update' to get any
# package updates or apply changes to composer.json.
composer.lock: | composer.json
	composer install

run-unit-tests: runtime-resources
	vendor/bin/phpunit --bootstrap init-environment.php test

run-tests: run-unit-tests

run-web-server:
	cd www && php -S localhost:6061 bootstrap.php

fix-data-permissions:
	chmod -R ugo+rwX spaces

redeploy: runtime-resources fix-data-permissions

everything: \
	run-tests \
	run-web-server
