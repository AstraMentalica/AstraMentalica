# UmbraeCodex

**ID:** umbraecodex
**Verzija:** 1.0.0
**Tip:** zbiralec
**Nivo:** 1
**Status:** testni

---

## Avtor

AstraMentalica Mojster

---

## Licenca

Zaprta koda

---

## Opis

Kodeks senc — senca po Jungu, integracija temne strani, arhetipske sence.

---

## Dostop

- **Minimalna vloga:** S2
- **Plan:** osnova
- **Javno vidno:** Da
- **Plačljivo:** Ne
- **Otroški:** Ne
- **Vidnost:** prijavljeni
- **Dovoljenja:** branje

---

## UI

- **Ima prikaz:** Da
- **Ikona:** 🌑
- **Barva:** #374151
- **Kategorija:** POTI
- **Dovoljene postavitve:** standard
- **Tags:** senca, jung, integracija, arhetip
- **Jeziki:** sl
- **Varuh:** (ni)
- **Duhec:** (ni)

---

## Odvisnosti

- **Bere iz:** (nič)
- **Prepovedani moduli:** (nič)
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Izvajanje

- **Tip:** ui
- **API only:** Ne
- **Prioriteta:** 50

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/UmbraeCodex/`
2. Aktiviraj modul v sistemu (registriraj v `PODATKI/registri/moduli_register.json`)
3. Poženi `php ASTRA/razvoj/orodja/generator.php --full`

---

## Testirano na

- Sistem >=2.0.0
- PHP 8.1, 8.2, 8.3

---

## Changelog

### 1.0.0 (22.06.2026 10:21)
- Prva izdaja

---

## Uporaba

```bash
curl http://example.com/umbraecodex/info
curl http://example.com/umbraecodex/domov
```

## Primeri

### Dobi informacije o modulu

```bash
curl -X GET http://example.com/umbraecodex/info
```
