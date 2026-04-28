const fs = require('fs');
const path = require('path');
const config = require('./rankray.config.json');

function ensureDirs() {
  fs.mkdirSync(config.userDataDir, { recursive: true });
  fs.mkdirSync(config.artifactsDir, { recursive: true });
}

function loadDraft() {
  const raw = fs.readFileSync(config.draftCopyPath, 'utf8');
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
  return { title, meta, body, html: htmlParts.join('\n') };
}

module.exports = { config, ensureDirs, loadDraft };
