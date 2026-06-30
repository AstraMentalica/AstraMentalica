# Kanpo · 漢方

**ID:** kanpo
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

Kampo / Kanpo (漢方) — japonska zeliščna medicina. Razvita iz TCM, prilagojena japonski tradiciji. 200+ formul, zeliščni slovar, diagnostika po japonski medicinski tradiciji.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 漢方 | CJK |

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
- **Ikona:** 🌿
- **Barva:** #4ade80
- **Kategorija:** ZDRAVJE
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** kanpo, kampo, japonska-medicina, zelisca, 漢方
- **Jeziki:** sl, en, ja, zh

---

## Odvisnosti

- **Bere iz:** (nič)
- **Oddaja:** (nič)
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/Kanpo/`
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
curl http://example.com/kanpo/zelisca
curl http://example.com/kanpo/formule
curl http://example.com/kanpo/diagnostika
curl http://example.com/kanpo/iskanje
```