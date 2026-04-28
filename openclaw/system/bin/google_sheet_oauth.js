const { google } = require('googleapis');
const fs = require('fs');

async function main() {
  const [command, ...args] = process.argv.slice(2);
  
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
  const drive = google.drive({ version: 'v3', auth: oauth2Client });

  try {
    if (command === 'create') {
      const title = args[0] || 'New Sheet';
      const res = await sheets.spreadsheets.create({
        requestBody: { properties: { title } },
      });
      console.log(JSON.stringify({ spreadsheetId: res.data.spreadsheetId, url: res.data.spreadsheetUrl }));
    } else if (command === 'rename') {
      const [id, newTitle] = args;
      await drive.files.update({
        fileId: id,
        requestBody: { name: newTitle },
      });
      console.log(`Renamed to ${newTitle}`);
    } else if (command === 'append') {
      const [id, range, dataJson] = args;
      const values = JSON.parse(dataJson);
      await sheets.spreadsheets.values.append({
        spreadsheetId: id,
        range: range,
        valueInputOption: 'RAW',
        requestBody: { values: values },
      });
      console.log(`Appended ${values.length} rows`);
    } else if (command === 'write') {
      const [id, range, dataJson] = args;
      const values = JSON.parse(dataJson);
      await sheets.spreadsheets.values.update({
        spreadsheetId: id,
        range: range,
        valueInputOption: 'RAW',
        requestBody: { values: values },
      });
      console.log(`Wrote data to ${range}`);
    } else {
      console.log('Unknown command');
      process.exit(1);
    }
  } catch (e) {
    console.error(e);
    process.exit(1);
  }
}
main();
