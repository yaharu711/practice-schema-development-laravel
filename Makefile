SHELL := /bin/bash
up:        ; docker compose up -d --build
down:      ; docker compose down
bash:      ; docker compose exec app bash
install:   ; mkdir -p src && docker compose run --rm app composer create-project laravel/laravel .
key:       ; docker compose exec app php artisan key:generate
migrate:   ; docker compose exec app php artisan migrate
logs:      ; docker compose logs -f
gen:       ; ./scripts/gen-laravel.sh

# ==== Contract Testing (Schemathesis) ====
SPEC_URL ?= https://raw.githubusercontent.com/yaharu711/practice-todo-contract/main/openapi/todo.yaml
# 既定は Docker の Nginx(8080)
BASE_URL ?= http://localhost:8080/api
SCHEMATHESIS_VERSION ?= 4.1.4

# 契約テスト（ローカル/CI共通）
contract:
	python3 -m venv .venv
	. .venv/bin/activate; python -m pip install --upgrade pip
	. .venv/bin/activate; python -m pip install schemathesis==$(SCHEMATHESIS_VERSION)
	.venv/bin/schemathesis run $(SPEC_URL) --url=$(BASE_URL) \
	  --checks=status_code_conformance,response_schema_conformance,content_type_conformance,response_headers_conformance,not_a_server_error
