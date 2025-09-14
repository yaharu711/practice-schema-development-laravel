#!/usr/bin/env bash
set -euo pipefail

# 使い方:
#   ./scripts/gen-laravel.sh [SPEC_URL_OR_PATH] [OUT_DIR]
# 省略時は contract リポ(main)の Raw URL と "generated/laravel" を使用します。

DEFAULT_SPEC_URL="https://raw.githubusercontent.com/yaharu711/practice-todo-contract/main/openapi/todo.yaml"
SPEC_INPUT=${1:-}
OUT_DIR=${2:-generated/laravel}

if [[ -z "${SPEC_INPUT}" ]]; then
  SPEC_INPUT="${DEFAULT_SPEC_URL}"
fi

mkdir -p "${OUT_DIR}"

docker run --rm -v "${PWD}:/local" openapitools/openapi-generator-cli:v7.8.0 \
  generate -i "${SPEC_INPUT}" -g php-laravel -o "/local/${OUT_DIR}" \
  --additional-properties=packageName=App,invokerPackage=App

echo "Generated -> ${OUT_DIR} (from: ${SPEC_INPUT})"
