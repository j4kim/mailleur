import os
from smtplib import SMTP
from dotenv import load_dotenv
from email.message import EmailMessage

load_dotenv(verbose=True)
SERVER = os.getenv("SERVER")
LOGIN = os.getenv("LOGIN")
PASSWORD = os.getenv("PASSWORD")
FROM = os.getenv("FROM")
RECIPIENTS = os.getenv("RECIPIENTS")

s = SMTP(SERVER)
s.login(LOGIN, PASSWORD)

for recipient in RECIPIENTS.split(","):
    msg = EmailMessage()
    msg['Subject'] = "Un message"
    msg['From'] = FROM
    msg['To'] = recipient
    msg.set_content("Salut !")
    s.send_message(msg)
    print("mail sent to " + recipient)

s.quit()