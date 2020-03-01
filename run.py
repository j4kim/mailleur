import os
from smtplib import SMTP
from dotenv import load_dotenv
from email.message import EmailMessage
from csv import reader
from datetime import datetime

load_dotenv(verbose=True)
SERVER = os.getenv("SERVER")
LOGIN = os.getenv("LOGIN")
PASSWORD = os.getenv("PASSWORD")
FROM = os.getenv("FROM")
SUBJECT = os.getenv("SUBJECT")
CSV = os.getenv("CSV")
TEMPLATE = os.getenv("TEMPLATE")

with open(CSV) as f:
    csv = list(reader(f, delimiter=";"))
    header = csv.pop(0)

with open(TEMPLATE) as f:
    template = f.read()

logdir = "logs/{0:%Y-%m-%d %H%M%S}".format(datetime.now())
os.makedirs(logdir)

s = SMTP(SERVER)
s.starttls()
s.login(LOGIN, PASSWORD)

for row in csv:
    row = dict(zip(header, row))
    msg = EmailMessage()
    msg['Subject'] = SUBJECT
    msg['From'] = FROM
    msg['To'] = row["Email"]
    content = template.format(**row, **msg)
    msg.add_alternative(content, subtype='html')
    s.send_message(msg)
    logfile = "{}/{}.html".format(logdir, row["Email"])
    with open(logfile, "w") as f:
        f.write(content)
    print("mail sent to " + row["Email"])

s.quit()
