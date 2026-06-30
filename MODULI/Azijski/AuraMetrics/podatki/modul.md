# AuraMetrics · 氣場測量

**ID:** aurametrics
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

AuraMetrics (氣場測量) — merjenje energijskega polja. Kvantitativni model aure, čaker in meridianskega sistema. Vprašalniki, časovne meritve, trendi, primerjave. Profesionalni izvoz za terapevte.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 氣場測量 | CJK |

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

- **Ima prikaz:** Ne
- **Ikona:** 📊
- **Barva:** #fb7185
- **Kategorija:** PROFESIONALNO
- **PWA podpora:** Ne
- **API only:** Ne
- **Tags:** aurametrics, merjenje, profesionalno, terapevti, 氣場測量
- **Jeziki:** sl, en, zh, ja

---

## Odvisnosti

- **Bere iz:** PODATKI/moduli/reiki/, PODATKI/moduli/wuxing/, PODATKI/moduli/qimapper/
- **Oddaja:** aurametrics.meritev.opravljena
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/AuraMetrics/`
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
- Večjezična podpora: sl, en, zh, ja

---

## Uporaba

```bash
curl http://example.com/aurametrics/merjenje
curl http://example.com/aurametrics/trend
curl http://example.com/aurametrics/primerjava
curl http://example.com/aurametrics/izvoz
```