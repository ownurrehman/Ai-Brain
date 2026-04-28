# Quick Start - WordPress API Pro

## ✅ Already Configured Sites

### Tonic Physio
- **Site ID:** `tonicphysio`
- **URL:** https://tonicphysio.com
- **User:** Dan
- **Status:** Ready to use

### Rank Ray
- **Site ID:** `rankray`
- **URL:** https://www.rankray.com
- **User:** openclaw
- **Status:** Ready to use

## Commands

### List Sites
```bash
cd /Users/sheikhown/.openclaw/workspace/skills/wordpress-api-pro
./wp.sh --list-sites
```

### Update ACF Fields (Herniated Disc Example)
```bash
# Get current ACF fields
python3 scripts/acf_fields.py \
  --url "https://tonicphysio.com" \
  --username "Dan" \
  --app-password "4vFk 18fN UlLB twaw B2hU 0kRE" \
  --post-id 6996

# Set ACF fields from JSON
python3 scripts/acf_fields.py \
  --url "https://tonicphysio.com" \
  --username "Dan" \
  --app-password "4vFk 18fN UlLB twaw B2hU 0kRE" \
  --post-id 6996 \
  --set '{
    "h1": "Get Rid of Herniated Disc Pain in Milton",
    "paragraph_1": "A slipped or herniated disc...",
    "h2": "Expert Care for Disc Injuries in Milton"
  }'
```

### Upload Media
```bash
python3 scripts/upload_media.py \
  --url "https://tonicphysio.com" \
  --username "Dan" \
  --app-password "4vFk 18fN UlLB twaw B2hU 0kRE" \
  --file "/path/to/image.jpg" \
  --title "Herniated Disc Treatment" \
  --alt-text "Expert herniated disc treatment in Milton"
```

### Update Page Content
```bash
python3 scripts/update_post.py \
  --url "https://tonicphysio.com" \
  --username "Dan" \
  --app-password "4vFk 18fN UlLB twaw B2hU 0kRE" \
  --post-id 6996 \
  --content "<!-- wp:paragraph -->New content<!-- /wp:paragraph -->" \
  --status "publish"
```

## Wrapper Script

Use `./wp.sh` for simpler commands:

```bash
# Update post on tonicphysio
./wp.sh tonicphysio update-post --id 6996 --content "New content"

# Get ACF fields
./wp.sh tonicphysio acf-fields --id 6996

# List all posts
./wp.sh tonicphysio list-posts --per-page 5
```

## Environment Variables (Optional)

Add to `~/.openclaw/.env`:
```bash
export WP_URL="https://tonicphysio.com"
export WP_USERNAME="Dan"
export WP_APP_PASSWORD="4vFk 18fN UlLB twaw B2hU 0kRE"
```

Then omit credentials from commands.
