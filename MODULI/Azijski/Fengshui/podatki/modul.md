# Fengshui · 風水

**ID:** fengshui
**Verzija:** 1.0.0
**Tip:** izvajalec
**Nivo:** 3
**Status:** razvoj

---

## Avtor

Damir Šafarič

---

## Licenca

Zaprta koda

---

## Opis

Feng Shui (風水) — veter in voda. Analiza prostora, smeri in toka qi energije. Bagua mreža, kua število, ugodne in neugodne smeri za dom, pisarno in spletni prostor.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 風水 | CJK |

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
- **Ikona:** 🧭
- **Barva:** #34d399
- **Kategorija:** PROSTOR
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** fengshui, bagua, qi, prostor, 風水
- **Jeziki:** sl, en, zh, zh-TW

---

## Odvisnosti

- **Bere iz:** PODATKI/moduli/bazi/
- **Oddaja:** fengshui.analiza.opravljena
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/Fengshui/`
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
- Večjezična podpora: sl, en, zh, zh-TW

---

## Uporaba

```bash
curl http://example.com/fengshui/analiza
curl http://example.com/fengshui/bagua
curl http://example.com/fengshui/kua
curl http://example.com/fengshui/ugodne-smeri
```