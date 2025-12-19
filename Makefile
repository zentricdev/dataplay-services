.PHONY: lint pint phpstan pest coverage artisan

PINT = ./vendor/bin/pint
PHPSTAN = ./vendor/bin/phpstan
PEST = ./vendor/bin/pest
TESTBENCH = ./vendor/bin/testbench

lint: pint phpstan composer-validate

composer-validate:
	@composer validate --strict

pint-check:
	@$(PINT) --test

pint:
	@$(PINT)

phpstan:
	@$(PHPSTAN)

pest:
	@$(PEST) $(filter-out $@,$(MAKECMDGOALS))

coverage:
	@XDEBUG_MODE=coverage $(PEST) --coverage $(filter-out $@,$(MAKECMDGOALS))

artisan:
	@$(TESTBENCH) $(filter-out $@,$(MAKECMDGOALS))

# Catch-all para que no de error al pasar argumentos
%:
	@:
