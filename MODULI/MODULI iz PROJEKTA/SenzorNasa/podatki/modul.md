# SenzornasaNasa

**ID:** senzornasanasa
**Verzija:** 1.0.0
**Tip:** zbiralec
**Nivo:** 1
**Status:** testni

---

## Avtor

AstraMentalica Mojster

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
- **Tags:** nasa, senzorji, vreme, geomagnetno, sonce
- **Jeziki:** sl
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
- **Prioriteta:** 100

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/SenzornasaNasa/`
2. Aktiviraj modul v sistemu (registriraj v `PODATKI/registri/moduli_register.json`)
3. Poženi `php ASTRA/razvoj/orodja/generator.php --full`

---

## Testirano na

- Sistem >=2.0.0
- PHP 8.1, 8.2, 8.3

---

## Changelog

### 1.0.0 (22.06.2026 10:21)
- Prva izdaja

---

## Uporaba

```bash
curl http://example.com/senzornasanasa/info
curl http://example.com/senzornasanasa/domov
```

## Primeri

### Dobi informacije o modulu

```bash
curl -X GET http://example.com/senzornasanasa/info
```
