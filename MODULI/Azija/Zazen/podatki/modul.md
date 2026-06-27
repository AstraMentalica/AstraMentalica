# Zazen · 坐禅

**ID:** zazen
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

Zazen (坐禅) — sedeča zen meditacija. Japonska budistična praksa tišine in prisotnosti. Vodene seje, timer, dnevnik uvida, koani za razmišljanje. Theravada in Mahayana pristopi.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 坐禅 | CJK |

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
- **Ikona:** 🧘
- **Barva:** #94a3b8
- **Kategorija:** PRAKSE
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** zazen, zen, meditacija, budizem, 坐禅
- **Jeziki:** sl, en, ja, zh

---

## Odvisnosti

- **Bere iz:** (nič)
- **Oddaja:** zazen.seja.opravljena
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/Zazen/`
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
curl http://example.com/zazen/seja
curl http://example.com/zazen/timer
curl http://example.com/zazen/koani
curl http://example.com/zazen/dnevnik
```