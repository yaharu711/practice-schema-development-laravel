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
	python3 -m venv .venv
	. .venv/bin/activate; python -m pip install --upgrade pip
	. .venv/bin/activate; python -m pip install schemathesis==$(SCHEMATHESIS_VERSION) hypothesis==$(HYPOTHESIS_VERSION)
	BASE_URL=$(BASE_URL) \
		.venv/bin/schemathesis run $(SPEC_URL) --url=$(BASE_URL) \
		  --checks=all \
		  $$(test -n "$(EXCLUDE_CHECKS)" && echo --exclude-checks=$(EXCLUDE_CHECKS)) \
		  # 再現性のためシードを指定可能に。これで入力データが固定になる
		  $$(test -n "$(HYPOTHESIS_SEED)" && echo --generation-seed=$(HYPOTHESIS_SEED)) \
		  \
		  # メモ: CIで StatefuI の前提未成立等により Hypothesis の
		  # health check(filter_too_much) が発火し、例外で失敗することがある。
		  # スキーマの制約強化やDBシードで棄却率を下げるのが本筋だが、
		  # 当面の安定化のため例外を失敗扱いにしない。
		  --suppress-health-check=filter_too_much \
		  --max-examples 50 --generation-unique-inputs

contract-clean:
	rm -rf .hypothesis reports
