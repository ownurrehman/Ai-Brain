# Changelog

All notable changes to SEO Engine AI will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Branch and release governance documentation for `main`, `dev`, and historical branch handling.
- A release scorecard to measure production readiness explicitly instead of relying on narrative status claims.
- A 2026 roadmap document focused on release hardening, compatibility, and productization.

### Changed
- Documentation now reflects the current staging status of the standalone plugin.
- Root README and status docs now align on `dev` as the integration branch and `main` as the stable baseline.


## [13.13.1] - 2026-02-18

### Epic Technical SEO Suite Upgrade

#### Added
- **Built-in Technical SEO Suite** with core capabilities usually split across multiple plugins.
- **Meta Title + Meta Description setter** via post/page meta box.
- **Yoast + Rank Math sync** for title/description post meta to improve compatibility.
- **Custom robots.txt directives** from plugin settings.
- **Built-in XML sitemap endpoint** at `/seoengineai-sitemap.xml`.
- **Redirect manager** using configurable rules (`/source|https://target|301`).
- **404 monitor** with logging + admin viewer and clear action.
- **hreflang output support** using configurable language-to-URL mappings with `{slug}` placeholder.
- **New Technical SEO admin page** for quick access and 404 monitoring.

#### Changed
- Core bootstrap now loads and initializes `Seoengineai_SEO_Suite`.
- Activation/deactivation flow now flushes rewrite rules for sitemap endpoint lifecycle.

---

## [13.12.5] - 2025-10-30

### AI Article Writer UI Relocation

#### Changed
- **Automated Blog Generator Moved to Dedicated Submenu**
  - New submenu location: `SEO Engine AI → Generate Pages → AI Article Writer`
  - Standalone interface for blog and article generation settings
  - Improved discoverability and user experience
  - All blog automation features consolidated in one dedicated page

#### Removed
- **Blog Settings Section from Settings Page**
  - Removed Automated Blog Generation section from Settings
  - Settings page now shows redirect notice pointing to AI Article Writer
  - All settings registration preserved for backward compatibility

#### Added
- **New Admin View: `admin/views/ai-article-writer.php`**
  - Complete blog generation interface with all settings
  - Enable/disable toggle
  - Generation frequency selector
  - Blog topics input (one per line)
  - Post type and status selectors
  - Topic rotation (Random/Sequential)
  - Test generation button
  - Status display showing next scheduled run and topic count
  - Card-based layout matching Generator page design

#### Technical
- Added `render_article_writer()` method to `Seoengineai_Core`
- Submenu registered under "Generate Pages" section
- Settings registration kept in `Seoengineai_Settings` for backward compatibility
- All existing cron jobs and scheduled tasks continue to work
- Database options unchanged (`seoengineai_auto_blog_enabled`, etc.)

#### Backward Compatibility
- All existing blog settings preserved
- Existing scheduled cron jobs unaffected
- Settings saved before v13.12.5 continue to work
- No data loss or migration required

---

## [13.12.4] - 2025-10-30

### Prompt Preset System Finalization

#### Added
- **Comprehensive Field Coverage** - Prompt templates for all field types:
  - Featured image alt text
  - Open Graph titles and descriptions
  - Twitter card titles and descriptions  
  - Schema fields (name, description)
  - Additional ACF field types (number, image, file, select, checkbox, radio, date, time, date_time)
  - Elementor widgets (text editor, heading, button, icon box)
  - Gutenberg blocks (paragraph, heading, list, quote, code, preformatted)
- **Tone Profiles** - Data structure added to JSON for future tone switching:
  - Each template now includes a `tone` property (professional, friendly, sales, informative)
  - Default tone: "professional"
  - Structure ready for UI implementation in future versions
- **Localization Support** - Infrastructure for multilingual presets:
  - Support for language-specific files: `prompt-presets.en.json`, `prompt-presets.fr.json`, etc.
  - Language detection and file switching logic
  - Defaults to English (`prompt-presets.json`)
- **Admin Interface Enhancements**:
  - **Edit Prompt Presets** - Inline JSON editor with syntax validation
  - **Reload Presets** - Button to clear cache and reload from file
  - **Active Preset File** - Display showing current preset file in use
  - Automatic backup creation on preset saves
  - JSON formatting helper button

#### Changed
- **JSON Structure** - Upgraded to nested format:
  - Old: `"title": "prompt text"`
  - New: `"title": { "prompt": "prompt text", "tone": "professional" }`
- **Backward Compatible** - Smart Prompts class supports both old and new JSON formats
- **Enhanced Templates** - All prompts rewritten for professional, SEO-focused content
- Field detector now uses Smart Prompts for ACF fields (previously had separate logic)

#### Technical
- Enhanced Smart Prompts class with localization and tone support
- Admin settings expanded with preset management tools
- JSON validation and error handling
- Automatic cache clearing on preset edits

---

## [13.12.3] - 2025-10-29

### Smart Default Prompt System + Editable Prompt Presets

#### Added
- **Smart Prompt System** - Context-aware, domain-specific prompt generation
  - Automatically detects website domain from WordPress site URL
  - Builds intelligent prompts that include domain context
  - Professional, SEO-focused prompt templates
  - Adapts tone and intent based on field type (Title, Meta, H1, FAQ, etc.)
