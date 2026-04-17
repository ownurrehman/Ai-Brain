# SEO Engine AI v1.0.0 — Testing Guide

**Version:** 1.0.0  
**Date:** October 22, 2025  
**Status:** Ready for Testing

---

## 🎯 Testing Overview

This guide provides comprehensive testing procedures for SEO Engine AI v1.0.0. Follow these steps to ensure all features work correctly before production deployment.

---

## 📋 Pre-Testing Requirements

### Environment Setup
- ✅ WordPress 5.8 or higher
- ✅ PHP 7.4 or higher
- ✅ MySQL/MariaDB database
- ✅ Admin access to WordPress
- ✅ OpenAI API key (for full testing)

### Optional Plugins (for complete testing)
- Yoast SEO (for SEO field testing)
- Rank Math (for SEO field testing)
- Advanced Custom Fields (for ACF testing)
- Elementor (for page builder testing)

---

## 🔧 Installation Testing

### Test 1: Plugin Installation
**Objective:** Verify plugin installs correctly

**Steps:**
1. Navigate to WordPress admin
2. Go to Plugins → Add New → Upload Plugin
3. Upload `seo-engine-ai.v1.0.0.zip`
4. Click "Install Now"

**Expected Results:**
- ✅ Upload completes without errors
- ✅ Installation succeeds
- ✅ "Activate Plugin" link appears

**Pass Criteria:**
- No errors during upload or installation
- Plugin files extracted to `/wp-content/plugins/seo-engine-ai/`

---

### Test 2: Plugin Activation
**Objective:** Verify plugin activates without errors

**Steps:**
1. Click "Activate Plugin" or
2. Go to Plugins → Installed Plugins
3. Find "SEO Engine AI"
4. Click "Activate"

**Expected Results:**
- ✅ Activation completes without fatal errors
- ✅ "SEO Engine AI" menu appears in admin sidebar
- ✅ Database table created: `{prefix}_seoengineai_logs`
- ✅ Version option set: `seoengineai_version` = `1.0.0`

**Pass Criteria:**
- No PHP errors or warnings
- Admin menu visible
- Database table exists

**How to Verify Database:**
```sql
SHOW TABLES LIKE '%seoengineai%';
SELECT option_value FROM wp_options WHERE option_name = 'seoengineai_version';
```

---

## ⚙️ Settings Testing

### Test 3: Settings Page Access
**Objective:** Verify settings page loads correctly

**Steps:**
1. Click "SEO Engine AI" in admin menu
2. Click "Settings" submenu

**Expected Results:**
- ✅ Settings page loads without errors
- ✅ "OpenAI API Key" field visible
- ✅ "OpenAI Model" dropdown visible
- ✅ "Save Changes" button visible

**Pass Criteria:**
- Page loads in < 2 seconds
- No JavaScript console errors
- All form elements present

---

### Test 4: API Key Configuration
**Objective:** Verify API key can be saved

**Steps:**
1. Go to Settings page
2. Enter a test API key: `sk-test1234567890`
3. Select "GPT-4" from model dropdown
4. Click "Save Changes"

**Expected Results:**
- ✅ Success notice appears: "Settings saved"
- ✅ API key stored in database
- ✅ Model selection saved
- ✅ Page refreshes with saved values

**How to Verify:**
```sql
SELECT option_value FROM wp_options WHERE option_name = 'seoengineai_openai_api_key';
SELECT option_value FROM wp_options WHERE option_name = 'seoengineai_openai_model';
```

**Pass Criteria:**
- Settings saved successfully
- Values persist after page refresh
- No security errors

---

### Test 5: Invalid API Key
**Objective:** Verify error handling for invalid keys

**Steps:**
1. Enter invalid characters: `<script>alert('test')</script>`
2. Click "Save Changes"

**Expected Results:**
- ✅ Input sanitized (script tags removed)
- ✅ Saved value: `scriptalert('test')/script`
- ✅ XSS prevented

