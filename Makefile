up:
	docker-compose up -d

build:
	docker-compose up -d --build

install:
	cp githooks/pre-push .git/hooks/pre-push
	chmod +x .git/hooks/pre-push
	make build
	make configure-app

configure-app:
	docker-compose exec app composer install

app-permissions:
	docker-compose exec app chmod -R og+w /var/www/html


run-test:
	docker-compose exec app php bin/phpunit