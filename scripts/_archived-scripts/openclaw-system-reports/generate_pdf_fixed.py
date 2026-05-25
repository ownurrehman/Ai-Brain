import os
from reportlab.lib.pagesizes import A4
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT

def generate_seo_report():
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
    
    styles.add(ParagraphStyle(name='SectionHeader', 
                          fontSize=18, 
                          leading=22, 
                          spaceBefore=20, 
                          spaceAfter=12, 
                          textColor=colors.darkblue,
                          fontName='Helvetica-Bold'))
    
    styles.add(ParagraphStyle(name='TableHeader', 
                          fontSize=10, 
                          leading=12,
                          fontName='Helvetica-Bold',
                          textColor=colors.darkblue))
    
    styles.add(ParagraphStyle(name='TableData', 
                          fontSize=9, 
                          leading=11,
                          fontName='Helvetica'))
    
    # Story container
    story = []
    
    # Cover page
    story.append(Paragraph("TonicPhysio Monthly SEO Performance Report", 
                      styles["ReportTitle"]))
    story.append(Spacer(1, 20))
    story.append(Paragraph("April 2026", styles["Normal"]))
    story.append(Paragraph("Prepared by Rank Ray SEO", styles["Normal"]))
    story.append(PageBreak())
    
    # Executive Summary section
    story.append(Paragraph("Executive Summary", styles["SectionHeader"]))
    story.append(Paragraph("TonicPhysio Monthly SEO Performance Report - April 2026", styles["Normal"]))
    story.append(Spacer(1, 12))
    
    # Add a table
    data = [
        ['Metric', 'March 2026', 'April 2026', 'MoM Change'],
        ['Total Clicks', '660', '708', '+7.3%'],
        ['Total Impressions', '76,644', '59,333', '-22.6%'],
        ['Avg. CTR', '0.86%', '1.19%', '+0.33 pp'],
        ['Avg. Position', '10.5', '14.1', '-3.6']
    ]
    
    table = Table(data)
    table.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.lightgrey),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.darkblue),
        ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
        ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
        ('FONTSIZE', (0, 0), (-1, -1), 8),
        ('GRID', (0,0), (-1,-1), 0.5, colors.black),
    ]))
    
    story.append(table)
    story.append(PageBreak())
    
    # Build the document
    doc.build(story)

if __name__ == "__main__":
    generate_seo_report()