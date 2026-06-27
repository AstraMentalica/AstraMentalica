# Numyra

**ID:** numyra
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

Numerologija in osebna koda.

---

## Dostop

- **Minimalna vloga:** S0
- **Plan:** osnova
- **Javno vidno:** Da
- **Plačljivo:** Ne
- **Otroški:** Ne
- **Vidnost:** vsi
- **Dovoljenja:** brati_zgodovino

---

## UI

- **Ima prikaz:** Da
- **Ikona:** 🔢
- **Barva:** #f9a8d4
- **Kategorija:** SIMBOLI
- **Dovoljene postavitve:** standard, pro
- **Tags:** numyra
- **Jeziki:** sl, en
- **Varuh:** (ni)
- **Duhec:** (ni)

---

## Odvisnosti

- **Bere iz:** (nič)
- **Odvisni moduli (zahteve):** (nič)
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

1. Kopiraj mapo modula v `MODULI/numyra/`
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
curl http://example.com/numyra/izracun
curl http://example.com/numyra/letna_napoved
curl http://example.com/numyra/osebna_koda
```

## Primeri

### Osnovni klic

```bash
curl -X GET http://example.com/numyra/izracun
```