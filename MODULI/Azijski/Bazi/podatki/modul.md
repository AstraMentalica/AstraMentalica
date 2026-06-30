# Bazi · 八字

**ID:** bazi
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

Bazi (八字) — štirje stebri usode. Kitajska natalna astrologija na osnovi leta, meseca, dneva in ure rojstva. Izračun desetletnih in letnih period.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 八字 | CJK |

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
- **Ikona:** 🏮
- **Barva:** #f87171
- **Kategorija:** NEBO
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** bazi, kitajska-astrologija, stebri-usode, 八字
- **Jeziki:** sl, en, zh, zh-TW

---

## Odvisnosti

- **Bere iz:** (nič)
- **Oddaja:** bazi.stebri.izracunani
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/Bazi/`
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
curl http://example.com/bazi/stebri
curl http://example.com/bazi/period
curl http://example.com/bazi/kompatibilnost
curl http://example.com/bazi/letna
```