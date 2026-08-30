> **Parent Hub:** [[skills/_archived-2026-08-28/INDEX|📦 Archived Skills Hub]] · [[skills/_CATALOG_MAP|⚡ Skills Catalog]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Rank Ray Location Page Updater

## Description
Automated tool for updating Rank Ray location pages (SEO Agency and Digital Marketing Agency) via WordPress REST API with ACF custom fields.

## Capabilities
- List all location pages with IDs and status
- Update individual or bulk location pages
- Refresh content for specific cities or page types
- Validate content against SEO rules before publishing
- Generate location-specific content using templates

## File Location
`/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/scripts/rankray-location-updater.py`

## Rules Reference
`/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/rankray-location-pages.md`

## Usage

### List all location pages
```bash
python3 scripts/rankray-location-updater.py --list
```

### Update a single page
```bash
python3 scripts/rankray-location-updater.py --id 19251 --type seo --city "Toronto"
```

### Bulk update all SEO agency pages
```bash
python3 scripts/rankray-location-updater.py --bulk --type seo
```

### Update specific city
```bash
python3 scripts/rankray-location-updater.py --city "Dubai" --type both
```

### Dry run (preview changes without applying)
```bash
python3 scripts/rankray-location-updater.py --id 19251 --dry-run
```

## API Authentication
Credentials loaded from `master-env.env`:
- WP_BASE_URL: https://rankray.com/wp-json/wp/v2/
- WP_USERNAME: openclaw
- WP_APP_PASSWORD: (from env)

## Content Templates
Templates stored in `templates/seo-agency/` and `templates/digital-marketing/`:
- `hero-section.md` - H1 and intro paragraphs
- `services-section.md` - 9 service cards
- `why-choose-section.md` - 6 feature boxes
- **`faq-section.md`** - 7 Q&A pairs

## Validation Rules
1. Meta description < 160 characters
2. Exact match keyword + LSI + "Rank Ray"
3. No double dashes
4. No emojis
5. City name appears naturally throughout
6. Internal links verified against sitemap

## Dependencies
- Python 3.8+
- requests library
- python-dotenv

## Author
Rank Ray - Enigma Agent
Last Updated: 2026-05-08
