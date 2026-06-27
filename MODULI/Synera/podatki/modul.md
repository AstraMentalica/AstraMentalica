# Synera

**ID:** synera
**Verzija:** 1.0.0
**Tip:** izvajalec
**Nivo:** 3
**Status:** testni

---

## Avtor

AstraMentalica Mojster

---

## Licenca

Zaprta koda

---

## Opis

Sinastria in odnosi — analiza kompatibilnosti, energijski vzorci v razmerjih.

---

## Dostop

- **Minimalna vloga:** S3
- **Plan:** osnova
- **Javno vidno:** Da
- **Plačljivo:** Da
- **Otroški:** Ne
- **Vidnost:** prijavljeni
- **Dovoljenja:** branje

---

## UI

- **Ima prikaz:** Da
- **Ikona:** 🔮
- **Barva:** #e879f9
- **Kategorija:** VIP
- **Dovoljene postavitve:** standard
- **Tags:** sinastria, razmerja, kompatibilnost, vip
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

1. Kopiraj mapo modula v `MODULI/Synera/`
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
curl http://example.com/synera/info
curl http://example.com/synera/domov
```

## Primeri

### Dobi informacije o modulu

```bash
curl -X GET http://example.com/synera/info
```
