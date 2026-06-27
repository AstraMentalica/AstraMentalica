📋 KONČNA KANONIČNA SPECIFIKACIJA (NAJČISTEJŠA)
1. manifest.json – kdo sem
json
{
    "_id": "nasa",
    "_verzija": "1.0.0",

    "modul": {
        "id": "nasa",
        "ime": "Kozmični Senzorji",
        "tip": "zbiralec",
        "nivo": 1,
        "verzija": "1.0.0",
        "aktiviran": true,
        "vstopna": "modul.php",
        "opis": "Kozmični senzorji — sončeva aktivnost (NASA DONKI), geomagnetna aktivnost (NOAA SWPC), lokalno vreme (OpenWeather).",
        "status": "stabilen",
        "demo": false,
        "zacasen": false
    },

    "dostop": {
        "minimalna_vloga": "gost",
        "plan": "osnova",
        "javno_vidno": false,
        "placljivo": false,
        "otroski": false,
        "vidnost": "skriti",
        "dovoljenja": [
            "branje",
            "rocni_zagon"
        ]
    },

    "cache": {
        "omogocen": true,
        "ttl": 900
    },

    "ui": {
        "ima_prikaz": false,
        "ikona": "🛰️",
        "barva": "#67e8f9",
        "kategorija": "NEBO",
        "dovoljene_postavitve": ["standard"],
        "tags": ["nasa", "senzorji", "vreme", "geomagnetno"],
        "jeziki": ["sl", "en"]
    },

    "izvajanje": {
        "tip": "cron",
        "api_only": true,
        "interval": 900,
        "ob_zagonu": true,
        "prioriteta": 100,
        "bootstrap": null
    },

    "migracije": {
        "obstajajo": false,
        "zadnja": null
    },

    "integriteta": {
        "checksum": null
    },

    "log": {
        "omogocen": true,
        "nivo": "info"
    },

    "cas": {
        "ustvarjen": "2026-06-19T10:30:00Z",
        "posodobljen": "2026-06-19T10:30:00Z",
        "zadnji_zagon": null
    }
}
2. api.json – kako me kličeš
json
{
    "_id": "nasa",
    "_verzija": "1.0.0",

    "kanali": ["api"],

    "vstop": {
        "web": "modul.php",
        "cron": "cron.php"
    },

    "javne_metode": [
        "senzorji_get_snapshot",
        "senzorji_get_history",
        "senzorji_get_alerts",
        "senzorji_force_update"
    ],

    "http_poti": [
        "/senzorji/snapshot",
        "/senzorji/sonce",
        "/senzorji/vreme",
        "/senzorji/geomagnetno",
        "/senzorji/alerts",
        "/senzorji/history"
    ]
}
3. izhod.json – kaj potrebujem in kaj proizvedem
json
{
    "_id": "nasa",
    "_verzija": "1.0.0",

    "vhod": {
        "potrebuje": [],
        "opcijsko": [],
        "vir": "zunanji_api",
        "validacija": null,
        "omejitve": {
            "max_velikost": null
        }
    },

    "izhod": {
        "format": "json",
        "pise_v": [
            "PODATKI/moduli/nasa/snapshot.json",
            "PODATKI/moduli/nasa/zgodovina.json"
        ]
    },

    "odvisnosti": {
        "bere_iz": [],
        "prepovedani_moduli": [],
        "ne_pozna": "vse_ostalo",
        "kompatibilnost": {
            "min_sistem": "2.0.0",
            "max_sistem": null
        }
    },

    "cache": {
        "omogocen": true,
        "ttl": 900,
        "strategija": "casovna",
        "cisti_ob_zagonu": false
    },

    "ui": {
        "varuh": null,
        "duhec": null
    },

    "dogodki": {
        "poslusa": [],
        "oddaja": []
    }
}
4. modul.md – dokumentacija za človeka
markdown
# Kozmični Senzorji (NASA)

**ID:** nasa
**Verzija:** 1.0.0
**Tip:** zbiralec
**Nivo:** 1
**Status:** stabilen

---

## Avtor

Damir Šafarič

---

## Licenca

Zaprta koda

