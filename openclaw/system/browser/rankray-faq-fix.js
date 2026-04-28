const { execSync } = require('child_process');
const auth = Buffer.from('openclaw:6Zz95gJL8uyAQH4gRQDHGV1j').toString('base64');
const api = 'https://rankray.com/wp-json/wp/v2/posts/12055';
function curl(method, url, bodyPath) {
  const parts = ['curl','-L','-sS','-X',method,'-H',`"Authorization: Basic ${auth}"`,'-H','"Content-Type: application/json"'];
  if (bodyPath) parts.push('--data-binary', `@${bodyPath}`);
  parts.push(`"${url}"`);
  return execSync(parts.join(' '), {encoding:'utf8', maxBuffer: 20*1024*1024});
}
const post = JSON.parse(curl('GET', `${api}?context=edit`));
let content = post.content.raw;

content = content.replace(/<h2>FAQ<\/h2>[\s\S]*?<h2>When to Get an SEO Audit<\/h2>/, `
<h2>Frequently Asked Questions</h2>
<h3>Why does SEO take so long?</h3>
<p>SEO takes time because search engines need to crawl, index, and evaluate your pages against competing results. Stronger competitors, weak authority, technical issues, and poor search intent alignment can all slow progress.</p>
<h3>How long does SEO take to work?</h3>
<p>Many sites see early movement within a few weeks, but meaningful improvement often takes three to six months. Competitive terms may take longer depending on the authority gap and the quality of the page.</p>
<h3>Why is my page indexed but not ranking?</h3>
<p>If a page is indexed but not ranking well, the most common causes are weak search intent match, stronger competitors, thin content, weak on-page optimization, or low topical authority.</p>
<h3>Can bad backlinks slow SEO?</h3>
<p>Low-quality backlinks can become a concern in some cases, but most ranking issues come from broader content, technical, and authority gaps. If you review toxic links, remember that Google’s disavow tool asks Google to ignore certain links. It does not remove them from the web.</p>
<h3>Should I update an existing article or rewrite it?</h3>
<p>If the page already has some relevance, rankings, or links, updating it is often smarter than starting over. The update should improve accuracy, depth, structure, and intent match rather than just changing a few sentences.</p>
<!-- wp:html -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Why does SEO take so long?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "SEO takes time because search engines need to crawl, index, and evaluate your pages against competing results. Stronger competitors, weak authority, technical issues, and poor search intent alignment can all slow progress."
      }
    },
    {
      "@type": "Question",
      "name": "How long does SEO take to work?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Many sites see early movement within a few weeks, but meaningful improvement often takes three to six months. Competitive terms may take longer depending on the authority gap and the quality of the page."
      }
    },
    {
      "@type": "Question",
      "name": "Why is my page indexed but not ranking?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "If a page is indexed but not ranking well, the most common causes are weak search intent match, stronger competitors, thin content, weak on-page optimization, or low topical authority."
      }
    },
    {
      "@type": "Question",
      "name": "Can bad backlinks slow SEO?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Low-quality backlinks can become a concern in some cases, but most ranking issues come from broader content, technical, and authority gaps. If you review toxic links, remember that Google’s disavow tool asks Google to ignore certain links. It does not remove them from the web."
      }
    },
    {
      "@type": "Question",
      "name": "Should I update an existing article or rewrite it?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "If the page already has some relevance, rankings, or links, updating it is often smarter than starting over. The update should improve accuracy, depth, structure, and intent match rather than just changing a few sentences."
      }
    }
  ]
}
</script>
<!-- /wp:html -->
<h2>When to Get an SEO Audit</h2>`);

const payloadPath='/Users/sheikhown/.openclaw/workspace/reports/browser-artifacts/rankray-faq-fix-payload.json';
require('fs').writeFileSync(payloadPath, JSON.stringify({content}, null, 2));
const updated = JSON.parse(curl('POST', api, payloadPath));
console.log(JSON.stringify({ok:true, status: updated.status, title: updated.title.rendered}, null, 2));