**Pass Criteria:**
- No script execution
- Input properly sanitized

---

## 🏠 Dashboard Testing

### Test 6: Dashboard Page
**Objective:** Verify dashboard displays correctly

**Steps:**
1. Click "SEO Engine AI" main menu
2. Review dashboard content

**Expected Results:**
- ✅ Dashboard loads without errors
- ✅ Plugin information displayed
- ✅ Version shows: 1.0.0
- ✅ API status indicator present
- ✅ Quick action buttons visible
- ✅ Feature list displayed
- ✅ How-to guide visible

**Pass Criteria:**
- All sections render correctly
- Links work properly
- Styling is clean and professional

---

### Test 7: API Status Indicator
**Objective:** Verify API status shows correctly

**Steps:**
1. View dashboard without API key
2. Add API key in settings
3. Return to dashboard

**Expected Results:**
- ✅ Without key: Shows "✗ Not Configured" in red
- ✅ With key: Shows "✓ Configured" in green

**Pass Criteria:**
- Status updates correctly
- Colors display properly

---

## 🎨 Generator UI Testing

### Test 8: Generator Page Load
**Objective:** Verify generator page loads correctly

**Steps:**
1. Click "Generate Pages" submenu
2. Review page layout

**Expected Results:**
- ✅ Page loads without errors
- ✅ All form sections visible:
  - Post type selector
  - Template selector (disabled initially)
  - Services textarea
  - Cities textarea
  - Placement options
  - Output mode options
  - Page count: 0
- ✅ Fields section hidden initially
- ✅ Generate button disabled

**Pass Criteria:**
- Clean layout
- All elements present
- No console errors

---

### Test 9: Post Type Selection
**Objective:** Verify post type dropdown works

**Steps:**
1. Click post type dropdown
2. Select "Pages"

**Expected Results:**
- ✅ Dropdown contains:
  - Posts
  - Pages
  - Any custom post types
- ✅ Template selector enables
- ✅ "Loading templates..." message appears
- ✅ Templates load via AJAX

**Pass Criteria:**
- AJAX request successful
- Templates populate correctly
- No errors in console

**How to Verify AJAX:**
Open browser DevTools → Network tab → Look for:
- Request to `admin-ajax.php`
- Action: `seoengineai_get_templates`
- Response: JSON with template options

---

### Test 10: Template Selection
**Objective:** Verify template loading and field detection

**Steps:**
1. Select post type "Pages"
2. Wait for templates to load
3. Select "Default Template"

**Expected Results:**
- ✅ Fields section appears
- ✅ "Loading fields..." spinner shows
- ✅ Fields load via AJAX
- ✅ Core fields displayed:
  - Post Title
  - Post Content
  - Post Excerpt
- ✅ Each field has:
  - Checkbox (unchecked)
  - Default prompt (disabled textarea)

**Pass Criteria:**
- Fields load in < 1 second
- At least 3 core fields present
- All UI elements functional

---

### Test 11: Elementor Template Filtering
**Objective:** Verify Elementor templates only show for pages

**Steps:**
1. Select post type "Posts"
2. Check template dropdown
3. Change to post type "Pages"
4. Check template dropdown again

**Expected Results:**
- ✅ Posts: No Elementor templates
- ✅ Pages: Elementor templates included (if Elementor is active)

**Pass Criteria:**
- Elementor templates filtered correctly
- No errors when switching post types

---

### Test 12: SEO Plugin Field Detection
**Objective:** Verify SEO plugin fields are detected

**Prerequisites:**
- Install and activate Yoast SEO OR Rank Math

**Steps:**
1. Select post type "Posts"
2. Select any template
3. Review detected fields

**Expected Results (Yoast SEO):**
- ✅ SEO Title (Yoast)
- ✅ Meta Description (Yoast)
- ✅ Focus Keyword (Yoast)

**Expected Results (Rank Math):**
- ✅ SEO Title (Rank Math)
- ✅ Meta Description (Rank Math)
- ✅ Focus Keyword (Rank Math)

