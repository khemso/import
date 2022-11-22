start:
	docker-compose up --detach

stop:
	docker-compose stop

destroy:
	docker-compose down --volumes

shell:
	docker-compose exec php sh

migrate:
	docker-compose exec php php artisan migrate