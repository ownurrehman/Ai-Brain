const { chromium } = require('playwright');
const fs = require('fs');

async function run() {
    const csvPath = '/Users/sheikhown/.openclaw/workspace/reports/khanllp-citation-tracker-2026-04-21.csv';
    const sheetUrl = 'https://docs.google.com/spreadsheets/d/1EnYut8v6-FO4PtPD7QibhJrvOcvbvXxgXVjpeaGKmmQ/edit';
    const email = 'oliverjakeseo@gmail.com';
    const password = 'OJ<<##1156128279430959165>@2025';

    console.log('Reading CSV...');
    const csvData = fs.readFileSync(csvPath, 'utf8');
    const lines = csvData.split('\n').filter(line => line.trim() !== '');
    const dataRows = lines.slice(1).map(line => {
        const cols = line.split(',');
        return {
            name: cols[0],
            url: cols[1],
            type: cols[4]
        };
    });

    const browser = await chromium.launch({ 
        headless: false, 
        args: ['--disable-blink-features=AutomationControlled'] 
    });
    const context = await browser.newContext({
        userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'
    });
    const page = await context.newPage();

    try {
        console.log('Logging into Google...');
        await page.goto('https://accounts.google.com/');
        
        // Email
        await page.fill('input[type="email"], input[name="identifier"]', email);
        await page.click('text=Next, #identifierNext, button[type="submit"]');
        
        console.log('Waiting for password field...');
        await page.waitForSelector('input[type="password"], input[name="password"]', { timeout: 10000 });
        await page.fill('input[type="password"], input[name="password"]', password);
        await page.click('text=Next, #passwordNext, button[type="submit"]');
        
        console.log('Navigating to Sheet...');
        await page.waitForTimeout(5000);
        await page.goto(sheetUrl);
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(10000);

        console.log('Updating Column J...');
        for (const row of dataRows) {
            if (!row.url) continue;
            
            // Find cell by text (URL)
            const cell = page.locator(`text=${row.url}`).first();
            if (await cell.count() > 0) {
                await cell.click();
                // Column B (2) to Column J (10) is 8 steps right
                for (let i = 0; i < 8; i++) {
                    await page.keyboard.press('ArrowRight');
                }
                await page.keyboard.press('Enter');
                await page.keyboard.type(row.type);
                await page.keyboard.press('Enter');
                await page.keyboard.press('Escape');
                console.log(`Updated ${row.name} -> ${row.type}`);
            }
        }

        console.log('Taking screenshot...');
        await page.screenshot({ path: '/Users/sheikhown/.openclaw/workspace/reports/sheet_updated_column_j.png', fullPage: true });
        console.log('Screenshot saved.');

    } catch (err) {
        console.error('Error occurred:', err);
        await page.screenshot({ path: '/Users/sheikhown/.openclaw/workspace/reports/error_snapshot.png' });
    } finally {
        await browser.close();
    }
}

run();