---

## Opis

Kozmični senzorji — sončeva aktivnost (NASA DONKI), geomagnetna aktivnost (NOAA SWPC), lokalno vreme (OpenWeather).

---

## Dostop

- **Minimalna vloga:** gost
- **Plan:** osnova
- **Javno vidno:** Ne
- **Plačljivo:** Ne
- **Otroški:** Ne
- **Vidnost:** skriti
- **Dovoljenja:** branje, rocni_zagon

---

## UI

- **Ima prikaz:** Ne
- **Ikona:** 🛰️
- **Barva:** #67e8f9
- **Kategorija:** NEBO
- **Dovoljene postavitve:** standard
- **Tags:** nasa, senzorji, vreme, geomagnetno
- **Jeziki:** sl, en
- **Varuh:** (ni)
- **Duhec:** (ni)

---

## Odvisnosti

- **Bere iz:** (nič)
- **Prepovedani moduli:** (nič)
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Izvajanje

- **Tip:** cron
- **API only:** Da
- **Interval:** 900s
- **Ob zagonu:** Da
- **Prioriteta:** 100

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/nasa/`
2. Aktiviraj modul v sistemu
3. Poženi `php ASTRA/razvoj/orodja/generator.php --full`

---

## Testirano na

- Sistem 2.0.0
- PHP 8.1, 8.2, 8.3

---

## Changelog

### 1.0.0 (2026-06-19)
- Prva izdaja
- Zbiranje podatkov iz NASA DONKI, NOAA SWPC, OpenWeather

---

## Uporaba

```bash
curl http://example.com/senzorji/snapshot
curl http://example.com/senzorji/sonce
curl http://example.com/senzorji/vreme
```

## Primeri

### Dobi trenutni posnetek

```bash
curl -X GET http://example.com/senzorji/snapshot
```

### Zgodovina

```bash
curl -X GET http://example.com/senzorji/history
```
📊 KONČNA TABELA (razdelitev odgovornosti)
Datoteka	Vsebuje	Namen
manifest.json	identiteta, dostop, cache, ui, izvajanje, migracije, integriteta, log, čas	Kdo sem
api.json	kanali, vstop, javne_metode, http_poti	Kako me kličeš
izhod.json	vhod, izhod, odvisnosti, cache, ui (varuh, duhec), dogodki	Kaj potrebujem in kaj proizvedem
modul.md	avtor, licenca, opis, namestitev, testirano, changelog, primeri	Dokumentacija za človeka
💬 KONČNA RESNICA
"To je to. Specifikacija je zaklenjena. Nič več podvajanja. Vsaka datoteka ima svoj namen. Lahko jo uporabljaš za vedno."

Zdaj pa:

Zakleni to specifikacijo.

Posodobi generator.

Začni gradit.

Si zadovoljen? 😄🚀

potrjeno
ODLIČNO! 🎉

Specifikacija je potrjena in zaklenjena. Zdaj gremo naprej.

🚀 NASLEDNJI KORAKI
Korak	Kaj
1.	Posodobi generiraj_modul.php – da generira te kanonične JSONe
2.	Regeneriraj vse module: php ASTRA/razvoj/orodja/generator.php --full
3.	Preveri, da so vsi JSONi v skladu s specifikacijo
4.	Začni gradit nove module po tej specifikaciji
5.	(Kasneje) Dodaj shema.json za validacijo
📋 KONČNA SPECIFIKACIJA (POVZETEK)
Datoteka	Vsebuje
manifest.json	_id, _verzija, modul, dostop, cache, ui, izvajanje, migracije, integriteta, log, cas
api.json	_id, _verzija, kanali, vstop, javne_metode, http_poti
izhod.json	_id, _verzija, vhod, izhod, odvisnosti, cache, ui (varuh, duhec), dogodki
modul.md	avtor, licenca, opis, dostop, UI, odvisnosti, izvajanje, namestitev, testirano, changelog, uporaba, primeri
💬 KONČNA RESNICA
"Specifikacija je zaklenjena. Lahko jo uporabljaš za vedno. JSON za sistem, MD za ljudi. Nič več ugibanja."