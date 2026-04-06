#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
SCHEMA_FILE="${ROOT_DIR}/database/13-auth-db-app/schema.sql"

require_env() {
    local var_name="$1"
    if [ -z "${!var_name+x}" ]; then
        printf 'Missing required env var: %s\n' "$var_name" >&2
        return 1
    fi
}

validate_env() {
    local missing=0

    require_env "AUTH_DB_HOST" || missing=1
    require_env "AUTH_DB_PORT" || missing=1
    require_env "AUTH_DB_USER" || missing=1
    require_env "AUTH_DB_PASSWORD" || missing=1
    require_env "AUTH_DB_NAME" || missing=1

    if [ "$missing" -ne 0 ]; then
        printf 'Required: AUTH_DB_HOST AUTH_DB_PORT AUTH_DB_USER AUTH_DB_PASSWORD AUTH_DB_NAME\n' >&2
        printf 'Example: AUTH_DB_HOST=127.0.0.1 AUTH_DB_PORT=3306 AUTH_DB_USER=student AUTH_DB_PASSWORD=secret AUTH_DB_NAME=assignment13_auth\n' >&2
        exit 1
    fi
}

build_mysql_args() {
    MYSQL_ARGS=(
        --protocol=TCP
        -h "$AUTH_DB_HOST"
        -P "$AUTH_DB_PORT"
        -u "$AUTH_DB_USER"
        --default-character-set=utf8mb4
        --batch
        --silent
    )
}

run_mysql_sql() {
    local sql="$1"
    local output

    if ! output=$(MYSQL_PWD="$AUTH_DB_PASSWORD" mysql "${MYSQL_ARGS[@]}" -e "$sql" 2>&1); then
        printf 'MySQL command failed. Check credentials/host/database variables and server availability.\n' >&2
        printf 'Command context: %s@%s:%s\n' "$AUTH_DB_USER" "$AUTH_DB_HOST" "$AUTH_DB_PORT" >&2
        printf 'MySQL error: %s\n' "$output" >&2
        return 1
    fi
}

validate_env
build_mysql_args

if [ ! -f "$SCHEMA_FILE" ]; then
    printf 'Schema file not found: %s\n' "$SCHEMA_FILE" >&2
    exit 1
fi

printf 'Resetting database "%s" for assignment 13...\n' "$AUTH_DB_NAME"

run_mysql_sql "SELECT 1;"
run_mysql_sql "CREATE DATABASE IF NOT EXISTS \`$AUTH_DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if ! output=$(MYSQL_PWD="$AUTH_DB_PASSWORD" mysql "${MYSQL_ARGS[@]}" "$AUTH_DB_NAME" < "$SCHEMA_FILE" 2>&1); then
    printf 'Failed to apply schema file to database "%s".\n' "$AUTH_DB_NAME" >&2
    printf 'MySQL error: %s\n' "$output" >&2
    exit 1
fi

run_mysql_sql "SELECT 1 FROM information_schema.tables WHERE table_schema = '$AUTH_DB_NAME' AND table_name = 'users' LIMIT 1;"
run_mysql_sql "SELECT 1 FROM information_schema.tables WHERE table_schema = '$AUTH_DB_NAME' AND table_name = 'user_sessions' LIMIT 1;"

printf 'Database reset complete: %s.users and %s.user_sessions recreated successfully.\n' "$AUTH_DB_NAME" "$AUTH_DB_NAME"
