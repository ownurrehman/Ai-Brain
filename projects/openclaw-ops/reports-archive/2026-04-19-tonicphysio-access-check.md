## Access Report: tonicphysio.com

### REST API: WORKING
- Public REST API returns 200, full schema available
- Authenticated REST API works with Application Password (email + app password)
- All core endpoints functional: posts, pages, media, types, users, taxonomies

### WP Admin: ACCESSIBLE
- wp-admin returns 200 with auth credentials
- Full admin dashboard access confirmed

### Auth Level: Administrator
- User: Dan Torres (ID: 10, slug: dan, email: tonicphysioseo@gmail.com)
- Role: administrator
- All core capabilities confirmed: edit/delete/publish posts, pages, media, users, plugins, themes, options
- WooCommerce management capabilities present
- Yoast SEO management (wpseo_manage_options) present
- Rank Math SEO capabilities present
- Elementor Pro capabilities present

### Capabilities: FULL
- **Posts:** create, read, update, delete (verified: created test draft ID 12094, deleted)
- **Pages:** read, edit confirmed (3 pages visible)
- **Media:** upload and delete confirmed (test upload ID 12095, deleted)
- **Users:** full management (edit_users, delete_users, create_users, promote_users)
- **Plugins:** install, activate, update, delete
- **Themes:** switch, edit, install, update, delete
- **SEO:** Yoast REST API active, Yoast data on posts/pages confirmed
- **Elementor Pro:** active, REST API available
- **Custom post types:** blocks, templates, navigation, elementor_library, elementskit_content, elementskit_template, templately_library, e-floating-buttons
- **Taxonomies:** categories, tags, nav_menu, wp_pattern_category

### Blockers: MINOR
1. **DNS resolution issue from macOS:** `curl` and `ping` fail to resolve tonicphysio.com directly. Workaround: use `--resolve 'tonicphysio.com:443:145.79.29.129'` in curl. Likely a local DNS cache issue (router at 192.168.100.1 resolves fine). Fix: `sudo dscacheutil -flushcache; sudo killall -HUP mDNSResponder`
2. **ACF REST API:** ACF v2 and v3 endpoints return 404. ACF plugin is installed (evidenced by `_acf_changed` in meta and `acf-disabled` status in post statuses), but REST API routes are not registered. ACF field data returns empty arrays on posts. This is a configuration issue, not an access issue. Fix: enable "ACF REST API" in ACF settings, or install ACF to REST API plugin.
3. **App password username mismatch:** The env var `TONIC_PHYSIO_REST_USER` is set to "Openclaw" but the working app password is tied to the email-based user "tonicphysioseo@gmail.com". The "Openclaw" username auth returns 401. Either the app password was revoked for that user, or the username is incorrect.

### Detected Plugins (from namespaces + HTML source)
- Elementor + Elementor Pro
- ElementsKit (elementskit)
- Essential Addons for Elementor Lite
- LiteSpeed Cache
- Wordfence (REST namespace present, 200 on auth)
- Yoast SEO (v27.3)
- Rank Math SEO
- All in One SEO (aioseo capabilities present)
- Contact Form 7
- Duplicate Post
- Google Site Kit
- Hostinger Tools
- Image Optimizer
- Link Whisper
- Redirection
- Simple History
- Templately
- Trustindex
- WPCode (Insert Headers and Footers)
- ACF (Advanced Custom Fields)
- WooCommerce
- Blog2Social
- WP AI (wpaicg)
- PublishPress Authors
- Breadcrumb NavXT (bcn)
- WP Custom CSS JS
- Contact Form 7 Database (cfdb7)
- Wordfence 2FA (wf2fa)

### Hosting
- Platform: Hostinger (hPanel)
- CDN: Hostinger CDN (hcdn)
- PHP: 8.3.30
- Server: hcdn (edge nodes)

### Recommended fixes
1. **Flush local DNS cache:** Run `sudo dscacheutil -flushcache; sudo killall -HUP mDNSResponder` to fix macOS DNS resolution for tonicphysio.com. This is a local issue, not a server issue.
2. **Fix app password for "Openclaw" user:** The `TONIC_PHYSIO_REST_USER=Openclaw` credential is not working. Either regenerate the app password for that user in wp-admin, or update the .env to use `tonicphysioseo@gmail.com` as the REST user (which works).
3. **Enable ACF REST API:** If ACF custom fields are needed for content operations, enable the ACF REST API in ACF settings (or add a filter: `add_filter('acf/settings/rest_api_enabled', '__return_true')`). Currently ACF data is not accessible via REST.
4. **Note:** Both Yoast SEO and Rank Math SEO are active. Running two SEO plugins is generally not recommended as they can conflict. Consider standardizing on one.