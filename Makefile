s := symfony
sc := symfony console

## Make entity
entity:
	$(sc) make:entity
.PHONY: entity

## Make database
database:
	$(sc) doctrine:database:create
.PHONY: database

## Make database test
database-test:
	$(sc) doctrine:database:create --env=test
.PHONY: database-test

## Make schema
schema:
	$(sc) doctrine:schema:update --force
.PHONY: schema

## Make schema test
schema-test:
	$(sc) doctrine:schema:update --force --env=test
.PHONY: schema-test

## Run all tests coverage
coverage-test:
	php bin/phpunit --coverage-html web-coverage-test/test-coverage
.PHONY: coverage-test
