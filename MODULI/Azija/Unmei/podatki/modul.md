# Unmei · 運命

**ID:** unmei
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

Unmei (運命) — japonska usoda in karma. Sistem životnih poti, preteklih življenj in karminskih vozlov po japonski duhovni tradiciji. Vključuje eto (干支) — japonski zodiak.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 運命 | CJK |

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
- **Ikona:** 🎋
- **Barva:** #86efac
- **Kategorija:** NEBO
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** unmei, japonska-usoda, karma, eto, 運命
- **Jeziki:** sl, en, ja, zh

---

## Odvisnosti

- **Bere iz:** (nič)
- **Oddaja:** unmei.pot.izracunana
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/Unmei/`
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
- Večjezična podpora: sl, en, ja, zh

---

## Uporaba

```bash
curl http://example.com/unmei/pot
curl http://example.com/unmei/eto
curl http://example.com/unmei/karma
curl http://example.com/unmei/letni
```