- **Editable Prompt Presets** - External JSON configuration file
  - All prompt templates stored in `/data/prompt-presets.json`
  - Organized by field source (core, seo, acf, headings, special)
  - Can be edited without modifying PHP code
  - Supports future multilingual preset packs
- **Admin Settings Integration**
  - Toggle "Use Smart Prompt System" on/off
  - Brand Domain override option (custom domain name)
  - Settings page section for Smart Prompt System configuration

#### Enhanced
- **Prompt Templates** - Professional SEO briefs instead of mechanical instructions
  - Domain-aware prompts: "Generate a concise, SEO-optimized page title for {domain}'s {service} page in {city}"
  - Context-aware field descriptions
  - Natural language with SEO best practices
  - Character limits integrated naturally
- **Placeholder Replacement** - Extended to support {domain} placeholder
  - Replaces {domain}, {service}, {city}, {field_name}, {limit} dynamically
  - Falls back gracefully if domain detection fails

#### Technical
- New class: `Seoengineai_Smart_Prompts` for loading and processing presets
- Caching system prevents repeated JSON file reads
- Graceful fallback to generic prompts if JSON missing/invalid
- Backward compatible: falls back to original prompts if Smart System disabled
- Updated field detector to use Smart Prompts for all field types (core, SEO, ACF)

#### Files Modified
- `includes/class-seoengineai-smart-prompts.php` (NEW)
- `data/prompt-presets.json` (NEW)
- `includes/class-seoengineai-field-detector.php` (updated prompt generation)
- `admin/class-seoengineai-generator.php` (domain placeholder replacement)
- `admin/class-seoengineai-settings.php` (Smart Prompts settings section)
- `includes/class-seoengineai-core.php` (load Smart Prompts class)

---

## [13.12.2] - 2025-10-29

### Final Generator & ACI Fix Update (Patch)

#### Fixed
- **Layout Compression** - Generator interface now uses full available width
  - Added `!important` flags to override any WordPress admin constraints
  - Ensured only hybrid layout renders (no legacy stacked layout)
  - Full-width responsive design across all screen sizes
- **Prompt Warning Logic** - Fixed misleading "prompt too long" messages
  - Removed incorrect comparison between prompt length and output limit
  - Now only warns if output limit is unreasonably short (<20 chars)
  - Added informational display showing prompt length and output limit separately
  - Clear, neutral messaging instead of confusing comparisons
- **ACF Field Character Limits** - Ensured all ACF fields display character limits
  - Added fallback logic in field table row generation
  - ACI system properly applied to all ACF fields during detection
  - Character limits guaranteed even if ACI initially returns 0 or missing
  - Badge display shows accurate limits for all ACF fields

#### Enhanced
- **Reset Prompts Functionality** - Improved reset logic
  - Now properly clears all localStorage prompts and char limit overrides
  - Resets both editor pane and all stored prompts
  - Better handling of filtered/visible fields
  - More comprehensive state reset
- **Sidebar Opening Logic** - Verified and reinforced
  - Checkbox clicks properly isolated with `e.stopPropagation()`
  - Editor only opens on Edit button or explicit row action
  - Keyboard navigation (Enter key) still works for opening editor

#### Technical
- JavaScript: Fixed indentation in `updatePromptLengthInfo()` function
- PHP: Added ACF field character limit fallback in `get_field_table_row()`
- PHP: Enhanced ACF field processing to ensure limits are always set
- CSS: Added `!important` flags for full-width layout enforcement

---

## [13.12.1] - 2025-10-29

### UI Fixes & Polish

#### Fixed
- **Full-Width Layout** - Generator interface now renders full-width across admin content area
  - Removed max-width constraint (800px → 100%)
  - Added proper flex layout with responsive breakpoints
  - Left pane: 40% width, Right pane: 60% width
  - Stacks vertically on screens <1024px
- **Reset Prompts Button** - Now works dynamically without page reload
  - Clears all prompts and character limit overrides instantly
  - Updates editor pane if open
  - Shows toast notification for user feedback
  - No longer triggers `location.reload()`
- **Sidebar Opening Logic** - Fixed to only open on Edit button/row click
  - Checkbox clicks no longer trigger editor panel
  - Added `e.stopPropagation()` to prevent checkbox events from bubbling
  - Editor opens only on explicit Edit action
- **AI Suggest Button** - Fully functional with OpenAI integration
  - Wired to `seoengineai_preview_field` AJAX endpoint
  - Shows loading state ("Generating...") during request
  - Displays generated content in editor textarea
  - Shows success/error toast notifications
  - Properly handles errors and empty responses

#### Enhanced
- **Toast Notification System** - Added user-friendly toast messages
  - Success (green), Error (red), Info (blue), Warning (yellow)
  - Auto-hides after 3 seconds with smooth animations
  - Positioned at top-right corner
- **Responsive Design** - Improved mobile/tablet experience
  - Layout stacks vertically on smaller screens
  - Action bar adapts to narrow widths
  - All features remain accessible

#### Technical
- CSS: Added `.seoengineai-generator-layout` wrapper for full-width control
- JavaScript: Enhanced `initializeFieldSelection()` with checkbox event isolation
- JavaScript: Added `showToastMessage()` utility function
- JavaScript: Enhanced Reset Prompts with comprehensive data clearing
- JavaScript: Implemented AI Suggest with proper error handling

---

## [13.12.0] - 2025-10-29

### Generator Interface Redesign - Hybrid Layout

