import pandas as pd, re, os
filepath = '/Users/sheikhown/.hermes/document_cache/doc_2c4d989b4ffc_teammotorcycle_seo.xlsx'
df = pd.read_excel(filepath, sheet_name='Sheet1')

# Identify key columns
slug_col = 'SEO Slug'
h1_col = 'Product Name & H1'
desc_col = 'Product Description'
title_col = 'Meta Title'
meta_col = 'Meta Description'

# Work on unique products, then merge back to all rows
unique_slugs = df[slug_col].unique()

# Helper: clean text

def clean(text):
    if pd.isna(text):
        return ''
    text = str(text).strip()
    text = text.replace('–','-').replace('—','-')
    text = re.sub(r'\s+', ' ', text)
    return text

def smart_shorten(full_name, max_len=55):
    """Shorten product name for meta title or H1."""
    full = clean(full_name)
    # Remove repetitive brand/vendor noise at start
    full = re.sub(r'^(High Mileage\s+)', '', full, flags=re.IGNORECASE)
    full = re.sub(r'^(Vance Leather\s+)', '', full, flags=re.IGNORECASE)
    full = re.sub(r'^(Men\'s?|Women\'s?|Mens|Womens)\s+', '', full, flags=re.IGNORECASE)
    full = re.sub(r'^HMM\d+[A-Z]*\s*', '', full)
    full = re.sub(r'^HML\d+[A-Z]*\s*', '', full)
    full = re.sub(r'^VL\d+[A-Z]*\s*', '', full)
    full = re.sub(r'^MV\d+[A-Z]*\s*', '', full)
    # Trim to a logical phrase length
    words = full.split()
    short = ''
    for w in words:
        if len(short) + len(w) + 1 <= max_len:
            short += (' ' + w if short else w)
        else:
            break
    return short.strip() or ' '.join(words[:6])

def generate_meta_title(product_name, slug):
    base = smart_shorten(product_name, max_len=38)
    suffix = 'Team Motorcycle'
    title = f"{base} | {suffix}"
    if len(title) > 60:
        base = smart_shorten(product_name, max_len=30)
        title = f"{base} | {suffix}"
    if len(title) > 60:
        base = smart_shorten(product_name, max_len=22)
        title = f"{base} | {suffix}"
    return title

def generate_h1(product_name):
    # Keep H1 longer than meta title but under 65 chars for readability
    full = clean(product_name)
    # Remove SKU prefixes
    full = re.sub(r'^(High Mileage\s+)', '', full, flags=re.IGNORECASE)
    full = re.sub(r'^(Vance Leather\s+)', '', full, flags=re.IGNORECASE)
    full = re.sub(r'^(Men\'s?|Women\'s?|Mens|Womens)\s+', '', full, flags=re.IGNORECASE)
    full = re.sub(r'^HMM\d+[A-Z]*\s*', '', full)
    full = re.sub(r'^HML\d+[A-Z]*\s*', '', full)
    full = re.sub(r'^VL\d+[A-Z]*\s*', '', full)
    full = re.sub(r'^MV\d+[A-Z]*\s*', '', full)
    if len(full) > 65:
        words = full.split()
        short = ''
        for w in words:
            if len(short) + len(w) + 1 <= 62:
                short += (' ' + w if short else w)
            else:
                break
        return short.strip() or ' '.join(words[:8])
    return full

def generate_meta_description(product_name, slug, existing_desc=''):
    # Create a fresh 150-160 char description with keyword, benefit, brand
    h1_base = generate_h1(product_name)
    # Extract product type from slug for exact match keyword
    slug_clean = clean(slug).replace('-', ' ')
    # Build a concise description
    core = smart_shorten(product_name, max_len=35)
    # Use simple readable sentence
    desc = f"Shop {core} at Team Motorcycle. Premium riding gear with fast free shipping and the lowest price guaranteed. Buy now."
    if len(desc) > 160:
        desc = f"Shop {core} at Team Motorcycle. Premium riding gear with fast free shipping. Lowest price guaranteed."
    if len(desc) > 160:
        desc = f"Shop {core} at Team Motorcycle. Fast free shipping and lowest price guaranteed."
    if len(desc) < 130:
        desc = f"Shop {core} at Team Motorcycle. Premium riding gear with fast free shipping and the lowest price guaranteed. Buy online today."
    if len(desc) > 160:
        desc = desc[:157].rsplit(' ',1)[0] + '.'
    return desc

# Update unique products
updates = []
for slug in unique_slugs:
    row = df[df[slug_col] == slug].iloc[0]
    name = row[h1_col]
    title_new = generate_meta_title(name, slug)
    meta_new = generate_meta_description(name, slug, str(row.get(meta_col,''))) 
    h1_new = generate_h1(name)
    updates.append({slug_col: slug, title_col: title_new, meta_col: meta_new, h1_col: h1_new})

updates_df = pd.DataFrame(updates)

# Merge updates into original dataframe
for col in [h1_col, title_col, meta_col]:
    df = df.drop(columns=[col], errors='ignore')
    df = df.merge(updates_df[[slug_col, col]], on=slug_col, how='left')

# Save to new file
out_path = '/Users/sheikhown/.hermes/document_cache/teammotorcycle_seo_UPDATED.xlsx'
df.to_excel(out_path, index=False, sheet_name='Sheet1')
print('Saved to:', out_path)

# Verification
unique_prods = df[[slug_col, h1_col, title_col, meta_col]].drop_duplicates(subset=[slug_col])
print('\nVerification (first 15):')
for _, r in unique_prods.head(15).iterrows():
    print(f"Slug: {r[slug_col]}")
    print(f"H1: {r[h1_col]} ({len(r[h1_col])})")
    print(f"Title: {r[title_col]} ({len(r[title_col])})")
    print(f"Desc: {r[meta_col]} ({len(r[meta_col])})")
    print()

print('\nOverall stats:')
print('Title lengths: min', unique_prods[title_col].str.len().min(), 'max', unique_prods[title_col].str.len().max(), 'mean', round(unique_prods[title_col].str.len().mean(),1))
print('Desc lengths: min', unique_prods[meta_col].str.len().min(), 'max', unique_prods[meta_col].str.len().max(), 'mean', round(unique_prods[meta_col].str.len().mean(),1))
print('H1 lengths: min', unique_prods[h1_col].str.len().min(), 'max', unique_prods[h1_col].str.len().max(), 'mean', round(unique_prods[h1_col].str.len().mean(),1))
