# Plan: PHP CSS + Template Separation

## Objective
Разделить всё, что находится в PHP-файлах репозитория, на чёткие зоны ответственности: отдельные файлы стилей (.css) и отдельные PHP-шаблоны/логику. Рефакторинг охватывает все 13 assignment-папок.

## Success Criteria
- [ ] Все инлайн `<style>` блоки вынесены из PHP в `public/assets/assignment-*.css`
- [ ] Инлайн `style="..."` атрибуты (кроме динамических) заменены на CSS-классы
- [ ] Где возможно, HTML-разметка вынесена в `templates/*.php` или `views/*.php`
- [ ] PHP-логика изолирована от презентации (include/require шаблонов)
- [ ] Все 13 assignment продолжают работать (`run-web-smoke.sh` и `run-cli-assignments.sh` проходят)

## Phase 1: Foundation (shared infrastructure)
**Goal:** Создать структуру для внешних стилей до начала рефакторинга assignment.

**Tasks:**
- [ ] Create `public/assets/assignment-common.css`
  - Move shared classes from 01-07 CLI wrappers: `.assignment-page`, `.assignment-shell`, `.assignment-title`, `.assignment-description`, `.assignment-output`
  - Ensure CSS variable usage matches `launchpad.css` tokens
- [ ] Update `public/assets/launchpad.css` references audit
  - Verify all 14 files link to `/assets/launchpad.css`
  - Document any hardcoded values that should use CSS variables

**Evidence to collect:**
- `php-lint-all.sh` passes
- `public/assets/assignment-common.css` exists with extracted styles

## Phase 2: CLI Wrappers 01-07 (identical pattern)
**Goal:** Extract identical CSS blocks from all 7 CLI assignments.

**Pattern per assignment (01 through 07):**
1. Read `assignments/XX-*/public/index.php`
2. Extract `<style>` block content (identical ~37 lines across all 7)
3. Remove `<style>` tag entirely
4. Add `<link rel="stylesheet" href="/assets/assignment-common.css">` if not present
5. Update references to use CSS variables where hardcoded

**Tasks:**
- [ ] `01-php-basics/public/index.php` — remove inline style, link common CSS
- [ ] `02-control-structures/public/index.php` — remove inline style, link common CSS
- [ ] `03-arrays/public/index.php` — remove inline style, link common CSS
- [ ] `04-associative-arrays/public/index.php` — remove inline style, link common CSS
- [ ] `05-multidimensional-arrays/public/index.php` — remove inline style, link common CSS
- [ ] `06-user-functions/public/index.php` — remove inline style, link common CSS
- [ ] `07-standard-functions/public/index.php` — remove inline style, link common CSS

**Evidence to collect:**
- Each assignment loads without 404 on CSS
- `run-cli-assignments.sh` still passes (functional unchanged)

## Phase 3: Browser Apps 08-12 (individual styles)
**Goal:** Extract assignment-specific CSS while preserving unique layouts.

### 08-string-generation
**Tasks:**
- [ ] Create `assignments/08-string-generation/public/assets/styles.css`
- [ ] Extract ~68 lines of `<style>` block from `public/index.php`
- [ ] Convert to CSS variables where hardcoded (`24px` → `--spacing-xl`)
- [ ] Remove `<style>` tag from index.php
- [ ] Add `<link rel="stylesheet" href="assets/styles.css">`

### 09-forms
**Tasks:**
- [ ] Create `assignments/09-forms/public/assets/styles.css`
- [ ] Extract ~13-line `<style>` from `public/index.php`
- [ ] Extract inline `style="..."` from `src/helpers.php` `render_alert()` function
  - Create `.alert` class with those styles
  - Update function to use `class="alert"` instead of inline style
- [ ] Update `public/index.php` and `public/result.php` to link new CSS

### 10-http-basics
**Tasks:**
- [ ] Create `assignments/10-http-basics/public/assets/styles.css`
- [ ] Extract minified CSS from PHP string concatenation in `public/index.php`
- [ ] Refactor to proper external stylesheet link
- [ ] Note: This file uses CSS embedded in PHP string (unique pattern)

### 11-sessions
**Tasks:**
- [ ] Create `assignments/11-sessions/public/assets/styles.css`
- [ ] Extract CSS from `src/bootstrap.php` `render_page()` function (~1 line minified)
- [ ] Refactor `render_page()` to accept stylesheet parameter or use global link
- [ ] Update all 5 public pages that use bootstrap.php to link external CSS

### 12-regex-validation
**Tasks:**
- [ ] Create `assignments/12-regex-validation/public/assets/styles.css`
- [ ] Extract ~14-line `<style>` block from `public/index.php`
- [ ] Form validation styles and error states

**Evidence to collect:**
- `run-web-smoke.sh` passes for assignments 08-12
- No 404 errors on CSS file requests

## Phase 4: Auth/DB App 13 (complex bootstrap)
**Goal:** Extract CSS from bootstrap.php while maintaining auth flow integrity.

**Tasks:**
- [ ] Create `assignments/13-auth-db-app/public/assets/styles.css`
- [ ] Extract ~14-line `<style>` block from `src/bootstrap.php` `render_layout()` function
- [ ] Refactor `render_layout()` to use external stylesheet instead of embedded CSS
- [ ] Styles to extract: nav, flash-success, flash-error, muted, form styles
- [ ] Update all pages using bootstrap.php to link the new CSS