#### Added
- **Hybrid Two-Pane Layout** - Complete redesign of "Generate Pages" interface
  - Left pane: Collapsible field groups table with organized view
  - Right pane: Smart prompt editor with field information
  - Optimized for 50-100+ fields with improved usability
  
- **Collapsible Field Groups**
  - Fields organized by source: Core WordPress, SEO Plugins, ACF, Elementor, Gutenberg, Custom
  - Expand/collapse groups with smooth animations
  - Group headers show field counts
  - Expand All / Collapse All buttons
  
- **Smart Search & Filtering**
  - Real-time search by field label, source, or ID
  - Filter options: All, Selected Only, Modified Only, Core Fields, SEO Fields, ACF Fields
  - Results update instantly
  
- **Right-Side Prompt Editor**
  - Click any field row to open editor
  - Displays field information (label, source, character limit)
  - Large editable textarea for prompt (no restrictions)
  - Character limit override input
  - Live character counter with visual warnings (non-blocking)
  - Preview, Reset, and AI Suggest buttons
  
- **Quick Actions Bar**
  - Select All / Deselect All fields
  - Expand All / Collapse All groups
  - Reset All Prompts button
  - Search box and filter dropdown
  
- **Keyboard Navigation**
  - ↑ / ↓ arrow keys to navigate field rows
  - Enter to open editor for selected field
  - Escape to close editor
  - Full keyboard accessibility support

#### Enhanced
- **Performance Optimizations** - Optimized for large field sets
  - Lazy rendering support ready
  - Efficient DOM manipulation
  - Local storage for prompt persistence during session
  
- **User Experience Improvements**
  - Visual status indicators (ready, modified, warning)
  - Active field highlighting
  - Selected field highlighting
  - Smooth animations and transitions
  - Responsive design (mobile-friendly)

#### Technical
- **Backend Changes**
  - New method: `group_fields_by_source()` - Organizes fields into groups
  - New method: `build_grouped_fields_html()` - Creates table structure
  - New method: `get_field_table_row()` - Generates table row HTML
  - AJAX response now includes raw fields data and group structure
  
- **Frontend Changes**
  - Complete CSS redesign for hybrid layout
  - Comprehensive JavaScript for collapsibles, search, editor
  - Local storage integration for prompts and char limits
  - Backward compatible with existing generation logic

#### Backward Compatibility
- All existing AJAX endpoints remain unchanged
- Field detection, ACI, and validation logic fully preserved
- Generation flow unchanged - only UI improved
- All prompts and settings maintain compatibility

---

## [13.11.1] - 2025-10-29

### 🐛 Bug Fixes

#### Fixed
- **Empty Content Generation Issue** - Fixed critical bug where generated pages were created without AI content
  - Fixed placeholder replacement in `generate_single_page()` method - now correctly uses service and city values instead of title
  - Added service and city extraction with intelligent fallback parsing from title
  - Enhanced field update logic to properly handle WordPress core fields (title, content, excerpt), ACF fields, and SEO plugin fields
  - Added empty content validation with detailed error logging
  
- **Prompt Textarea Restrictions** - Removed blocking character limit restrictions from prompt input fields
  - Removed `maxlength` attribute from prompt textareas (was preventing user input)
  - Added visual warning system for prompt length (non-blocking, informational only)
  - Character limits now only apply to AI output, not user prompt editing
  - Real-time prompt length monitoring with helpful visual feedback

#### Enhanced
- **Error Handling** - Improved error logging for debugging generation issues
- **Field Update Logic** - Unified field saving logic between bulk and single page generation

#### Technical
- Updated JavaScript to pass service and city separately in AJAX calls
- Fixed field row selector consistency (`.field-row` vs `.field-item`)
- Added prompt sanitization improvements

---

## [13.11.0] - 2025-10-29

### 🧠 Adaptive Character Intelligence (ACI) System

#### Added
- **Adaptive Character Intelligence (ACI)** - Intelligent 3-tier character limit system
  - **Tier 1 (Highest Priority):** Manual user-defined limits during generation
  - **Tier 2 (Medium Priority):** Benchmark learning from reference pages
  - **Tier 3 (Default):** Heuristic limits based on field type and SEO best practices
  
- **Benchmark Learning System**
  - Select any SEO-optimized page as benchmark in Settings
  - "Learn from Page" button analyzes field character counts
  - Stores learned limits for automatic application in future generations
  - Flexible field matching by ID, name, or label
  
- **Manual Character Limit Overrides**
  - Override input available in expanded prompt area for each field
  - Real-time character limit display updates
  - Manual limits always take highest priority during generation
  
- **Settings Integration**
  - New "Adaptive Character Intelligence" section in Settings
  - Benchmark page dropdown with all published posts/pages
  - Status display showing learned fields count
  - Clear learning data option

#### Enhanced
- **Field Detection** - Now uses ACI system when available
  - Automatically applies optimal character limits from benchmarks
  - Falls back gracefully to heuristic method if ACI unavailable
  - Backward compatible with existing field detection logic

#### Technical
- New class: `class-seoengineai-adaptive-char-intelligence.php`
- AJAX endpoints: `seoengineai_learn_from_benchmark`, `seoengineai_clear_benchmark`
- Persistent storage of learned limits in WordPress options
- Integration with field detector for seamless operation

