# 📊 Poročilo — AstraMentalica moduli
**Datum:** 2026-06-28  
**Avtor:** Claude (za Damir Šafarič)  
**Projekt:** AstraMentalica — modularna mistična platforma

---

## 1. Pregled danes

Danes sem opravil več taskov na modulski plasti projekta:

1. **Reorganizacija modulov v regionalne/kulturne skupine**
2. **Nadgradnja generatorja modulov** (`generator_modul_v2.php`)
3. **Ustvarjanje front page** za izbrane module v `GLOBALNO/`
4. **Popravljanje manjkajočih `modul.php`** datotek

---

## 2. Reorganizacija modulov

### Kategorije
Ustvaril sem **15 novih regionalnih/kulturnih map** pod `MODULI/`:

| Kategorija | Število modulov |
|------------|-----------------|
| Antika | 18 |
| EvropaKelti | 3 |
| EvropaNordija | 4 |
| EvropaSlovani | 4 |
| HebrejskaMistika | 7 |
| Indija | 13 |
| Japonska | 4 |
| Kitajska | 11 |
| Koreja | 2 |
| Prihodnost | 9 |
| Samanizem | 5 |
| Univerzalno | 25 |
| Znanost | 5 |
| AmerikaAmazonija | 1 |
| AmerikaMezoamerika | 5 |

### Stanje
- Skupno ~130+ map/modulov v novih kategorijah
- Nekateri moduli so duplikati med kategorijami (npr. `AfricanaMystica`, `Buddhica`, `TaoMystica`)
- Nekateri moduli imajo samo `podatki/` brez `modul.php`

### Arhiv
- Stara struktura v `MODULI iz PROJEKTA/` in `MODULI_komplet/` ostaja
- `Azijski/` mapa še obstaja ločeno (25 modulov)

---

## 3. Generator modulov v2

### Kaj je novo
- **Admin dostop** preko `Modul_Bridge::uporabnik_pridobi()` (vloga 100+)
- **Sheme** za `manifest.json`, `api.json`, `izhod.json`
- **CLI + HTTP** v eni datoteki (`generator_modul_v2.php`)
- **Log** v `MODULI/cache/generator_log.txt`

### Ukazi
```bash
php MODULI/generator_modul_v2.php pomoč
php MODULI/generator_modul_v2.php kategorije
php MODULI/generator_modul_v2.php ustvari-kategorijo <ime>
php MODULI/generator_modul_v2.php ustvari-modul <ime> <kategorija> [opis]
php MODULI/generator_modul_v2.php sheme
php MODULI/generator_modul_v2.php popravi-manifeste
php MODULI/generator_modul_v2.php dodaj-modul-php
php MODULI/generator_modul_v2.php seznam-modulov [kategorija]
```

### HTTP API
```json
POST /MODULI/generator_modul_v2.php
{"akcija":"ustvari-modul","ime":"Novi","kategorija":"Antika","opis":"Opis"}
```

---

## 4. Front page za module

Ustvaril sem front page datoteke v `GLOBALNO/postavitev/strani/moduli/`:

| Datoteka | Modul | Opis |
|----------|-------|------|
| `tarot.php` | Tarot | Arhetipske karte, vprašanje, znamenje |
| `mystaia.php` | Mystaia | Iniciacije, trgovina, iskanje |
| `aetheris.php` | Aetheris | Forum, avra, barvna analiza, objave |
| `codex.php` | Codex | Osebna knjiga, knjige, zaznamki |
| `runaris.php` | Runaris | Elder Futhark rune, metanje |
| `stelaris.php` | Stelaris | Astrologija, horoskop, tranziti |
| `senzornasa.php` | SenzorNasa | NASA/NOAA senzorji, kozmični podatki |

Vse uporabljajo:
- `modul_layout.php` — skupni layout
- Gradnike: `navigacija`, `kartica`, `obrazec`, `seznam`

---

## 5. Težave in rešitve

### Težava: Generator ni znal generirati `modul.php`
**Rešitev:** Dodal sem `ustvari_modul_php()` funkcijo, ki generira standardni `modul.php` z `Modul_Bridge` vstavkom.

### Težava: Duplikati med kategorijami
**Stanje:** Še nerešeno — moduli kot `AfricanaMystica`, `Animaris`, `Buddhica` obstajajo v več kategorijah.

### Težava: Koreja ima samo 2 modula
**Stanje:** `Hanbang` in `Musok` — morda premalo za samostojno kategorijo.

---

## 6. Odprte točke

- [ ] Razčistiti duplikate med kategorijami
- [ ] Dodati manjkajoče `modul.php` za module ki imajo samo `podatki/`
- [ ] Poravnati `Koreja/` (samo 2 modula)
- [ ] Preveriti ali so vsi manifesti v kanoničnem formatu
- [ ] Obravnati `Azijski/` arhiv (25 modulov)
- [ ] Povezati front page z dejanskimi API-ji preko Bridge-a
- [ ] Dodati več gradnikov (tabele, grafikoni)

