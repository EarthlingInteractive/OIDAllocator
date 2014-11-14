dev_resources = \
	build/db/create-database.sql \
	build/db/drop-database.sql \
	build/db/upgrades/0110-create-tables.sql \
	build/db/upgrades/0097-drop-tables.sql \
	util/phptemplateprojectdatabase-psql \
	util/phptemplateprojectdatabase-pg_dump \
	util/SchemaSchemaDemo.jar \
	schema/schema.php \
	vendor
runtime_resources = \
	schema/schema.php \
	vendor

resources = ${dev_resources} ${runtime_resources}

run_schema_processor = \
	java -jar util/SchemaSchemaDemo.jar \
	-o-create-tables-script build/db/upgrades/0110-create-tables.sql \
	-o-drop-tables-script build/db/upgrades/0097-drop-tables.sql \
	-o-schema-php schema/schema.php -php-schema-class-namespace EarthIT_Schema \
	schema/schema.txt

default: resources run-tests

.DELETE_ON_ERROR:

.PHONY: \
	default \
	resources \
	run-tests \
	run-unit-tests \
	rebuild-database \
	clean

dev-resources: ${dev_resources}
runtime-resources: ${runtime_resources}
resources: ${resources}

clean:
	rm -f ${resources}

vendor: composer.lock
	composer install
	touch "$@"

# If composer.lock doesn't exist at all,
# this will 'composer install' for the first time.
# After that, it's up to you to 'composer update' to get any
# package updates or apply changes to composer.json.
composer.lock:
	composer install

util/phptemplateprojectdatabase-psql: config/dbc.json
	vendor/bin/generate-psql-script -psql-exe psql "$<" >"$@"
	chmod +x "$@"
util/phptemplateprojectdatabase-pg_dump: config/dbc.json
	vendor/bin/generate-psql-script -psql-exe pg_dump "$<" >"$@"
	chmod +x "$@"

%: %.urn vendor
	cp -n config/ccouch-repos.lst.example config/ccouch-repos.lst
	vendor/bin/fetch -repo @config/ccouch-repos.lst -o "$@" `cat "$<"`

build/db/upgrades/0110-create-tables.sql: schema/schema.txt util/SchemaSchemaDemo.jar
	${run_schema_processor}

build/db/upgrades/0097-drop-tables.sql: schema/schema.txt util/SchemaSchemaDemo.jar
	${run_schema_processor}

schema/schema.php: schema/schema.txt util/SchemaSchemaDemo.jar
	${run_schema_processor}

build/db/create-database.sql: config/dbc.json vendor
	vendor/bin/generate-create-database-sql "$<" >"$@"
build/db/drop-database.sql: config/dbc.json vendor
	vendor/bin/generate-drop-database-sql "$<" >"$@"

create-database drop-database: %: build/db/%.sql
	sudo su postgres -c "cat '$<' | psql"

rebuild-database: resources
	cat build/db/upgrades/*.sql | util/phptemplateprojectdatabase-psql -q

run-unit-tests: runtime-resources
	vendor/bin/phpunit --bootstrap init-environment.php test

run-tests: run-unit-tests