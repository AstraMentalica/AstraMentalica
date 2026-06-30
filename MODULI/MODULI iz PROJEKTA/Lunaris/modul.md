# Lunaris

**ID:** lunaris
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

Mesečeve faze, lunarni koledar in vpliv lune na počutje. Lunarni cikel.

---

## Dostop

- **Minimalna vloga:** S0
- **Plan:** osnova
- **Javno vidno:** Da
- **Plačljivo:** Ne
- **Otroški:** Ne
- **Vidnost:** vsi
- **Dovoljenja:** 

---

## UI

- **Ima prikaz:** Da
- **Ikona:** 🌙
- **Barva:** #a78bfa
- **Kategorija:** NEBO
- **Dovoljene postavitve:** standard, pro
- **Tags:** lunaris
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
- **Ob zagonu:** Da
- **Interval:** 3600s
- **Prioriteta:** 10

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/lunaris/`
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
curl http://example.com/lunaris/faze
curl http://example.com/lunaris/koledar
curl http://example.com/lunaris/vpliv
```

## Primeri

### Osnovni klic

```bash
curl -X GET http://example.com/lunaris/faze
```