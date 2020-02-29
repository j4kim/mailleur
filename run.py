import os
from smtplib import SMTP
from dotenv import load_dotenv

load_dotenv(verbose=True)
SERVER = os.getenv("SERVER")
LOGIN = os.getenv("LOGIN")
PASSWORD = os.getenv("PASSWORD")
FROM = os.getenv("FROM")
RECIPIENTS = os.getenv("RECIPIENTS")

s = SMTP(SERVER)
s.login(LOGIN, PASSWORD)

for recipient in RECIPIENTS.split(","):
    s.sendmail(FROM, [recipient], "From: "+FROM+"\r\nTo: "+recipient+"\r\n\r\nHello !")
    print("mail sent to " + recipient)

s.quit()