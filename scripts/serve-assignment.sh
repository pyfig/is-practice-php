#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

KNOWN_SLUGS=(
  "08-string-generation"
  "09-forms"
  "10-http-basics"
  "11-sessions"
  "12-regex-validation"
  "13-auth-db-app"
)

usage() {
  printf 'Usage: bash scripts/serve-assignment.sh <assignment-slug> <port>\n' >&2
  printf 'Known slugs: %s\n' "${KNOWN_SLUGS[*]}" >&2
}

is_known_slug() {
  local needle="$1"
  local slug
  for slug in "${KNOWN_SLUGS[@]}"; do
    if [[ "$slug" == "$needle" ]]; then
      return 0
    fi
  done
  return 1
}

if [[ $# -ne 2 ]]; then
  usage
  exit 64
fi

slug="$1"
port="$2"

if ! is_known_slug "$slug"; then
  printf 'Error: unknown assignment slug "%s".\n' "$slug" >&2
  usage
  exit 65
fi

if [[ ! "$port" =~ ^[0-9]+$ ]] || (( port < 1 || port > 65535 )); then
  printf 'Error: invalid port "%s". Expected integer 1..65535.\n' "$port" >&2
  exit 66
fi

assignment_dir="${ROOT_DIR}/assignments/${slug}"
public_dir="${assignment_dir}/public"

if [[ ! -d "$assignment_dir" ]]; then
  printf 'Error: assignment directory not found: %s\n' "$assignment_dir" >&2
  exit 67
fi

if [[ ! -d "$public_dir" ]]; then
  printf 'Error: public directory not found for "%s": %s\n' "$slug" "$public_dir" >&2
  exit 68
fi

if ! command -v php >/dev/null 2>&1; then
  printf 'Error: php binary not found in PATH. Install PHP to serve assignments.\n' >&2
  exit 69
fi

printf 'Serving "%s" from %s on http://127.0.0.1:%s\n' "$slug" "$public_dir" "$port"
exec php -S "127.0.0.1:${port}" -t "$public_dir"
