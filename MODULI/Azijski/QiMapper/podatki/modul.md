# QiMapper · 氣圖

**ID:** qimapper
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

QiMapper (氣圖) — profesionalni kartograf qi energije. Združuje Feng Shui, Bazi in Wu Xing v vizualno energijsko karto prostora ali osebe. API za integracije, JSON izvoz, PDF poročila.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 氣圖 | CJK |

> Vsebina modula se sklicuje na izvirne vire. Vsak sistem je
> predstavljen v svojem kulturnem kontekstu — ne prilagojen zahodni
> mistiki, temveč ohranjem v izvirni obliki z razlago za uporabnika.

---

## Dostop

- **Minimalna vloga:** S2
- **Plan:** pro
- **Plačljivo:** Da
- **Otroški:** Ne
- **Vidnost:** clani
- **Dovoljenja:** brati_zgodovino

---

## UI & PWA

- **Ima prikaz:** Ne
- **Ikona:** 🗺️
- **Barva:** #22d3ee
- **Kategorija:** PROFESIONALNO
- **PWA podpora:** Ne
- **API only:** Da
- **Tags:** qimapper, profesionalno, api, qi-karta, 氣圖
- **Jeziki:** sl, en, zh, zh-TW, ja

---

## Odvisnosti

- **Bere iz:** PODATKI/moduli/bazi/, PODATKI/moduli/fengshui/, PODATKI/moduli/wuxing/
- **Oddaja:** qimapper.karta.generirana
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/QiMapper/`
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
- Večjezična podpora: sl, en, zh, zh-TW, ja

---

## Uporaba

```bash
curl http://example.com/qimapper/karta
curl http://example.com/qimapper/analiza
curl http://example.com/qimapper/izvoz
curl http://example.com/qimapper/api
```