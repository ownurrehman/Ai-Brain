#!/usr/bin/env node
const puppeteer = require('puppeteer');

const CONFIG = {
  wpLogin: 'https://rankray.com/wp-admin',
  username: 'openclaw',
  password: 'OC#admin@2026',
  aiPageEdit: 'https://rankray.com/wp-admin/post.php?post=18073&action=edit',
  aiPageLive: 'https://rankray.com/ai-automation/',
  chromePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
};

const CONTENT = {
  metaDescription: 'AI automation services by Rank Ray streamline workflows, reduce costs, and boost efficiency. Get intelligent automation solutions for sustainable growth.',
  h1_service_page: 'AI Automation Services',
  h1_paragraph: 'Transform repetitive manual tasks into intelligent automated workflows with Rank Ray. Our AI automation solutions help businesses cut operational costs, accelerate processes, and scale efficiently using machine learning, predictive analytics, and smart workflow orchestration.',
  h2_first: 'What Is AI Automation?',
  h2_paragraph_1: 'AI automation combines artificial intelligence with automated workflows to handle tasks that once required human judgment. At Rank Ray, we build systems that analyze data patterns, make decisions, and execute actions without manual intervention. This technology goes beyond simple rule-based automation by learning from data and adapting to new scenarios over time.',
  h2_paragraph_2: 'Our AI automation integrates seamlessly with your existing marketing stack. Whether you need chatbots handling customer inquiries after hours or predictive models forecasting demand for inventory, we connect these tools to your content marketing and email marketing systems for unified customer journeys.',
  h2_paragraph_3: 'The result is a business that runs continuously, responds instantly to customer needs, and allocates human talent to strategic work rather than repetitive tasks. This operational efficiency translates directly into cost savings and competitive advantage in fast-moving markets.',
  services_heading__h2: 'Our AI Automation Services',
  before_services_paragraph: 'We deliver comprehensive AI automation solutions tailored to your operational challenges. Each service is designed to reduce manual work while maintaining quality and brand consistency across every customer touchpoint.',
  services_1_heading: 'AI Strategy & Consulting',
  services_1_paragraph: 'We evaluate your current processes to identify high-impact automation opportunities. Our team creates a detailed roadmap integrating AI capabilities with your SEO strategy and business objectives, ensuring every implementation delivers measurable ROI.',
  services_2_heading: 'Process Automation & RPA',
  services_2_paragraph: 'Robotic Process Automation eliminates repetitive data entry, document processing, and system updates. This accuracy boost supports your conversion rate optimization goals by removing human error from critical workflow steps.',
  services_3_heading: 'Chatbots & Virtual Assistants',
  services_3_paragraph: 'AI-powered conversational agents provide instant customer support across websites, social platforms, and messaging apps. These assistants integrate with your email marketing automation to qualify leads and nurture prospects around the clock.',
  services_4_heading: 'Predictive Analytics & Forecasting',
  services_4_paragraph: 'Machine learning models analyze historical data to forecast trends, identify risks, and guide strategic decisions. These insights inform your web development priorities and marketing budget allocation based on predicted outcomes.',
  services_5_heading: 'Integration & Workflow Automation',
  services_5_paragraph: 'We connect disparate systems into unified workflows that share data automatically. Your CRM, inventory, marketing platforms, and accounting tools synchronize in real time, supporting comprehensive digital marketing services campaigns.',
  services_6_heading: 'Monitoring & Optimization',
  services_6_paragraph: 'Continuous performance tracking ensures your AI systems improve over time. We refine algorithms based on real-world results, maintaining alignment with your branding guidelines and business goals.',
  why_us_box_1_heading: 'Expert AI Specialists',
  why_us_box_1_paragraph: 'Our engineers and data scientists have implemented automation across finance, healthcare, retail, and B2B sectors. This cross-industry experience means we understand regulatory requirements, integration challenges, and change management for your specific vertical.',
  why_us_box_2_heading: 'Custom Solutions',
  why_us_box_2_paragraph: 'Every business has unique processes and constraints. We design automation workflows around your existing systems rather than forcing generic templates that create more work than they save.',
  why_us_box_3_heading: 'Data-Driven Approach',
  why_us_box_3_paragraph: 'We measure everything. Before implementation, we baseline your current metrics. After deployment, we track time savings, error reduction, and cost impacts to prove value and identify optimization opportunities.',
  why_us_box_4_heading: 'Transparent Reporting',
  why_us_box_4_paragraph: 'Monthly dashboards show exactly what your AI automation achieves. You see ticket resolution times, processing costs per transaction, and conversion improvements with full visibility into system performance.',
  why_us_box_5_heading: 'Scalable & Flexible',
  why_us_box_5_paragraph: 'Start with one automated process and expand as value becomes clear. Our architecture supports growing transaction volumes and additional use cases without requiring complete rebuilds.',
  why_us_box_6_heading: 'Ongoing Support',
  why_us_box_6_paragraph: 'AI systems require maintenance as data patterns shift and business needs evolve. We provide continuous monitoring, retraining, and optimization to keep your automation delivering results.'
};

function delay(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

(async () => {
  console.log('Starting AI Automation page update...\n');
  
  const browser = await puppeteer.launch({
    headless: false,
    executablePath: CONFIG.chromePath,
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
    defaultViewport: { width: 1920, height: 1080 }
  });

  const page = await browser.newPage();

  try {
    // Step 1: Login
    console.log('1. Logging into WordPress...');
    await page.goto(CONFIG.wpLogin, { waitUntil: 'networkidle2' });
    await page.waitForSelector('#user_login', { timeout: 10000 });
    await page.type('#user_login', CONFIG.username);
    await page.type('#user_pass', CONFIG.password);
    await page.click('#wp-submit');
    await page.waitForNavigation({ waitUntil: 'networkidle2' });
    console.log('   Logged in\n');

    await delay(2000);

    // Step 2: Before screenshot
    console.log('2. Taking BEFORE screenshot...');
    await page.goto(CONFIG.aiPageLive, { waitUntil: 'networkidle2' });
    await delay(3000);
    await page.screenshot({ 
      path: '/Users/sheikhown/.openclaw/workspace/ai-automation-BEFORE.png',
      fullPage: true 
    });
    console.log('   SAVED: ai-automation-BEFORE.png\n');

    // Step 3: Go to edit page
    console.log('3. Opening Edit Page...');
    await page.goto(CONFIG.aiPageEdit, { waitUntil: 'networkidle2' });
    await delay(3000);

    // Step 4: Update Rank Math Meta
    console.log('4. Updating Rank Math meta description...');
    try {
      const rankMathTab = await page.$('#rank_math_metabox_link');
      if (rankMathTab) {
        await rankMathTab.click();
        await delay(1500);
        
        await page.evaluate((desc) => {
          const field = document.querySelector('#rank_math_description, textarea[name="rank_math_description"]');
          if (field) {
            field.value = desc;
            field.dispatchEvent(new Event('input', { bubbles: true }));
