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

usage() {
    printf 'Usage: %s [--include <php-file-path>]\n' "$0"
}

extra_paths=()

while [[ $# -gt 0 ]]; do
    case "$1" in
        --include)
            if [[ $# -lt 2 ]]; then
                usage
                exit 2
            fi
            extra_paths+=("$2")
            shift 2
            ;;
        -h|--help)
            usage
            exit 0
            ;;
        *)
            printf '[ERROR] Unknown argument: %s\n' "$1" >&2
            usage
            exit 2
            ;;
    esac
done

php_files=()

while IFS= read -r -d '' php_file; do
    php_files+=("$php_file")
done < <(find "$PROJECT_ROOT" -type f -name '*.php' -not -path '*/.git/*' -print0)

if [[ ${#extra_paths[@]} -gt 0 ]]; then
    for extra_path in "${extra_paths[@]}"; do
        if [[ ! -f "$extra_path" ]]; then
            printf '[ERROR] Included path does not exist or is not a file: %s\n' "$extra_path" >&2
            exit 2
        fi
        php_files+=("$extra_path")
    done
fi

if [[ ${#php_files[@]} -eq 0 ]]; then
    printf '[OK] No PHP files found. Nothing to lint.\n'
    exit 0
fi

lint_failures=0

for php_file in "${php_files[@]}"; do
    if "$PHP_BIN" -l "$php_file" > /dev/null; then
        printf '[OK] %s\n' "$php_file"
    else
        printf '[FAIL] PHP lint failed: %s\n' "$php_file" >&2
        lint_failures=$((lint_failures + 1))
    fi
done

if [[ $lint_failures -gt 0 ]]; then
    printf '[SUMMARY] Lint failed for %d file(s).\n' "$lint_failures" >&2
    exit 1
fi

printf '[SUMMARY] Lint passed for %d file(s).\n' "${#php_files[@]}"
