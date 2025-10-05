SHELL := /bin/bash
up:        ; docker compose up -d --build
down:      ; docker compose down
bash:      ; docker compose exec app bash
# init:   ; mkdir -p src && docker compose run --rm app composer create-project laravel/laravel .
key:       ; docker compose exec app php artisan key:generate
migrate:   ; docker compose exec app php artisan migrate
logs:      ; docker compose logs -f
gen:       ; ./scripts/gen-laravel.sh

# ==== Contract Testing (Schemathesis) ====
SPEC_URL ?= https://raw.githubusercontent.com/yaharu711/practice-todo-contract/main/openapi/todo.yaml
# 既定は Docker の Nginx(8080)
BASE_URL ?= http://localhost:8080/api
SCHEMATHESIS_VERSION ?= 4.1.4
# CI/ローカル差異を無くすために Hypothesis を明示ピン
HYPOTHESIS_VERSION ?= 6.140.3

# シンプルに: デフォルトは `--checks=all`、不要なものだけ除外
# 例) EXCLUDE_CHECKS=ignored_auth,use_after_free,ensure_resource_availability
EXCLUDE_CHECKS ?=use_after_free,ensure_resource_availability,ignored_auth,unsupported_method


# 契約テスト（ローカル/CI共通）
contract-check:
	# - --suppress-health-check=filter_too_much: Stateful での棄却過多による FailedHealthCheck を
	#   一時的に無視してCI安定化（本質対策はスキーマ/DBデータ調整）。
	# - --generation-seed: 再現性を上げたい→入力値毎回同じにしたい場合のみ指定（通常は未指定）。
	python3 -m venv .venv
	. .venv/bin/activate; python -m pip install --upgrade pip
	. .venv/bin/activate; python -m pip install schemathesis==$(SCHEMATHESIS_VERSION) hypothesis==$(HYPOTHESIS_VERSION)
	BASE_URL=$(BASE_URL) \
		.venv/bin/schemathesis run $(SPEC_URL) --url=$(BASE_URL) \
		  --checks=all \
		  $(if $(EXCLUDE_CHECKS),--exclude-checks=$(EXCLUDE_CHECKS)) \
		  --suppress-health-check=filter_too_much \
		  --max-examples 50 --generation-unique-inputs \
		  $(if $(HYPOTHESIS_SEED),--generation-seed=$(HYPOTHESIS_SEED))

contract-clean:
	rm -rf .hypothesis reports
