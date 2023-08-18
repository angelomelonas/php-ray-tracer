MIN_MSI=100
MIN_COVERED_MSI=100

ifeq ("${CI}", "true")
	PARALLELISM=4
else
	UNAME_S := $(shell uname -s)

	ifeq ($(UNAME_S),Linux)
		PARALLELISM=$(shell nproc)
	endif
	ifeq ($(UNAME_S),Darwin)
		PARALLELISM=$(shell sysctl -n hw.ncpu)
	endif
endif

vendor: composer.json composer.lock
	composer install
	@touch -c vendor

coding-standard: vendor
	vendor/bin/phpcs --ignore=vendor/*,bin/* --parallel=$(PARALLELISM)

coding-standard-fix: vendor
	vendor/bin/phpcbf --ignore=vendor/*,bin/* --parallel=$(PARALLELISM) || true

static-analysis: vendor
	vendor/bin/phpstan analyse --memory-limit=-1 $(EXTRA_FLAGS)

unit-test: vendor
	vendor/bin/phpunit --testsuite unit --stop-on-error --stop-on-failure $(EXTRA_FLAGS)

behat-test: vendor
	./vendor/bin/behat