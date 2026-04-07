#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

bash "${ROOT_DIR}/scripts/reset-auth-db.sh"

smoke_sql="$(mktemp)"
cleanup() {
    rm -f "${smoke_sql}"
}
trap cleanup EXIT

cat > "${smoke_sql}" <<'SQL'
DO $$
DECLARE
    users_count bigint;
    sessions_count bigint;
    email_unique bigint;
    token_unique bigint;
    foreign_key_count bigint;
BEGIN
    SELECT COUNT(*) INTO users_count FROM public.users;
    IF users_count <> 0 THEN
        RAISE EXCEPTION 'Smoke check failed: expected empty users table after reset.';
    END IF;

    SELECT COUNT(*) INTO sessions_count FROM public.user_sessions;
    IF sessions_count <> 0 THEN
        RAISE EXCEPTION 'Smoke check failed: expected empty user_sessions table after reset.';
    END IF;

    SELECT COUNT(*) INTO email_unique
    FROM information_schema.table_constraints tc
    JOIN information_schema.key_column_usage kcu
      ON tc.constraint_catalog = kcu.constraint_catalog
     AND tc.constraint_schema = kcu.constraint_schema
     AND tc.constraint_name = kcu.constraint_name
     AND tc.table_name = kcu.table_name
    WHERE tc.table_schema = 'public'
      AND tc.table_name = 'users'
      AND tc.constraint_type = 'UNIQUE'
      AND kcu.column_name = 'email';

    IF email_unique = 0 THEN
        RAISE EXCEPTION 'Smoke check failed: users.email unique constraint is missing.';
    END IF;

    SELECT COUNT(*) INTO token_unique
    FROM information_schema.table_constraints tc
    JOIN information_schema.key_column_usage kcu
      ON tc.constraint_catalog = kcu.constraint_catalog
     AND tc.constraint_schema = kcu.constraint_schema
     AND tc.constraint_name = kcu.constraint_name
     AND tc.table_name = kcu.table_name
    WHERE tc.table_schema = 'public'
      AND tc.table_name = 'user_sessions'
      AND tc.constraint_type = 'UNIQUE'
      AND kcu.column_name = 'token_hash';

    IF token_unique = 0 THEN
        RAISE EXCEPTION 'Smoke check failed: user_sessions.token_hash unique constraint is missing.';
    END IF;

    SELECT COUNT(*) INTO foreign_key_count
    FROM information_schema.table_constraints tc
    JOIN information_schema.key_column_usage kcu
      ON tc.constraint_catalog = kcu.constraint_catalog
     AND tc.constraint_schema = kcu.constraint_schema
     AND tc.constraint_name = kcu.constraint_name
     AND tc.table_name = kcu.table_name
    JOIN information_schema.constraint_column_usage ccu
      ON tc.constraint_catalog = ccu.constraint_catalog
     AND tc.constraint_schema = ccu.constraint_schema
     AND tc.constraint_name = ccu.constraint_name
    WHERE tc.table_schema = 'public'
      AND tc.table_name = 'user_sessions'
      AND tc.constraint_type = 'FOREIGN KEY'
      AND kcu.column_name = 'user_id'
      AND ccu.table_name = 'users';

    IF foreign_key_count = 0 THEN
        RAISE EXCEPTION 'Smoke check failed: user_sessions.user_id foreign key to users is missing.';
    END IF;
END $$;
SQL

npx supabase db query --linked --file "${smoke_sql}" >/dev/null

printf 'DB smoke checks passed for public.users and public.user_sessions.\n'
