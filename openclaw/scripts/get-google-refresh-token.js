#!/usr/bin/env node
/**
 * Google OAuth Refresh Token Generator
 * Run this, authorize in browser, get the refresh token.
 * Scopes: Gmail, Sheets, Drive, Analytics, Search Console
 */
const http = require('http');
const url = require('url');
const crypto = require('crypto');
const querystring = require('querystring');

// Read from env file manually
const fs = require('fs');
const path = require('path');
const os = require('os');

function loadEnv(filePath) {
  const content = fs.readFileSync(filePath, 'utf8');
  const lines = content.split('\n');
  for (const line of lines) {
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith('#')) continue;
    const idx = trimmed.indexOf('=');
    if (idx > 0) {
      const key = trimmed.slice(0, idx).trim();
      const val = trimmed.slice(idx + 1).trim();
      process.env[key] = val;
    }
  }
}

loadEnv(path.join(os.homedir(), '.openclaw', '.env'));

const CLIENT_ID = process.env.GOOGLE_CLIENT_ID;
const CLIENT_SECRET = process.env.GOOGLE_CLIENT_SECRET;
const REDIRECT_URI = 'http://localhost:3000/oauth2callback';

if (!CLIENT_ID || !CLIENT_SECRET) {
  console.error('Missing GOOGLE_CLIENT_ID or GOOGLE_CLIENT_SECRET in ~/.openclaw/.env');
  process.exit(1);
}

// Scopes for full agentic access
const SCOPES = [
  'https://www.googleapis.com/auth/gmail.modify',
  'https://www.googleapis.com/auth/gmail.send',
  'https://www.googleapis.com/auth/spreadsheets',
  'https://www.googleapis.com/auth/drive',
  'https://www.googleapis.com/auth/analytics.readonly',
  'https://www.googleapis.com/auth/webmasters.readonly',
  'https://www.googleapis.com/auth/userinfo.email',
  'https://www.googleapis.com/auth/userinfo.profile',
  'openid'
].join(' ');

const STATE = crypto.randomBytes(16).toString('hex');

// Step 1: Generate auth URL
const authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' + querystring.stringify({
  client_id: CLIENT_ID,
  redirect_uri: REDIRECT_URI,
  response_type: 'code',
  scope: SCOPES,
  access_type: 'offline',
  prompt: 'consent',
  state: STATE
});

console.log('\n========================================');
console.log('Google OAuth Refresh Token Generator');
console.log('========================================\n');
console.log('1. Open this URL in your browser:');
console.log('\n' + authUrl + '\n');
console.log('2. Sign in with oliverjakeseo@gmail.com');
console.log('3. Authorize the app\n');
console.log('Waiting for callback on http://localhost:3000...\n');

// Step 2: Start local server to catch callback
const server = http.createServer(async (req, res) => {
  const parsedUrl = url.parse(req.url, true);
  const query = parsedUrl.query;

  if (parsedUrl.pathname === '/oauth2callback') {
    if (query.error) {
      res.writeHead(400);
      res.end('Error: ' + query.error);
      console.error('OAuth error:', query.error);
      server.close();
      process.exit(1);
    }

    const code = query.code;
    if (!code) {
      res.writeHead(400);
      res.end('No code received');
      server.close();
      process.exit(1);
    }

    // Exchange code for tokens
    try {
      const tokenResponse = await fetch('https://oauth2.googleapis.com/token', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: querystring.stringify({
          code: code,
          client_id: CLIENT_ID,
          client_secret: CLIENT_SECRET,
          redirect_uri: REDIRECT_URI,
          grant_type: 'authorization_code'
        })
      });

      const tokenData = await tokenResponse.json();

      if (tokenData.error) {
        throw new Error(tokenData.error + ': ' + tokenData.error_description);
      }

      res.writeHead(200, { 'Content-Type': 'text/html' });
      res.end(`
        <html><body style="font-family:sans-serif;text-align:center;padding:50px;">
          <h1>✅ Authorization Complete!</h1>
          <p>You can close this tab and return to the terminal.</p>
        </body></html>
      `);

      console.log('\n========================================');
      console.log('TOKENS RECEIVED!');
      console.log('========================================\n');
      console.log('Access Token:');
      console.log(tokenData.access_token);
      console.log('\nRefresh Token (SAVE THIS):');
      console.log('\n***' + tokenData.refresh_token + '***\n');
      console.log('Expires in:', tokenData.expires_in, 'seconds');
      console.log('Token type:', tokenData.token_type);
      console.log('Scope:', tokenData.scope);
      console.log('\n========================================');
      console.log('NEXT STEPS:');
      console.log('========================================');
      console.log('Add this line to ~/.openclaw/.env:');
      console.log('\nGOOGLE_REFRESH_TOKEN=' + tokenData.refresh_token + '\n');
      console.log('========================================\n');

      server.close();
      process.exit(0);

    } catch (err) {
      res.writeHead(500);
      res.end('Error exchanging code');
      console.error('Token exchange error:', err.message);
      server.close();
      process.exit(1);
    }
  } else {
    res.writeHead(404);
    res.end('Not found');
  }
});

server.listen(3000, () => {
  console.log('Local server running on http://localhost:3000');
});

// Timeout after 5 minutes
setTimeout(() => {
  console.error('\nTimeout: No callback received in 5 minutes.');
  server.close();
  process.exit(1);
}, 5 * 60 * 1000);
