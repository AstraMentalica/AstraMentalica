# CosmicaScientia

**ID:** cosmicascientia
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

Kozmična znanost — biopijski ritmi, Schmannove resonance, kozmobiologija.

---

## Dostop

- **Minimalna vloga:** S1
- **Plan:** osnova
- **Javno vidno:** Da
- **Plačljivo:** Ne
- **Otroški:** Ne
- **Vidnost:** vsi
- **Dovoljenja:** branje

---

## UI

- **Ima prikaz:** Da
- **Ikona:** 🔭
- **Barva:** #818cf8
- **Kategorija:** NEBO
- **Dovoljene postavitve:** standard
- **Tags:** bioritem, schumann, resonanca, kozmobiologija
- **Jeziki:** sl
- **Varuh:** (ni)
- **Duhec:** (ni)

---

## Odvisnosti

- **Bere iz:** senzornasanasa
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

1. Kopiraj mapo modula v `MODULI/CosmicaScientia/`
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
curl http://example.com/cosmicascientia/info
curl http://example.com/cosmicascientia/domov
```

## Primeri

### Dobi informacije o modulu

```bash
curl -X GET http://example.com/cosmicascientia/info
```
