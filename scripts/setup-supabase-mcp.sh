#!/bin/bash
# Supabase MCP Setup Script

echo "🔧 Supabase MCP Setup for OpenCode"
echo "=================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check if opencode CLI is installed
if ! command -v opencode &> /dev/null; then
    echo -e "${RED}❌ OpenCode CLI not found${NC}"
    echo ""
    echo "Please install OpenCode CLI first:"
    echo "  npm install -g opencode-ai"
    echo ""
    echo "Or visit: https://opencode.ai/docs/getting-started"
    exit 1
fi

echo -e "${GREEN}✓ OpenCode CLI found${NC}"
echo ""

# Create config directory
CONFIG_DIR="$HOME/.config/opencode"
mkdir -p "$CONFIG_DIR"

echo "📁 Configuration directory: $CONFIG_DIR"
echo ""

# Copy config file
echo "📝 Creating configuration file..."
cat > "$CONFIG_DIR/opencode.json" << 'EOF'
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
EOF

echo -e "${GREEN}✓ Configuration saved to $CONFIG_DIR/opencode.json${NC}"
echo ""

# Show the config
echo "📋 Configuration content:"
cat "$CONFIG_DIR/opencode.json"
echo ""

# Instructions for authentication
echo "=================================="
echo "🚀 Next step: Authenticate with Supabase"
echo ""
echo "Run the following command in your terminal:"
echo ""
echo -e "${YELLOW}  opencode mcp auth supabase${NC}"
echo ""
echo "This will open your browser to complete OAuth authentication."
echo ""

# Optional: Install Agent Skills
echo "=================================="
echo "📚 Optional: Install Agent Skills"
echo ""
echo "Agent Skills provide better Supabase integration:"
echo ""
echo -e "${YELLOW}  npx skills add supabase/agent-skills${NC}"
echo ""

# Update project reference note
echo "=================================="
echo "⚠️  Important: Update Project Reference"
echo ""
echo "The current config uses project_ref: dpbftxcafjobqwdwktxj"
echo ""
echo "To use your own Supabase project:"
echo "1. Go to https://supabase.com/dashboard"
echo "2. Select your project"
echo "3. Find your project reference in the URL or settings"
echo "4. Update the URL in ~/.config/opencode/opencode.json"
echo ""
echo "Example:"
echo "  \"url\": \"https://mcp.supabase.com/mcp?project_ref=YOUR_PROJECT_REF\""
echo ""
