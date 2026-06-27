# REGISTER PROJEKTA
> **Živi dokument** — izpolnjuje se sproti, ob vsaki spremembi.
> Vsak izvajalec (AI ali razvijalec) ki doda, spremeni ali zbriše karkoli — TAKOJ vpiše sem.
> Brez tega dokumenta naslednji izvajalec ne ve kaj obstaja.

---

## NAVODILO ZA IZVAJALCE

Ko narediš karkoli na projektu:
1. Poišči pravi razdelek spodaj
2. Dodaj/posodobi vnos
3. Zapiši datum in kdo je naredil
4. Če razdelka ni — ga dodaj po vzorcu

Format datuma: `LLLL-MM-DD`
Format avtorja: `ime` / `GPT` / `Claude` / `DeepSeek` / `AI`

---

## 1. DATOTEKE

Vsaka datoteka v projektu ima tukaj vnos.

### Format vnosa:
```
### pot/do/datoteke.php
- **Namen:** kaj dela
- **Nivo:** 0 (root) / 1 (SISTEM/) / 2 (SISTEM/kernel/) / 3 (SISTEM/kernel/jedro/)
- **Verzija:** 1.0
- **Avtor:** ime | datum
- **Odvisno od:** seznam datotek ki jih ta datoteka kliče
- **Kličejo jo:** seznam datotek ki kličejo to datoteko
- **Izvozi:** seznam funkcij/konstant ki jih ponuja navzven
- **Opombe:** posebnosti, opozorila, TODO
```

---

### pot.php
- **Namen:** Absolutno sidro — definira vse POT_* konstante
- **Nivo:** 0
- **Verzija:** —
- **Avtor:** — | —
- **Odvisno od:** nič
- **Kličejo jo:** vse datoteke v projektu (prva vrstica)
- **Izvozi:** `POT_KOREN`, `POT_SISTEM`, `POT_GLOBALNO`, `POT_MODULI`, `POT_UPORABNIKI`, `POT_ASTRA`, `POT_VSEBINA`, `POT_PODATKI`
- **Opombe:** ⚠ Ne premikaj, ne preimenuj, ne briši

---

*(sem se dodajajo datoteke ko nastajajo)*

---

## 2. KONSTANTE

Vse `define()` konstante v projektu.

| Konstanta | Vrednost / Opis | Definirana v | Datum |
|---|---|---|---|
| `POT_KOREN` | Absolutna pot do root/ | `pot.php` | — |
| `POT_SISTEM` | `.../SISTEM/` | `pot.php` | — |
| `POT_GLOBALNO` | `.../GLOBALNO/` | `pot.php` | — |
| `POT_MODULI` | `.../MODULI/` | `pot.php` | — |
| `POT_UPORABNIKI` | `.../UPORABNIKI/` | `pot.php` | — |
| `POT_ASTRA` | `.../ASTRA/` | `pot.php` | — |
| `POT_VSEBINA` | `.../VSEBINA/` | `pot.php` | — |
| `POT_PODATKI` | `.../PODATKI/` | `pot.php` | — |

---

## 3. FUNKCIJE

Vse PHP funkcije v projektu — globalne in javne metode razredov.

### Format vnosa:
```
| funkcija() | Kaj dela | Parametri | Vrne | Datoteka | Avtor | Datum |
```

| Funkcija | Namen | Parametri | Vrača | Datoteka | Avtor | Datum |
|---|---|---|---|---|---|---|
| *(sem se dodajajo ko nastajajo)* | | | | | | |

---

## 4. RAZREDI

Vsi PHP razredi v projektu.

### Format vnosa:
```
### ImeRazreda
- **Namen:** kaj dela
- **Datoteka:** pot/do/datoteke.php
- **Metode:** seznam javnih metod
- **Avtor:** ime | datum
```

*(sem se dodajajo ko nastajajo)*

---

## 5. BAZE IN SHEME

Vse baze in njihove tabele/sheme.

### Format vnosa:
```
### ime_baze (tip: sqlite/postgres/...)
- **Lokacija:** pot/do/baze.db
- **Uporablja jo:** seznam modulov/datotek
- **Tabele:** seznam tabel
- **Avtor:** ime | datum
```

---

### Json osnovne Baze in Sistemske baze (načrtovane)

| Baza | Tip | Lokacija | Namen | Status |
|---|---|---|---|---|
| Seje/cache | json | `PODATKI/sistem/seja/` | Sistemske seje | aktivno |
| Seje/cache | SQLite | `PODATKI/.baze/sqlite/` | Sistemske seje | načrtovano |
| Astra | SQLite | `ASTRA/.baze/astra.sqlite` | Admin podatki | načrtovano |
| moduli | json | `PODATKI/moduli/[ime_modula]/` | sistemski modulski podatki | aktivno |
| Skupni moduli | PostgreSQL | `MODULI/.baze/postgres/` | Relacije med moduli | načrtovano |
| Analitika | ClickHouse | `PODATKI/.baze/clickhouse/` | Obiskovalci, eventi | načrtovano |
| Uporabniki | json | `UPORABNIKI/uporabniki/[id_uporabnika]]/` | osebni up. podatki | aktivno |
| Uporabniki | json | `PODATKI/uporabniki/[id_uporabnika]]/` | sistemski up. podatki | aktivno |
| Uporabniki | Cassandra | `PODATKI/.baze/cassandra/` | Neomejeni uporabniki | načrtovano |

