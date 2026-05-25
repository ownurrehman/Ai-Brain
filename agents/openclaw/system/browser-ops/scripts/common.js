const fs = require('fs');
const path = require('path');
const { chromium } = require('playwright');
const sites = require('../config/sites.json');

function getSite(name) {
  const site = sites[name];
  if (!site) throw new Error(`Unknown site: ${name}`);
  fs.mkdirSync(site.profileDir, { recursive: true });
  fs.mkdirSync('/Users/sheikhown/.openclaw/workspace/browser-ops/artifacts', { recursive: true });
  return site;
}

async function openPersistent(siteName, opts = {}) {
  const site = getSite(siteName);
  const context = await chromium.launchPersistentContext(site.profileDir, {
    headless: opts.headless ?? false,
    viewport: { width: 1440, height: 1000 },
    slowMo: opts.slowMo ?? 0
  });
  const page = context.pages()[0] || await context.newPage();
  return { site, context, page };
}

module.exports = { getSite, openPersistent };