**Evidence to collect:**
- `run-web-smoke.sh` passes for assignment 13
- `run-db-smoke.sh` passes (database schema unchanged)

## Phase 5: Template Separation (where applicable)
**Goal:** Separate HTML presentation from PHP logic in applicable assignments.

**Scope decisions:**
- **01-07**: Minimal separation — keep inline PHP echo for CLI output, CSS is main win
- **08-12**: Moderate separation — extract reusable components:
  - Form templates
  - Navigation/header components
  - Footer components
- **13**: Strongest separation — auth app benefits most:
  - `templates/layout.php` — base HTML structure
  - `templates/nav.php` — navigation component
  - `templates/flash.php` — flash messages component
  - Page files include templates, keep only business logic

**Template separation tasks (for 11, 13 primarily):**
- [ ] Create `assignments/11-sessions/templates/` directory
- [ ] Move `render_page()` HTML to `templates/layout.php`
- [ ] Update bootstrap.php to include template file
- [ ] Create `assignments/13-auth-db-app/templates/` directory
- [ ] Move `render_layout()` HTML to `templates/layout.php`
- [ ] Create `templates/nav.php` for navigation markup
- [ ] Create `templates/flash.php` for flash message rendering
- [ ] Update all public pages to use template includes

## Phase 6: Cleanup & Standardization
**Goal:** Ensure consistency across all extracted CSS.

**Tasks:**
- [ ] Audit all extracted CSS for CSS variable usage
- [ ] Convert remaining hardcoded values to `launchpad.css` tokens
- [ ] Ensure consistent naming convention (kebab-case for classes)
- [ ] Remove any duplicate styles between `assignment-common.css` and individual assignment CSS
- [ ] Verify UTF-8 encoding without BOM on all new CSS files

**Evidence to collect:**
- `php-lint-all.sh` passes
- `run-web-smoke.sh` passes for all browser assignments
- `run-cli-assignments.sh` passes for all CLI assignments

## Rollback Plan
If any phase fails verification:
1. `git checkout -- assignments/` to restore original PHP files
2. `rm -f public/assets/assignment-*.css` to remove new CSS files
3. Re-run verification to confirm clean state
4. Debug and retry failed phase

## Verification Strategy
**After each phase:**
1. `bash scripts/php-lint-all.sh` — syntax check all PHP
2. `bash scripts/run-cli-assignments.sh` — functional test 01-07
3. `bash scripts/run-web-smoke.sh` — HTTP/content test 08-13
4. `bash scripts/run-db-smoke.sh` — database test for 13

**Note:** Current smoke tests verify HTTP status and content strings, NOT visual rendering. CSS extraction is low-risk for verification failure.

## File Structure After Completion

```
public/assets/
├── launchpad.css                 # existing, unchanged
├── assignment-common.css         # NEW: shared by 01-07 CLI wrappers
assignments/
├── 01-php-basics/public/index.php          # no <style>, links common.css
├── 02-control-structures/public/index.php  # no <style>, links common.css
... (01-07 all same pattern)
├── 08-string-generation/
│   ├── public/index.php          # no <style>, links assets/styles.css
│   └── public/assets/styles.css  # NEW: extracted 08-specific styles
├── 09-forms/
│   ├── public/index.php          # no <style>, links assets/styles.css
│   ├── public/result.php         # links assets/styles.css
│   ├── public/assets/styles.css  # NEW: extracted 09-specific styles
│   └── src/helpers.php           # no inline style on render_alert()
├── 10-http-basics/
│   ├── public/index.php          # no embedded CSS string
│   └── public/assets/styles.css  # NEW: extracted 10-specific styles
├── 11-sessions/
│   ├── public/*.php              # link external CSS
│   ├── public/assets/styles.css  # NEW: extracted from bootstrap.php
│   ├── src/bootstrap.php         # render_page() uses external CSS
│   └── templates/                # NEW: layout.php, nav.php, flash.php
├── 12-regex-validation/
│   ├── public/index.php          # no <style>, links assets/styles.css
│   └── public/assets/styles.css  # NEW: extracted 12-specific styles
└── 13-auth-db-app/
    ├── public/*.php              # link external CSS
    ├── public/assets/styles.css  # NEW: extracted from bootstrap.php
    ├── src/bootstrap.php         # render_layout() uses templates/
    └── templates/                # NEW: layout.php, nav.php, flash.php
```

## Evidence Files to Collect
Per `.sisyphus/evidence/` convention:
- `css-extraction-phase-1.txt` — foundation verification
- `css-extraction-phase-2.txt` — 01-07 verification
- `css-extraction-phase-3.txt` — 08-12 verification
- `css-extraction-phase-4.txt` — 13 verification
- `css-extraction-phase-5.txt` — template separation verification
- `css-extraction-final.txt` — full smoke test run

## Commit Strategy
- One commit per phase (6 commits total)
- Commit messages: "refactor(css): extract shared styles for 01-07", "refactor(css): extract 08-12 assignment styles", etc.
- Each commit includes evidence file update