---

## 6. API KLICI / POTI

Vse poti ki jih `api.php` sprejema.

### Format vnosa:
```
| METHOD | /pot | Kaj dela | N2 handler | Pravice | Avtor | Datum |
```

| Metoda | Pot | Namen | Handler | Pravice | Avtor | Datum |
|---|---|---|---|---|---|---|
| *(sem se dodajajo ko nastajajo)* | | | | | | |

---

## 7. MODULI

Vsi moduli v projektu.

| Ime | Kategorija | Tip | Baza | Pravice | Status | Avtor | Datum |
|---|---|---|---|---|---|---|---|
| Aeternum | osnovni | integriran | sqlite | javno | načrtovano | — | — |

---

## 8. ODVISNOSTI MED DATOTEKAMI

Graf kdo kliče koga. Posodobi ko dodaš novo odvisnost.

```
pot.php
    ← vse datoteke v projektu

SISTEM/api.php (N1)
    → SISTEM/kernel/zaganjalnik.php

SISTEM/kernel/zaganjalnik.php (N2)
    → SISTEM/kernel/jedro/01_upravljalec_svetov.php
    → SISTEM/kernel/jedro/02_napake.php
    → SISTEM/kernel/jedro/03_varnost.php
    → SISTEM/kernel/jedro/04_seja.php
    → SISTEM/kernel/jedro/05_pravice.php
    → SISTEM/kernel/jedro/06_cache.php
    → SISTEM/kernel/jedro/07_dogodki.php
    → SISTEM/kernel/jedro/08_kavlji.php
    → SISTEM/kernel/jedro/09_ponudniki.php
    → SISTEM/kernel/jedro/10_middleware.php
    → SISTEM/kernel/jedro/11_usmerjevalnik.php
    → SISTEM/kernel/jedro/12_validacija.php
    → SISTEM/kernel/jedro/13_api.php
    → SISTEM/kernel/jedro/14_zagon.php
    → SISTEM/kernel/jedro/15_pogon.php
    # 16_upravljalec_runtime.php – IZBRISAN, runtime ne obstaja (glej TEMELJ.md)
```

*(dopolnjuj ko nastajajo nove odvisnosti)*

---

## 9. ZNANE TEŽAVE IN TODO

Aktivne težave in načrtovane izboljšave.

### Format:
```
| ID | Tip | Opis | Datoteka | Prioriteta | Avtor | Datum |
```

| ID | Tip | Opis | Datoteka | Prioriteta | Avtor | Datum |
|---|---|---|---|---|---|---|
| — | — | *(sem se dodajajo)* | — | — | — | — |

**Tipi:** `BUG` / `TODO` / `HACK` / `FIXME` / `IDEJA`
**Prioritete:** `visoka` / `srednja` / `nizka`

---

## 10. SPREMEMBE — DNEVNIK

Kronološki zapis vseh sprememb na projektu.

### Format:
```
### LLLL-MM-DD — Avtor
- **Dodano:** ...
- **Spremenjeno:** ...
- **Odstranjeno:** ...
- **Opombe:** ...
```

---

### 2026-01-01 — Začetek projekta
- **Dodano:** Dokumentacija (USTAVA, ARHITEKTURA, STANDARDI, MODULI, VIZIJA, FAZE, REGISTER)
- **Opombe:** Faza 1 zaključena. Naslednje: Faza 2 — jedro sistema.

---

*(sem se dodajajo vnosi ko nastajajo)*

---

## NAVODILO ZA AI ASISTENTA

Ko dobiš nalogo in jo zaključiš, **obvezno posodobi REGISTER.md**:

```
1. Razdelek 1 (Datoteke)     → dodaj vsako novo datoteko
2. Razdelek 2 (Konstante)    → dodaj vsako novo konstanto
3. Razdelek 3 (Funkcije)     → dodaj vsako novo funkcijo
4. Razdelek 4 (Razredi)      → dodaj vsak nov razred
5. Razdelek 5 (Baze)         → dodaj vsako novo bazo/tabelo
6. Razdelek 6 (API)          → dodaj vsako novo pot
7. Razdelek 8 (Odvisnosti)   → posodobi graf
8. Razdelek 9 (TODO)         → dodaj znane težave
9. Razdelek 10 (Dnevnik)     → zapiši kaj si naredil
```

Če tega ne narediš — naslednji izvajalec dela v temi.

---

*REGISTER.md — verzija 1.0 — živi dokument*
