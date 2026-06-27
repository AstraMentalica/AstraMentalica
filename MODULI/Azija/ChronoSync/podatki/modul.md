# ChronoSync · 時間同步

**ID:** chronosync
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

ChronoSync (時間同步) — sinhronizacija kozmičnih in zemeljskih ciklov. Združuje kitajski lunarni, solarni (Sekki), gregorijanski in vedski (Jyotir) koledar. Ugodni dnevi za akcije, opomniki.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 時間同步 | CJK |

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

- **Ima prikaz:** Da
- **Ikona:** 🕐
- **Barva:** #a78bfa
- **Kategorija:** PROFESIONALNO
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** chronosync, koledar, ugodni-dnevi, profesionalno, 時間同步
- **Jeziki:** sl, en, zh, zh-TW, ja

---

## Odvisnosti

- **Bere iz:** PODATKI/moduli/sekki/, PODATKI/moduli/lunaris/, PODATKI/moduli/bazi/
- **Oddaja:** chronosync.ugodni_dan.oznacen
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/ChronoSync/`
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
curl http://example.com/chronosync/danes
curl http://example.com/chronosync/teden
curl http://example.com/chronosync/ugodni
curl http://example.com/chronosync/api
```