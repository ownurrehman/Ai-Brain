import pandas as pd
filepath = '/Users/sheikhown/.hermes/document_cache/doc_2c4d989b4ffc_teammotorcycle_seo.xlsx'
df = pd.read_excel(filepath, sheet_name='Sheet1')

products = df[['SEO Slug', 'Product Name & H1', 'Product Description', 'Meta Title', 'Meta Description']].drop_duplicates(subset=['SEO Slug'])

print('=== FULL AUDIT REPORT ===')
print('Total rows:', len(df))
print('Unique products:', len(products))
print()

# 1. Meta Title check
print('--- META TITLE ISSUES ---')
titles = products['Meta Title'].astype(str)
products['title_len'] = titles.apply(len)
missing_title = products[titles.isin(['nan', 'None', ''])]
short_title = products[products['title_len'] < 30]
long_title = products[products['title_len'] > 60]
dup_titles = products[products.duplicated(subset=['Meta Title'], keep=False)]
print('Missing meta titles:', len(missing_title))
print('Too short (<30):', len(short_title))
print('Too long (>60):', len(long_title))
print('Duplicate titles:', len(dup_titles))
if len(dup_titles) > 0:
    print('Duplicate title values:')
    print(dup_titles[['SEO Slug','Meta Title']].to_string(index=False))
print()

# 2. Meta Description check
print('--- META DESCRIPTION ISSUES ---')
descs = products['Meta Description'].astype(str)
products['desc_len'] = descs.apply(len)
missing_desc = products[descs.isin(['nan', 'None', ''])]
short_desc = products[products['desc_len'] < 100]
long_desc = products[products['desc_len'] > 160]
dup_descs = products[products.duplicated(subset=['Meta Description'], keep=False)]
print('Missing meta descriptions:', len(missing_desc))
print('Too short (<100):', len(short_desc))
print('Too long (>160):', len(long_desc))
print('Duplicate descriptions:', len(dup_descs))
print()

# 3. H1 / Product Name check
print('--- H1 / PRODUCT NAME ISSUES ---')
h1s = products['Product Name & H1'].astype(str)
products['h1_len'] = h1s.apply(len)
short_h1 = products[products['h1_len'] < 20]
long_h1 = products[products['h1_len'] > 70]
print('H1 too short (<20):', len(short_h1))
print('H1 too long (>70):', len(long_h1))
print()

# 4. Product Description check
print('--- PRODUCT DESCRIPTION ISSUES ---')
pds = products['Product Description'].astype(str)
products['pd_len'] = pds.apply(len)
missing_pd = products[pds.isin(['nan', 'None', ''])]
short_pd = products[products['pd_len'] < 200]
print('Missing descriptions:', len(missing_pd))
print('Descriptions <200 chars:', len(short_pd))
print()

# 5. Worst offenders (longest meta titles and descriptions)
print('--- TOP 5 LONGEST META TITLES ---')
print(products.nlargest(5, 'title_len')[['SEO Slug','Meta Title','title_len']].to_string(index=False))
print()
print('--- TOP 5 LONGEST META DESCRIPTIONS ---')
print(products.nlargest(5, 'desc_len')[['SEO Slug','Meta Description','desc_len']].to_string(index=False))

# 6. H2 inside product description check
print()
print('--- H2 IN PRODUCT DESCRIPTIONS ---')
h2_yes = products[pds.str.contains('<h2>', case=False, na=False)]
print('Products with H2 tags in description:', len(h2_yes))
h2_no = products[~pds.str.contains('<h2>', case=False, na=False)]
print('Products WITHOUT H2 tags in description:', len(h2_no))
