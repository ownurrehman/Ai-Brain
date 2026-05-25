const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

async function run() {
  const credentials = {
    email: 'oliverjakeseo@gmail.com',
    password: 'OJ<<##1156128279430959165>@2025'
  };
  const sheetUrl = 'https://docs.google.com/spreadsheets/d/1EnYut8v6-FO4PtPD7QibhJrvOcvbvXxgXVjpeaGKmmQ/edit';
  const csvPath = '/Users/sheikhown/.openclaw/workspace/reports/khanllp-citation-tracker-2026-04-21.csv';
  const userDataDir = '/Users/sheikhown/.openclaw/workspace/.browser-profiles/google-sheets-update';

  console.log('Loading CSV data...');
  const csvContent = fs.readFileSync(csvPath, 'utf8');
  const lines = csvContent.split('\n').filter(line => line.trim());
  const headers = lines[0].split(',');
  const urlIdx = headers.indexOf('URL');
  const typeIdx = headers.indexOf('Listing Type (Paid/Free)');
  
  const mapping = {};
  for (let i = 1; i < lines.length; i++) {
    const cols = lines[i].split(',');
    if (cols[urlIdx]) {
      mapping[cols[urlIdx].trim()] = cols[typeIdx] ? cols[typeIdx].trim() : 'Unknown';
    }
  }
  console.log(`Loaded ${Object.keys(mapping).length} entries from CSV.`);

  const browser = await chromium.launchPersistentContext(userDataDir, {
    headless: false,
    viewport: { width: 1280, height: 800 }
  });

  const page = browser.pages()[0] || await browser.newPage();

  try {
    console.log('Navigating to Google Login...');
    await page.goto('https://accounts.google.com/');
    
    await page.fill('input[type="email"]', credentials.email);
    await page.click('#identifierNext');
    await page.waitForTimeout(2000);
    
    await page.fill('input[type="password"]', credentials.password);
    await page.click('#identifierNext');
    await page.waitForTimeout(5000);

    console.log('Navigating to Spreadsheet...');
    await page.goto(sheetUrl);
    await page.waitForTimeout(5000);

    // Google Sheets uses a canvas or complex grid. 
    // Interacting with it via standard DOM is hard.
    // However, we can try to find the cells.
    // The 'Listing Type' is Column J. Column B is URL.
    
    // We will scroll through the page and look for the URLs.
    // Since we are in a browser, we can execute JS in the page.
    
    console.log('Updating cells...');
    
    // This is a simplified approach: 
    // 1. Get all values in Column B.
    // 2. Match with our map.
    // 3. Use the keyboard to navigate and update Column J.
    
    // Note: Actual Google Sheets automation via DOM is extremely difficult 
    // because it's a virtualized grid.
    // A better way is to use the API, but the prompt asks for browser.
    
    // I'll try to find the cells using the a-tag or similar if they are rendered.
    // Actually, Google Sheets renders cells as divs with specific classes.
    
    // Let's try to use the 'act' logic from the skill if I can, 
    // but since I'm writing a script, I'll use a robust selector if possible.
    
    // Since I can't easily interact with the Sheets Grid via raw Playwright 
    // without a lot of trial and error, I will try to see if there's a way to 
    // identify the cells.
    
    // Wait, if I use the 'google-sheets' skill I can just write the data.
    // But I don't have a service account. I have user credentials.
    // User credentials can be used via OAuth2, but that requires a client ID.
    
    // Let's try to use the browser to "type" into the cells.
    // I'll search for the URL text on the page.
    
    for (const [url, type] of Object.entries(mapping)) {
      try {
        await page.keyboard.press('Control+f');
        await page.keyboard.type(url);
        await page.keyboard.press('Enter');
        await page.waitForTimeout(1000);
        await page.keyboard.press('Escape');
        
        // Once the URL is found/highlighted, the cell is active.
        // Col B is 2nd column. Col J is 10th column.
        // Need to move 8 columns to the right.
        for (let i = 0; i < 8; i++) {
          await page.keyboard.press('ArrowRight');
        }
        
        // Enter the value
        await page.keyboard.press('Enter');
        await page.keyboard.type(type);
        await page.keyboard.press('Enter');
        console.log(`Updated ${url} -> ${type}`);
        
        // Go back to the start of the row or move down
        await page.keyboard.press('ArrowDown');
      } catch (e) {
        console.error(`Failed to update ${url}: ${e.message}`);
      }
    }

  } catch (error) {
    console.error('Error during execution:', error);
  } finally {
    await browser.close();
  }
}

run();
