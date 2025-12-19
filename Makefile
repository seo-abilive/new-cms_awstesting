.PHONY: init run down clear
init:
	docker compose build

run:
	docker compose up -d
	docker exec -it new-cms-api sh -c \
	"php artisan migrate"
	docker exec -id new-cms-console sh -c \
	"npm install"
	docker exec -id new-cms-demo sh -c \
	"composer install"
	docker exec -id new-cms-demo-next sh -c \
	"npm install"

down:
	docker compose down

migrate:
	docker exec -it new-cms-api sh -c \
	"php artisan migrate"

clear:
	docker exec -it new-cms-api sh -c \
	"php artisan optimize:clear"

bash:
	docker exec -it new-cms-$(container) sh