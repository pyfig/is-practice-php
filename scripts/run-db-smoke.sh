#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

bash "${ROOT_DIR}/scripts/reset-auth-db.sh"

mysql_query() {
    local sql="$1"
    MYSQL_PWD="$AUTH_DB_PASSWORD" mysql \
        --protocol=TCP \
        -h "$AUTH_DB_HOST" \
        -P "$AUTH_DB_PORT" \
        -u "$AUTH_DB_USER" \
        --default-character-set=utf8mb4 \
        --batch \
        --skip-column-names \
        "$AUTH_DB_NAME" \
        -e "$sql"
}

users_count="$(mysql_query "SELECT COUNT(*) FROM users;")"
if [ "$users_count" != "0" ]; then
    printf 'Smoke check failed: expected empty users table after reset, got %s rows.\n' "$users_count" >&2
    exit 1
fi

email_unique="$(mysql_query "SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = '$AUTH_DB_NAME' AND table_name = 'users' AND column_name = 'email' AND non_unique = 0;")"
if [ "$email_unique" = "0" ]; then
    printf 'Smoke check failed: users.email unique constraint is missing.\n' >&2
    exit 1
fi

printf 'DB smoke checks passed for %s.users.\n' "$AUTH_DB_NAME"
printf 'Ready for future auth flow smoke checks in this script.\n'
