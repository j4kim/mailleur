import os
from smtplib import SMTP
from dotenv import load_dotenv

load_dotenv(verbose=True)
SERVER = os.getenv("SERVER")
LOGIN = os.getenv("LOGIN")
PASSWORD = os.getenv("PASSWORD")
FROM = os.getenv("FROM")
TO = os.getenv("TO")

with SMTP(SERVER) as s:
    s.login(LOGIN, PASSWORD)
    s.sendmail(FROM, [TO], "From: "+FROM+"\r\nTo: "+TO+"\r\n\r\nSalut !")