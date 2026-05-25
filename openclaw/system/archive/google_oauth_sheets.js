const { google } = require('googleapis');
const fs = require('fs');

async function main() {
  const tokens = fs.readFileSync('/Users/sheikhown/.openclaw/.google_oauth_tokens', 'utf8')
    .trim()
    .split('\n')
    .reduce((acc, line) => {
      const [key, val] = line.split('=');
      if (key) acc[key] = val;
      return acc;
    }, {});

  const oauth2Client = new google.auth.OAuth2(
    '803355012183-bfgbc7g540isfs1pkno6f3fknb135cqb.apps.googleusercontent.com',
    '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'
  );

  oauth2Client.setCredentials({
    refresh_token: tokens.GOOGLE_OAUTH_REFRESH_TOKEN
  });

  const sheets = google.sheets({ version: 'v4', auth: oauth2Client });

  try {
    const res = await sheets.spreadsheets.create({
      requestBody: {
        properties: { title: 'Khan LLP Citation Findings' },
      },
    });
    console.log(JSON.stringify({
      spreadsheetId: res.data.spreadsheetId,
      spreadsheetUrl: res.data.spreadsheetUrl
    }));
  } catch (e) {
    console.error(e);
    process.exit(1);
  }
}
main();
