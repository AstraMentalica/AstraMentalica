# ZodiacAPI · 星象API

**ID:** zodiacapi
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

ZodiacAPI (星象API) — enotni API za vse astrološke sisteme. Zahodna, vedska (Jyotir), kitajska (Bazi, Ziwei) in japonska (Unmei) astrologija v enem klicu. Za razvijalce in integracije.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 星象API | CJK |

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
- **Dovoljenja:** branje

---

## UI & PWA

- **Ima prikaz:** Ne
- **Ikona:** 🔌
- **Barva:** #6366f1
- **Kategorija:** PROFESIONALNO
- **PWA podpora:** Ne
- **API only:** Da
- **Tags:** zodiacapi, api, razvijalci, astrologija, 星象API
- **Jeziki:** sl, en, zh, ja

---

## Odvisnosti

- **Bere iz:** PODATKI/moduli/bazi/, PODATKI/moduli/ziwei/, PODATKI/moduli/unmei/, PODATKI/moduli/stelaris/
- **Oddaja:** (nič)
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/ZodiacAPI/`
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
- Večjezična podpora: sl, en, zh, ja

---

## Uporaba

```bash
curl http://example.com/zodiacapi/v1/profil
curl http://example.com/zodiacapi/v1/kompatibilnost
curl http://example.com/zodiacapi/v1/forecast
curl http://example.com/zodiacapi/v1/docs
```