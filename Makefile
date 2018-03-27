help:
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'
.PHONY: help

dmd: ## Generate Doctrine Migrations Diff
	bin/console doctrine:migrations:diff
.Phony: dmd

dmm: ## Run Doctrine Migrations
	bin/console doctrine:migrations:migrate --no-interaction
.Phony: dmm

stan: ## Run Static Analysis Of Code
	phpstan analyse src --level=7
.PHONY: stan

test: ## Run Whole Test Suit
	rm -rf var/cache/* && \
	bin/console doctrine:migrations:migrate --env=test --no-interaction && \
	bin/console doctrine:fixtures:load --env=test --no-interaction && \
	phpunit
.PHONY: test

testf: ## Filter Test With Argument t=TEST_NAME_HERE
	rm -rf var/cache/* && \
	bin/console doctrine:migrations:migrate --env=test --no-interaction && \
	bin/console doctrine:fixtures:load --env=test --no-interaction && \
	phpunit --filter $(t)
.PHONY: testf

user: ## Create User
	bin/console user:create
.PHONY: user
