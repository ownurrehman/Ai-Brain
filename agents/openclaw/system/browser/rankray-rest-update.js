const fs = require('fs');
const { execSync } = require('child_process');

const username = 'openclaw';
const appPassword = '6Zz95gJL8uyAQH4gRQDHGV1j';
const postId = 12055;
const apiBase = 'https://rankray.com/wp-json/wp/v2/posts';
const draftPath = '/Users/sheikhown/.openclaw/workspace/reports/rankray-latest-blog-draft-copy-2026-03-27.md';
const artifact = '/Users/sheikhown/.openclaw/workspace/reports/browser-artifacts';
fs.mkdirSync(artifact, { recursive: true });

const raw = fs.readFileSync(draftPath, 'utf8');
const title = (raw.match(/## Proposed SEO title\n([\s\S]*?)\n\n/) || [])[1]?.trim() || '';
const meta = (raw.match(/## Proposed meta description\n([\s\S]*?)\n\n/) || [])[1]?.trim() || '';
const body = raw.split('## Updated article body\n')[1].trim();
const paragraphs = body.split(/\n\n+/).map(s => s.trim()).filter(Boolean);
const htmlParts = [];
for (const p of paragraphs) {
  if (p.startsWith('## ')) htmlParts.push(`<h2>${p.slice(3).trim()}</h2>`);
  else if (p.startsWith('### ')) htmlParts.push(`<h3>${p.slice(4).trim()}</h3>`);
  else if (/^- /m.test(p)) {
    const items = p.split('\n').filter(l => l.startsWith('- ')).map(l => `<li>${l.slice(2).trim()}</li>`).join('');
    htmlParts.push(`<ul>${items}</ul>`);
  } else if (/^\d+\. /m.test(p)) {
    const items = p.split('\n').filter(l => /^\d+\. /.test(l)).map(l => `<li>${l.replace(/^\d+\.\s*/, '').trim()}</li>`).join('');
    htmlParts.push(`<ol>${items}</ol>`);
  } else {
    htmlParts.push(`<p>${p.replace(/\n/g, '<br>')}</p>`);
  }
}
const content = htmlParts.join('\n');

const payload = { title, content, status: 'draft' };
fs.writeFileSync(`${artifact}/rankray-rest-payload.json`, JSON.stringify(payload, null, 2));

function curl(method, url, dataPath) {
  const auth = Buffer.from(`${username}:${appPassword}`).toString('base64');
  const parts = [
    'curl', '-L', '-sS', '-X', method,
    '-H', `"Authorization: Basic ${auth}"`,
    '-H', '"Content-Type: application/json"'
  ];
  if (dataPath) parts.push('--data-binary', `@${dataPath}`);
  parts.push(`"${url}"`);
  return execSync(parts.join(' '), { encoding: 'utf8', maxBuffer: 20 * 1024 * 1024 });
}

const updateRaw = curl('POST', `${apiBase}/${postId}`, `${artifact}/rankray-rest-payload.json`);
fs.writeFileSync(`${artifact}/rankray-rest-update-response.json`, updateRaw);

let metaResult = { attempted: true, ok: false };
try {
  const metaPayloadPath = `${artifact}/rankray-rest-meta-payload.json`;
  fs.writeFileSync(metaPayloadPath, JSON.stringify({ meta: { _yoast_wpseo_metadesc: meta } }, null, 2));
  const metaRaw = curl('POST', `${apiBase}/${postId}`, metaPayloadPath);
  fs.writeFileSync(`${artifact}/rankray-rest-meta-response.json`, metaRaw);
  metaResult.ok = true;
} catch (e) {
  fs.writeFileSync(`${artifact}/rankray-rest-meta-error.txt`, String(e));
}

const verifyRaw = curl('GET', `${apiBase}/${postId}?context=edit`, null);
fs.writeFileSync(`${artifact}/rankray-rest-verify-response.json`, verifyRaw);
const verify = JSON.parse(verifyRaw);

console.log(JSON.stringify({
  ok: true,
  postId,
  status: verify.status,
  link: verify.link,
  title: verify.title?.rendered || '',
  metaAttempted: metaResult.attempted,
  metaApiWriteLikelyOk: metaResult.ok
}, null, 2));