#### Documentation
- Consolidated all version-specific documentation into `docs/VERSION-HISTORY-AND-UPDATES.md`
- Created `docs/README.md` for documentation navigation
- Removed redundant documentation files
- Updated architecture references

---

## [13.10.0] - 2025-10-23

### 🚀 MAJOR MILESTONE - Client–Server SaaS Transition Initiated

#### Architecture Evolution
- **Unified Enterprise Architecture** - Merged and upgraded architecture documentation
  - Combined standalone plugin features with SaaS architecture planning
  - Added comprehensive Phase 10 SaaS Evolution roadmap
  - Defined client-server split with dual-plugin system
  - Enhanced security, performance, and scalability planning

#### Documentation Overhaul
- **Single Source of Truth** - Consolidated all architecture documents
  - Removed duplicate and outdated architecture files
  - Created unified `architecture-v13.md` with complete roadmap
  - Added Phase 10 SaaS Evolution section with detailed implementation plan
  - Enhanced coding standards and development guidelines

#### Future-Ready Foundation
- **SaaS Architecture Preparation** - Ready for server integration
  - License management system design
  - JWT-based authentication framework
  - REST API endpoint specifications
  - Usage tracking and analytics planning
  - Migration tools and compatibility layer

#### Development Standards
- **Enterprise-Grade Standards** - Enhanced development practices
  - Universal naming conventions (`seoengineai_` / `seoengineaicloud_`)
  - Comprehensive testing strategy (unit, integration, performance)
  - Security checklist and implementation guidelines
  - Performance optimization and monitoring
  - Continuous integration and deployment

### 📋 Technical Improvements
- **Build System Enhancement** - Improved build and backup process
  - Incremental backup system with version preservation
  - Clean file structure with archived duplicates
  - Enhanced build scripts for client and server plugins
  - Comprehensive documentation and changelog management

### 🎯 Next Phase Ready
- **Phase 10 Implementation** - Ready to begin SaaS development
  - Client plugin enhancements for license management
  - Server plugin architecture with API endpoints
  - Token-based authentication system
  - Usage tracking and credit management
  - Multi-site and enterprise deployment capabilities

---

## [1.9.3] - 2025-10-23

### Fixed
- **Test Blog Post 404 Error** - Fixed "Edit Post" and "View Post" links leading to 404
  - Edit URL now uses `get_edit_post_link()` with 'raw' context for proper admin URL
  - Draft posts now show "Preview Post" button with preview URL instead of permalink
  - Published posts show "View Post" button with permalink
  - Success notice differentiates between "(Draft)" and "(Published)" status
  - Cron `generate_blog_post()` now returns post data for AJAX response
  - All links tested and working correctly

### Improved
- **Test Blog Success Message** - Enhanced feedback
  - Shows post status in parentheses: "(Draft)" or "(Published)"
  - Button text changes based on status: "Preview Post" vs "View Post"
  - Success notice includes three links: Edit Post | Preview/View Post | View Logs
  - Notice stays visible for 15 seconds (increased from 10)

---

## [1.9.2] - 2025-10-23

### Improved
- **Test Blog Button UX** - Better user guidance and error reporting
  - Added clear warning: "If you just added topics, click Save Changes first"
  - Enhanced error notices appear at top of page
  - Better visual feedback for failed tests
  - Clearer instruction flow: Configure → Save → Test → Enable

---

## [1.9.1] - 2025-10-23

### Fixed
- **Test Blog Button Validation** - Fixed "Please add blog topics" error appearing even with topics
  - Added proper trimming of whitespace before validation
  - Added array parsing to check for valid topics (one per line)
  - More helpful error message distinguishes between empty and invalid topics
  - Now correctly validates that at least one non-empty topic exists

---

## [1.9.0] - 2025-10-23

### 🚀 Major Upgrade: SaaS-Level Chunked Generation System

### Added
- **Intelligent One-by-One Page Generation** - No more server overload!
  - Pages generated sequentially (one at a time) instead of bulk
  - 200ms delay between pages prevents server timeouts
  - Perfect for shared hosting environments
  - Works smoothly even with 100+ pages
  - No more max_execution_time errors

- **Real-Time Progress Tracking** - See exactly what's happening
  - Live progress bar based on actual page completion
  - Shows current page being generated: "Generating page 5 of 25"
  - Displays page title and slug in real-time
  - After each page: "✓ Page 5 of 25 created - Toronto Dentist"
  - Shows fields generated count per page
  - Accurate percentage (not simulated)

- **Professional User Feedback**
  - Current status: "Generating page X of Y"
  - Page title highlighted in blue
  - Slug shown in gray
  - After completion: "✓ Created/Updated/Skipped" with green/orange/gray icons
  - Fields count: "Slug: toronto-dentist | 5 fields generated"

- **Server-Friendly Architecture**
  - New AJAX endpoint: `seoengineai_generate_single_page`
  - Lightweight requests (1 page per request)
  - Automatic retry on failure
  - Prevents PHP memory exhaustion
  - No database lock issues

### Improved
- **Progress Bar Accuracy** - 100% accurate, not simulated
  - Updates after each page completes
  - Math.round calculation: (current / total) * 100
  - Smooth visual progression
  
- **Completion Summary** - Enhanced final report
  - Bold numbers for key metrics
  - Color-coded status icons
  - Direct "View All Pages" button
  - "View Activity Logs" button
  - Clean, professional layout

