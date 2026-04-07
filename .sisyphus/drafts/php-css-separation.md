# Draft: PHP CSS Separation

## Requirements (confirmed)
- separation-request: "необходимо все что написано на пыхе - и что в ней находится - разделить на .css & .php"

## Technical Decisions
- planning-mode: Produce a work plan for separating styling concerns from PHP files; no implementation in this session.
- working-scope: Treat this as a repo-wide refactor candidate until exploration/user scope confirms narrower boundaries.

## Research Findings
- repo-structure: `assignments/` contains 13 isolated assignment apps.
- planning-artifacts: `.sisyphus/drafts/` and `.sisyphus/plans/` already exist for planning files.
- browser-vs-cli-hint: Repo README distinguishes CLI assignments `01-07` from browser assignments `08-13`, which likely affects CSS extraction scope.

## Open Questions
- [x] exact-scope: All 13 assignments (01-13), per user clarification
- [x] separation-depth: CSS + templates — extract both styles and presentation markup where feasible

## Scope Boundaries
- INCLUDE: planning the separation of styling concerns and template markup from PHP files
- EXCLUDE: direct code changes during planning

## Decisions
- extraction-strategy: Two-pass refactor — (1) CSS extraction to shared assets, (2) template logic separation where applicable
- coverage: All 13 assignments
- assignments-01-07: CLI apps wrapped in HTML shell; extract common wrapper styles + minor template separation
- assignments-08-13: Full web apps; extract per-assignment styles + stronger template/PHP split
- existing-shared-css: `public/assets/launchpad.css` already provides design tokens
- verification-risk: Low — smoke tests verify content/HTTP, not visual styling
