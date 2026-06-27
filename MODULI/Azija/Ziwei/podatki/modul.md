# Ziwei · 紫微斗數

**ID:** ziwei
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

Ziwei Dou Shu (紫微斗數) — purpurna zvezda. Cesarska kitajska astrologija z 12 hišami in 114 zvezdami. Natančna analiza osebnosti, kariere, zdravja in odnosov.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 紫微斗數 | CJK |

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
- **Ikona:** ⭐
- **Barva:** #c084fc
- **Kategorija:** NEBO
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** ziwei, purpurna-zvezda, cesarska-astrologija, 紫微
- **Jeziki:** sl, en, zh, zh-TW

---

## Odvisnosti

- **Bere iz:** (nič)
- **Oddaja:** ziwei.horoskop.generiran
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/Ziwei/`
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
- Večjezična podpora: sl, en, zh, zh-TW

---

## Uporaba

```bash
curl http://example.com/ziwei/horoskop
curl http://example.com/ziwei/hiše
curl http://example.com/ziwei/zvezde
curl http://example.com/ziwei/period
```