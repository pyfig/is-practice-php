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

user_sessions_count="$(mysql_query "SELECT COUNT(*) FROM user_sessions;")"
if [ "$user_sessions_count" != "0" ]; then
    printf 'Smoke check failed: expected empty user_sessions table after reset, got %s rows.\n' "$user_sessions_count" >&2
    exit 1
fi

email_unique="$(mysql_query "SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = '$AUTH_DB_NAME' AND table_name = 'users' AND column_name = 'email' AND non_unique = 0;")"
if [ "$email_unique" = "0" ]; then
    printf 'Smoke check failed: users.email unique constraint is missing.\n' >&2
    exit 1
fi

token_hash_unique="$(mysql_query "SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = '$AUTH_DB_NAME' AND table_name = 'user_sessions' AND column_name = 'token_hash' AND non_unique = 0;")"
if [ "$token_hash_unique" = "0" ]; then
    printf 'Smoke check failed: user_sessions.token_hash unique constraint is missing.\n' >&2
    exit 1
fi

fk_count="$(mysql_query "SELECT COUNT(*) FROM information_schema.referential_constraints WHERE constraint_schema = '$AUTH_DB_NAME' AND table_name = 'user_sessions' AND referenced_table_name = 'users';")"
if [ "$fk_count" = "0" ]; then
    printf 'Smoke check failed: user_sessions.user_id foreign key to users is missing.\n' >&2
    exit 1
fi

printf 'DB smoke checks passed for %s.users and %s.user_sessions.\n' "$AUTH_DB_NAME" "$AUTH_DB_NAME"
