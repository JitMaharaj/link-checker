.PHONY: install clear test

install:
	composer update
	composer install

test:
	composer run-script test

clear:
	rm -rf vendor .phpunit.result.cache
