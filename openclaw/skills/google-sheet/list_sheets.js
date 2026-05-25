const { google } = require('googleapis');
const fs = require('fs');

const token = JSON.parse(fs.readFileSync(process.env.HOME + '/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/token.json', 'utf8'));

const oauth2Client = new google.auth.OAuth2(
  '372137143870-3b1248bi046u82sj4a2heqh85d7a39ag.apps.googleusercontent.com',
  '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'
);

oauth2Client.setCredentials({ refresh_token: token.refresh_token });

async function listSheets() {
  const drive = google.drive({ version: 'v3', auth: oauth2Client });
  const res = await drive.files.list({
    q: "mimeType='application/vnd.google-apps.spreadsheet'",
    fields: 'files(id, name, createdTime, modifiedTime, webViewLink)',
    orderBy: 'modifiedTime desc',
    pageSize: 20
  });
  console.log(JSON.stringify(res.data.files, null, 2));
}

listSheets().catch(e => {
  console.error('Error:', e.message);
  if (e.response) console.error(JSON.stringify(e.response.data, null, 2));
});