- **Error Handling** - Graceful failure recovery
  - Individual page failures don't stop the queue
  - Failed pages logged and counted
  - Continue generating remaining pages
  - Full error details in Activity Logs

### Performance
- **Shared Hosting Safe** 
  - 200ms between requests = max 5 pages/second
  - Prevents Apache/Nginx timeouts
  - No PHP max_execution_time issues
  - Works on cheapest hosting plans
  
- **Scalable**
  - Tested with 100+ pages
  - Memory usage: ~10MB per page (vs 500MB+ for bulk)
  - Can be paused/resumed (browser stays open)
  - No server load spikes

### Technical
- Replaced bulk `generate_pages` with chunked JavaScript queue
- New backend endpoint handles single page generation
- Frontend builds page queue, processes sequentially
- 200ms setTimeout prevents server overload
- AJAX long-polling compatible

---

## [1.8.4] - 2025-10-23

### Added
- **Test Blog Generation Button** - Easy way to verify automated blog settings before enabling
  - New "Generate Test Blog Post Now" button in Settings → Automated Blog Generation section
  - Creates a blog post immediately using current settings (topics, post type, status, etc.)
  - Validates API key and topics before attempting generation
  - Shows real-time status with success/error messages
  - Provides direct "Edit Post" and "View Post" links after successful generation
  - Logs test generation in Activity Logs for tracking
  - Helpful for users to confirm everything works before enabling scheduled automation

### Improved
- **Automated Blog Settings UX** - Added blue highlighted test section at top
  - Clear instructions: "🧪 Test Before Enabling"
  - Explains purpose: verify settings before scheduling
  - Better visual hierarchy in settings page

### User Workflow
1. Configure OpenAI API key
2. Add blog topics (one per line)
3. Set post type, status, frequency, rotation
4. Click "Generate Test Blog Post Now" to verify
5. Review the generated post
6. If satisfied, enable "Enable Automated Blogs" checkbox
7. Save settings

---

## [1.8.3] - 2025-10-23

### Improved
- **Activity Logs Optimization** - More compact UI showing 2-3x more logs per screen
  - Reduced card sizes in summary section (48px → 20px icons)
  - Compact inline summary cards instead of large grid layout
  - Smaller activity items (16px padding → 8px padding)
  - Reduced icon sizes (40px circles → 20px simple icons)
  - Tighter spacing throughout (4px gaps instead of 8-16px)
  - Smaller fonts (14px → 13px for messages, 12px → 11px for meta)
  - Can now see ~20 logs instead of ~7 in the same screen space

- **CSV Export Enhancement** - User-friendly spreadsheet format
  - Renamed columns: Date, Time, Status, Activity Type, Description, Page Title, Page ID, Details
  - Split timestamp into Date and Time columns for Excel sorting
  - Changed "success" to "Successful", "error" to "Failed" for clarity
  - Extracts page titles and IDs into dedicated columns
  - Shows action type (Create/Update) in Details column
  - Shows generation summary (e.g., "25 pages (5 services × 5 cities)")
  - Easy to filter and analyze in Excel/Google Sheets

### Fixed
- Removed oversized dashicons that consumed too much screen space
- Reduced visual noise with simpler icon approach
- Better information density without sacrificing readability

---

## [1.8.2] - 2025-10-23

### Fixed
- **Quotation Marks in Generated Content** - OpenAI responses no longer wrapped in quotes
  - Enhanced system prompt to explicitly instruct "Never wrap response in quotation marks"
  - Added automatic quote removal safety filter for all content types
  - Strips both straight quotes (`"`, `'`) and smart quotes (`"`, `"`, `'`, `'`)
  - Removes leading and trailing quotes from all generated content
  - Ensures clean, professional content output without quote artifacts
  - Prevents titles like `"Toronto Dentist"` → now outputs: `Toronto Dentist`

### Technical
- Updated `Seoengineai_OpenAI::generate_content()` method
- Added regex pattern matching for comprehensive quote detection
- Double-pass trimming (before and after quote removal)
- Applies to all fields: titles, meta descriptions, content, custom fields

---

## [1.8.1] - 2025-10-23

### Improved
- **Activity Logs Complete UI/UX Redesign** - Customer-friendly activity monitoring
  - Renamed "Logs & Debug" to simply "Logs" in menu
  - Modern timeline-style activity feed with date separators
  - Clean summary cards showing Successful, Last 24h, Errors, Warnings
  - Customer-friendly messages instead of technical jargon
  - Enhanced generation logs show page titles with direct "Edit Page" links
  - Color-coded activity items (green=success, red=error, blue=info)
  - Proper dashicons for visual clarity
  - Removed confusing technical stats
  - Better filter UI with clear labels ("All Activity", "Successful Only", "Errors Only")
  - Improved empty state with call-to-action button
  - Better mobile responsiveness
  - Cleaner date/time formatting ("Today", "Yesterday", readable timestamps)
  - Error details now hidden by default (toggle to view)
  - Professional WordPress admin styling throughout

### Fixed
- Removed awkward "red 0" error counter display
- Fixed misaligned alert icon in stats section
- Removed developer-focused "Debug" terminology
- Made system info less prominent (removed from main view)

---

## [1.8.0] - 2025-10-23

