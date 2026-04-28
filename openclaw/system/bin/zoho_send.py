import smtplib
import sys
import argparse
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

def send_email(recipient, subject, body, sender_email, sender_password):
    # Zoho SMTP Settings
    smtp_server = "smtp.zoho.com"
    smtp_port = 587

    # Create the email
    message = MIMEMultipart()
    message["From"] = sender_email
    message["To"] = recipient
    message["Subject"] = subject
    message.attach(MIMEText(body, "plain"))

    try:
        # Connect and send
        server = smtplib.SMTP(smtp_server, smtp_port)
        server.starttls() # Secure the connection
        server.login(sender_email, sender_password)
        server.send_message(message)
        server.quit()
        return True, "Email sent successfully"
    except Exception as e:
        return False, str(e)

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Send email via Zoho SMTP")
    parser.add_argument("--to", required=True, help="Recipient email")
    parser.add_argument("--subject", required=True, help="Email subject")
    parser.add_argument("--body", required=True, help="Email body")
    parser.add_argument("--user", required=True, help="Zoho email")
    parser.add_argument("--password", required=True, help="Zoho password")

    args = parser.parse_args()

    success, msg = send_email(args.to, args.subject, args.body, args.user, args.password)
    if success:
        print(f"SUCCESS: {msg}")
        sys.exit(0)
    else:
        print(f"ERROR: {msg}")
        sys.exit(1)
