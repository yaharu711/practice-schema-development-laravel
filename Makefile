SHELL := /bin/bash
up:        ; docker compose up -d --build
down:      ; docker compose down
bash:      ; docker compose exec app bash
install:   ; mkdir -p src && docker compose run --rm app composer create-project laravel/laravel .
key:       ; docker compose exec app php artisan key:generate
migrate:   ; docker compose exec app php artisan migrate
logs:      ; docker compose logs -f
gen:       ; ./scripts/gen-laravel.sh openapi/openapi.yaml generated/laravel
