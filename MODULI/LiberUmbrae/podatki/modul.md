# Liber Umbrae

**ID:** liberumbrae
**Verzija:** 1.0.0
**Tip:** zbiralec
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

Osebna knjiga senc in transformacija travm.

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
- **Ikona:** 📖
- **Barva:** #4b5563
- **Kategorija:** POTI
- **Dovoljene postavitve:** standard, pro
- **Tags:** liberumbrae
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
- **Cron vstop:** (ni)

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/liberumbrae/`
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
curl http://example.com/liberumbrae/izpust
curl http://example.com/liberumbrae/zapis
curl http://example.com/liberumbrae/zgodovina
```

## Primeri

### Osnovni klic

```bash
curl -X GET http://example.com/liberumbrae/izpust
```