**Pass Criteria:**
- SEO fields detected automatically
- Default prompts generated
- Fields grouped under "SEO Fields"

---

### Test 13: ACF Field Detection
**Objective:** Verify ACF fields are detected

**Prerequisites:**
- Install and activate ACF
- Create field group for "Posts"
- Add text field "custom_subtitle"

**Steps:**
1. Select post type "Posts"
2. Select template
3. Review detected fields

**Expected Results:**
- ✅ "custom_subtitle" field appears
- ✅ Listed under "Custom Fields (ACF)"
- ✅ Default prompt generated

**Pass Criteria:**
- ACF fields auto-detected
- Template-specific detection works
- Fields properly labeled

---

### Test 14: Page Count Calculator
**Objective:** Verify real-time calculation

**Steps:**
1. Enter services:
   ```
   Dentist
   Plumber
   Electrician
   ```
2. Enter cities:
   ```
   Toronto
   Montreal
   ```
3. Observe page count

**Expected Results:**
- ✅ Count updates in real-time
- ✅ Shows: 6 (3 services × 2 cities)
- ✅ Updates on every keystroke

**Test Variations:**
- 5 services × 5 cities = 25 pages
- 1 service × 10 cities = 10 pages
- 0 services × 5 cities = 0 pages

**Pass Criteria:**
- Calculation always correct
- Updates immediately
- No lag or delay

---

### Test 15: Field Selection
**Objective:** Verify field checkbox functionality

**Steps:**
1. Load fields for a template
2. Click checkbox for "Post Title"
3. Click checkbox for "Post Content"

**Expected Results:**
- ✅ Checkbox changes to checked state
- ✅ Prompt textarea enables
- ✅ Prompt contains default text
- ✅ Prompt is editable

**Pass Criteria:**
- Checkboxes respond immediately
- Textareas enable/disable correctly
- No JavaScript errors

---

### Test 16: Generate Button State
**Objective:** Verify button enables/disables correctly

**Conditions to Test:**

| Services | Cities | Fields | Expected State |
|----------|--------|--------|----------------|
| Empty    | Empty  | None   | Disabled       |
| Filled   | Empty  | None   | Disabled       |
| Filled   | Filled | None   | Disabled       |
| Filled   | Filled | 1+     | **Enabled**    |

**Pass Criteria:**
- Button only enables when all conditions met
- State updates in real-time

---

## 🔍 Preview System Testing

### Test 17: Preview Modal
**Objective:** Verify preview displays correctly

**Steps:**
1. Enter services:
   ```
   Dentist
   Plumber
   ```
2. Enter cities:
   ```
   Toronto
   Montreal
   ```
3. Select placement: "City + Service"
4. Check "Post Title" field
5. Click "Generate Pages"

**Expected Results:**
- ✅ Modal appears with overlay
- ✅ Summary section shows:
  - Total Pages: 4
  - Post Type: Posts (or selected type)
  - Template: Default Template (or selected)
  - Fields to Generate: 1
- ✅ Preview list shows 4 items:
  1. Title: "Toronto Dentist", Slug: "toronto-dentist"
  2. Title: "Toronto Plumber", Slug: "toronto-plumber"
  3. Title: "Montreal Dentist", Slug: "montreal-dentist"
  4. Title: "Montreal Plumber", Slug: "montreal-plumber"
- ✅ "Confirm Generation" button visible
- ✅ "Cancel" button visible

**Pass Criteria:**
- All titles correct
- All slugs correct
- Modal centered and styled
- Scrollable if many pages

---

### Test 18: Preview - Service + City Placement
**Objective:** Verify alternate placement works

**Steps:**
1. Same as Test 17, but select "Service + City"
2. Click "Generate Pages"

