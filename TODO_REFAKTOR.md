# AstraMentalica — TODO Refaktor

## Pregled

Ta dokument vsebuje seznam identificiranih podvajanj, arhitekturnih pomanjkljivosti in predlogov za izboljšave. Namenjen je prihodnjemu razvoju in ne vsebuje sprememb kode.

---

## Kritične najdbe

### 1. Podvojene datoteke

#### AI arhitekturni agenti

| Datoteka 1 | Datoteka 2 | Akcija |
|------------|------------|--------|
| `deepseek_arhitekt.php` | `deepseek_arhitekt (2).php` | ⚠️ CONSOLIDATE |
| `deepseek_nacrtovalec.php` | `deepseek_nacrtovalec2.php` | ⚠️ CONSOLIDATE |
| `deepseek_nadzornik.php` | `deepseek_nadzornik2.php` | ⚠️ CONSOLIDATE |
| `deepseek_nadzornik.php` | `deepseek_nadzornik3.php` | ⚠️ CONSOLIDATE |

**Predlog:** Analiziraj vse verzije, izberi najboljšo, druge arhiviraj.

#### Pot datoteke

| Datoteka | Akcija |
|----------|--------|
| `pot.php` | ✅ V uporabi |
| `pot (2).php` | ⚠️ ARHIVIRAJ ali izbriši |

**Predlog:** Preveri ali je `pot (2).php` še v uporabi. Če ne, izbriši.

#### Moduli register

| Datoteka | Akcija |
|----------|--------|
| `PODATKI/registri/moduli_register.json` | ✅ V uporabi |
| `PODATKI/registri/moduli_register (2).json` | ⚠️ ARHIVIRAJ |

---

### 2. Podvojena logika

#### Modul Boilerplate

**Problem:** Vsak modul ima duplicirano kodo za Bridge iskanje in standardne odzive.

**Lokacije:**
- `MODULI/Univerzalno/Tarot/modul.php` (vrstice 42-70)
- `MODULI/Univerzalno/Synera/modul.php` (vrstice 42-70)
- Vsi drugi moduli...

**Rešitev:** Premakni v `MODULI/Modul_Bridge/embed/`:

```php
// V mini_izhod.php ali novo datoteko mini_odzivi.php
function odziv_uspeh(array $vsebina, string $sporocilo = ''): array {
    return [
        'status' => 'uspeh',
        'status_koda' => 200,
        'sporocilo' => $sporocilo,
        'vsebina' => $vsebina
    ];
}

function odziv_napaka(string $sporocilo, int $koda = 400): array {
    return [
        'status' => 'napaka',
        'status_koda' => $koda,
        'sporocilo' => $sporocilo,
        'vsebina' => []
    ];
}
```

**Predlog:** Dodaj `mini_odzivi.php` in ga naloži v demo stack.

---

### 3. Manjkajoče datoteke

#### Moduli v registeru ki ne obstajajo

```json
"NEBO/Stelaris": { ... }  // ❌ Mapa ne obstaja
"NEBO/Lunaris": { ... }   // ❌ Mapa ne obstaja
"NEBO/Jyotir": { ... }    // ❌ Mapa ne obstaja
```

**Lokacija:** `PODATKI/registri/moduli_register.json`

**Predlog:** Ali ustvari mape ali odstrani iz registra.

---

#### Moduli v registeru ki obstajajo

```json
"ORAKLEUM/Tarot": { ... }  // ✅ MODULI/Univerzalno/Tarot/
"VIP/Synera": { ... }      // ✅ MODULI/Univerzalno/Synera/
```

---

### 4. Prazne mape

| Pot | Akcija |
|-----|---------|
| `AI/zasebniAi/sistemskiAi/` | ⚠️ PREVERI ali izbriši |

---

## Srednja prioriteta

### 5. AI Koder podvajanja

**Problem:** Več AI koder datotek z podobno funkcijo.

**Datoteke:**
- `AI/zasebniAi/arhitekturniAi/deepseek_koder.php`
- `AI/zasebniAi/arhitekturniAi/openclaw_coder.php`

**Predlog:** Analiziraj razlike in konsolidiraj v enega.

---

### 6. Numerološka baza

**Pot:** `GLOBALNO/numyra_baza/pomeni.json`

**Problem:** Baza obstaja samo v kodi (`GLOBALNO/funkcije.php` vsebuje fallback).

**Predlog:** Preveri ali `numyra_baza/pomeni.json` res obstaja.

---

### 7. AI integracija poti

**Problem:** `asistentAi.php` vključuje `../includes/ai_deepseek_json.php` ki ne obstaja.

**Lokacija:** `AI/asistentAi.php:7`

```php
require_once '../includes/ai_deepseek_json.php';  // ❌ Pot ne obstaja
```

**Predlog:** Popravi pot ali ustvari manjkajočo datoteko.

---

## Nizka prioriteta

### 8. Dokumentacijska podvajanja

| Datoteka | Opomba |
|----------|--------|
| `AstraMentalica.md` | Glavni README |
| `PROJEKT_PREGLED.md` | (Novo ustvarjeno) |
| `MODULI.md` | (Novo ustvarjeno) |
| `AGENTI.md` | (Novo ustvarjeno) |
| `KNJIGE.md` | (Novo ustvarjeno) |
| `ARHITEKTURA.md` | (Novo ustvarjeno) |
| `TODO_REFAKTOR.md` | (Novo ustvarjeno) |

