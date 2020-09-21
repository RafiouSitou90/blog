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

## Make schema
schema:
	$(sc) doctrine:schema:update --force
.PHONY: schema
