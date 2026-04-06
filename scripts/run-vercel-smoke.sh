#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

if [[ $# -lt 1 ]]; then
    printf 'Usage: %s <base_url> [options]\n' "$0" >&2
    printf 'Example: %s http://127.0.0.1:4010\n' "$0" >&2
    printf '\nOptions:\n' >&2
    printf '  --with-db    Include DB-dependent checks (requires .env.vercel.local)\n' >&2
    exit 1
fi

BASE_URL="${1%/}"
WITH_DB=false

for arg in "$@"; do
    if [[ "$arg" == "--with-db" ]]; then
        WITH_DB=true
    fi
done

PASS_COUNT=0
FAIL_COUNT=0

log_pass() {
    printf '  [OK] %s\n' "$1"
    PASS_COUNT=$((PASS_COUNT + 1))
}

log_fail() {
    printf '  [FAIL] %s\n' "$1" >&2
    FAIL_COUNT=$((FAIL_COUNT + 1))
}

http_get() {
    local url="$1"
    curl -fsSL --max-time 10 "$url" 2>/dev/null || echo ""
}

http_get_code() {
    local url="$1"
    curl -sSL -o /dev/null -w '%{http_code}' --max-time 10 "$url" 2>/dev/null || echo "000"
}

http_get_headers() {
    local url="$1"
    curl -fsSL -I --max-time 10 "$url" 2>/dev/null || echo ""
}

check_launchpad() {
    printf '\n=== Check: Launchpad root ===\n'
    local response
    response=$(http_get "${BASE_URL}/")
    
    if [[ -z "$response" ]]; then
        log_fail "Root URL unreachable"
        return 1
    fi
    
    if [[ "$response" == *'data-launchpad-grid'* ]]; then
        log_pass "Launchpad grid selector found"
    else
        log_fail "Launchpad grid selector (data-launchpad-grid) not found"
    fi
    
    if [[ "$response" == *'data-home-logo'* ]]; then
        log_pass "Home logo selector found"
    else
        log_fail "Home logo selector (data-home-logo) not found"
    fi
}

check_card_count() {
    printf '\n=== Check: Assignment cards ===\n'
    local response
    local card_count
    response=$(http_get "${BASE_URL}/")
    
    card_count=$(echo "$response" | grep -o 'data-assignment-card' | wc -l | tr -d ' ')
    
    if [[ "$card_count" -eq 13 ]]; then
        log_pass "Found exactly 13 assignment cards"
    else
        log_fail "Expected 13 cards, found $card_count"
    fi
}

check_cli_wrappers() {
    printf '\n=== Check: CLI assignment wrappers ===\n'
    local response
    
    response=$(http_get "${BASE_URL}/01-php-basics")
    if [[ "$response" == *'data-home-logo'* ]] && [[ "$response" == *'Rectangle'* || "$response" == *'Периметр'* || "$response" == *'прямоугольника'* ]]; then
        log_pass "/01-php-basics loads with header and content"
    else
        log_fail "/01-php-basics missing header or expected content"
    fi
    
    response=$(http_get "${BASE_URL}/07-standard-functions")
    if [[ "$response" == *'data-home-logo'* ]] && [[ "$response" == *'http'* || "$response" == *'HTTP'* || "$response" == *'функций'* ]]; then
        log_pass "/07-standard-functions loads with header and content"
    else
        log_fail "/07-standard-functions missing header or expected content"
    fi
}

check_mounted_pages() {
    printf '\n=== Check: Mounted web pages ===\n'
    local response
    
    response=$(http_get "${BASE_URL}/08-string-generation")
    if [[ "$response" == *'data-home-logo'* ]]; then
        log_pass "/08-string-generation has header"
    else
        log_fail "/08-string-generation missing header"
    fi
    
    response=$(http_get "${BASE_URL}/12-regex-validation")
    if [[ "$response" == *'data-home-logo'* ]] && [[ "$response" == *'form'* || "$response" == *'input'* ]]; then
        log_pass "/12-regex-validation has header and form"
    else
        log_fail "/12-regex-validation missing header or form"
    fi
}

check_mounted_refactors() {
    printf '\n=== Check: Mounted route refactors ===\n'
    local response
    local headers
    local status
    
    response=$(http_get "${BASE_URL}/09-forms/result.php?name=Ivan&age=20&salary=500")
    if [[ "$response" == *'GET'* ]] && [[ "$response" == *'Ivan'* ]]; then
        log_pass "/09-forms/result.php handles GET parameters"
    else
        log_fail "/09-forms/result.php GET handling failed"
    fi
    
    response=$(curl -fsSL -X POST -d 'name=Ivan&age=20&salary=500' --max-time 10 "${BASE_URL}/09-forms/result.php" 2>/dev/null || echo "")
    if [[ "$response" == *'POST'* ]] && [[ "$response" == *'Ivan'* ]]; then
        log_pass "/09-forms/result.php handles POST parameters"
    else
        log_fail "/09-forms/result.php POST handling failed"
    fi
    
    status=$(http_get_code "${BASE_URL}/10-http-basics/status/404")
    if [[ "$status" == "404" ]]; then
        log_pass "/10-http-basics/status/404 returns HTTP 404"
    else
        log_fail "/10-http-basics/status/404 returned HTTP $status instead of 404"
    fi
    
    headers=$(http_get_headers "${BASE_URL}/10-http-basics/status/302")
    if [[ "$headers" == *'302'* ]] && [[ "$headers" == *'redirect-target'* ]]; then
        log_pass "/10-http-basics/status/302 returns redirect to redirect-target"
    else
        log_fail "/10-http-basics/status/302 redirect handling failed"
    fi
    
    response=$(http_get "${BASE_URL}/11-sessions")
    if [[ "$response" == *'data-home-logo'* ]]; then
        log_pass "/11-sessions is reachable with header"
    else
        log_fail "/11-sessions not reachable or missing header"
    fi
    
    response=$(http_get "${BASE_URL}/13-auth-db-app")
    if [[ "$response" == *'data-home-logo'* ]]; then
        log_pass "/13-auth-db-app is reachable with header"
    else
        log_fail "/13-auth-db-app not reachable or missing header"
    fi
}

check_stateful_routes() {
    if [[ "$WITH_DB" != true ]]; then
        printf '\n=== Check: Stateful routes (skipped, use --with-db) ===\n'
        return 0
    fi
    
    printf '\n=== Check: Stateful routes ===\n'
    
    local cookie_file
    local response
    
    cookie_file=$(mktemp)
    
    curl -fsSL -c "$cookie_file" -b "$cookie_file" -X POST -d 'country=Россия' --max-time 10 "${BASE_URL}/11-sessions" >/dev/null 2>&1 || true
    response=$(curl -fsSL -c "$cookie_file" -b "$cookie_file" --max-time 10 "${BASE_URL}/11-sessions/test.php" 2>/dev/null || echo "")
    if [[ "$response" == *'Россия'* ]]; then
        log_pass "/11-sessions country persistence works"
    else
        log_fail "/11-sessions country persistence failed"
    fi
    
    curl -fsSL -c "$cookie_file" -b "$cookie_file" -X POST -d 'email=test@example.com' --max-time 10 "${BASE_URL}/11-sessions/email-step1.php" >/dev/null 2>&1 || true
    response=$(curl -fsSL -c "$cookie_file" -b "$cookie_file" --max-time 10 "${BASE_URL}/11-sessions/email-step2.php" 2>/dev/null || echo "")
    if [[ "$response" == *'test@example.com'* ]]; then
        log_pass "/11-sessions email flow works"
    else
        log_fail "/11-sessions email flow failed"
    fi
    
    rm -f "$cookie_file"
    
    cookie_file=$(mktemp)
    local timestamp
    timestamp=$(date +%s)
    local test_email="test${timestamp}@example.com"
    
    response=$(curl -fsSL -c "$cookie_file" -b "$cookie_file" -X POST -d "full_name=Test User&email=${test_email}&password=password123" --max-time 10 "${BASE_URL}/13-auth-db-app/register.php" 2>/dev/null || echo "")
    if [[ "$response" == *'успешно'* || "$response" == *'success'* || "$response" == *'Вход'* || "$response" == *'login'* ]]; then
        log_pass "/13-auth-db-app registration works"
        
        response=$(curl -fsSL -c "$cookie_file" -b "$cookie_file" -X POST -d "email=${test_email}&password=password123" --max-time 10 "${BASE_URL}/13-auth-db-app/login.php" 2>/dev/null || echo "")
        if [[ "$response" == *'вошли'* || "$response" == *'logged'* || "$response" == *'dashboard'* || "$response" == *'профиль'* ]]; then
            log_pass "/13-auth-db-app login works"
        else
            log_fail "/13-auth-db-app login failed"
        fi
    else
        log_fail "/13-auth-db-app registration failed (DB may not be configured)"
    fi
    
    rm -f "$cookie_file"
}

check_card_types() {
    printf '\n=== Check: Card metadata ===\n'
    local response
    response=$(http_get "${BASE_URL}/")
    
    local cli_count
    local web_count
    local stateful_count
    
    cli_count=$(echo "$response" | grep -o 'data-assignment-type="cli"' | wc -l | tr -d ' ')
    web_count=$(echo "$response" | grep -o 'data-assignment-type="web"' | wc -l | tr -d ' ')
    stateful_count=$(echo "$response" | grep -o 'data-assignment-type="stateful"' | wc -l | tr -d ' ')
    
    if [[ "$cli_count" -eq 7 ]]; then
        log_pass "Found 7 CLI type cards"
    else
        log_fail "Expected 7 CLI cards, found $cli_count"
    fi
    
    if [[ "$web_count" -eq 4 ]]; then
        log_pass "Found 4 Web type cards"
    else
        log_fail "Expected 4 Web cards, found $web_count"
    fi
    
    if [[ "$stateful_count" -eq 2 ]]; then
        log_pass "Found 2 Stateful type cards"
    else
        log_fail "Expected 2 Stateful cards, found $stateful_count"
    fi
}

main() {
    printf '=== Vercel Smoke Tests ===\n'
    printf 'Base URL: %s\n' "$BASE_URL"
    printf 'DB checks: %s\n' "$([[ "$WITH_DB" == true ]] && echo "enabled" || echo "disabled")"
    
    check_launchpad
    check_card_count
    check_card_types
    check_cli_wrappers
    check_mounted_pages
    check_mounted_refactors
    check_stateful_routes
    
    printf '\n=== Summary ===\n'
    printf 'Passed: %d\n' "$PASS_COUNT"
    printf 'Failed: %d\n' "$FAIL_COUNT"
    
    if [[ "$FAIL_COUNT" -gt 0 ]]; then
        exit 1
    fi
    
    printf '\nAll smoke checks passed!\n'
}

main "$@"