**Predlog:** Konsolidiraj v en glavni README ali ohrani ločeno za Copilot.

---

### 9. Modul knjižnice podvajanja

**Problem:** Codex in Orakleum imata zelo podobne datoteke.

**Orakleum datoteke:**
- `modul_orakleum_api.php`
- `modul_orakleum_config.php`
- `modul_orakleum_demo.php`
- `modul_orakleum_funkcije.php`
- `modul_orakleum_jsonbaza.php`
- `modul_orakleum_pravila.php`

**Codex datoteke:**
- `modul_codex_api.php`
- `modul_codex_config.php`
- `modul_codex_demo.php`
- `modul_codex_funkcije.php`
- `modul_codex_jsonbaza.php`
- `modul_codex_pravila.php`

**Predlog:** Ustvari skupno osnovo in podeduje.

---

### 10. Globalne funkcije lokacija

**Problem:** `GLOBALNO/funkcije.php` vsebuje samo numerološke funkcije.

**Predlog:** Preimenuj v `GLOBALNO/funkcije_numyra.php` ali ustvari `GLOBALNO/funkcije/` mapo.

---

## Arhkturne izboljšave

### 11. PHP Tipi

**Problem:** Večina funkcij nima PHPDoc tipov.

**Predlog:** Dodaj tipe za:
- Vse javne funkcije
- Return tipe
- Parametrične tipe

---

### 12. Error Handling

**Problem:** Nekateri error handling-i so inkonsistentni.

**Predlog:** Standardiziraj:
```php
// Namesto
echo json_encode(['napaka' => 'Modul_Bridge ni najden']);
exit;

// Uporabi
return ['status' => 'napaka', 'sporocilo' => 'Modul_Bridge ni najden'];
```

---

### 13. Skupni vmesnik za module

**Problem:** Vsak modul ima svojo implementacijo akcij.

**Predlog:** Definiraj vmesnik:
```php
interface ModulInterface {
    public static function akcija(string $akcija, array $podatki): array;
    public static function info(): array;
}
```

---

### 14. Centralizirano beleženje

**Problem:** `error_log()` uporabljen na več mestih.

**Predlog:** Ustvari Logger razred:
```php
class Logger {
    public static function napaka(string $sporocilo, array $kontekst = []): void;
    public static function opozorilo(string $sporocilo, array $kontekst = []): void;
    public static function info(string $sporocilo, array $kontekst = []): void;
}
```

---

## Copilot optimizacije

### 15. .claude/ mapa

**Trenutno stanje:** Mapa obstaja.

**Predlog:** Dodaj `instructions.md` za Copilot:
```markdown
# AstraMentalica - Copilot instrukcije

## Projekt struktura
[opis strukture]

## Koda standardi
- Slovenščina v komentarjih
- strict_types=1
- PHPDoc za vse funkcije

## Pomembna pravila
[specifična pravila]
```

---

### 16. Type stubs

**Predlog:** Ustvari type stubs za JSON baze:
```php
// PODATKI/stubs/numyra_pomeni.stub.php
/**
 * @return array<string, array{
 *   ime: string,
 *   opis: string,
 *   mozne_izbire: string[]
 * }>
 */
function numyra_baza(): array {}
```

---

### 17. Test stubs

**Predlog:** Ustvari test mape za ključne funkcije:
```
MODULI/
├── Tarot/
│   └── tests/
│       ├── modul_tarot_test.php
│       └── modul_tarot_akcija_test.php
```

---

## Akcijski načrt

### Fazni pristop

#### Faza 1: Čiščenje (hitro)
- [ ] Izbriši `pot (2).php` če ni v uporabi
- [ ] Izbriši `moduli_register (2).json` če ni v uporabi
- [ ] Preveri `sistemskiAi/` mapo

#### Faza 2: Konsolidacija (srednje)
- [ ] Analiziraj DeepSeek podvojitve
- [ ] Ustvari `mini_odzivi.php` v Modul_Bridge
- [ ] Konsolidiraj module boilerplate

#### Faza 3: Arhitektura (dolgo)
- [ ] Dodaj tipe za jedro
- [ ] Implementiraj Logger razred
- [ ] Ustvari ModulInterface

#### Faza 4: Dokumentacija (med)
- [ ] Posodobi Copilot instrukcije
- [ ] Dodaj type stubs
- [ ] Ustvari test strukturo

---

## Izključitve

Te datoteke/map so namensko puščene ali imajo specifičen Namen:

| Pot | Razlog |
|-----|--------|
| `NI_ZA_GIT/` | Izrecno izključeno iz git-a |
| `.venv/` | Python virtualno okolje |
| `.cpanel.yml` | CPanel konfiguracija |
| `.github/` | GitHub akcije |
| `.vscode/` | VSCode nastavitve |

---

## Status legend

| Oznaka | Pomen |
|--------|-------|
| ✅ | V redu / Dokončano |
| ⚠️ | Potrebuje pozornost |
| ❌ | Manjkajoče |
| 🔧 | Za popraviti |

---

## Avtor

**AstraMentalica Mojster**
**Datum:** 30.6.2026
