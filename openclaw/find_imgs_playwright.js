const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
  });
  
  const results = {};

  // Try free stock image URLs directly
  const directUrls = {
    "physio-knee-1": "https://images.pexels.com/photos/6502356/pexels-photo-6502356.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1",
    "physio-shoulder-1": "https://images.pexels.com/photos/6502654/pexels-photo-6502654.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1",
    "physio-leg-1": "https://images.pexels.com/photos/6502596/pexels-photo-6502596.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1",
    "physio-back-1": "https://images.pexels.com/photos/6502527/pexels-photo-6502527.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1",
    "physio-elderly-1": "https://images.pexels.com/photos/6502652/pexels-photo-6502652.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1",
    "physio-stretch-1": "https://images.pexels.com/photos/6502290/pexels-photo-6502290.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1",
    "physio-running-1": "https://images.pexels.com/photos/6502571/pexels-photo-6502571.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1",
    "physio-posture-1": "https://images.pexels.com/photos/6502547/pexels-photo-6502547.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1",
  };

  // Download all
  const fs = require('fs');
  const https = require('https');
  
  function download(url, path) {
    return new Promise((resolve, reject) => {
      if (fs.existsSync(path)) { resolve(path); return; }
      const file = fs.createWriteStream(path);
      https.get(url, { headers: { 'User-Agent': 'Mozilla/5.0' } }, response => {
        if (response.statusCode === 200) {
          response.pipe(file);
          file.on('finish', () => { file.close(); resolve(path); });
        } else {
          file.close();
          fs.unlinkSync(path);
          reject(`HTTP ${response.statusCode}`);
        }
      }).on('error', err => { file.close(); if (fs.existsSync(path)) fs.unlinkSync(path); reject(err); });
    });
  }

  const basePath = '/Users/sheikhown/.openclaw/workspace/tonicphysio';
  // Only download if not already there
  for (const [name, url] of Object.entries(directUrls)) {
    const path = `${basePath}/${name}.jpg`;
    if (!fs.existsSync(path)) {
      try {
        await download(url, path);
        console.log(`Downloaded: ${name}`);
      } catch(e) {
        console.log(`Failed ${name}: ${e}`);
      }
    } else {
      console.log(`Exists: ${name}`);
    }
  }
  
  console.log("\nDone! Files in directory:");
  fs.readdirSync(basePath).filter(f => f.endsWith('.jpg') || f.endsWith('.png')).forEach(f => {
    const stat = fs.statSync(`${basePath}/${f}`);
    console.log(`  ${f} (${Math.round(stat.size/1024)}KB)`);
  });
  
  await browser.close();
})();
