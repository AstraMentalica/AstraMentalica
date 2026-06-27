# BotanicaSacra

**ID:** botanicasacra
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

Sveta botanika — zdravilne rastline, zeliščarstvo, rastlinska magija in cvetni jeziki.

---

## Dostop

- **Minimalna vloga:** S0
- **Plan:** osnova
- **Javno vidno:** Da
- **Plačljivo:** Ne
- **Otroški:** Ne
- **Vidnost:** vsi
- **Dovoljenja:** branje

---

## UI

- **Ima prikaz:** Da
- **Ikona:** 🌿
- **Barva:** #4ade80
- **Kategorija:** ZEMLJA
- **Dovoljene postavitve:** standard
- **Tags:** rastline, zeliscje, botanika, magija
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

- **Tip:** ui
- **API only:** Ne
- **Prioriteta:** 50

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/BotanicaSacra/`
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
curl http://example.com/botanicasacra/info
curl http://example.com/botanicasacra/domov
```

## Primeri

### Dobi informacije o modulu

```bash
curl -X GET http://example.com/botanicasacra/info
```