### Added
- **Duplicate Page Detection & Smart Update System** - Revolutionary conflict resolution for existing pages
  - Automatic detection of existing pages before generation
  - Enhanced preview modal shows which pages exist (🔄 UPDATE, ✨ NEW, ⏭️ SKIP)
  - Three conflict resolution modes:
    - **Update Existing** (default): Regenerate content for existing pages
    - **Skip**: Don't create/update if page exists
    - **Create New with -2**: Create duplicate page with modified slug
  - Real-time status indicators in preview and completion summary
  - Direct links to edit existing pages from preview
  - Smart slug handling with automatic -2, -3 increments

### Improved
- **Generation Results Display**
  - Separate counters for Created, Updated, Skipped, Failed pages
  - Color-coded status icons (✓ Created, 🔄 Updated, ⏭️ Skipped, ✗ Failed)
  - Quick access links to view pages and logs after generation
  - More detailed completion summary

- **Generator Backend Logic**
  - `post_exists_by_title()` method for accurate duplicate detection
  - `preview_pages()` method returns conflict analysis before generation
  - Support for `conflict_action` parameter in generation flow
  - Separate tracking of created vs updated pages
  - Enhanced logging for update vs create actions

- **AJAX Endpoints**
  - New `seoengineai_preview_pages` endpoint for pre-generation check
  - New `seoengineai_get_existing_posts` endpoint for update mode (future)
  - Better conflict handling in generation endpoint

- **User Experience**
  - More transparent about what will happen before generation starts
  - Prevents accidental duplicates
  - Easy content refresh for existing pages
  - Better informed decision-making

### Technical
- Added `mode` and `conflict_action` parameters to generator
- Enhanced database queries for post existence checking
- Improved result structure with `created`, `updated`, `skipped` counters
- Better JavaScript preview modal with conditional rendering

---

## [1.7.1] - 2025-10-23

### Fixed
- **Critical AJAX Error** - Fixed nonce mismatch causing "Request failed" error
  - Changed form nonce from `seoengineai_nonce` to `seoengineai-nonce` (match AJAX handler)
  - Added comprehensive error logging in AJAX handler
  - Added try-catch block to capture fatal errors
  - Improved JavaScript error handler to show actual error messages
  - Better browser console logging for debugging

### Improved
- **Error Visibility**
  - AJAX errors now show in browser console with full details
  - Fatal errors logged to Logs & Debug with stack trace
  - Error messages include file/line information
  - Better user-facing error messages

---

## [1.7.0] - 2025-10-23

### Added
- **Logs & Debug Console** - New comprehensive logging system with centralized debug dashboard
  - Real-time activity monitoring with filterable logs
  - Error tracking and warning alerts
  - Success/Info/Debug level logging
  - System information display (WordPress, PHP, Server, Plugin status)
  - Active plugins detection (Yoast, Rank Math, ACF, Elementor)
  - CSV export functionality for logs
  - Search and filter logs by level, category, date
  - View detailed context for each log entry
  - Statistics dashboard showing errors, warnings, success count
  - Last 24h activity summary
  - One-click log clearing
  - Copy to clipboard functionality

### Improved
- **Logger Class** (`Seoengineai_Logger`)
  - Centralized logging for all plugin operations
  - 5 log levels: error, warning, info, success, debug
  - Automatic log rotation (keeps last 500 entries)
  - Context data support for detailed debugging
  - IP address tracking for security
  - User ID tracking
  - Integration with WordPress error_log when WP_DEBUG enabled

### Integration
- Logging integrated into:
  - Page generation process (start, success, failures)
  - OpenAI API calls (requests, retries, errors, success)
  - AJAX handlers (permission checks, requests)
  - Field validation
  - Content generation for each field
  - Schema application
  - Automated blog generation

### Fixed
- Improved error visibility - all errors now logged to central console
- Better debugging capabilities for generation issues
- Network error tracking for API calls
- Permission denied attempts now logged

---

## [1.6.0] - 2025-10-22

### 🎯 SEO Enhancement Suite (P6 Complete)

#### Added
- **Schema Markup Generation**
  - Automatic JSON-LD schema output for all post types
  - Multiple schema types: Article, BlogPosting, LocalBusiness, Service, FAQPage, Product
  - Intelligent schema type detection based on content
  - Supports custom schema fields and properties
  - Validates against Schema.org standards
  
- **Internal Linking System**
  - Automatic internal link insertion in content
  - Related posts detection based on categories/tags
  - Keyword extraction for natural anchor text
  - Configurable maximum links per post (default: 3)
  - Prevents duplicate linking of same keyword
  - Smart link placement in content
  
- **Meta Tag Optimization**
  - Open Graph (OG) tags for social sharing
  - Twitter Card tags for Twitter optimization
  - Automatic featured image inclusion
  - SEO-optimized meta descriptions
  - Canonical URL support
  
- **SEO Optimizer Integration**
  - Automatic schema application to generated posts
  - Schema type detection for location/service pages
  - Integration with page generation workflow
  - Post-generation SEO enhancements

#### Changed
- Generated pages now automatically include schema markup
- Internal linking enabled by default for better SEO
- Meta tags optimized for social sharing
- Enhanced SEO metadata for all generated content

#### Technical
- New class: `class-seoengineai-seo-optimizer.php`
- Schema markup output in wp_head
- Content filtering for internal links
- Open Graph and Twitter Card meta tags
- Automatic SEO application in generator
- Backup created for P6 completion

---

## [1.5.0] - 2025-10-22

### ⏰ Automated Blog Generation System (P5 Complete)

