const { execSync } = require('child_process');
const fs = require('fs');
const auth = Buffer.from('openclaw:6Zz95gJL8uyAQH4gRQDHGV1j').toString('base64');
const api = 'https://rankray.com/wp-json/wp/v2';
const postId = 12055;
function curl(method, url, body) {
  const parts = ['curl','-L','-sS','-X',method,'-H',`"Authorization: Basic ${auth}"`,'-H','"Content-Type: application/json"'];
  if (body) parts.push('--data-binary', `@${body}`);
  parts.push(`"${url}"`);
  return execSync(parts.join(' '), {encoding:'utf8', maxBuffer: 20*1024*1024});
}
const post = JSON.parse(curl('GET', `${api}/posts/${postId}?context=edit`));
let content = post.content.raw;
content = content.replace('<h2>How Long Does SEO Usually Take?</h2>', `<h2>How Long Does SEO Usually Take?</h2>\n<figure class="wp-block-image size-large"><img src="https://rankray.com/wp-content/uploads/2026/03/analytics.jpg" alt="SEO analytics dashboard showing ranking and traffic trends"/><figcaption>Copyright-free visual to support timeline and performance analysis.</figcaption></figure>`);
content = content.replace('<h2>3. Your Site Lacks Topical Authority and Strong Backlinks</h2>', `<h2>3. Your Site Lacks Topical Authority and Strong Backlinks</h2>\n<figure class="wp-block-image size-large"><img src="https://rankray.com/wp-content/uploads/2026/03/content-strategy.jpg" alt="Content strategy planning session for topical authority and link building"/><figcaption>Relevant visual for topical authority, content planning, and sustainable link growth.</figcaption></figure>`);
content = content.replace('<h2>6. Technical Performance and Mobile UX Are Holding You Back</h2>', `<h2>6. Technical Performance and Mobile UX Are Holding You Back</h2>\n<figure class="wp-block-image size-large"><img src="https://rankray.com/wp-content/uploads/2026/03/seo-dashboard.jpg" alt="SEO dashboard and performance metrics on screen"/><figcaption>Readable visual support for technical SEO, speed, and UX discussion.</figcaption></figure>`);

const excerpt = 'Why SEO takes so long usually comes down to indexing, intent, authority, and technical SEO issues. Here are 8 practical reasons rankings stall.';
const payloadPath = '/Users/sheikhown/.openclaw/workspace/reports/browser-artifacts/rankray-rest-enhance-payload.json';
fs.writeFileSync(payloadPath, JSON.stringify({ content, excerpt }, null, 2));
const updated = JSON.parse(curl('POST', `${api}/posts/${postId}`, payloadPath));
fs.writeFileSync('/Users/sheikhown/.openclaw/workspace/reports/browser-artifacts/rankray-rest-enhance-response.json', JSON.stringify(updated, null, 2));
console.log(JSON.stringify({ ok:true, status: updated.status, title: updated.title.rendered, excerpt: updated.excerpt?.rendered || '' }, null, 2));
