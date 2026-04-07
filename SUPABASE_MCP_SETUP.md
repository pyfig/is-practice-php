# Supabase MCP Setup Guide

This guide helps you configure Supabase MCP (Model Context Protocol) for OpenCode.

## Quick Setup

### 1. Run the Setup Script

```bash
bash scripts/setup-supabase-mcp.sh
```

This will:
- Check for OpenCode CLI
- Create `~/.config/opencode/opencode.json`
- Show next steps

### 2. Manual Configuration (if script doesn't work)

Create the config file manually:

```bash
mkdir -p ~/.config/opencode
```

Create `~/.config/opencode/opencode.json`:

```json
{
  "$schema": "https://opencode.ai/config.json",
  "mcp": {
    "supabase": {
      "type": "remote",
      "url": "https://mcp.supabase.com/mcp?project_ref=dpbftxcafjobqwdwktxj",
      "enabled": true
    }
  }
}
```

### 3. Authenticate

```bash
opencode mcp auth supabase
```

This opens your browser for OAuth authentication.

### 4. Install Agent Skills (Optional but Recommended)

```bash
npx skills add supabase/agent-skills
```

## Using Your Own Supabase Project

The default config uses a placeholder project. To use your own:

1. Go to https://supabase.com/dashboard
2. Create or select your project
3. Copy the project reference from the URL
4. Update `~/.config/opencode/opencode.json`:

```json
{
  "mcp": {
    "supabase": {
      "type": "remote",
      "url": "https://mcp.supabase.com/mcp?project_ref=YOUR_PROJECT_REF",
      "enabled": true
    }
  }
}
```

## Verification

After setup, test the MCP connection:

```bash
opencode mcp list
```

You should see `supabase` in the list.

## Troubleshooting

### "opencode: command not found"

Install OpenCode CLI:
```bash
npm install -g opencode-ai
```

### Authentication fails

1. Make sure you're logged into Supabase in your browser
2. Try running `opencode mcp auth supabase` again
3. Check that your project_ref is correct

### MCP not showing in list

1. Verify the config file exists: `cat ~/.config/opencode/opencode.json`
2. Check for JSON syntax errors
3. Restart your terminal/editor

## Next Steps

Once MCP is configured, you can:

1. **Manage database from OpenCode**:
   - Create tables
   - Run queries
   - Manage auth users

2. **Deploy with MCP**:
   - Automatically configure environment variables
   - Run database migrations
   - Verify deployment

3. **Use in Assignment 13**:
   The assignment is already configured for Supabase PostgreSQL.
   MCP will help manage the database schema and users.
