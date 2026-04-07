# PHP Practical Assignments Workspace

This repository keeps each practical assignment in its own isolated folder under `assignments/`.

## Folder map
- `assignments/` contains the 13 numbered assignment workspaces.
- `scripts/` is the only place for shared helper scripts and verification helpers.
- `database/` is reserved for database bootstrap and reset assets used by the database assignment.
- `.sisyphus/evidence/` stores task evidence collected during execution.
- Root `.docx` files remain the source briefs and should not be edited.

## Local entrypoints
- CLI assignments should expose deterministic output and a `tests/run.php` runner inside the target assignment folder.
- Browser assignments should expose a `public/` entrypoint and run with PHP's built-in server from that assignment folder.
- The database assignment should keep its own reset and bootstrap assets and must not reuse state from other assignments.

## Working rules
- Keep all files in UTF-8 without BOM.
- Preserve visible Russian learner-facing text from the briefs.
- Don't merge assignments into one app.
- Don't share assignment business logic across folders.

## Numbered assignments
- `assignments/01-php-basics`
- `assignments/02-control-structures`
- `assignments/03-arrays`
- `assignments/04-associative-arrays`
- `assignments/05-multidimensional-arrays`
- `assignments/06-user-functions`
- `assignments/07-standard-functions`
- `assignments/08-string-generation`
- `assignments/09-forms`
- `assignments/10-http-basics`
- `assignments/11-sessions`
- `assignments/12-regex-validation`
- `assignments/13-auth-db-app`

## Vercel Deployment

This project is configured for deployment on Vercel using the `vercel-php` runtime.

**Two database options available:**
- MySQL (traditional)
- Supabase PostgreSQL (cloud-native, recommended)

### Prerequisites

- Node.js and npm installed
- Vercel CLI: `npm i -g vercel`
- Database for assignment 13 (MySQL local/cloud OR Supabase PostgreSQL)

## Supabase + Vercel Deployment (Recommended)

Assignment 13 now uses Supabase as the remote database backend:
- application runtime talks to Supabase PostgREST with a server-side key
- schema reset/smoke scripts run against the linked Supabase project via Supabase CLI / Management API

### Quick Deploy

1. **Create or link a Supabase Project**
   - Sign up at https://supabase.com
   - Create a project
   - Link this repo once: `npx supabase link --project-ref YOUR_PROJECT_REF`

2. **Configure & Deploy**
   ```bash
   cp .env.supabase.local.example .env.supabase.local
   # Fill SUPABASE_URL / SUPABASE_SERVICE_ROLE_KEY / ASSIGNMENT13_AUTH_SECRET
   bash scripts/reset-auth-db.sh
   bash scripts/run-db-smoke.sh
   ```

3. **Preview deploy to Vercel**
   ```bash
   vercel env add SUPABASE_URL preview
   vercel env add SUPABASE_SERVICE_ROLE_KEY preview
   vercel env add ASSIGNMENT13_AUTH_SECRET preview
   vercel deploy -y
   ```

4. **Manual Local Setup (if needed)**
   
   Create `.env.supabase.local`:
   ```bash
   SUPABASE_URL=https://xxxxxxxxxxxxxxxxxxxx.supabase.co
   SUPABASE_SERVICE_ROLE_KEY=your-service-role-or-secret-key
   ASSIGNMENT13_AUTH_SECRET=$(openssl rand -base64 32)
   ```

See `assignments/13-auth-db-app/SUPABASE_SETUP.md` for detailed instructions about the linked-project flow.

## MySQL Deployment (Alternative)

Use this if you prefer traditional MySQL database.

### Environment Setup

1. Copy the example environment file:
   ```bash
   cp .env.vercel.local.example .env.vercel.local
   ```

2. Edit `.env.vercel.local` with your values:
   ```bash
   # MySQL database for assignment 13
   AUTH_DB_HOST=your-db-host
   AUTH_DB_PORT=3306
   AUTH_DB_USER=your-db-user
   AUTH_DB_PASSWORD=your-db-password
   AUTH_DB_NAME=your-db-name
   
   # Secrets for signed cookies (generate long random strings)
   ASSIGNMENT11_STATE_SECRET=your-random-secret-here
   ASSIGNMENT13_AUTH_SECRET=your-random-secret-here
   ```

3. For production, add runtime environment variables:
   ```bash
   # Add database connection variables (use your cloud MySQL provider details)
   vercel env add AUTH_DB_HOST production
   # Enter your database host (e.g., your-db-host.provider.com)
   
   vercel env add AUTH_DB_PORT production  
   # Enter: 3306
   
   vercel env add AUTH_DB_USER production
   # Enter your database username
   
   vercel env add AUTH_DB_PASSWORD production
   # Enter your database password (will be encrypted)
   
   vercel env add AUTH_DB_NAME production
   # Enter your database name
   
   vercel env add ASSIGNMENT11_STATE_SECRET production
   # Enter a long random string for session signing
   
   vercel env add ASSIGNMENT13_AUTH_SECRET production
   # Enter a long random string for auth token signing
   ```
   
   Note: These are runtime environment variables, not Vercel secrets. PHP reads them via `getenv()`.

### Local Development

1. Start the Vercel dev server:
   ```bash
   vercel dev --listen 127.0.0.1:4010
   ```

2. In another terminal, run smoke tests:
   ```bash
   bash scripts/run-vercel-smoke.sh http://127.0.0.1:4010
   ```

3. For full testing with database:
   ```bash
   set -a && source .env.vercel.local && set +a
   bash scripts/run-vercel-smoke.sh http://127.0.0.1:4010 --with-db
   ```

### Production Deployment

1. Deploy to production:
   ```bash
   vercel deploy --prod --yes | tee .sisyphus/evidence/vercel-deploy.txt
   ```

2. Extract the deployment URL:
   ```bash
   DEPLOY_URL=$(python3 scripts/extract_vercel_url.py .sisyphus/evidence/vercel-deploy.txt)
   echo "Deployed to: $DEPLOY_URL"
   ```

3. Run smoke tests against production:
   ```bash
   bash scripts/run-vercel-smoke.sh "$DEPLOY_URL"
   ```

### Verification Scripts

| Script | Purpose |
|--------|---------|
| `bash scripts/php-lint-all.sh` | Lint all PHP files |
| `bash scripts/run-cli-assignments.sh` | Test CLI assignments 01-07 |
| `bash scripts/run-web-smoke.sh` | Test local web assignments 08-13 |
| `bash scripts/run-vercel-smoke.sh <url>` | Test Vercel deployment |
| `bash scripts/reset-auth-db.sh` | Reset database schema |
| `bash scripts/run-db-smoke.sh` | Verify database schema |

### Project Structure for Vercel

```
.
├── api/
│   ├── index.php          # Vercel entry point
│   └── assignments.php    # Route dispatcher
├── vercel.json            # Vercel configuration
├── public/assets/         # Shared CSS and images
├── assignments/           # Assignment code
└── .env.vercel.local      # Local environment (not committed)
```

### Route Mapping

Assignments are served under short canonical routes:
- `/` - Launchpad with all 13 assignments
- `/01-php-basics` through `/07-standard-functions` - CLI wrappers
- `/08-string-generation` through `/13-auth-db-app` - Web assignments

Stateful assignments use signed cookies instead of native PHP sessions for Vercel compatibility.
