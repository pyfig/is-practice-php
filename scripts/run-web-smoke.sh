#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
SERVE_SCRIPT="${SCRIPT_DIR}/serve-assignment.sh"

SLUGS=(
  "08-string-generation"
  "09-forms"
  "10-http-basics"
  "11-sessions"
  "12-regex-validation"
  "13-auth-db-app"
)

PORTS=(8090 8091 8092 8093 8094 8095)

CURRENT_SERVER_PID=""
CURRENT_SERVER_LOG=""

cleanup() {
  if [[ -n "$CURRENT_SERVER_PID" ]] && kill -0 "$CURRENT_SERVER_PID" 2>/dev/null; then
    kill "$CURRENT_SERVER_PID" 2>/dev/null || true
    wait "$CURRENT_SERVER_PID" 2>/dev/null || true
  fi
  if [[ -n "$CURRENT_SERVER_LOG" ]] && [[ -f "$CURRENT_SERVER_LOG" ]]; then
    rm -f "$CURRENT_SERVER_LOG"
  fi
}

trap cleanup EXIT

wait_for_http() {
  local url="$1"
  local attempts=25
  local sleep_seconds=0.2
  local i

  for (( i=1; i<=attempts; i++ )); do
    if curl -sS -o /dev/null "$url"; then
      return 0
    fi
    sleep "$sleep_seconds"
  done
  return 1
}

run_check() {
  local name="$1"
  local url="$2"

  printf '  - %s ... ' "$name"
  if curl -sS -o /dev/null "$url"; then
    printf 'OK\n'
    return 0
  fi

  printf 'FAIL\n'
  return 1
}

run_http_basics_checks() {
  local base_url="$1"
  local response

  run_check "GET /" "${base_url}/"

  printf '  - %s ... ' 'HTTP 200 status'
  response="$(curl -i -sS "${base_url}/status/200")"
  if [[ "$response" == *$'HTTP/1.1 200'* ]]; then printf 'OK\n'; else printf 'FAIL\n'; return 1; fi

  printf '  - %s ... ' 'HTTP 302 status/location'
  response="$(curl -i -sS "${base_url}/status/302")"
  if [[ "$response" == *$'HTTP/1.1 302'* ]] && [[ "$response" == *$'Location: /redirect-target'* ]]; then printf 'OK\n'; else printf 'FAIL\n'; return 1; fi

  printf '  - %s ... ' 'HTTP 400 status'
  response="$(curl -i -sS "${base_url}/status/400")"
  if [[ "$response" == *$'HTTP/1.1 400'* ]]; then printf 'OK\n'; else printf 'FAIL\n'; return 1; fi

  printf '  - %s ... ' 'HTTP 404 status'
  response="$(curl -i -sS "${base_url}/status/404")"
  if [[ "$response" == *$'HTTP/1.1 404'* ]]; then printf 'OK\n'; else printf 'FAIL\n'; return 1; fi
}

run_forms_checks() {
  local base_url="$1"
  local response

  run_check "GET /" "${base_url}/"

  printf '  - %s ... ' 'GET result.php'
  response="$(curl -sS "${base_url}/result.php?name=Ilya&age=25&salary=1234.5")"
  if [[ "$response" == *'Метод запроса: <strong>GET</strong>'* ]] && [[ "$response" == *'Имя: Ilya'* ]]; then printf 'OK\n'; else printf 'FAIL\n'; return 1; fi

  printf '  - %s ... ' 'POST result.php'
  response="$(curl -sS -X POST -d 'name=Ilya&age=25&salary=1234.5' "${base_url}/result.php")"
  if [[ "$response" == *'Метод запроса: <strong>POST</strong>'* ]] && [[ "$response" == *'Зарплата: 1 234.50'* ]]; then printf 'OK\n'; else printf 'FAIL\n'; return 1; fi
}

run_sessions_checks() {
  local base_url="$1"
  local cookie_file
  local response

  cookie_file="$(mktemp)"
  run_check "GET /" "${base_url}/"

  printf '  - %s ... ' 'Session country flow'
  curl -sS -c "$cookie_file" -b "$cookie_file" -X POST -d 'country=Россия' "${base_url}/" >/dev/null
  response="$(curl -sS -c "$cookie_file" -b "$cookie_file" "${base_url}/test.php")"
  if [[ "$response" == *'Россия'* ]]; then printf 'OK\n'; else rm -f "$cookie_file"; printf 'FAIL\n'; return 1; fi

  printf '  - %s ... ' 'Session email flow'
  curl -sS -c "$cookie_file" -b "$cookie_file" -X POST -d 'email=student@example.com' "${base_url}/email-step1.php" >/dev/null
  response="$(curl -sS -c "$cookie_file" -b "$cookie_file" "${base_url}/email-step2.php")"
  rm -f "$cookie_file"
  if [[ "$response" == *'student@example.com'* ]]; then printf 'OK\n'; else printf 'FAIL\n'; return 1; fi
}

