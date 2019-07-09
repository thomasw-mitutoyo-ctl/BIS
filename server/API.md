# API Dokumentation

Der BIS Server stellt eine REST API bereit. Der Endpunkt ist `<servername>/api/`

## Termine

Gibt die Termine im json Format zurück  
Endpunkt: `<servername>/api/appointments.php`  
Parameter:

* `relevant`: True/False (Optional)
* `date`: Datum in DD-MM-YYYY (Optional, wird nur beachtet wenn relevant=true ist)

### Termine Beispiel

Aufruf: `<servername>/api/appointments.php?relevant=true&date=21-03-2017`
Antwort:

```json
[
    {
        "day": "Di",
        "date": "2017-03-21",
        "start": "2017-03-21",
        "end": "2017-03-23",
        "location": "Kawasaki",
        "title": "Workshop",
        "time": "08:00"
    }
]
```

## Geburtstage

Gibt die Geburtstage im json Format zurück
Endpunkt: `<servername>/api/birthdays.php`

### Geburtstage Beispiel

Aufruf: `<servername>/api/birthdays.php`
Antwort:

```json
[
    {
        "name": "Max Mustermann",
        "date": "2016-11-22",
        "id": 20
    },
    {
        "name": "Frank Hezel",
        "date": "2016-02-17",
        "id": 57
    },
    {
        "name": "Gerhard Müller",
        "date": "2016-08-01",
        "id": 27
    }
]
```

## Bilder

Endpunkt: `<servername>/api/pictures.php`
Parameter:

* `limit`: Integer Wert oder `all`. Gibt an, wie viele Bilder zurückgegeben werden. Wird ein Limit angegeben, sind es zufällige Bilder

### Bilder Beispiel

Gibt Bilder im json Format zurück
Aufruf: `<servername>/api/pictures.php?limit=10`
Antwort:

```json
[
    "pictures\/Sonstige\/9.jpg",
    "pictures\/Seattle1\/6.jpg",
    "pictures\/Washington1\/8.jpg",
    "pictures\/Washington1\/15.jpg",
    "pictures\/Washington2\/17.jpg",
    "pictures\/Seattle1\/3.jpg",
    "pictures\/Hamburg\/6.jpg",
    "pictures\/RobinsBilder2\/2.jpg",
    "pictures\/RobinsBilder3\/15.jpg",
    "pictures\/Uganda1\/5.jpg"
]
```

## Tickereinträge

Gibt die Tickereinträge im json Format zurück
Endpunkt: `<servername>/api/tickers.php`

### Tickereinträge Beispiel

Aufruf: `<servername>/api/tickers.php`
Antwort:

```json
[
    {
        "message": "Herzlich Willkommen",
        "start": "2018-07-19",
        "end": "2018-07-23",
        "id": 190
    },
    {
        "message": "Here we go again...",
        "start": "2018-08-08",
        "end": "2018-08-08",
        "id": 203
    }
]
```
