lint:
	@make pint
	@make phpstan

pint:
	@./vendor/bin/pint

phpstan:
	@./vendor/bin/phpstan

pest:
	@./vendor/bin/pest $(filter-out $@,$(MAKECMDGOALS))

coverage:
	@make pest -- --coverage

artisan:
	@./vendor/bin/testbench $(filter-out $@,$(MAKECMDGOALS))

# catch all
%:
    @:
