import os
from smtplib import SMTP
from dotenv import load_dotenv
from email.message import EmailMessage
from csv import reader
from datetime import datetime
from argparse import ArgumentParser

parser = ArgumentParser()
parser.add_argument("-t", "--template", help="path to your template file (default defined in .env)")
parser.add_argument("-c", "--csv", help="path to the csv containing the recipients (default defined in .env)")
parser.add_argument("-s", "--subject", help="email subject (default defined in .env)")
parser.add_argument("-S", "--send", help="really send email (otherwise, just log)", action="store_true")
parser.add_argument("-C", "--cc", help="Addresses to send a copy of each email")
args = parser.parse_args()

load_dotenv(verbose=True)
SERVER = os.getenv("SERVER")
LOGIN = os.getenv("LOGIN")
PASSWORD = os.getenv("PASSWORD")
FROM = os.getenv("FROM")
SUBJECT = args.subject or os.getenv("SUBJECT")
CSV = args.csv or os.getenv("CSV")
TEMPLATE = args.template or os.getenv("TEMPLATE")
CC = args.cc or os.getenv("CC")

with open(CSV) as f:
    csv = list(reader(f, delimiter=";"))
    header = csv.pop(0)

with open(TEMPLATE) as f:
    template = f.read()

logdir = "logs/{0:%Y-%m-%d_%H-%M-%S}".format(datetime.now())
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
    msg['Cc'] = CC
    content = template.format(**row, **msg)
    msg.add_alternative(content, subtype='html')
    logfile = "{}/{}.html".format(logdir, row["Email"])
    with open(logfile, "w") as f:
        f.write(content)
    if args.send:
        s.send_message(msg)
        print("mail sent to " + row["Email"])
    else:
        print("mail logged in " + logfile)

s.quit()
