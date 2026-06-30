# AstraMentalica — Moduli

## Pregled

Sistem AstraMentalica vsebuje **25+ samostojnih modulov**, organiziranih v dve glavni kategoriji:
- **Univerzalno** — uporabniški moduli
- **Knjižnice** — specializirane knjižnice z bogato vsebino

Vsak modul je **popolnoma neodvisen** in komunicira z jedrom preko **Modul_Bridge**.

---

## Modul_Bridge (Centralni most)

**Pot:** `MODULI/Modul_Bridge/modul_bridge.php`

### Javni API

```php
Modul_Bridge::vloga_preveri(string $zahtevana): bool
    // Preveri ali ima uporabnik zahtevano vlogo
    // $zahtevana: 'GOST', 'S0', 'S1', 'S2', 'S3', 'S4', 'S5', 'ADMIN'

Modul_Bridge::uporabnik_pridobi(): array
    // Pridobi trenutnega uporabnika
    // Vrne: ['id' => int, 'ime' => string, 'vloga' => int]

Modul_Bridge::podatki_beri(string $kljuc): mixed
    // Beri podatke preko Bridge-a

Modul_Bridge::podatki_pisi(string $kljuc, $vrednost): bool
    // Shrani podatke preko Bridge-a

Modul_Bridge::modul_klic(string $modul, string $akcija, array $podatki): array
    // Pokliči drugega modula preko Bridge-a

Modul_Bridge::klic(string $akcija, array $podatki): array
    // Klic sistema preko mostu
```

### Demo način

Če Modul_Bridge ne najde polnega sistema, deluje v **demo načinu**:
- Vloge vedno vrnejo `true`
- Uporabnik je "Demo" z vlogo 100 (admin)
- Podatkovni klici vrnejo `null`

### Potrebne datoteke za demo način

```
MODULI/Modul_Bridge/embed/
├── mini_konstante.php
├── mini_vloge.php
├── mini_seja.php
├── mini_cache.php
└── mini_izhod.php
```

---

## Struktura modula (standard)

Vsak modul vsebuje:

```
IME_MODULA/
├── .htaccess          # Zaščita mape
├── modul.php          # Glavna vstopna točka
├── podatki/           # JSON/CSV podatki modula
├── cache/             # Predpomnilnik
└── temp/              # Začasne datoteke
```

### modul.php predloga

```php
<?php
declare(strict_types=1);

// ── POIŠČI BRIDGE ──────────────────────────────────────────
$bridgePoti = [
    __DIR__ . '/../../Modul_Bridge/modul_bridge.php',
];

$bridgeNajden = false;
foreach ($bridgePoti as $pot) {
    if (file_exists($pot)) {
        require_once $pot;
        $bridgeNajden = true;
        break;
    }
}

if (!$bridgeNajden) {
    header('Content-Type: application/json');
    echo json_encode(['napaka' => 'Modul_Bridge ni najden']);
    exit;
}

// ── STANDARDNI ODZIVI ──────────────────────────────────────
if (!function_exists('odziv_uspeh')) {
    function odziv_uspeh(array $vsebina, string $sporocilo = ''): array {
        return ['status' => 'uspeh', 'status_koda' => 200, 'sporocilo' => $sporocilo, 'vsebina' => $vsebina];
    }
    function odziv_napaka(string $sporocilo, int $koda = 400): array {
        return ['status' => 'napaka', 'status_koda' => $koda, 'sporocilo' => $sporocilo, 'vsebina' => []];
    }
}

// ============================
// VSTOPNA TOČKA MODULA
// ============================

function modul_IME_akcija(string $akcija, array $podatki = []): array {
    if (!Modul_Bridge::vloga_preveri('S0')) {
        return odziv_napaka('Dostop zavrnjen', 403);
    }

    return match($akcija) {
        'info'  => _modul_ime_info($podatki),
        'domov' => _modul_ime_domov($podatki),
        default => odziv_napaka("Neznana akcija: $akcija", 400),
    };
}

// ── ČE SE KLIČE DIREKTNO ──────────────────────────────────
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'modul.php' && !defined('SISTEM_OBSTAJA')) {
    $akcija  = $_REQUEST['akcija'] ?? 'domov';
    $odziv   = modul_IME_akcija($akcija, $_REQUEST);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($odziv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE');
}
```

---

## Seznam modulov

### Kategorija: Univerzalno

