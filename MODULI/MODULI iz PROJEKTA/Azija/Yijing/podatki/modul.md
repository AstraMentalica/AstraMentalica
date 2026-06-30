# Yijing · 易經

**ID:** yijing
**Verzija:** 1.0.0
**Tip:** izvajalec
**Nivo:** 3
**Status:** razvoj

---

## Avtor

Damir Šafarič

---

## Licenca

Zaprta koda

---

## Opis

Yi Jing (易經) — Knjiga sprememb. 64 heksagramov, 384 črt. Starodavna kitajska divinacija in filozofija. Metanje kovancev, yarrow stick metoda, interpretacija sprememb.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 易經 | CJK |

> Vsebina modula se sklicuje na izvirne vire. Vsak sistem je
> predstavljen v svojem kulturnem kontekstu — ne prilagojen zahodni
> mistiki, temveč ohranjem v izvirni obliki z razlago za uporabnika.

---

## Dostop

- **Minimalna vloga:** S0
- **Plan:** osnova
- **Plačljivo:** Ne
- **Otroški:** Ne
- **Vidnost:** vsi
- **Dovoljenja:** brati_zgodovino

---

## UI & PWA

- **Ima prikaz:** Da
- **Ikona:** ☰
- **Barva:** #f59e0b
- **Kategorija:** DIVINACIJA
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** yijing, i-ching, heksagrami, divinacija, 易經
- **Jeziki:** sl, en, zh, zh-TW, ja

---

## Odvisnosti

- **Bere iz:** (nič)
- **Oddaja:** yijing.heksagram.generiran
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/Yijing/`
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
curl http://example.com/yijing/vprasaj
curl http://example.com/yijing/heksagram
curl http://example.com/yijing/zgodovina
curl http://example.com/yijing/knjiznica
```