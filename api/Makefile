COMPOSE = docker compose

up:
	$(COMPOSE) up -d

restart:
	$(COMPOSE) restart app

ps:
	$(COMPOSE) ps

logs:
	$(COMPOSE) logs -f app

clear:
	$(COMPOSE) exec app php artisan optimize:clear

migrate:
	$(COMPOSE) exec app php artisan migrate

status:
	$(COMPOSE) exec app php artisan migrate:status

artisan:
	$(COMPOSE) exec app php artisan $(cmd)
