#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
ENV_FILE="${ROOT_DIR}/.env.supabase.local"

if [[ ! -f "${ENV_FILE}" ]]; then
    printf 'Environment file not found: %s\n' "${ENV_FILE}" >&2
    printf 'Create it from .env.supabase.local.example and fill SUPABASE_URL, SUPABASE_SERVICE_ROLE_KEY, ASSIGNMENT13_AUTH_SECRET.\n' >&2
    exit 1
fi

if ! command -v vercel >/dev/null 2>&1; then
    printf 'vercel CLI not found in PATH.\n' >&2
    exit 1
fi

if ! command -v npx >/dev/null 2>&1; then
    printf 'npx not found in PATH. Install Node.js to use Supabase CLI.\n' >&2
    exit 1
fi

set -a
source "${ENV_FILE}"
set +a

required_vars=(
    "SUPABASE_URL"
    "SUPABASE_SERVICE_ROLE_KEY"
    "ASSIGNMENT13_AUTH_SECRET"
)

for var_name in "${required_vars[@]}"; do
    if [[ -z "${!var_name:-}" ]]; then
        printf 'Missing required variable in %s: %s\n' "${ENV_FILE}" "${var_name}" >&2
        exit 1
    fi
done

printf 'Linting assignment 13 PHP files...\n'
php -l "${ROOT_DIR}/assignments/13-auth-db-app/src/db.php" >/dev/null
php -l "${ROOT_DIR}/assignments/13-auth-db-app/src/auth.php" >/dev/null
php -l "${ROOT_DIR}/assignments/13-auth-db-app/src/bootstrap.php" >/dev/null
php -l "${ROOT_DIR}/assignments/13-auth-db-app/public/index.php" >/dev/null
php -l "${ROOT_DIR}/assignments/13-auth-db-app/public/register.php" >/dev/null
php -l "${ROOT_DIR}/assignments/13-auth-db-app/public/login.php" >/dev/null
php -l "${ROOT_DIR}/assignments/13-auth-db-app/public/logout.php" >/dev/null

printf 'Resetting and smoke-checking linked Supabase project...\n'
bash "${ROOT_DIR}/scripts/reset-auth-db.sh"
bash "${ROOT_DIR}/scripts/run-db-smoke.sh"

printf 'Deploying preview to Vercel with deployment-scoped runtime env...\n'
deploy_json="$(
    vercel deploy "${ROOT_DIR}" \
        --target preview \
        --format json \
        -y \
        -e "SUPABASE_URL=${SUPABASE_URL}" \
        -e "SUPABASE_SERVICE_ROLE_KEY=${SUPABASE_SERVICE_ROLE_KEY}" \
        -e "ASSIGNMENT13_AUTH_SECRET=${ASSIGNMENT13_AUTH_SECRET}"
)"

preview_url="$(printf '%s' "${deploy_json}" | php -r '$input = stream_get_contents(STDIN); $json = json_decode($input, true); if (!is_array($json) || !isset($json["deployment"]["url"])) { fwrite(STDERR, "Failed to parse Vercel deploy output.\n"); exit(1); } echo $json["deployment"]["url"];')"
inspector_url="$(printf '%s' "${deploy_json}" | php -r '$input = stream_get_contents(STDIN); $json = json_decode($input, true); if (!is_array($json) || !isset($json["deployment"]["inspectorUrl"])) { fwrite(STDERR, "Failed to parse Vercel inspector URL.\n"); exit(1); } echo $json["deployment"]["inspectorUrl"];')"

printf 'Preview deployment is ready.\n'
printf 'Preview: %s\n' "${preview_url}"
printf 'Inspector: %s\n' "${inspector_url}"
printf 'Assignment 13: %s/13-auth-db-app\n' "${preview_url}"
