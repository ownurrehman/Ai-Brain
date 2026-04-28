import csv
import re

tracker_path = '/Users/sheikhown/.openclaw/workspace/reports/khanllp-citation-tracker-2026-04-21.csv'
output_path = '/Users/sheikhown/.openclaw/workspace/reports/khanllp-citation-tracker-UPDATED.csv'

with open('/Users/sheikhown/.openclaw/workspace/gsheet_raw.txt', 'r') as f:
    gs_text = f.read()

gs_data = {
    "https://lso.ca/public-resources/finding-a-lawyer-or-paralegal/lawyer-and-paralegal-directory": {"cat": "Core Legal Directory", "cost": "Free", "notes": "Mandatory"},
    "https://www.canadianlawlist.com": {"cat": "Core Legal Directory", "cost": "Free + Paid tiers", "notes": "Highest-authority Canadian legal directory"},
    "https://www.lexpert.ca/directory": {"cat": "Core Legal Directory", "cost": "Free + Paid", "notes": "Premium positioning signal"},
    "https://www.findlaw.ca": {"cat": "Core Legal Directory", "cost": "Paid", "notes": "Thomson Reuters property"},
    "https://www.lawyers.com": {"cat": "Core Legal Directory", "cost": "Free", "notes": "Martindale-Hubbell network"},
    "https://www.martindale.com": {"cat": "Core Legal Directory", "cost": "Free + Paid", "notes": "Peer review ratings"},
    "https://www.yellowpages.ca": {"cat": "Canadian Business", "cost": "Free + Paid", "notes": "Top citation for Canadian local SEO"},
    "https://www.canada411.ca": {"cat": "Canadian Business", "cost": "Free", "notes": "Owned by YP"},
    "https://411.ca": {"cat": "Canadian Business", "cost": "Free", "notes": "Core Canadian business directory"},
    "https://www.yelp.ca": {"cat": "Canadian Business", "cost": "Free", "notes": "Reviews drive local pack rankings"},
    "https://www.bbb.org": {"cat": "Canadian Business", "cost": "Paid Accreditation", "notes": "Accreditation signals trust"},
    "https://www.miltonchamber.ca": {"cat": "Local Chamber", "cost": "Paid Membership", "notes": "Dofollow member link"},
    "https://www.oakvillechamber.com": {"cat": "Local Chamber", "cost": "Paid Membership", "notes": "Critical for Oakville"},
    "https://www.bot.com": {"cat": "Local Chamber", "cost": "Paid Membership", "notes": "Strong Toronto authority"},
    "https://www.avvo.com": {"cat": "Legal Directory", "cost": "Free", "notes": "Individual lawyer profiles"},
    "https://www.justia.com": {"cat": "Legal Directory", "cost": "Free", "notes": "High DR"},
    "https://www.lawyer.com": {"cat": "Legal Directory", "cost": "Free", "notes": "General attorney directory"},
    "https://www.hg.org": {"cat": "Legal Directory", "cost": "Free + Paid", "notes": "Legal resource directory"},
    "https://www.bestlawyers.com": {"cat": "Legal Directory", "cost": "Nomination", "notes": "Peer-nominated"},
    "https://www.superlawyers.com": {"cat": "Legal Directory", "cost": "Nomination", "notes": "Trust + authority signal"},
    "https://familylawyermagazine.com": {"cat": "Family Law Specific", "cost": "Paid", "notes": "Niche family law directory"},
    "https://www.divorcemag.com": {"cat": "Family Law Specific", "cost": "Paid", "notes": "Directly targets family law intent"},
    "https://www.orea.com": {"cat": "Real Estate Specific", "cost": "Relationship-based", "notes": "Ontario Real Estate Association"},
    "https://www.trreb.ca": {"cat": "Real Estate Specific", "cost": "Relationship-based", "notes": "TRREB member realtors"},
    "https://www.reco.on.ca": {"cat": "Real Estate Specific", "cost": "Free resource", "notes": "Industry resource listings"},
    "https://www.cylex-canada.ca": {"cat": "Canadian Business", "cost": "Free", "notes": "Populates downstream sites"},
    "https://www.n49.com": {"cat": "Canadian Business", "cost": "Free", "notes": "Canadian local business directory"},
    "https://www.goldbook.ca": {"cat": "Canadian Business", "cost": "Free", "notes": "Canadian business directory"},
    "https://www.brownbook.net": {"cat": "Canadian Business", "cost": "Free", "notes": "Global but indexed well in Canada"},
    "https://www.hotfrog.ca": {"cat": "Canadian Business", "cost": "Free", "notes": "Feeds secondary sites"},
    "https://www.ourbis.ca": {"cat": "Canadian Business", "cost": "Free", "notes": "Canadian-specific directory"},
    "https://www.profilecanada.com": {"cat": "Canadian Business", "cost": "Free + Paid", "notes": "Business profile citation"},
    "https://canadianbusinessdirectory.ca": {"cat": "Canadian Business", "cost": "Free", "notes": "Low effort citation"},
    "https://www.bingplaces.com": {"cat": "Global Business", "cost": "Free", "notes": "Bing local search"},
    "https://register.apple.com/placesonmaps": {"cat": "Global Business", "cost": "Free", "notes": "Apple Maps + Siri"},
    "https://business.foursquare.com": {"cat": "Global Business", "cost": "Free", "notes": "Data aggregator"},
    "https://www.facebook.com/business": {"cat": "Global Business", "cost": "Free", "notes": "Reviews matter"},
    "https://www.linkedin.com/company": {"cat": "Global Business", "cost": "Free", "notes": "Critical for legal B2B"},
    "https://www.milton.ca": {"cat": "Local Milton", "cost": "Free / Community", "notes": "Municipal directory"},
    "https://www.oakville.com": {"cat": "Local Oakville", "cost": "Free + Paid", "notes": "Local community portal"},
    "https://oakvillenews.org": {"cat": "Local Oakville", "cost": "Paid / Editorial", "notes": "Pitch legal explainers"},
    "https://www.blogto.com": {"cat": "Local Toronto", "cost": "Paid / Editorial", "notes": "High DR Toronto authority"},
    "https://www.toronto.com": {"cat": "Local Toronto", "cost": "Paid", "notes": "Local business directory"},
    "https://www.insidehalton.com": {"cat": "Local Halton", "cost": "Paid / Editorial", "notes": "Metroland-owned"},
    "https://www.theifp.ca": {"cat": "Local Halton", "cost": "Editorial", "notes": "Editorial placements"},
    "https://www.clio.com": {"cat": "Niche Legal", "cost": "Free (Clio users)", "notes": "If using Clio"},
    "https://lawyerist.com": {"cat": "Niche Legal", "cost": "Free", "notes": "Legal industry hub"},
    "https://www.nolo.com": {"cat": "Niche Legal", "cost": "Paid", "notes": "High-conversion"},
    "https://www.lawinfo.com": {"cat": "Niche Legal", "cost": "Free + Paid", "notes": "Consumer-facing"},
    "https://www.attorneypages.com": {"cat": "Niche Legal", "cost": "Free", "notes": "Low effort"},
    "https://www.divorcenet.com": {"cat": "Niche Family Law", "cost": "Paid", "notes": "Family-law specific"},
    "https://www.divorcedmoms.com": {"cat": "Niche Family Law", "cost": "Editorial", "notes": "Guest post opportunities"},
    "https://www.reincanada.com": {"cat": "Niche Real Estate", "cost": "Membership", "notes": "Canadian real estate investor network"},
    "https://www.movesmartly.com": {"cat": "Niche Real Estate", "cost": "Editorial", "notes": "Pitch guest posts on closing law topics"},
    "https://storeys.com": {"cat": "Niche Real Estate", "cost": "Editorial", "notes": "Pitch expert commentary on real estate legal trends"},
    "https://www.neustarlocaleze.biz": {"cat": "Data Aggregator", "cost": "Paid", "notes": "Powers Apple Bing Yelp"},
    "https://www.data-axle.com": {"cat": "Data Aggregator", "cost": "Paid", "notes": "Data aggregator"},
    "https://www.trustpilot.com": {"cat": "Review Platform", "cost": "Free + Paid", "notes": "Reviews = trust signals"},
    "https://birdeye.com": {"cat": "Review Platform", "cost": "Paid", "notes": "Review aggregation platform"},
    "https://business.instagram.com": {"cat": "Social", "cost": "Free", "notes": "NAP in bio"},
    "https://www.youtube.com": {"cat": "Social", "cost": "Free", "notes": "FAQ video content"},
    "https://www.bizhwy.com": {"cat": "Canadian Biz", "cost": "Free", "notes": "Canadian business directory"},
    "https://www.2findlocal.com": {"cat": "Canadian Biz", "cost": "Free", "notes": "Canadian city-specific"},
    "https://ca.findopen.com": {"cat": "Canadian Biz", "cost": "Free", "notes": "Hours + NAP citation"},
    "https://openinghours.ca": {"cat": "Canadian Biz", "cost": "Free", "notes": "NAP + hours directory"},
    "https://www.americanbar.org": {"cat": "Legal Niche", "cost": "Referral", "notes": "ABA does not list Canadian lawyers"},
    "https://www.cba.org": {"cat": "Legal Niche", "cost": "Membership", "notes": "CBA membership"},
    "https://www.oba.org": {"cat": "Legal Niche", "cost": "Membership", "notes": "OBA section memberships"},
    "https://haltoncountylawassociation.ca": {"cat": "Legal Niche", "cost": "Membership", "notes": "Hyper-local Halton area"},
    "https://www.peellaw.com": {"cat": "Legal Niche", "cost": "Membership", "notes": "Mississauga/Brampton reach"},
    "https://tlaonline.ca": {"cat": "Legal Niche", "cost": "Membership", "notes": "Toronto legal association"},
    "https://www.advocatedaily.com": {"cat": "Legal Niche", "cost": "Paid", "notes": "Expert profile + articles"},
    "https://www.lawtimesnews.com": {"cat": "Legal Niche", "cost": "Editorial", "notes": "Pitch commentary"},
    "https://www.canadianlawyermag.com": {"cat": "Legal Niche", "cost": "Editorial", "notes": "Thomson Reuters"},
    "https://www.mapquest.com": {"cat": "Local Biz", "cost": "Free", "notes": "Data aggregator"},
    "https://www.superpages.com": {"cat": "Local Biz", "cost": "Free", "notes": "YP network"},
    "https://tupalo.com": {"cat": "Local Biz", "cost": "Free", "notes": "Global local directory"},
    "https://where.to": {"cat": "Local Biz", "cost": "Free", "notes": "Canadian business citation"},
    "https://www.bizvotes.com": {"cat": "Local Biz", "cost": "Free", "notes": "General business directory"},
    "https://localstack.com": {"cat": "Local Biz", "cost": "Free", "notes": "Secondary citation fill"},
    "https://www.expressbusinessdirectory.com": {"cat": "Local Biz", "cost": "Free", "notes": "Low-effort fill"},
    "https://www.ebusinesspages.com": {"cat": "Local Biz", "cost": "Free", "notes": "Volume citation"},
    "https://www.kijiji.ca": {"cat": "Local Biz", "cost": "Free/Paid", "notes": "Canadian classified"},
    "https://www.elocal.com": {"cat": "Local Biz", "cost": "Paid Lead-Gen", "notes": "Paid lead-gen platform"},
    "https://www.alignable.com": {"cat": "Community", "cost": "Free", "notes": "Small business network"},
    "https://business.nextdoor.com": {"cat": "Community", "cost": "Free", "notes": "Hyper-local neighborhood reach"},
    "https://www.meetup.com": {"cat": "Community", "cost": "Free", "notes": "Host free events"},
}