run_regex_checks() {
  local base_url="$1"
  local response

  run_check "GET /" "${base_url}/"

  printf '  - %s ... ' 'Valid regex submission'
  response="$(curl -sS -X POST -d 'email=test@example.com&login=ivan_123&password=pass1234&phone=%2B79991234567' "${base_url}/")"
  if [[ "$response" == *'Все поля успешно прошли проверку'* ]]; then printf 'OK\n'; else printf 'FAIL\n'; return 1; fi

  printf '  - %s ... ' 'Invalid regex submission'
  response="$(curl -sS -X POST -d 'email=bad&login=1bad&password=short&phone=123' "${base_url}/")"
  if [[ "$response" == *'Форма содержит ошибки'* ]]; then printf 'OK\n'; else printf 'FAIL\n'; return 1; fi
}

run_auth_checks() {
  local base_url="$1"
  local response

  run_check "GET /" "${base_url}/"

  printf '  - %s ... ' 'Register page reachable'
  response="$(curl -sS "${base_url}/register.php")"
  if [[ "$response" == *'Регистрация'* ]]; then printf 'OK\n'; else printf 'FAIL\n'; return 1; fi

  printf '  - %s ... ' 'Login page reachable'
  response="$(curl -sS "${base_url}/login.php")"
  if [[ "$response" == *'Вход'* ]]; then printf 'OK\n'; else printf 'FAIL\n'; return 1; fi
}

run_default_checks() {
  local base_url="$1"
  run_check "GET /" "${base_url}/"
}

run_slug_smoke() {
  local slug="$1"
  local port="$2"
  local assignment_dir="${ROOT_DIR}/assignments/${slug}"
  local public_dir="${assignment_dir}/public"
  local base_url="http://127.0.0.1:${port}"

  if [[ ! -d "$public_dir" ]]; then
    printf '[SKIP] %s: missing public directory (%s)\n' "$slug" "$public_dir"
    return 0
  fi

  CURRENT_SERVER_LOG="$(mktemp)"
  bash "$SERVE_SCRIPT" "$slug" "$port" >"$CURRENT_SERVER_LOG" 2>&1 &
  CURRENT_SERVER_PID="$!"

  if ! wait_for_http "$base_url/"; then
    printf '[FAIL] %s: server did not become reachable on %s\n' "$slug" "$base_url"
    if [[ -f "$CURRENT_SERVER_LOG" ]]; then
      printf 'Server log:\n' >&2
      cat "$CURRENT_SERVER_LOG" >&2
    fi
    return 1
  fi

  printf '[RUN ] %s on %s\n' "$slug" "$base_url"
  if [[ "$slug" == "10-http-basics" ]]; then
    run_http_basics_checks "$base_url"
  elif [[ "$slug" == "09-forms" ]]; then
    run_forms_checks "$base_url"
  elif [[ "$slug" == "11-sessions" ]]; then
    run_sessions_checks "$base_url"
  elif [[ "$slug" == "12-regex-validation" ]]; then
    run_regex_checks "$base_url"
  elif [[ "$slug" == "13-auth-db-app" ]]; then
    run_auth_checks "$base_url"
  else
    run_default_checks "$base_url"
  fi

  kill "$CURRENT_SERVER_PID" 2>/dev/null || true
  wait "$CURRENT_SERVER_PID" 2>/dev/null || true
  CURRENT_SERVER_PID=""
  rm -f "$CURRENT_SERVER_LOG"
  CURRENT_SERVER_LOG=""

  return 0
}

main() {
  local index
  local failures=0

  if ! command -v php >/dev/null 2>&1; then
    printf 'Web smoke cannot run: php binary not found in PATH.\n' >&2
    return 2
  fi

  for index in "${!SLUGS[@]}"; do
    slug="${SLUGS[$index]}"
    port="${PORTS[$index]}"
    if ! run_slug_smoke "$slug" "$port"; then
      failures=$((failures + 1))
    fi
  done

  if (( failures > 0 )); then
    printf 'Web smoke completed with %d failure(s).\n' "$failures" >&2
    return 1
  fi

  printf 'Web smoke completed successfully.\n'
}

main "$@"