#### Added
- **WP-Cron Integration for Automated Blogging**
  - Created `Seoengineai_Cron` class for scheduled content generation
  - Custom cron schedules (every 6 hours, 12 hours, twice daily)
  - Automatic blog post generation based on configured topics
  - Topic rotation modes (random or sequential)
  - Configurable post status (draft, publish, pending)
  
- **Blog Generation Settings**
  - Enable/disable automated blog generation
  - Set generation frequency (hourly, daily, weekly, etc.)
  - Configure blog topics (one per line)
  - Select post type for generated blogs
  - Choose post status for new blogs
  - Topic rotation strategy
  
- **Content Generation Features**
  - AI-generated titles with SEO optimization
  - Comprehensive blog content (500-800 words)
  - Automatic excerpt generation
  - Keyword extraction and tagging
  - Category assignment support
  - Auto-generated post metadata tracking
  
- **Logging and Monitoring**
  - Generation attempt logging (success/failure)
  - Last 100 logs stored with timestamps
  - Automatic cleanup of logs older than 30 days
  - View next scheduled run time
  - Manual trigger option for testing

#### Changed
- Enhanced cron scheduling system
- Added custom WordPress cron intervals
- Integrated automated blog settings into Settings page
- Improved topic management system

#### Technical
- New class: `class-seoengineai-cron.php`
- Custom cron hooks: `seoengineai_generate_blog_post`, `seoengineai_cleanup_old_logs`
- Comprehensive settings integration for blog automation
- Backup created for P5 completion

---

## [1.4.0] - 2025-10-22

### 🛡️ Field Validation & Template Binding (P4 Complete)

#### Added
- **Comprehensive Field Validation System**
  - Created `Seoengineai_Field_Validator` class for data integrity
  - SEO-specific validation rules (meta titles, descriptions, slugs, alt text)
  - Type-based validation (text, textarea, WYSIWYG, URL, email)
  - Character limit enforcement with detailed error messages
  - Content structure analysis for SEO best practices
  
- **Field Preview System**
  - Live AI content preview with sample data
  - AJAX endpoint for real-time field generation testing
  - Shows character count, word count, and validation status
  - Helps users optimize prompts before bulk generation
  
- **Enhanced Generator Integration**
  - Integrated validator into page generation workflow
  - Automatic content sanitization before saving
  - Validation logging for quality assurance
  - Safety checks prevent invalid content from being saved

#### Changed
- Generator now validates all content before database insertion
- Added validator initialization in generation process
- Enhanced error logging with validation details
- Improved content quality checks with validation layer

#### Technical
- New class: `class-seoengineai-field-validator.php`
- Added `preview_field()` AJAX endpoint
- Integrated validator into `Seoengineai_Generator`
- Validation rules by field type with SEO optimization
- Backup created for P4 completion

---

## [1.3.1] - 2025-10-22

### 🔬 Intelligent SEO-Based Character Limits

#### Added
- **Research-Based SEO Character Limits**
  - Implemented 2025 SEO standards based on Google SERP display limits
  - Title Tags: 60 characters (optimal SERP display)
  - Meta Descriptions: 160 characters (optimal snippet length)
  - H1 Headings: 70 characters (primary heading with keyword)
  - H2/H3 Headings: 80 characters (subheadings)
  - Alt Text: 125 characters (accessibility + SEO)
  - URL Slugs: 75 characters (readability + SEO)
  - Excerpts: 155 characters (preview text)
  - Keywords: 50 characters (focus keyword length)
  
- **Intelligent Field Detection**
  - Auto-detects SEO-specific fields by name/label matching
  - Applies appropriate limits based on field purpose
  - Respects ACF maxlength settings when defined
  - Falls back to type-based limits for generic fields

#### Changed
- Enhanced `get_field_char_limit()` with intelligent SEO detection
- Optimized character limits for token efficiency
- Prevents content overload in specific page sections
- Reduced token waste for AI generation

#### Technical
- Added field name/label pattern matching for SEO fields
- Comprehensive SEO standards documentation in code comments
- Backup created: `class-seoengineai-field-detector.BACKUP-v1.3.1-seo-limits.php`

---

## [1.3.0] - 2025-10-22

### 🎯 Character Limit Enforcement

#### Added
- **Strict Character Limits for All Fields**
  - SEO-based character limits: Titles (60 chars), Meta Descriptions (160 chars), Excerpts (155 chars)
  - ACF field limits based on field type (text: 200, textarea: 1000, wysiwyg: 3000, etc.)
  - Respect ACF field `maxlength` settings when defined
  - Visual character limit display in UI with badges
  - Character limit enforcement in OpenAI system prompts
  - Hard truncation safety net if AI exceeds limits
  
- **Enhanced Field Detection**
  - Character limits automatically assigned based on field type and SEO best practices
  - Display char limits in field list with blue badges
  - Show char limits in expanded prompt area
  
- **Quality Check Updates**
  - Character limit validation added to quality checks
  - Log critical warnings when limits are exceeded
  - Enforce limits before saving to database

#### Changed
- All default prompts now include "STRICT REQUIREMENT" language for character limits
- OpenAI API calls now include character limit in system message
- Field HTML updated to show character limits prominently
- Quality check method signature updated to accept char_limit parameter

