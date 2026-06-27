# Reiki · 靈氣

**ID:** reiki
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

Reiki (靈氣) — duhovna energija. Japonska tehnika energijskega zdravljenja. Simboli Reiki, distance healing, chakra uravnavanje po japonski tradiciji. Vodeni seansi in meditacije.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 靈氣 | CJK |

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
- **Ikona:** ✋
- **Barva:** #a5f3fc
- **Kategorija:** PROSTOR
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** reiki, energijsko-zdravljenje, simboli, 靈氣
- **Jeziki:** sl, en, ja, zh

---

## Odvisnosti

- **Bere iz:** (nič)
- **Oddaja:** reiki.seansa.opravljena
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/Reiki/`
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
curl http://example.com/reiki/simboli
curl http://example.com/reiki/seansa
curl http://example.com/reiki/chakre
curl http://example.com/reiki/distance
```