| Modul | Pot | Namen | Min Vloga | Nivo | Tip |
|-------|-----|-------|-----------|------|-----|
| **Tarot** | `Univerzalno/Tarot/` | Arhetipske karte, razlage | S0 | 3 | izvajalec |
| **Synera** | `Univerzalno/Synera/` | Sinergija zavesti | S3 | 3 | izvajalec |
| **CorpusMysticum** | `Univerzalno/CorpusMysticum/` | Duhovna knjižnica | S2 | 3 | izvajalec |
| **Lunaris** | `Univerzalno/Lunaris/` | Lunina modrost | S0 | 1 | zbiralec |
| **Transmutaria** | `Univerzalno/Transmutaria/` | Transmutacija energije | S0 | 1 | zbiralec |
| **Aeternum** | `Univerzalno/Aeternum/` | Večna modrost | - | - | - |
| **Aetheris** | `Univerzalno/Aetheris/` | Eterična energija | - | - | - |
| **AlchymiaAurea** | `Univerzalno/AlchymiaAurea/` | Zlata alkimija | - | - | - |
| **AuroraMystica** | `Univerzalno/AuroraMystica/` | Morgonska skrivnost | - | - | - |
| **Buddhica** | `Univerzalno/Buddhica/` | Budistična modrost | - | - | - |
| **Celestara** | `Univerzalno/Celestara/` | Nebeška modrost | - | - | - |
| **Crystallum** | `Univerzalno/Crystallum/` | Kristalna energija | - | - | - |
| **Energetica** | `Univerzalno/Energetica/` | Energijska polja | - | - | - |
| **GeometriaSacra** | `Univerzalno/GeometriaSacra/` | Sveta geometrija | - | - | - |
| **GeometricaSacra** | `Univerzalno/GeometricaSacra/` | Sveta geometrija (razno) | - | - | - |
| **Gnostica** | `Univerzalno/Gnostica/` | Gnostična modrost | - | - | - |
| **Hermetica** | `Univerzalno/Hermetica/` | Hermetična filozofija | - | - | - |
| **Mystaia** | `Univerzalno/Mystaia/` | Mistična trgovina | - | - | - |
| **NadaBrahma** | `Univerzalno/NadaBrahma/` | Zvočna meditacija | - | - | - |
| **Occultum** | `Univerzalno/Occultum/` | Okultno znanje | - | - | - |
| **QuantumMystica** | `Univerzalno/QuantumMystica/` | Kvantna mistika | - | - | - |
| **Sonaris** | `Univerzalno/Sonaris/` | Zvočna harmonija | - | - | - |
| **Sufica** | `Univerzalno/Sufica/` | Sufistična modrost | - | - | - |
| **ViaAnimae** | `Univerzalno/ViaAnimae/` | Pot duše | - | - | - |
| **VibraMystica** | `Univerzalno/VibraMystica/` | Vibracijska mistika | - | - | - |

### Kategorija: Knjižnice (Orakleum)

| Knjižnica | Pot | Namen |
|-----------|-----|-------|
| **Orakleum** | `Knjiznice/Orakleum/` | Mistični orakelj z API |
| **Codex** | `Knjiznice/Codex/` | Starodavna modrost z API |
| **CodexAntiqua** | `Knjiznice/CodexAntiqua/` | Antični zapiski |
| **UmbraeCodex** | `Knjiznice/UmbraeCodex/` | Senčna knjižnica |
| **OraculumVisionis** | `Knjiznice/OraculumVisionis/` | Vizijski orakelj |

### Orakleum struktura (primer knjižnice)

```
Knjiznice/Orakleum/
├── modul.php                    # Glavni modul
├── modul.json                   # Konfiguracija
├── modul_orakleum.php           # Logika
├── modul_orakleum_api.php      # API endpoint
├── modul_orakleum_config.php    # Nastavitve
├── modul_orakleum_demo.php      # Demo način
├── modul_orakleum_funkcije.php  # Pomožne funkcije
├── modul_orakleum_jsonbaza.php  # JSON baza podatkov
├── modul_orakleum_pravila.php  # Pravila in omejitve
├── README.md                    # Dokumentacija
├── podatki/                     # Podatki oraklja
├── cache/                       # Cache
└── temp/                        # Začasne datoteke
```

---

## Registracija modulov

Moduli se registrirajo v `PODATKI/registri/moduli_register.json`:

```json
{
    "KATEGORIJA/IME_MODULA": {
        "aktiviran": true,
        "nivo": 1-3,
        "tip": "zbiralec|sestavljalec|izvajalec",
        "ime": "Ime Modula",
        "ikona": "🔮",
        "vloga_min": 10-50
    }
}
```

---

## Akcije modulov

Vsak modul podpira naslednje standardne akcije:

| Akcija | Opis | Zahtevana vloga |
|--------|------|-----------------|
| `info` | Informacije o modulu | Odvisno od modula |
| `domov` | Domača stran modula | Odvisno od modula |

### Dodatne akcije (primer Tarot)

```php
// V modul_tarot_akcija()
return match($akcija) {
    'info'         => _modul_tarot_info($podatki),
    'domov'        => _modul_tarot_domov($podatki),
    'karilista'    => _modul_tarot_karilista($podatki),
    'razlaga'      => _modul_tarot_razlaga($podatki),
    'metanje'      => _modul_tarot_metanje($podatki),
    default        => odziv_napaka("Neznana akcija: $akcija", 400),
};
```

---

## Modul Tipi

| Tip | Opis | Nivo |
|-----|-------|------|
| **zbiralec** | Zbira podatke (1) | Nivo 1 |
| **sestavljalec** | Sestavlja in analizira (2) | Nivo 2 |
| **izvajalec** | Izvaja kompleksne operacije (3) | Nivo 3 |

---

## Pomembna opozorila

1. **Ne kliči SISTEM/ direktno** — uporabljaj samo Modul_Bridge
2. **Ne uporabljaj $_SESSION, $_POST, $_GET direktno** — uporabljaj Bridge ali request parameterje
3. **Ne piši izven lastne mape** — uporabljaj Bridge za shranjevanje podatkov
4. **Vedno preveri vlogo** pred izvedbo akcije

---

## TODO Refaktor

1. **Standardizacija** — Vsi moduli naj imajo identično strukturo
2. **Skupne funkcije** — `odziv_uspeh()` in `odziv_napaka()` v Modul_Bridge
3. **Skupna konfiguracija** — `modul.json` format za vse module
4. **Tipi** — Dodaj PHPDoc tipe za vse funkcije
5. **Testi** — Dodaj unit teste za ključne module

---

## Avtor

**AstraMentalica Mojster**
