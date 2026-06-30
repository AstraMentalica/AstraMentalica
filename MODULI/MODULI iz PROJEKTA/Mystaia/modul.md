# Mystaia

**ID:** mystaia
**Verzija:** 1.0.0
**Tip:** izvajalec
**Nivo:** 3
**Status:** stabilen

---

## Avtor

Damir Šafarič

---

## Licenca

Zaprta koda

---

## Opis

Aromatična butična spletna trgovina.

---

## Dostop

- **Minimalna vloga:** S0
- **Plan:** osnova
- **Javno vidno:** Da
- **Plačljivo:** Ne
- **Otroški:** Ne
- **Vidnost:** vsi
- **Dovoljenja:** brati_zgodovino, pisati_nastavitve

---

## UI

- **Ima prikaz:** Da
- **Ikona:** ✦
- **Barva:** #e8c84a
- **Kategorija:** SVET
- **Dovoljene postavitve:** standard, pro
- **Tags:** mystaia
- **Jeziki:** sl, en
- **Varuh:** (ni)
- **Duhec:** (ni)

---

## Odvisnosti

- **Bere iz:** PODATKI/moduli/corpusmysticum/
- **Odvisni moduli (zahteve):** corpusmysticum
- **Prepovedani moduli:** (nič)
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Izvajanje

- **Tip:** ui
- **API only:** Ne
- **Ob zagonu:** Ne
- **Interval:** (ni)
- **Prioriteta:** 10

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/mystaia/`
2. Aktiviraj modul v sistemu
3. Poženi `php ASTRA/razvoj/orodja/generator.php --full`

---

## Testirano na

- Sistem 2.0.0
- PHP 8.1, 8.2, 8.3

---

## Changelog

### 1.0.0 (19.06.2026)
- Migracija na kanonično strukturo (manifest.json + api.json + izhod.json + modul.md)

---

## Uporaba

```bash
curl http://example.com/mystaia/izdelki
curl http://example.com/mystaia/kosarica
curl http://example.com/mystaia/narocilo
curl http://example.com/mystaia/webhook
```

## Primeri

### Osnovni klic

```bash
curl -X GET http://example.com/mystaia/izdelki
```