**Expected Results:**
- ✅ Preview list shows:
  1. Title: "Dentist in Toronto", Slug: "dentist-in-toronto"
  2. Title: "Plumber in Toronto", Slug: "plumber-in-toronto"
  3. Title: "Dentist in Montreal", Slug: "dentist-in-montreal"
  4. Title: "Plumber in Montreal", Slug: "plumber-in-montreal"

**Pass Criteria:**
- Title format changes correctly
- Slugs updated to match

---

### Test 19: Preview Modal Actions
**Objective:** Verify modal buttons work

**Steps:**
1. Open preview modal
2. Click "Cancel"
3. Open modal again
4. Click "Confirm Generation"

**Expected Results:**
- ✅ Cancel: Modal closes, no generation
- ✅ Confirm: Modal closes, generation starts

**Pass Criteria:**
- Buttons respond correctly
- Modal closes smoothly

---

## 🚀 Content Generation Testing

### Test 20: Mock Generation (No API Key)
**Objective:** Verify error handling without API key

**Steps:**
1. Remove API key from settings
2. Complete generator form
3. Confirm preview

**Expected Results:**
- ✅ Error message appears
- ✅ Message indicates missing API key
- ✅ Link to settings page

**Pass Criteria:**
- Clear error message
- No PHP errors
- Generation stops gracefully

---

### Test 21: Full Generation (With API Key)
**Objective:** Verify complete generation workflow

**Prerequisites:**
- Valid OpenAI API key configured
- API key with available credits

**Steps:**
1. Select post type: "Posts"
2. Select template: "Default Template"
3. Enter services:
   ```
   Web Design
   SEO Services
   ```
4. Enter cities:
   ```
   New York
   Los Angeles
   ```
5. Select placement: "City + Service"
6. Select output mode: "Draft"
7. Check fields:
   - ✅ Post Title
   - ✅ Post Content
   - ✅ Post Excerpt
8. Edit prompt for Post Content:
   ```
   Write a comprehensive guide about {service} services in {city}. Include benefits, process, and why choose local services.
   ```
9. Click "Generate Pages"
10. Confirm preview

**Expected Results:**
- ✅ Progress section appears
- ✅ Progress bar animates
- ✅ Status text updates
- ✅ Generation completes
- ✅ Success message shows:
  - Created: 4 pages
  - Failed: 0 pages
- ✅ "View Pages" link appears

**Pass Criteria:**
- All 4 pages created
- Pages have correct titles
- Content generated by AI
- Excerpts generated by AI
- All pages in "Draft" status
- No errors during generation

---

### Test 22: Verify Generated Content
**Objective:** Verify pages created correctly

**Steps:**
1. Click "View Pages" link (or go to Posts)
2. Open each generated post
3. Review content

**Expected Content:**

**Post 1: "New York Web Design"**
- ✅ Title: "New York Web Design"
- ✅ Slug: "new-york-web-design"
- ✅ Status: Draft
- ✅ Content: AI-generated article about web design in New York
- ✅ Excerpt: AI-generated summary
- ✅ Variables replaced correctly

**Post 2: "New York SEO Services"**
- ✅ Title: "New York SEO Services"
- ✅ Content mentions SEO and New York

**Post 3: "Los Angeles Web Design"**
- ✅ Title: "Los Angeles Web Design"
- ✅ Content mentions web design and Los Angeles

**Post 4: "Los Angeles SEO Services"**
- ✅ Title: "Los Angeles SEO Services"
- ✅ Content mentions SEO and Los Angeles

**Pass Criteria:**
- All pages exist
- Titles correct
- Slugs correct
- Content relevant and unique
- Variables replaced
- No duplicate content

---

### Test 23: Publish Mode
**Objective:** Verify publish mode works

**Steps:**
1. Complete generator form
2. Select output mode: "Publish"
3. Generate pages

**Expected Results:**
- ✅ Pages created with "Published" status
- ✅ Pages visible on frontend
- ✅ Pages appear in sitemap

**Pass Criteria:**
- Status is "publish" not "draft"
- Pages accessible by URL

---