---

## 7. Datoteke

### Nove datoteke
- `MODULI/generator_modul_v2.php` — nadgradnjen generator
- `GLOBALNO/postavitev/strani/moduli/tarot.php`
- `GLOBALNO/postavitev/strani/moduli/mystaia.php`
- `GLOBALNO/postavitev/strani/moduli/aetheris.php`
- `GLOBALNO/postavitev/strani/moduli/codex.php`
- `GLOBALNO/postavitev/strani/moduli/runaris.php`
- `GLOBALNO/postavitev/strani/moduli/stelaris.php`
- `GLOBALNO/postavitev/strani/moduli/senzornasa.php`
- `porocilo.md` — to datoteko

### Spremenjene datoteke
- `CLAUDE_DELOVNI_DNEVNIK.md` — dodani vpisi za 2026-06-28

### Generirane/stvarjene
- 46 novih `modul.php` (po generatorju)
- 22 kopiranih `modul.php` iz arhiva
- Manjkajoči `modul.php` za `Runaris` in `SenzorNasa`

---

## 8. Nasvete in spoznanja

### Arhitektura
- **Enkapsulacija deluje.** Ko sem prilagodil `generator_modul_v2.php` za uporabo `Modul_Bridge`, sem ugotovil, da je ločitev med module in jedrom ključna za vzdrževanje.
- **Sheme so nujne.** Brez privzete sheme za `manifest.json`, `api.json` in `izhod.json` bi generirani moduli imeli nepopolne ali nepovezane identitete.
- **Duplikati so samo vidna težava.** Duplikati med kategorijami (npr. `AfricanaMystica` v `Samanizem` in `Prihodnost`) niso tehnična napaka — problem je v dokumentaciji in pričakovanjih.

### Delovni proces
- **CLI je hitrejši od HTTP.** Za masovne operacije (generiranje 70+ `modul.php`) je CLI precej bolj zanesljiv.
- **Log datoteke so neutrudne.** Beleženje v `cache/generator_log.txt` omogoča sledljivost brez odvečnih izpisov.
- **Front page naj ostaja ločen od logike.** Ko sem ustvarjal `tarot.php`, `mystaia.php` itd., sem ohranil ločitev: prikaz v `GLOBALNO/`, logika v `MODULI/`.

### Generator
- **Mankajoče funkcije so lahko skrite.** Napaka z `ustvari_modul_php()` je pokazala, da lahko tako imenomignjena funkcija prepreči celoten tok generiranja.
- **Normalizacija ID-jev je nujna.** Brez `strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $ime))` bi imeli težave s posebnimi znaki (šumniki, whitespace).
- **Arhiv je dober vir vzorcev.** Kopiranje iz `MODULI iz PROJEKTA/` za `Runaris` in `SenzorNasa` je bilo hitrejše od generiranja od nič.

### Pravila
- **Pravila služijo sistemu, ne obratno.** Arhitektura (`PRAVILA_vse.md`) jasno določa, da moduli ne poznajo `SISTEM` — to omogoča samostojnost in testiranje.
- **`pot.php` je neprikosljivo.** Ko sem iskal poti do `Modul_Bridge`, sem vedno uporabljal `__DIR__` relativne poti — nikoli ne pozabi na sidro.

### Načrtovanje
- **Mapna struktura je pomembna.** Preorganizacija v 15 kategorij je bila samostojen korak, ki je zdaj olajša iskanje in vzdrževanje.
- **Front page ne rešuje vsega.** UI strani v `GLOBALNO/` so le okvir — resnična vsebina pride iz modulov preko Bridge-a.

---

## 9. Zaključek

Danes smo dosegli:
- ✅ Reorganizirano strukturo modulov v 15 regionalnih/kulturnih skupin
- ✅ Nadgradnjen generator (`generator_modul_v2.php`) z CLI + HTTP + shemami
- ✅ 7 front page datotek za module (Tarot, Mystaia, Aetheris, Codex, Runaris, Stelaris, SenzorNasa)
- ✅ Popravljeno generiranje manjkajočih `modul.php`

Naslednji koraki:
1. **Duplikati** — določiti primarno kategorijo za vsak modul
2. **Povezava front page** — z `Modul_Bridge::klic()` zamenjati placeholder podatke z dejanskimi
3. **Azijski arhiv** — 25 modulov iz `Azijski/` integrirati v nove kategorije
4. **Koreja** — ali samo 2 modula ostajata samostojna kategorija

---

*Poročilo pripravil Claude za Damir Šafarič, 2026-06-28.*

