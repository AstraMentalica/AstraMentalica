# Sekki · 節氣

**ID:** sekki
**Verzija:** 1.0.0
**Tip:** zbiralec
**Nivo:** 1
**Status:** razvoj

---

## Avtor

Damir Šafarič

---

## Licenca

Zaprta koda

---

## Opis

Jie Qi / Sekki (節氣) — 24 solarnih terminov kitajskega leta. Lichun, Qingming, Dongzhi... Vsak termin nosi svoja priporočila za prehrano, počitek, aktivnost in rituale.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 節氣 | CJK |

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
- **Dovoljenja:** branje

---

## UI & PWA

- **Ima prikaz:** Da
- **Ikona:** 🌸
- **Barva:** #f9a8d4
- **Kategorija:** NARAVA
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** sekki, jieqi, 24-terminov, lunarni-koledar, 節氣
- **Jeziki:** sl, en, zh, zh-TW, ja

---

## Odvisnosti

- **Bere iz:** (nič)
- **Oddaja:** sekki.termin.sprememba
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/Sekki/`
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
curl http://example.com/sekki/trenutni
curl http://example.com/sekki/koledar
curl http://example.com/sekki/priporocila
curl http://example.com/sekki/ritual
```