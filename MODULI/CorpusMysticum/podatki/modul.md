# CorpusMysticum

**ID:** corpusmysticum
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

Knjižnica zavesti — enciklopedija ezoteričnih znanj, simbolov in tradicij.

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
- **Ikona:** 📚
- **Barva:** #a78bfa
- **Kategorija:** SVET
- **Dovoljene postavitve:** standard
- **Tags:** knjiznica, enciklopedija, znanje, ezoterija
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

1. Kopiraj mapo modula v `MODULI/CorpusMysticum/`
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
curl http://example.com/corpusmysticum/info
curl http://example.com/corpusmysticum/domov
```

## Primeri

### Dobi informacije o modulu

```bash
curl -X GET http://example.com/corpusmysticum/info
```
