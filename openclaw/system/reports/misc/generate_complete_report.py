import os
from reportlab.lib.pagesizes import A4
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT

def generate_complete_seo_report():
    # Create PDF document
    doc = SimpleDocTemplate("system/reports/tonicphysio-monthly-performance-april-2026.pdf", 
                          pagesize=A4,
                          rightMargin=72,
                          leftMargin=72,
                          topMargin=72,
                          bottomMargin=18)
    
    # Styles
    styles = getSampleStyleSheet()
    
    # Add custom styles
    styles.add(ParagraphStyle(name='ReportTitle', 
                          fontSize=24, 
                          leading=28, 
                          spaceAfter=30, 
                          alignment=TA_CENTER, 
                          textColor=colors.darkblue,
                          fontName='Helvetica-Bold'))
    
    styles.add(ParagraphStyle(name='ReportSubTitle', 
                          fontSize=14, 
                          leading=18, 
                          spaceAfter=20, 
                          alignment=TA_CENTER,
                          textColor=colors.darkblue,
                          fontName='Helvetica-Bold'))
    
    styles.add(ParagraphStyle(name='SectionHeader', 
                          fontSize=16, 
                          leading=20, 
                          spaceBefore=15, 
                          spaceAfter=10, 
                          textColor=colors.darkblue,
                          fontName='Helvetica-Bold'))
    
    styles.add(ParagraphStyle(name='SubSectionHeader', 
                          fontSize=12, 
                          leading=15, 
                          spaceBefore=12, 
                          spaceAfter=8, 
                          textColor=colors.black,
                          fontName='Helvetica-Bold'))
    
    styles.add(ParagraphStyle(name='TableHeader', 
                          fontSize=9, 
                          leading=11,
                          fontName='Helvetica-Bold',
                          textColor=colors.white))
    
    styles.add(ParagraphStyle(name='TableData', 
                          fontSize=8, 
                          leading=10,
                          fontName='Helvetica'))
    
    # Story container
    story = []
    
    # Cover page
    story.append(Paragraph("TonicPhysio Monthly SEO Performance Report", 
                      styles["ReportTitle"]))
    story.append(Spacer(1, 20))
    story.append(Paragraph("April 2026", styles["ReportSubTitle"]))
    story.append(Spacer(1, 10))
    story.append(Paragraph("Prepared by Rank Ray SEO", styles["Normal"]))
    story.append(Spacer(1, 10))
    story.append(Paragraph("Date: April 30, 2026", styles["Normal"]))
    story.append(Spacer(1, 10))
    story.append(Paragraph("Property: tonicphysio.com", styles["Normal"]))
    story.append(PageBreak())
    
    # Executive Summary section
    story.append(Paragraph("Executive Summary", styles["SectionHeader"]))
    story.append(Spacer(1, 5))
    
    exec_summary = """April 2026 delivered a +7.3% increase in organic clicks (708 vs 660 in March) despite a 22.6% drop in total impressions. This signals improved ranking precision — fewer but more targeted impressions converting to clicks. CTR improved from 0.86% to 1.19%. However, average position weakened from 10.5 to 14.1, primarily driven by increased ranking for broader/long-tail queries outside the core local keyword set.

The standout win: "lymphatic drainage massage near me" jumped from position 5 to position 2, now ranking directly below the local pack for a high-volume term (SV: 2,900).

GA4 tracking shows a data anomaly — April reports only 279 sessions vs March's 1,312. The GSC data confirms actual traffic increased, suggesting a GA4 tracking issue (possible tag firing failure, consent management change, or property configuration shift) that needs immediate investigation."""
    
    story.append(Paragraph(exec_summary, styles["Normal"]))
    story.append(Spacer(1, 12))
    
    # Key Wins section
    story.append(Paragraph("Key Wins", styles["SubSectionHeader"]))
    
    key_wins = [
        "Click growth of +7.3% MoM - despite fewer impressions, more users clicked through from search.",
        "CTR improved by 38% (0.86% → 1.19%), indicating better meta title/description relevance.",
        '"lymphatic drainage massage near me" gained 3 positions - now at #2 for a 2,900 SV keyword.',
        '"prenatal massage milton" gained 3 positions - improved from #9 to #6 (SV: 110).',
        "Brand keywords holding strong - 'tonic physio' (1.1), 'tonic physiotherapy' (1.1), 'tonic physio milton' (1.0) all maintain top positions.",
        "Google Business Profile driving significant traffic - GBP organic was the 3rd largest traffic source at 56 sessions."
    ]
    
    for win in key_wins:
        story.append(Paragraph("• " + win, styles["Normal"]))
    
    story.append(Spacer(1, 12))
    
    # Traffic Trends section
    story.append(Paragraph("Google Search Console: Clicks & Impressions", styles["SubSectionHeader"]))
    
    # Traffic Trends table
    data = [
        ['Metric', 'March 2026', 'April 2026', 'MoM Change'],
        ['Total Clicks', '660', '708', '+7.3%'],
        ['Total Impressions', '76,644', '59,333', '-22.6%'],
        ['Avg. CTR', '0.86%', '1.19%', '+0.33 pp'],
        ['Avg. Position', '10.5', '14.1', '-3.6']
    ]
    
    table = Table(data)
    table.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.darkblue),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
        ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
        ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
        ('FONTSIZE', (0, 0), (-1, -1), 8),
        ('GRID', (0,0), (-1,-1), 0.5, colors.black),
    ]))
    
    story.append(table)
    story.append(Spacer(1, 12))
    
    # Device breakdown table
    story.append(Paragraph("Device Breakdown (April 2026)", styles["SubSectionHeader"]))
    
    device_data = [
        ['Device', 'Clicks', 'Impressions', 'CTR', 'Avg. Position'],
        ['Mobile', '465', '34,042', '1.37%', '6.5'],
        ['Desktop', '231', '24,546', '0.94%', '23.7'],
        ['Tablet', '12', '745', '1.61%', '6.3']
    ]
    
    device_table = Table(device_data)
    device_table.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.lightgrey),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.black),
        ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
        ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
        ('FONTSIZE', (0, 0), (-1, -1), 8),
        ('GRID', (0,0), (-1,-1), 0.5, colors.black),
        ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold')
    ]))
    
    story.append(device_table)
    story.append(Spacer(1, 12))
    
    # Keyword data
    story.append(Paragraph("Top Performing Keywords (April 2026)", styles["SubSectionHeader"]))
    
    keyword_data = [
        ['Keyword', 'Position', 'Search Volume', 'GSC Clicks'],
        ['tonic physio', '1', '260', '153'],
        ['tonic physiotherapy', '1', '70', '78'],
        ['tonic physio milton', '1', '70', '36'],
        ['lymphatic drainage massage near me', '2', '2,900', '10'],
        ['lymphatic drainage massage milton', '1', '110', '9'],
        ['compression socks milton', '2', '50', '-'],
        ['manual lymphatic drainage near me', '2', '90', '1'],
        ['pediatric physiotherapy near me', '1', '110', '-'],
        ['milton physio', '1', '70', '-']
    ]
    
    keyword_table = Table(keyword_data)
    keyword_table.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.lightgrey),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.black),
        ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
        ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
        ('FONTSIZE', (0, 0), (-1, -1), 8),
        ('GRID', (0,0), (-1,-1), 0.5, colors.black),
    ]))
    
    story.append(keyword_table)
    story.append(Spacer(1, 12))
    
    # Add action items
    story.append(Paragraph("Action Plan: May 2026", styles["SectionHeader"]))
    
    actions = [
        "Critical (This Week):",
        "1. Fix GA4/GTM tracking gap - Audit the GA4 tag implementation. Check if tag was removed during recent WordPress updates.",
        "2. Re-establish conversion tracking - Set up cross-domain tracking for JaneApp booking flow.",
        "3. Protect the 'lymphatic drainage massage near me' gain - Refresh the landing page with updated content.",
        "",
        "High Priority (Next 2 Weeks):",
        "4. Target page-2 opportunities - Optimize pages for keywords on page 2 (positions 11-20).",
        "5. Create service pillar content - Gap analysis shows content volume is the primary differentiator.",
        "6. Build internal links - Add contextual internal links from top-performing guides to service pages.",
        "",
        "Ongoing (Throughout May):",
        "7. GBP optimization - Post weekly updates, respond to all reviews within 24 hours.",
        "8. Monitor AI discovery - Track ChatGPT and other AI platforms as referral sources.",
        "9. Local citation maintenance - Ensure NAP consistency across all local directories.",
        "10. Monthly reporting cadence - Schedule next report for May 31, 2026."
    ]
    
    for action in actions:
        if action.strip() == "":
            story.append(Spacer(1, 6))
        else:
            story.append(Paragraph(action, styles["Normal"]))
    
    # Build the document
    doc.build(story)

if __name__ == "__main__":
    generate_complete_seo_report()