#### Technical
- Added `char_limit` field to all field definitions
- Added `get_field_char_limit()` helper method
- Updated `generate_content()` to accept and enforce char_limit parameter
- Updated AJAX handler to collect and pass char_limits
- Updated generator to pass char_limits to OpenAI and enforce post-generation

---

## [1.0.0] - 2025-10-22

### 🎉 Initial Release

#### Added
- **Core Plugin Infrastructure**
  - Plugin activation, deactivation, and uninstallation handlers
  - Database table creation and management
  - Version tracking and upgrade system
  - Settings API integration

- **Dashboard**
  - Welcome screen with plugin status
  - Quick action buttons
  - Plugin information display
  - Step-by-step usage guide
  - Feature overview

- **Settings Page**
  - OpenAI API key configuration
  - GPT model selection (GPT-4, GPT-3.5 Turbo)
  - Secure settings storage
  - API key validation

- **Page Generator**
  - Post type selection (WordPress default + custom post types)
  - Template selection system
    - WordPress theme templates
    - Elementor page templates
    - Custom templates
  - Service input (one per line)
  - City input (one per line)
  - Title placement options
    - City + Service (e.g., "Toronto Dentist")
    - Service + City (e.g., "Dentist in Toronto")
  - Output mode selection (Draft/Publish)
  - Real-time page count calculator

- **Field Detection System**
  - Layer 1: WordPress Core Fields
    - Post Title
    - Post Content
    - Post Excerpt
  - Layer 2: SEO Plugin Fields
    - Yoast SEO (Title, Description, Keywords, Focus Keyword)
    - Rank Math (Title, Description, Focus Keyword)
    - All in One SEO (Title, Description, Keywords)
  - Layer 3: Advanced Custom Fields (ACF)
    - Text fields
    - Textarea fields
    - WYSIWYG fields
    - URL fields
  - Layer 4: Custom Post Meta
    - Auto-detection of existing meta fields
  - Layer 5: Elementor Fields
    - Page settings
    - Custom fields
  - Layer 6: Gutenberg Blocks
    - Block attributes
    - Custom block fields

- **AI Content Generation**
  - OpenAI API integration
  - GPT-4 and GPT-3.5 Turbo support
  - Custom prompt system
  - Default prompts for all field types
  - Field-specific content generation
  - Variable replacement system
    - {service} - Current service name
    - {city} - Current city name
    - {title} - Generated page title

- **Preview System**
  - Pre-generation page preview
  - Title and slug display
  - Page count summary
  - Template information
  - Field count display
  - Confirmation modal

- **Progress Tracking**
  - Real-time progress bar
  - Status updates during generation
  - Success/failure counts
  - Error handling and display
  - Generation completion summary

- **Admin Interface**
  - Modern, clean design
  - WordPress native styling
  - Responsive layout
  - Intuitive form controls
  - Modal dialogs
  - Loading states
  - Error notifications

- **Build System**
  - Automated build script
  - Version extraction from main file
  - ZIP packaging
  - Builds directory management
  - Proper folder structure (seo-engine-ai/)

#### Security
- Nonce verification on all AJAX requests
- Capability checks (manage_options required)
- Input sanitization and validation
- Secure API key storage
- XSS prevention
- SQL injection prevention

#### Performance
- Efficient field detection
- Optimized database queries
- AJAX-based asynchronous operations
- Minimal resource footprint
- Proper asset loading (only on plugin pages)

---

## [1.0.1] - 2025-10-22

### 🔧 Fixed
- **Template Detection Enhancement**
  - Added support for custom post type templates
  - Now detects theme-specific templates (e.g., `single-{post_type}.php`, `archive-{post_type}.php`)
  - Added Elementor Single and Archive templates for custom post types
  - Improved template detection using both `wp_get_theme()` and `get_page_templates()`
  
- **Build System**
  - Fixed build script to preserve all previous version zip files
  - Now creates timestamped backups if rebuilding the same version
  - No longer deletes old builds - keeps complete version history

### 🎯 Enhanced
- Custom post types (like Location Pages) now show all applicable templates
- Better Elementor template detection and labeling
- Incremental backup system for all builds

---

## [Unreleased]

### Planned for v1.1.0
- Cron-based scheduled generation
- Blog post automation
- Content variation strategies
- Batch processing optimization
- Generation history and logs
- Undo/rollback functionality

### Planned for v2.0.0
- Server-side SaaS integration
- Token usage tracking
- Credit system
- Advanced analytics
- Multi-user support
- Team management

---

## Version History Summary

| Version | Date       | Status    | Description                |
|---------|------------|-----------|----------------------------|
| 1.0.0   | 2025-10-22 | Released  | Initial public release     |
| 0.0.7   | 2025-10-22 | Beta      | Settings fix               |
| 0.0.6   | 2025-10-22 | Beta      | Template loading fix       |
| 0.0.5   | 2025-10-22 | Beta      | Build script fix           |
| 0.0.4   | 2025-10-22 | Alpha     | Activation fix             |
| 0.0.3   | 2025-10-22 | Alpha     | Database improvements      |
| 0.0.2   | 2025-10-22 | Alpha     | Core structure             |
| 0.0.1   | 2025-10-22 | Alpha     | Initial development build  |

---

**Note**: This project follows [Semantic Versioning](https://semver.org/)
- **Major version** (X.0.0): Breaking changes
- **Minor version** (0.X.0): New features, backward compatible
- **Patch version** (0.0.X): Bug fixes, backward compatible
