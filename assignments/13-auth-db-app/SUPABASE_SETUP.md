# Supabase Setup Guide for Assignment 13

## Quick Start

1. **Create Supabase Project**
   - Go to https://supabase.com
   - Sign up or log in
   - Create new project
   - Choose a name and region closest to you
   - Wait for database provisioning

2. **Get Runtime API Credentials**
   - Go to Project Settings → API
   - Copy:
     - `Project URL`
     - `service_role` key or a secret key with `service_role` claims

3. **Link the repo to the Supabase project**
   ```bash
   npx supabase link --project-ref YOUR_PROJECT_REF
   ```

4. **Configure Local Environment**
   Create `.env.supabase.local`:
   ```bash
   SUPABASE_URL=https://xxxxxxxxxxxxxxxxxxxx.supabase.co
   SUPABASE_SERVICE_ROLE_KEY=your-service-role-or-secret-key
   
   # Auth secret for signed cookies
   ASSIGNMENT13_AUTH_SECRET=your-random-secret-min-32-chars
   ```

5. **Initialize Database Schema**
   ```bash
   bash scripts/reset-auth-db.sh
   bash scripts/run-db-smoke.sh
   ```
   The reset/smoke scripts use the linked Supabase project through Supabase CLI / Management API, so a raw Postgres password is not required for routine verification.

## Vercel Deployment

1. **Add Environment Variables to Vercel**
   ```bash
   vercel env add SUPABASE_URL preview
   # Enter: https://xxxxxxxxxxxxxxxxxxxx.supabase.co

   vercel env add SUPABASE_SERVICE_ROLE_KEY preview
   # Enter: your server-side key
   
   vercel env add ASSIGNMENT13_AUTH_SECRET preview
   # Enter: long-random-string
   ```

2. **Deploy**
   ```bash
   vercel deploy
   ```

## Features

- Server-side Supabase PostgREST access from PHP
- Linked-project reset/smoke flow through Supabase CLI / Management API
- Session management with persistent `user_sessions`
- Email uniqueness enforcement
- Secure password hashing with bcrypt

## Database Schema

### users table
```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);
```

### user_sessions table
```sql
CREATE TABLE user_sessions (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    token_hash VARCHAR(64) NOT NULL UNIQUE,
    expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
    last_seen_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);
```

## Troubleshooting

**Supabase unavailable**
- Check that your Supabase project is active
- Verify `SUPABASE_URL` and `SUPABASE_SERVICE_ROLE_KEY`
- Check Vercel runtime logs for upstream 401/404/5xx responses

**Reset script fails**
- Ensure the repo is linked: `npx supabase link --project-ref YOUR_PROJECT_REF`
- Run `npx supabase projects list` and confirm the linked project is correct
- Retry `bash scripts/reset-auth-db.sh`

**Session not persisting**
- Verify `ASSIGNMENT13_AUTH_SECRET` is set
- Check cookie settings in browser
- Ensure `user_sessions` table exists
