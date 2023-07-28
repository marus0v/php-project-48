install: # установить зависимости
	composer install

validate: # проверка validate
	composer validate

gendiff: # запустить gendiff
	./bin/gendiff

lint: # установить CodeSniffer
	composer exec --verbose phpcs -- --standard=PSR12 src bin

test: # запустить test
	composer exec --verbose phpunit test

test-coverage: # запустить test-coverage
	XDEBUG_MODE=coverage composer exec --verbose phpunit test -- --coverage-clover build/logs/clover.xml