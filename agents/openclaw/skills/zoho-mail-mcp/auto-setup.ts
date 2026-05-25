/**
 * Non-interactive setup for Zoho Mail MCP
 * Uses existing credentials from .env
 */

const ZOHO_EU_OAUTH = "https://accounts.zoho.eu/oauth/v2";

async function main() {
  const clientId = process.env.ZOHO_CLIENT_ID || "1000.EM2WZ7OV9O5OO4EW1RRJUSGQHZSKNR";
  const clientSecret = process.env.ZOHO_CLIENT_SECRET || "0dd0a9ade704d3c526fe07d8672629c8186c293e23";
  
  console.log("=== Zoho Mail MCP - Auto Setup ===\n");
  console.log(`Client ID: ${clientId}`);
  console.log(`Client Secret: ${clientSecret}\n`);
  
  console.log("Step 1: Generate authorization URL...\n");
  
  const authUrl = `${ZOHO_EU_OAUTH}/auth?scope=ZohoMail.accounts.READ,ZohoMail.folders.READ,ZohoMail.messages.READ,ZohoMail.messages.CREATE,ZohoMail.messages.DELETE&client_id=${clientId}&access_type=offline&response_type=code&redirect_uri=https://rankray.com`;
  
  console.log("OPEN THIS URL IN YOUR BROWSER:");
  console.log(authUrl);
  console.log("\n");
  console.log("After you authorize, you'll get a CODE in the redirect URL.");
  console.log("Copy that code and run this command:\n");
  console.log(`curl -X POST "${ZOHO_EU_OAUTH}/token" -H "Content-Type: application/x-www-form-urlencoded" -d "grant_type=authorization_code&client_id=${clientId}&client_secret=${clientSecret}&code=YOUR_CODE_HERE"`);
  console.log("\nThe response will contain your refresh_token");
  console.log("Add it to .env as: ZOHO_OAUTH_REFRESH_TOKEN=your_token_here\n");
}

main().catch(console.error);