with open(tracker_path, 'r') as f:
    reader = csv.reader(f)
    header = next(reader)
    rows = list(reader)

try:
    cost_idx = header.index('Cost')
    url_idx = header.index('URL')
    notes_idx = header.index('Notes')
except ValueError:
    print("Error: Required columns not found in tracker CSV")
    exit(1)

final_rows = []
processed_urls = set()

for row in rows:
    url = row[url_idx]
    processed_urls.add(url)
    if url in gs_data:
        if not row[cost_idx] or row[cost_idx] == "Pending":
            row[cost_idx] = gs_data[url]['cost']
    final_rows.append(row)

for url, info in gs_data.items():
    if url not in processed_urls:
        new_row = [""] * len(header)
        new_row[url_idx] = url
        new_row[cost_idx] = info['cost']
        try:
            pri_idx = header.index('Priority')
            new_row[pri_idx] = "Medium"
        except ValueError: pass
        try:
            cat_idx = header.index('Category')
            new_row[cat_idx] = info['cat']
        except ValueError: pass
        new_row[notes_idx] = info['notes']
        final_rows.append(new_row)

with open(output_path, 'w', newline='') as f:
    writer = csv.writer(f)
    writer.writerow(header)
    writer.writerows(final_rows)

print(f"Updated tracker written to {output_path}")
