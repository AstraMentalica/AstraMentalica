# Hieroglyphicus

**ID:** hieroglyphicus
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

Hieroglifi kot zapisniki duše in staroegipčanska modrost.

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
- **Ikona:** 𓂀
- **Barva:** #fcd34d
- **Kategorija:** SIMBOLI
- **Dovoljene postavitve:** standard, pro
- **Tags:** hieroglyphicus
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

1. Kopiraj mapo modula v `MODULI/hieroglyphicus/`
2. Aktiviraj modul v sistemu
3. Poženi `php ASTRA/razvoj/orodja/generator.php --full`

---

## Testirano na

- Sistem 2.0.0
- PHP 8.1, 8.2, 8.3

---

## Uporaba

```bash
curl http://example.com/hiero/simboli
curl http://example.com/hiero/iskanje
curl http://example.com/hiero/pomeni
curl http://example.com/hiero/prevodi