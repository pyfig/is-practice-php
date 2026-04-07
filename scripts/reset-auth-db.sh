#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
SCHEMA_FILE="${ROOT_DIR}/database/13-auth-db-app/schema.sql"

if [[ ! -f "${SCHEMA_FILE}" ]]; then
    printf 'Schema file not found: %s\n' "${SCHEMA_FILE}" >&2
    exit 1
fi

if ! command -v npx >/dev/null 2>&1; then
    printf 'npx not found in PATH. Install Node.js to use Supabase CLI.\n' >&2
    exit 1
fi

printf 'Resetting linked Supabase schema for assignment 13...\n'
npx supabase db query --linked --file "${SCHEMA_FILE}" >/dev/null

verification_sql="$(mktemp)"
cleanup() {
    rm -f "${verification_sql}"
}
trap cleanup EXIT

cat > "${verification_sql}" <<'SQL'
DO $$
BEGIN
    IF to_regclass('public.users') IS NULL THEN
        RAISE EXCEPTION 'Expected table public.users to exist after reset.';
    END IF;

    IF to_regclass('public.user_sessions') IS NULL THEN
        RAISE EXCEPTION 'Expected table public.user_sessions to exist after reset.';
    END IF;
END $$;
SQL

npx supabase db query --linked --file "${verification_sql}" >/dev/null

printf 'Database reset complete: public.users and public.user_sessions recreated successfully.\n'
