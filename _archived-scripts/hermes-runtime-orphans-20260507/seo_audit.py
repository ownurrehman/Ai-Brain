import pandas as pd
filepath = '/Users/sheikhown/.hermes/document_cache/doc_2c4d989b4ffc_teammotorcycle_seo.xlsx'
df = pd.read_excel(filepath, sheet_name='Sheet1')

key_cols = ['SEO Slug', 'Product Name & H1', 'Product Description', 'Meta Title', 'Meta Description']
available = [c for c in key_cols if c in df.columns]
print('Available key columns:', available)
print()

products = df[available].drop_duplicates(subset=['SEO Slug'])
print('Total rows:', len(df))
print('Unique products (by slug):', len(products))
print()

# Analyze Meta Titles
print('--- META TITLE ANALYSIS ---')
products['Meta Title_len'] = products['Meta Title'].astype(str).apply(len)
print('Meta Title length stats:')
print(products['Meta Title_len'].describe())
bad_titles = products[(products['Meta Title_len'] < 30) | (products['Meta Title_len'] > 60)]
print('Products with title length issues (<30 or >60):', len(bad_titles))
print()

# Analyze Meta Descriptions
print('--- META DESCRIPTION ANALYSIS ---')
products['Meta Description_len'] = products['Meta Description'].astype(str).apply(len)
print('Meta Description length stats:')
print(products['Meta Description_len'].describe())
bad_descs = products[(products['Meta Description_len'] < 100) | (products['Meta Description_len'] > 160)]
print('Products with description length issues (<100 or >160):', len(bad_descs))
print()

# Show first 15 unique products preview
print('--- FIRST 15 UNIQUE PRODUCTS ---')
preview = products.head(15).copy()
out_cols = ['SEO Slug', 'Product Name & H1', 'Meta Title', 'Meta Title_len', 'Meta Description', 'Meta Description_len']
print(preview[out_cols].to_string(index=False))