### Test 24: Large Batch Generation
**Objective:** Test performance with many pages

**Steps:**
1. Enter 5 services
2. Enter 10 cities
3. Generate 50 pages

**Expected Results:**
- ✅ Preview shows all 50 pages
- ✅ Generation completes successfully
- ✅ Progress updates smoothly
- ✅ No timeout errors
- ✅ No memory errors

**Pass Criteria:**
- All 50 pages created
- No errors or warnings
- Reasonable completion time (< 5 minutes)

---

## 🔒 Security Testing

### Test 25: Nonce Verification
**Objective:** Verify AJAX requests require valid nonce

**Steps:**
1. Open browser DevTools → Console
2. Run invalid AJAX request:
```javascript
jQuery.post(ajaxurl, {
    action: 'seoengineai_get_templates',
    nonce: 'invalid_nonce',
    post_type: 'post'
}, function(response) {
    console.log(response);
});
```

**Expected Results:**
- ✅ Request fails
- ✅ Error response returned
- ✅ No templates returned

**Pass Criteria:**
- Nonce verification works
- Invalid requests blocked

---

### Test 26: Capability Check
**Objective:** Verify only admins can access

**Steps:**
1. Log in as Editor or Subscriber
2. Try to access SEO Engine AI menu

**Expected Results:**
- ✅ Menu not visible to non-admins
- ✅ Direct URL access blocked
- ✅ Permission error shown

**Pass Criteria:**
- Only users with `manage_options` can access

---

### Test 27: Input Sanitization
**Objective:** Verify XSS prevention

**Test Inputs:**
```
Service: <script>alert('XSS')</script>
City: <img src=x onerror=alert('XSS')>
```

**Expected Results:**
- ✅ Scripts stripped or escaped
- ✅ No script execution
- ✅ Safe content in database

**Pass Criteria:**
- All inputs sanitized
- No XSS vulnerabilities

---

## 🔧 Plugin Management Testing

### Test 28: Plugin Deactivation
**Objective:** Verify clean deactivation

**Steps:**
1. Deactivate plugin
2. Check for scheduled tasks
3. Check for transients

**Expected Results:**
- ✅ Plugin deactivates cleanly
- ✅ Scheduled tasks removed
- ✅ Transients cleared
- ✅ Data preserved

**Pass Criteria:**
- No errors on deactivation
- Cleanup runs successfully
- User data not deleted

---

### Test 29: Plugin Reactivation
**Objective:** Verify settings persist

**Steps:**
1. Deactivate plugin (with API key configured)
2. Reactivate plugin
3. Check settings

**Expected Results:**
- ✅ API key still present
- ✅ Model selection preserved
- ✅ No data loss

**Pass Criteria:**
- Settings persist across deactivation

---

### Test 30: Plugin Update
**Objective:** Verify upgrade works correctly

**Steps:**
1. Activate v1.0.0
2. Check version in database
3. Simulate upgrade by running activator again

**Expected Results:**
- ✅ Version updates in database
- ✅ Database schema updated (if changes)
- ✅ No data loss

**Pass Criteria:**
- Upgrade mechanism works
- Data migrated correctly

---

### Test 31: Plugin Uninstallation
**Objective:** Verify complete cleanup

**Steps:**
1. Set constant: `define('SEOENGINEAI_ALLOW_UNINSTALL_PURGE', true);` in wp-config.php
2. Deactivate plugin
3. Delete plugin
4. Check database

**Expected Results:**
- ✅ All tables removed
- ✅ All options removed
- ✅ All transients removed

**Pass Criteria:**
- Complete cleanup
- No orphaned data

---

## 📊 Performance Testing

### Test 32: Page Load Speed
**Objective:** Measure admin page performance

**Metrics to Check:**
- Dashboard load time: < 2 seconds
- Generator load time: < 2 seconds
- Settings load time: < 1 second

**Tools:**
- Browser DevTools → Performance tab

**Pass Criteria:**
- All pages load quickly
- No significant lag

