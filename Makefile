install: # установить зависимости
	composer install

validate: # проверка validate
	composer validate

gendiff: # запустить gendiff
	./bin/gendiff

lint: # установить CodeSniffer
	composer exec --verbose phpcs -- --standard=PSR12 src bin