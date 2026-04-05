#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
PHP_BIN="${PHP_BIN:-php}"

if ! command -v "$PHP_BIN" > /dev/null 2>&1; then
    printf '[ERROR] PHP binary not found: %s\n' "$PHP_BIN" >&2
    printf '[HINT] Install PHP or run with PHP_BIN=/absolute/path/to/php\n' >&2
    exit 127
fi

assignments=(
    "01-php-basics"
    "02-control-structures"
    "03-arrays"
    "04-associative-arrays"
    "05-multidimensional-arrays"
    "06-user-functions"
    "07-standard-functions"
)

executed=0
skipped=0
failed=0

for assignment in "${assignments[@]}"; do
    runner_path="${PROJECT_ROOT}/assignments/${assignment}/tests/run.php"

    if [[ ! -f "$runner_path" ]]; then
        printf '[SKIP] %s: runner missing (%s). Not implemented yet.\n' "$assignment" "$runner_path"
        skipped=$((skipped + 1))
        continue
    fi

    printf '[RUN] %s\n' "$runner_path"
    if "$PHP_BIN" "$runner_path"; then
        printf '[OK] %s\n' "$assignment"
        executed=$((executed + 1))
    else
        printf '[FAIL] %s\n' "$assignment" >&2
        executed=$((executed + 1))
        failed=$((failed + 1))
    fi
done

printf '[SUMMARY] executed=%d skipped=%d failed=%d\n' "$executed" "$skipped" "$failed"

if [[ $failed -gt 0 ]]; then
    exit 1
fi
