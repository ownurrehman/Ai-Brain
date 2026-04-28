#!/usr/bin/env node
/**
 * OpenClaw AI Automation Page Manager
 * Audits and updates rankray.com/ai-automation/ via WordPress admin
 */

const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const CONFIG = {
  wpLogin: 'https://rankray.com/wp-admin',
  username: 'openclaw',
  password: 'OC#admin@2026',
  aiPageEdit: 'https://rankray.com/wp-admin/post.php?post=18073&action=edit',
  aiPageLive: 'https://rankray.com/ai-automation/',
  chromePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
};

// New humanized SEO content
const NEW_CONTENT = {
  metaDescription: 'AI automation services by Rank Ray streamline workflows, reduce costs, and boost efficiency. Get intelligent automation solutions for sustainable growth.',
  h1: 'AI Automation Services',
  h1Paragraph: 'Transform repetitive manual tasks into intelligent automated workflows with Rank Ray. Our AI automation solutions help businesses cut operational costs, accelerate processes, and scale efficiently using machine learning, predictive analytics, and smart workflow orchestration.',
  h2First: 'What Is AI Automation?',
  h2Para1: 'AI automation combines artificial intelligence with automated workflows to handle tasks that once required human judgment. At Rank Ray, we build systems that analyze data patterns, make decisions, and execute actions without manual intervention. This technology goes beyond simple rule-based automation by learning from data and adapting to new scenarios over time.',
  h2Para2: 'Our AI automation integrates seamlessly with your existing marketing stack. Whether you need chatbots handling customer inquiries after hours or predictive models forecasting demand for inventory, we connect these tools to your content marketing and email marketing systems for unified customer journeys.',
  h2Para3: 'The result is a business that runs continuously, responds instantly to customer needs, and allocates human talent to strategic work rather than repetitive tasks. This operational efficiency translates directly into cost savings and competitive advantage in fast-moving markets.',
  servicesHeading: 'Our AI Automation Services',
  servicesIntro: 'We deliver comprehensive AI automation solutions tailored to your operational challenges. Each service is designed to reduce manual work while maintaining quality and brand consistency across every customer touchpoint.',
  service1: {
    heading: 'AI Strategy & Consulting',
    para: 'We evaluate your current processes to identify high-impact automation opportunities. Our team creates a detailed roadmap integrating AI capabilities with your SEO strategy and business objectives, ensuring every implementation delivers measurable ROI.'
  },
  service2: {
    heading: 'Process Automation & RPA',
    para: 'Robotic Process Automation eliminates repetitive data entry, document processing, and system updates. This accuracy boost supports your conversion rate optimization goals by removing human error from critical workflow steps.'
  },
  service3: {
    heading: 'Chatbots & Virtual Assistants',
    para: 'AI-powered conversational agents provide instant customer support across websites, social platforms, and messaging apps. These assistants integrate with your email marketing automation to qualify leads and nurture prospects around the clock.'
  },
  service4: {
    heading: 'Predictive Analytics & Forecasting',
    para: 'Machine learning models analyze historical data to forecast trends, identify risks, and guide strategic decisions. These insights inform your web development priorities and marketing budget allocation based on predicted outcomes.'
  },
  service5: {
    heading: 'Integration & Workflow Automation',
    para: 'We connect disparate systems into unified workflows that share data automatically. Your CRM, inventory, marketing platforms, and accounting tools synchronize in real time, supporting comprehensive digital marketing services campaigns.'
  },
  service6: {
    heading: 'Monitoring & Optimization',
    para: 'Continuous performance tracking ensures your AI systems improve over time. We refine algorithms based on real-world results, maintaining alignment with your branding guidelines and business goals.'
  }
};

class AIPageManager {
  constructor() {
    this.browser = null;
    this.page = null;
    this.results = {
      audit: {},
      updates: [],
      errors: []
    };
  }

  async delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  async init() {
    console.log('Initializing browser...');
    this.browser = await puppeteer.launch({
      headless: false, // Set to true for production
      executablePath: CONFIG.chromePath,
      args: ['--no-sandbox', '--disable-setuid-sandbox'],
      defaultViewport: { width: 1920, height: 1080 }
    });
    this.page = await this.browser.newPage();
    console.log('Browser ready');
  }

  async login() {
    console.log('\n=== STEP 1: Logging into WordPress ===');
    await this.page.goto(CONFIG.wpLogin, { waitUntil: 'networkidle2' });
    
    await this.page.waitForSelector('#user_login', { visible: true });
    await this.page.type('#user_login', CONFIG.username);
    await this.page.type('#user_pass', CONFIG.password);
    await this.page.click('#wp-submit');
    
    await this.page.waitForNavigation({ waitUntil: 'networkidle2' });
    console.log('Logged in successfully');
    await this.delay(2000);
  }

  async auditCurrentPage() {
    console.log('\n=== STEP 2: Auditing Current AI Page ===');
    
    // Go to edit page
    await this.page.goto(CONFIG.aiPageEdit, { waitUntil: 'networkidle2' });
    await this.delay(3000);
    
    // Take screenshot of current state
    const screenshotPath = '/Users/sheikhown/.openclaw/workspace/ai-page-audit-before.png';
    await this.page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`Screenshot saved: ${screenshotPath}`);
    
    // Extract current meta description
    const currentMeta = await this.page.evaluate(() => {
      const metaField = document.querySelector('#rank_math_description, textarea[name="rank_math_description"], #snippet_meta_description');
      return metaField ? metaField.value : 'Meta field not found';
    });
    
    this.results.audit.currentMeta = currentMeta;
    console.log('Current Meta Description:', currentMeta.substring(0, 100) + '...');
    
    // Check for "X" markers in content (indicating AI/placeholder content)
    const hasXMarkers = await this.page.evaluate(() => {
      const textareas = document.querySelectorAll('textarea');
      for (const ta of textareas) {
        if (ta.value && ta.value.includes('X ')) return true;
      }
      return false;
    });
    
    this.results.audit.hasXMarkers = hasXMarkers;
    console.log('Has X markers (needs fixing):', hasXMarkers);
    
    return this.results.audit;
  }

  async updateMetaDescription() {
    console.log('\n=== STEP 3: Updating Meta Description ===');
    
    try {
      // Click on Rank Math tab
      const rankMathTab = await this.page.$('#rank_math_metabox_link, a[href="#rank_math_metabox"]');
      if (rankMathTab) {
        await rankMathTab.click();
        await this.delay(1000);
      }
      
      // Find and update description
      await this.page.evaluate((newDesc) => {
        const selectors = [
          '#rank_math_description',
          'textarea[name="rank_math_description"]',
          '#snippet_meta_description',
          '[data-setting="description"] textarea'
        ];
        
        for (const sel of selectors) {
          const el = document.querySelector(sel);
          if (el) {
            el.value = newDesc;
            el.dispatchEvent(new Event('input', { bubbles: true }));
