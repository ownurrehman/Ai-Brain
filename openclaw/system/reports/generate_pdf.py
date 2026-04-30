import os
from reportlab.lib.pagesizes import A4
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import inch
from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT, TA_RIGHT
from reportlab.platypus import PageTemplate, Frame, BaseDocTemplate
import re

def parse_markdown_tables(content):
    """Parse markdown tables and convert to ReportLab tables"""
    lines = content.split('\n')
    tables = []
    current_table = []
    in_table = False
    headers = []
    
    for line in lines:
        # Check if we're starting a table
        if '|' in line and line.strip().startswith('|'):
            if not in_table:
                # Start of a new table
                in_table = True
                current_table = []
                headers = line.strip().split('|')[1:-1]  # Remove first and last empty elements
                # Clean headers
                headers = [h.strip() for h in headers]
            else:
                # Data row
                row = line.strip().split('|')[1:-1]  # Remove first and last empty elements
                row = [r.strip() for r in row]
                current_table.append(row)
        elif in_table and line.strip() == '':
            # End of table
            if headers and current_table:
                tables.append((headers, current_table))
            in_table = False
            headers = []
            current_table = []
        elif in_table:
            # Continue processing table data
            continue
        else:
            # Not in a table, continue
            pass
    
    return tables

def markdown_to_html(markdown_text):
    """Convert markdown to simple HTML for ReportLab"""
    # Headers
    html = re.sub(r'^# (.+)$', r'<h1>\1</h1>', markdown_text, flags=re.MULTILINE)
    html = re.sub(r'^## (.+)$', r'<h2>\1</h2>', html, flags=re.MULTILINE)
    html = re.sub(r'^### (.+)$', r'<h3>\1</h3>', html, flags=re.MULTILINE)
    
    # Bold text
    html = re.sub(r'\*\*(.+?)\*\*', r'<b>\1</b>', html)
    
    # Process tables separately
    lines = html.split('\n')
    processed_lines = []
    for line in lines:
        if '|' in line and line.strip().startswith('|'):
            # Skip table lines as we'll process them separately
            continue
        else:
            processed_lines.append(line)
    
    return '\n'.join(processed_lines)

def create_seo_report():
    # Read the markdown content
    with open('system/reports/tonicphysio-monthly-performance-april-2026.md', 'r') as f:
        content = f.read()
    
    # Create PDF document
    doc = SimpleDocTemplate("system/reports/tonicphysio-monthly-performance-april-2026.pdf", 
                          pagesize=A4,
                          rightMargin=72,
                          leftMargin=72,
                          topMargin=72,
                          bottomMargin=18)
    
    # Styles
    styles = getSampleStyleSheet()
    styles.add(ParagraphStyle(name='CustomTitle', 
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
    
    styles.add(ParagraphStyle(name='SubSectionHeader', 
                          fontSize=14, 
                          leading=18, 
                          spaceBefore=15, 
                          spaceAfter=6, 
                          textColor=colors.darkblue,
                          fontName='Helvetica-Bold'))
    
    styles.add(ParagraphStyle(name='Normal', 
                          fontSize=10, 
                          leading=12,
                          fontName='Helvetica'))
    
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
    story.append(Paragraph("TonicPhysio Monthly SEO Performance Report", styles["CustomTitle"]))
    story.append(Paragraph("April 2026", styles["Normal"]))
    story.append(Spacer(1, 20))
    story.append(Paragraph("Prepared by Rank Ray SEO", styles["Normal"]))
    story.append(PageBreak())
    
    # Parse the content and create sections
    sections = content.split('---')
    section_content = sections[1] if len(sections) > 1 else sections[0]
    
    # Add Executive Summary section
    story.append(Paragraph("Executive Summary", styles["SectionHeader"]))
    
    # Find the executive summary content
    exec_summary_lines = []
    in_exec_summary = False
    
    for line in content.split('\n'):
        if line.strip() == "## Executive Summary":
            in_exec_summary = True
            continue
        elif in_exec_summary and line.startswith("## ") and "Executive Summary" not in line:
            break
        elif in_exec_summary:
            if line.strip().startswith("**"):
                # Format bold text properly
                bold_text = re.sub(r'\*\*(.+?)\*\*', r'<b>\1</b>', line)
                exec_summary_lines.append(bold_text)
            else:
                exec_summary_lines.append(line)
    
    # Add executive summary content
    exec_summary_text = " ".join(exec_summary_lines).strip()
    if exec_summary_text:
        story.append(Paragraph(exec_summary_text, styles["Normal"]))
    
    story.append(Spacer(1, 12))
    
    # Add Key Wins section
    story.append(Paragraph("Key Wins", styles["SubSectionHeader"]))
    
    # Find key wins content
    key_wins_lines = []
    in_key_wins = False
    
    lines = content.split('\n')
    for line in lines:
        if line.strip() == "## Key Wins (April 2026)":
            in_key_wins = True
            continue
        elif in_key_wins and line.startswith("## ") and "Key Wins" not in line:
            break
        elif in_key_wins:
            key_wins_lines.append(line)
    
    key_wins_text = " ".join(key_wins_lines).strip()
    if key_wins_text:
        story.append(Paragraph(key_wins_text, styles["Normal"]))
    
    # Add tables
    # Parse tables from the markdown content
    tables_data = [
        # Traffic Trends table
        {
            'headers': ['Metric', 'March 2026', 'April 2026', 'MoM Change'],
            'data': [
                ['Total Clicks', '660', '708', '+7.3%'],
                ['Total Impressions', '76,644', '59,333', '-22.6%'],
                ['Avg. CTR', '0.86%', '1.19%', '+0.33 pp'],
                ['Avg. Position', '10.5', '14.1', '-3.6']
            ],
            'title': 'Google Search Console: Clicks & Impressions'
        },
        # Device Breakdown table
        {
            'headers': ['Device', 'Clicks', 'Impressions', 'CTR', 'Avg. Position'],
            'data': [
                ['Mobile', '465', '34,042', '1.37%', '6.5'],
                ['Desktop', '231', '24,546', '0.94%', '23.7'],
                ['Tablet', '12', '745', '1.61%', '6.3']
            ],
            'title': 'Device Breakdown (April 2026)'
        }
    ]
    
    # Add tables to story
    for table_data in tables_data:
        story.append(Paragraph(table_data['title'], styles["SubSectionHeader"]))
        
        # Create table data with headers
        table_headers = [[
            Paragraph(cell, styles["TableHeader"]) for cell in table_data['headers']
        ]]
        
        table_rows = []
        for row in table_data['data']:
            table_rows.append([
                Paragraph(cell, styles["TableData"]) for cell in row
            ])
        
        # Create the table
        table = Table(table_headers + table_rows)
        table.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, 0), colors.lightgrey),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, -1), 8),
            ('GRID', (0, 0), (-1, -1), 1, colors.black)
        ]))
        
        story.append(table)
        story.append(Spacer(1, 12))
    
    # Build the document
    doc.build(story)
    
    print("PDF report generated successfully at: system/reprints/tonicphysio-monthly-performance-april-2026.pdf")

if __name__ == "__main__":
    create_seo_report()