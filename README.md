# Mailleur

Python email generation from template and sending from csv

## Requirements

* Python 3
* [python-dotenv](https://pypi.org/project/python-dotenv/)

## Set up

Copy
* `.env.example` to `.env`,
* `recipients.example.csv` to `recipients.csv` and
* `template.example.html` to `template.html`.

Adapt `.env`.

## Run

```bash
python run.py --send
```

For other command line arguments, see help:
```bash
python run.py --help
```

## Virtual env

Using virtualenvwrapper.

The first time:
```
mkvirtualenv mailleur
```

Then:
```
workon mailleur
```

Install requirements:
```
pip install -r requirements.txt 
```