---

### Test 33: AJAX Response Time
**Objective:** Measure AJAX performance

**Metrics:**
- Get templates: < 500ms
- Get fields: < 1 second
- Generate pages: Varies (depends on OpenAI)

**Pass Criteria:**
- AJAX requests responsive
- No unnecessary delays

---

### Test 34: Memory Usage
**Objective:** Verify efficient memory use

**Steps:**
1. Generate 50 pages
2. Monitor memory usage

**Expected Results:**
- ✅ Peak memory < 128MB
- ✅ No memory exhaustion errors

**Pass Criteria:**
- Works on standard shared hosting
- No memory leaks

---

## ✅ Final Checklist

### Installation & Activation
- [ ] Plugin installs without errors
- [ ] Plugin activates successfully
- [ ] Database tables created
- [ ] Version set correctly

### Settings
- [ ] Settings page loads
- [ ] API key saves correctly
- [ ] Model selection works
- [ ] Input sanitized

### Dashboard
- [ ] Dashboard loads correctly
- [ ] All sections display
- [ ] Links work
- [ ] Status indicator works

### Generator UI
- [ ] Page loads without errors
- [ ] Post types populate
- [ ] Templates load via AJAX
- [ ] Fields load via AJAX
- [ ] Page count calculates
- [ ] Checkboxes work
- [ ] Prompts editable
- [ ] Button states correct

### Field Detection
- [ ] Core fields detected
- [ ] SEO plugin fields detected
- [ ] ACF fields detected
- [ ] Custom meta detected
- [ ] Elementor filtered correctly

### Preview System
- [ ] Modal appears correctly
- [ ] Titles displayed
- [ ] Slugs displayed
- [ ] Both placements work
- [ ] Buttons functional

### Content Generation
- [ ] Pages created successfully
- [ ] Content populated
- [ ] Templates applied
- [ ] Status correct (draft/publish)
- [ ] Variables replaced
- [ ] No errors

### Security
- [ ] Nonce verification works
- [ ] Capability checks work
- [ ] Input sanitized
- [ ] Output escaped
- [ ] XSS prevented

### Plugin Management
- [ ] Deactivation clean
- [ ] Reactivation preserves data
- [ ] Updates work correctly
- [ ] Uninstall cleanup complete

### Performance
- [ ] Pages load quickly
- [ ] AJAX responsive
- [ ] Memory efficient
- [ ] No errors or warnings

---

## 📝 Test Results Template

```
# SEO Engine AI v1.0.0 Test Results

**Tester:** [Your Name]
**Date:** [Test Date]
**Environment:**
- WordPress Version: X.X.X
- PHP Version: X.X.X
- Theme: [Theme Name]
- Plugins: [List active plugins]

## Test Results

| Test # | Test Name | Status | Notes |
|--------|-----------|--------|-------|
| 1 | Plugin Installation | ✅ PASS | |
| 2 | Plugin Activation | ✅ PASS | |
| ... | ... | ... | ... |

## Issues Found

1. [Issue description]
   - Severity: High/Medium/Low
   - Steps to reproduce:
   - Expected vs Actual:

## Overall Assessment

- Total Tests: XX
- Passed: XX
- Failed: XX
- Success Rate: XX%

## Recommendation

[ ] Approve for production
[ ] Needs fixes before release
[ ] Requires additional testing
```

---

## 🎯 Success Criteria

**Plugin is READY FOR PRODUCTION if:**
- ✅ All critical tests pass (1-23)
- ✅ No security vulnerabilities
- ✅ No fatal errors
- ✅ Core functionality works
- ✅ Performance acceptable

**Plugin needs MORE WORK if:**
- ❌ Any critical test fails
- ❌ Security issues found
- ❌ Fatal errors occur
- ❌ Core features broken
- ❌ Performance poor

---

**Happy Testing!** 🚀

For questions or to report issues:
- Email: support@rankray.com
- Include test results template
