# Draft: Vercel Launchpad Deployment

## Requirements (confirmed)
- развернуть каждый проект на Vercel
- на главной должны быть папочки по сетке от 1 до 13 в стиле launchpad / Apple
- на каждой странице должен быть header
- из header должен быть возврат на главную
- header должен оставаться на каждой странице

## Technical Decisions
- deployment model: single Vercel project with launchpad home and per-assignment routes
- assignments 01-07: add web wrappers while preserving existing CLI logic
- assignment 13 DB: use external MySQL, not Vercel-native DB replacement

## Research Findings
- `README.md`: browser entrypoints currently expected only for browser-oriented assignments
- `scripts/serve-assignment.sh`: local serving supports only `08-string-generation` through `13-auth-db-app`
- repo currently has no `vercel.json` or `package.json`
- there is no root `index.php` or root `public/index.php` today
- `08`, `09`, `10`, `12` are standalone HTML entry files; `11` and `13` already have assignment-local layout helpers
- `09`, `10`, `11`, `13` use absolute routes today, so subpath mounting would break without route refactoring
- `10-http-basics` contains raw HTTP/text routes that must stay unwrapped by HTML shelling

## Open Questions
- pending repo fact check: exact Vercel rewrite/serving shape for a single-project deployment with PHP apps
- pending repo fact check: exact root/home entrypoint and Vercel rewrite shape

## Scope Boundaries
- INCLUDE: deployment architecture, launchpad home, persistent header, navigation strategy
- EXCLUDE: implementation details until architecture and DB hosting constraints are fixed
