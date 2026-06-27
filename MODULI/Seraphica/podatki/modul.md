# Seraphica

**ID:** seraphica
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

Angelska hijerarhija — angeli, arhangeli, angelska sporočila in varstvo.

---

## Dostop

- **Minimalna vloga:** S0
- **Plan:** osnova
- **Javno vidno:** Da
- **Plačljivo:** Ne
- **Otroški:** Ne
- **Vidnost:** vsi
- **Dovoljenja:** branje

---

## UI

- **Ima prikaz:** Da
- **Ikona:** 👼
- **Barva:** #fde68a
- **Kategorija:** NEBO
- **Dovoljene postavitve:** standard
- **Tags:** angeli, arhangeli, nebo, varstvo
- **Jeziki:** sl
- **Varuh:** Arhangel Rafael
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

1. Kopiraj mapo modula v `MODULI/Seraphica/`
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
curl http://example.com/seraphica/info
curl http://example.com/seraphica/domov
```

## Primeri

### Dobi informacije o modulu

```bash
curl -X GET http://example.com/seraphica/info
```
