#!/usr/bin/env bash
set -euo pipefail
SPEC_PATH=${1:-openapi/openapi.yaml}
OUT_DIR=${2:-generated/laravel}
mkdir -p "${OUT_DIR}"
docker run --rm -v "${PWD}:/local" openapitools/openapi-generator-cli:v7.8.0 \
  generate -i "/local/${SPEC_PATH}" -g php-laravel -o "/local/${OUT_DIR}" \
  --additional-properties=packageName=App,invokerPackage=App
echo "Generated -> ${OUT_DIR}"

