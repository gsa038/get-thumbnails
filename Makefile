# Makefile for Docker Nginx PHP Composer MySQL

include .env

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  clean               Clean directories for reset"
	@echo "  composer-up         Update PHP dependencies with composer"
	@echo "  docker-start        Create and start containers"
	@echo "  docker-stop         Stop and clear all services except db backups"
	@echo "  logs                Follow log output"

init:
	@$(shell cp -n $(shell pwd)/api/composer.json.dist $(shell pwd)/api/composer.json 2> /dev/null)
	@$(shell mkdir -p $(shell pwd)/api/thumbnails $(shell pwd)/logs/api $(shell pwd)/logs/client) 
	@$(shell chmod -R 777 $(shell pwd)/api/thumbnails $(shell pwd)/logs)

clean:
	@rm -Rf api/vendor
	@rm -Rf api/composer.lock
	@rm -Rf logs/*
	@rm -Rf api/thumbnail/*

composer-up:
	@docker run --rm -v $(shell pwd)/api/app:/app composer update

docker-start: init
	docker-compose up -d

docker-stop:
	@docker-compose down -v
	@make clean

logs:
	@docker-compose logs -f
