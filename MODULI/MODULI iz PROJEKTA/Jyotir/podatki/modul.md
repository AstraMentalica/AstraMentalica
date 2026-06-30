# Jyotir

**ID:** jyotir
**Verzija:** 1.0.0
**Tip:** sestavljalec
**Nivo:** 2
**Status:** stabilen

---

## Avtor

Damir Šafarič

---

## Licenca

Zaprta koda

---

## Opis

Vedska astrologija – Shadbala izračuni, Dasha periode, rojstni horoskop.

---

## Dostop

- **Minimalna vloga:** S1
- **Plan:** razsirjeno
- **Javno vidno:** Da
- **Plačljivo:** Ne
- **Otroški:** Ne
- **Vidnost:** vsi
- **Dovoljenja:** brati_zgodovino, pisati_nastavitve

---

## UI

- **Ima prikaz:** Da
- **Ikona:** ⭐
- **Barva:** #fbbf24
- **Kategorija:** NEBO
- **Dovoljene postavitve:** pro
- **Tags:** jyotir
- **Jeziki:** sl, en
- **Varuh:** (ni)
- **Duhec:** (ni)

---

## Odvisnosti

- **Bere iz:** PODATKI/moduli/stelaris/, PODATKI/moduli/lunaris/
- **Odvisni moduli (zahteve):** stelaris, lunaris
- **Prepovedani moduli:** (nič)
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Izvajanje

- **Tip:** ui
- **API only:** Ne
- **Ob zagonu:** Ne
- **Interval:** (ni)
- **Prioriteta:** 20
- **Cron vstop:** (ni)

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/jyotir/`
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
curl http://example.com/jyotir/dasha
curl http://example.com/jyotir/horoskop
curl http://example.com/jyotir/rojstni
curl http://example.com/jyotir/shadbala
```

## Primeri

### Osnovni klic

```bash
curl -X GET http://example.com/jyotir/dasha
```