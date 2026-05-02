// Lead-to-Cash Pipeline Orchestrator
// Main entry point that initializes and manages all pipeline stages

const CRMPipeline = require('./crm/connector');
const LeadMonitor = require('./leads/monitor');
const ContentGenerator = require('./content/generator');
const PersonalizedDrafting = require('./content/drafter');
const EmailMonitor = require('./outreach/monitor');
const ContractTrigger = require('./contracts/trigger');
const FulfillmentEngine = require('./fulfillment/handover');

class PipelineOrchestrator {
  constructor() {
    this.components = {
      crm: new CRMPipeline(),
      leadMonitor: new LeadMonitor(),
      contentGenerator: new ContentGenerator(),
      emailDrafting: new PersonalizedDrafting(),
      emailMonitor: new EmailMonitor(),
      contractTrigger: new ContractTrigger(),
      fulfillmentEngine: new FulfillmentEngine()
    };
  }

  async start() {
    console.log('Starting Lead-to-Cash Pipeline...');
    
    try {
      // Initialize CRM connection
      await this.components.crm.init();
      
      // Start monitoring for new leads
      // Note: In a real implementation, these would run concurrently
      console.log('Pipeline started. Monitoring for new leads...');
      
      // For demonstration, we'll just log the start of the pipeline
      console.log('Lead-to-Cash Pipeline is now active');
    } catch (error) {
      console.error('Error starting pipeline:', error);
    }
  }
}

// Export for use in other modules
module.exports = PipelineOrchestrator;

// For standalone execution
if (require.main === module) {
  const orchestrator = new PipelineOrchestrator();
  orchestrator.start().catch(console.error);
}