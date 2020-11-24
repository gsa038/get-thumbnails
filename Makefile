# Makefile for Docker Nginx PHP Composer MySQL

include .env

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  apidoc              Generate documentation of API"
	@echo "  code-sniff          Check the API with PHP Code Sniffer (PSR2)"
	@echo "  clean               Clean directories for reset"
	@echo "  composer-up         Update PHP dependencies with composer"
	@echo "  docker-start        Create and start containers"
	@echo "  docker-stop         Stop and clear all services except db backups"
	@echo "  gen-certs           Generate SSL certificates"
	@echo "  logs                Follow log output"
	@echo "  phpmd               Analyse the API with PHP Mess Detector"
	@echo "  test                Test application"

init:
	@$(shell cp -n $(shell pwd)/api/composer.json.dist $(shell pwd)/api/composer.json 2> /dev/null)
	@$(shell mkdir $(shell pwd)/api/thumbnails $(shell pwd)/logs/api $(shell pwd)/logs/client) 
	@$(shell chown -R 777 $(shell pwd)/api/thumbnails $(shell pwd)/logs)

apidoc:
	@docker run --rm -v $(shell pwd):/data phpdoc/phpdoc -i=vendor/ -d /data/api/app/src -t /data/api/app/doc
	@make resetOwner

clean:
	@rm -Rf api/vendor
	@rm -Rf api/composer.lock
	@rm -Rf api/doc
	@rm -Rf api/report
	@rm -Rf logs/*
	@rm -Rf etc/ssl/*

code-sniff:
	@echo "Checking the standard code..."
	@docker-compose exec -T php ./app/vendor/bin/phpcs -v --standard=PSR2 app/src

composer-up:
	@docker run --rm -v $(shell pwd)/api/app:/app composer update

docker-start: init
	docker-compose up -d

docker-stop:
	@docker-compose down -v
	@make clean

gen-certs:
	@docker run --rm -v $(shell pwd)/etc/ssl:/certificates -e "SERVER=$(NGINX_HOST)" jacoelho/generate-certificate

logs:
	@docker-compose logs -f

phpmd:
	@docker-compose exec -T php \
	./app/vendor/bin/phpmd \
	./app/src text cleancode,codesize,controversial,design,naming,unusedcode

test: code-sniff
	@docker-compose exec -T php ./app/vendor/bin/phpunit --colors=always --configuration ./app/
	@make resetOwner

resetOwner:
	@$(shell chown -Rf $(SUDO_USER):$(shell id -g -n $(SUDO_USER)) "$(shell pwd)/etc/ssl" "$(shell pwd)/api" 2> /dev/null)

.PHONY: clean test code-sniff init