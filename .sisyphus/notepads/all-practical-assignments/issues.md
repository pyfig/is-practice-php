# Issues

- Local environment blocker for DB tasks: `mysql` CLI is unavailable (`mysql: command not found`), so success-path verification for `bash scripts/reset-auth-db.sh` cannot be completed until MySQL client/server access is provisioned.
- Resolved in-session: PHP and MySQL were installed via Homebrew, MySQL service was started locally, and DB harness verification now succeeds with a dedicated `assignment13` TCP user.
