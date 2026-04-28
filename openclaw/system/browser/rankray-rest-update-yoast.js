const { execSync } = require('child_process');
const fs = require('fs');
const auth = Buffer.from('openclaw:6Zz95gJL8uyAQH4gRQDHGV1j').toString('base64');
const artifact = '/Users/sheikhown/.openclaw/workspace/reports/browser-artifacts';
fs.mkdirSync(artifact, { recursive: true });
function curl(url){
  return execSync(`curl -L -sS -H "Authorization: Basic ${auth}" "${url}"`, {encoding:'utf8', maxBuffer: 20*1024*1024});
}
const post = JSON.parse(curl('https://rankray.com/wp-json/wp/v2/posts/12055?context=edit'));
fs.writeFileSync(`${artifact}/rankray-post-edit-context.json`, JSON.stringify(post, null, 2));
console.log(JSON.stringify({
  keys: Object.keys(post).filter(k=>k.toLowerCase().includes('yoast') || k==='meta' || k==='yoast_head_json' || k==='yoast_head'),
  metaSample: post.meta,
  yoastTitle: post.yoast_head_json?.title || null,
  yoastDesc: post.yoast_head_json?.description || null
}, null, 2));
