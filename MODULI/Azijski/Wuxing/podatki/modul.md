# Wuxing · 五行

**ID:** wuxing
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

Wu Xing (五行) — pet elementov: les, ogenj, zemlja, kovina, voda. Temelj kitajske kozmologije. Cikli ustvarjanja in uničevanja, osebnostni profil po elementih, sezonska priporočila.

---

## Izvor tradicije

| Tradicija | Izvirno ime | Jezik |
|-----------|-------------|-------|
| Kitajska / Japonska | 五行 | CJK |

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
- **Ikona:** ☯️
- **Barva:** #fbbf24
- **Kategorija:** PROSTOR
- **PWA podpora:** Da
- **API only:** Ne
- **Tags:** wuxing, pet-elementov, kitajska-kozmologija, 五行
- **Jeziki:** sl, en, zh, zh-TW, ja

---

## Odvisnosti

- **Bere iz:** (nič)
- **Oddaja:** (nič)
- **Ne pozna:** vse ostalo
- **Kompatibilnost:** Sistem >=2.0.0

---

## Namestitev

1. Kopiraj mapo modula v `MODULI/Wuxing/`
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
- Večjezična podpora: sl, en, zh, zh-TW, ja

---

## Uporaba

```bash
curl http://example.com/wuxing/elementi
curl http://example.com/wuxing/profil
curl http://example.com/wuxing/cikel
curl http://example.com/wuxing/sezona
```