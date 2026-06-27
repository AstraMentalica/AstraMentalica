# USTAVA PROJEKTA
> Dokument ki definira **nespremenljiva pravila** sistema.
> Vsak AI asistent, razvijalec ali orodje ki dela na tem projektu mora prebrati ta dokument najprej.
> Pravila v USTAVI se ne kršijo. Nikoli.

---

## 0. IDENTITETA PROJEKTA

- Projekt je pisan **izključno v slovenščini** — mape, datoteke, spremenljivke, funkcije, komentarji
- Izjema so: PHP ključne besede, HTML tagi, SQL rezervirane besede, standardne PHP konstante (`true`, `false`, `null`, `echo`, `return`...)
- Projekt je zasnovan za **dolgo življenje** — vsaka odločitev mora biti sprejeta z mislijo "bo to vzdrževal nekdo drug čez 5 let?"
- Projekt je **modularen** — vsak del dela neodvisno, skupaj delajo samo čez SISTEM

---

## 1. ZLATA PRAVILA (absolutna)

### 1.0 pot.php je absolutno sidro
```
pot.php je PRVA datoteka ki se naloži — vedno, povsod, brez izjeme.
Ona ve kje je root. Vse ostalo ve za poti samo čez njo.
```

- `pot.php` leži v **rootu projekta**
- Vsaka datoteka ki potrebuje karkoli vključi NAJPREJ `pot.php`
- `POT_SEF` je izrecno dovoljen in ostane kot absolutna pot za varno okolje/secret prostor
- `pot.php` se prilagodi glede na okolje (lokalno, staging, produkcija) — **sama**, brez da ji kdo pove
- **Ne vsebuje logike** — samo definira konstante `POT_*`
- **Se ne premika, ne preimenuje, ne briše**
- AI asistent jo **nikoli ne sme spremeniti** brez eksplicitne odobritve lastnika

```php
// Vsaka datoteka začne tako:
require_once dirname(__FILE__, /* N nivojev gor do root */) . '/pot.php';

// Potem vse poti čez konstante:
require_once POT_SISTEM . '/api.php';
```

### 1.1 En vhod
```
VSE gre čez SISTEM/api.php. Nobena mapa ne kliče direktno druge mape.
```

### 1.2 En smer
```
Tok gre VEDNO navzdol: N1 → N2 → N3
Nikoli navzgor. Nikoli mimo nivoja.
```

### 1.3 Izolacija
```
Globalne mape (GLOBALNO, MODULI, UPORABNIKI, ASTRA, VSEBINA, PODATKI)
se med seboj NE vidijo, NE kličejo, NE poznajo.
Edina skupna točka je SISTEM.
```

### 1.4 SISTEM ne renderira
```
SISTEM vrača samo podatke (PHP array, JSON).
HTML/CSS/JS generira VEDNO samo frontend (GLOBALNO, ali samostojni moduli).
```

### 1.5 Jedro je zaklenjeno
```
SISTEM/kernel/jedro/ se NE spreminja brez eksplicitnega razloga.
Vsaka sprememba jedra zahteva komentar: // SPREMEMBA: razlog + datum
```

---

## 2. FILOZOFIJA

### Preživeti mora brez avtorja
Vsaka datoteka mora biti razumljiva osebi, ki projekta nikoli ni videla.
To pomeni: jasna imena, komentarji kjer ni očitno, logična struktura.

### Ena stvar, ena datoteka
Vsaka datoteka dela **točno eno stvar**.
Če datoteka dela dve stvari — jo razdelimo.

### Podatki ločeni od logike, logika ločena od prikaza
```
podatki → SISTEM (logika) → frontend (prikaz)
```
Nikoli SQL v HTML. Nikoli HTML v PHP logiki.

### Napake so vredne, ne sramota
Vsaka napaka mora biti:
1. Ujeta (try/catch ali error handler)
2. Zabeležena (PODATKI/log/)
3. Prikazana smiselno (uporabniku sporočilo, ne stack trace)

---

## 3. KAJ JE PREPOVEDANO

| Prepovedano | Zakaj |
|---|---|
| `die()` ali `exit()` v logiki | Nenadzorovano ustavljanje |
| `$_GET`, `$_POST` direktno v logiki | Vedno najprej sanitizacija v N1 |
| SQL direktno v frontend | Kršitev izolacije |
| `require` med globalnimi mapami | Kršitev izolacije |
| Anglešk spremenljivke/funkcije | Kršitev jezikovnega standarda |
| Hardcoded poti (`/var/www/...`) | Neprenosljivost — vedno `POT_*` konstante |
| Komentarji v angleščini | Kršitev jezikovnega standarda |
| Funkcije daljše od 40 vrstic | Razdeliti na manjše |
| Datoteke daljše od 300 vrstic | Razdeliti po odgovornosti |

---

## 4. VERZIONIRANJE IN SPREMEMBE

### Vsaka datoteka ima glavo
```php
<?php
/**
 * DATOTEKA: ime_datoteke.php
 * NAMEN:    Kratko, kaj ta datoteka dela (1-2 vrstici)
 * NIVO:     1 / 2 / 3 (sistemski nivo)
 * ODVISNO:  seznam datotek ki jih kliče (ali "nič")
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 */
```

### Spremembe se beležijo
```php
// SPREMEMBA 1.1 [2026-02-15]: Dodana validacija gesla — Janez
// SPREMEMBA 1.2 [2026-03-01]: Popravek seje pri odjavi — AI/GPT
```

### Verzije
- `1.0` — osnovna različica
- `1.x` — manjše popravke, brez spremembe vmesnika
- `2.0` — sprememba vmesnika ali obnašanja (zahteva posodobitev odvisnih)

---

## 5. KDAJ SME AI DELATI SAMOSTOJNO

AI asistent (GPT, Claude, DeepSeek...) sme samostojno:
- ✅ Dodajati nove module (po MODULI.md)
- ✅ Pisati novo logiko v N2 (nikoli N3 brez odobritve)
- ✅ Pisati frontend v GLOBALNO
- ✅ Pisati vsebino v VSEBINA/
- ✅ Popravljati napake v obstoječih datotekah

AI asistent mora vprašati preden:
- ⛔ Spreminja karkoli v `SISTEM/kernel/jedro/`
- ⛔ Dodaja nove odvisnosti (knjižnice, paketi)
- ⛔ Spreminja strukturo baz
- ⛔ Briše datoteke
- ⛔ Spreminja `pot.php` ali `api.php`

---

## 6. PRIORITETE PRI KONFLIKTIH

Ko si pravili v konfliktu, zmaga višja prioriteta:

```
1. Varnost
2. Pravilnost (deluje kot mora)
3. Izolacija (arhitekturna pravila)
4. Berljivost
5. Zmogljivost
```

> Primer: če bi hitrejša koda zahtevala klic med dvema globalnima mapama — zmaga izolacija, koda ostane počasnejša.

---

*USTAVA.md — verzija 1.0 — temelj projekta*
