const https = require('https');

const wpUrl = 'rankray.com';
const username = 'openclaw';
const password = 'OC#admin@2026';
const pageId = '18073';

const auth = Buffer.from(`${username}:${password}`).toString('base64');

const content = {
  content: "AI automation services by Rank Ray streamline workflows, reduce costs, and boost efficiency. Get intelligent automation solutions for sustainable growth.",
  meta: {
    rank_math_description: "AI automation services by Rank Ray streamline workflows, reduce costs, and boost efficiency. Get intelligent automation solutions for sustainable growth."
  }
};

const postData = JSON.stringify(content);

const options = {
  hostname: wpUrl,
  path: `/wp-json/wp/v2/pages/${pageId}`,
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Basic ${auth}`,
    'Content-Length': Buffer.byteLength(postData)
  }
};

console.log('Attempting WordPress API update...');

const req = https.request(options, (res) => {
  let data = '';
  res.on('data', (chunk) => data += chunk);
  res.on('end', () => {
    console.log('Status:', res.statusCode);
    console.log('Response:', data.substring(0, 500));
  });
});

req.on('error', (e) => {
  console.error('Error:', e.message);
});

req.write(postData);
req.end();
