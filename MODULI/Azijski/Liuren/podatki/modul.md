# Liuren · 六壬

**ID:** liuren
**Verzija:** 1.0.0
**Tip:** sestavljalec
**Nivo:** 2
**Status:** razvoj

---

## Avtor

Damir Šafarič

---

## Licenca

Zaprta koda

---

## Opis

Liu Ren (六壬) — šest nebeških ploščic. Ena najstarejših kitajskih divinacijskih metod. Dvanajst nebeških dostojanstvenikov, analiza časa in prostora za odločitve.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 六壬 | CJK |

> Vsebina modula se sklicuje na izvirne vire. Vsak sistem je
> predstavljen v svojem kulturnem kontekstu — ne prilagojen zahodni
> mistiki, temveč ohranjem v izvirni obliki z razlago za uporabnika.

---

## Dostop

- **Minimalna vloga:** S1
- **Plan:** razsirjeno
- **Plačljivo:** Ne
- **Otroški:** Ne
- **Vidnost:** clani
- **Dovoljenja:** brati_zgodovino

---

## UI & PWA

- **Ima prikaz:** Da
- **Ikona:** 🔯
- **Barva:** #818cf8
- **Kategorija:** DIVINACIJA
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** liuren, kitajska-divinacija, nebeške-ploščice, 六壬
- **Jeziki:** sl, en, zh

---

## Odvisnosti

- **Bere iz:** (nič)
- **Oddaja:** liuren.analiza.opravljena
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/Liuren/`
2. Aktiviraj modul v sistemu
3. Poženi `php ASTRA/razvoj/orodja/generator.php --full`

---

## Testirano na

- Sistem 2.0.0
- PHP 8.1, 8.2, 8.3

---

## Changelog

### 1.0.0 (23.06.2026)
- Prva izdaja — azijski in japonski modul
- Večjezična podpora: sl, en, zh

---

## Uporaba

```bash
curl http://example.com/liuren/vprasaj
curl http://example.com/liuren/ploščice
curl http://example.com/liuren/analiza
```