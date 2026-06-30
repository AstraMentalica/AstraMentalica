# Shiatsu · 指圧

**ID:** shiatsu
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

Shiatsu (指圧) — pritisk s prsti. Japonska tehnika pritiskovnih točk na meridianih. Interaktivni atlas točk, vodene self-shiatsu sekvence, diagnostika po meridianih.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 指圧 | CJK |

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
- **Ikona:** 👆
- **Barva:** #f87171
- **Kategorija:** ZDRAVJE
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** shiatsu, pritiskovne-točke, meridiani, 指圧
- **Jeziki:** sl, en, ja, zh

---

## Odvisnosti

- **Bere iz:** PODATKI/moduli/qivitalis/
- **Oddaja:** shiatsu.sekvenca.opravljena
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/Shiatsu/`
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
curl http://example.com/shiatsu/točke
curl http://example.com/shiatsu/sekvence
curl http://example.com/shiatsu/meridiani
curl http://example.com/shiatsu/diagnostika
```