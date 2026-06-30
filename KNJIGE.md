# AstraMentalica — Knjige in podatkovne baze

## Pregled

Sistem AstraMentalica vsebuje obsežne zbirke podatkov in "knjig" v različnih formatih:
- **JSON baze** — strukturirani podatki
- **Modulske baze** — podatki specifični za module
- **Knjižnice** — specializirane zbirke znanja (Orakleum, Codex...)
- **Registri** — sistemski registri in konfiguracije

---

## Glavne JSON baze

### PODATKI/data_bus.json

**Pot:** `PODATKI/data_bus.json`

**Namen:** Message bus za interno komunikacijo med komponentami

**Struktura:**
```json
{
    "topics": {
        "runaris.metanje": [
            {
                "id": "bb4f6b6f56468487",
                "cas": "2026-06-24T08:55:55+00:00",
                "topic": "runaris.metanje",
                "payload": {
                    "rune": [
                        {
                            "runa": "Fehu",
                            "interpretacija": "Blaginja"
                        }
                    ],
                    "_meta": {
                        "source": "Runaris"
                    }
                }
            }
        ]
    }
}
```

---

### PODATKI/zbirka.json

**Pot:** `PODATKI/zbirka.json`

**Namen:** Glavna zbirka sistemskih podatkov

---

### PODATKI/frekvence.json

**Pot:** `PODATKI/frekvence.json`

**Namen:** Seznam frekvenc za zvočne in vibracijske module

---

### PODATKI/kanonični_varuhi.json

**Pot:** `PODATKI/kanonični_varuhi.json`

**Namen:** Seznam kanoničnih varuhov sistema

---

### PODATKI/globalno/popravki.json

**Pot:** `PODATKI/globalno/popravki.json`

**Namen:** Zgodovina popravkov sistema

---

### PODATKI/globalno/postavitve.json

**Pot:** `PODATKI/globalno/postavitve.json`

**Namen:** Sistemske postavitve in konfiguracije

---

### PODATKI/globalno/inventura.json

**Pot:** `PODATKI/globalno/inventura.json`

**Namen:** Inventura modulov in komponent

---

## Registri

### PODATKI/registri/moduli_register.json

**Pot:** `PODATKI/registri/moduli_register.json`

**Namen:** Register vseh aktivnih modulov

**Struktura:**
```json
{
    "KATEGORIJA/IME_MODULA": {
        "aktiviran": true|false,
        "nivo": 1-3,
        "tip": "zbiralec|sestavljalec|izvajalec",
        "ime": "Ime Modula",
        "ikona": "🔮",
        "vloga_min": 10-50
    }
}
```

---

### PODATKI/registri/rbac/

**Pot:** `PODATKI/registri/rbac/`

**Namen:** RBAC pravila dostopa

---

## Knjižnice Orakleum

**Pot:** `MODULI/Knjiznice/`

Knjižnice so specializirane zbirke znanja z bogatim API-jem:

### Orakleum

**Pot:** `MODULI/Knjiznice/Orakleum/`

**Datoteke:**
| Datoteka | Namen |
|----------|-------|
| `modul.php` | Glavni modul |
| `modul.json` | Konfiguracija |
| `modul_orakleum.php` | Logika |
| `modul_orakleum_api.php` | API endpoint |
| `modul_orakleum_config.php` | Nastavitve |
| `modul_orakleum_demo.php` | Demo način |
| `modul_orakleum_funkcije.php` | Pomožne funkcije |
| `modul_orakleum_jsonbaza.php` | JSON baza |
| `modul_orakleum_pravila.php` | Pravila |
| `README.md` | Dokumentacija |
| `Elementi/` | Elementi oraklja |
| `podatki/` | Podatki oraklja |
| `cache/` | Cache |
| `temp/` | Začasne datoteke |

---

### Codex

**Pot:** `MODULI/Knjiznice/Codex/`

**Namen:** Starodavna modrost z API-jem

**Datoteke:** (podobno kot Orakleum)
| Datoteka | Namen |
|----------|-------|
| `modul.php` | Glavni modul |
| `modul.json` | Konfiguracija |
| `modul_codex.php` | Logika |
| `modul_codex_api.php` | API endpoint |
| `modul_codex_config.php` | Nastavitve |
| `modul_codex_demo.php` | Demo način |
| `modul_codex_funkcije.php` | Pomožne funkcije |
| `modul_codex_jsonbaza.php` | JSON baza |
| `modul_codex_pravila.php` | Pravila |
| `README.md` | Dokumentacija |
| `Elementi/` | Elementi codex |
| `podatki/` | Podatki codex |
| `cache/` | Cache |
| `temp/` | Začasne datoteke |

---

### CodexAntiqua

**Pot:** `MODULI/Knjiznice/CodexAntiqua/`

**Namen:** Antični zapiski

---

### UmbraeCodex

**Pot:** `MODULI/Knjiznice/UmbraeCodex/`

**Namen:** Senčna knjižnica

---

### OraculumVisionis

**Pot:** `MODULI/Knjiznice/OraculumVisionis/`

**Namen:** Vizijski orakelj

---

## Numerološka baza (NumYra)

**Pot:** `GLOBALNO/numyra_baza/pomeni.json`

**Namen:** Numerološke interpretacije in pomeni

**Struktura:**
```json
{
    "stevila": {
        "1": {
            "ime": "Energija",
            "opis": "...",
            "mozne_izbire": [...]
        }
    },
    "letni_cikli": {
        "1": "Opis leta 1",
        "2": "Opis leta 2"
    },
    "glavni_cikli": {
        "mladost": {
            "obdobje": "0-28 let",
            "opis": "..."
        },
        "zrelost": {
            "obdobje": "28-56 let",
            "opis": "..."
        },
        "starost": {
            "obdobje": "56+ let",
            "opis": "..."
        }
    },
    "angelska_stevila": {
        "111": "Ponovitev angela 111",
        "222": "Ponovitev angela 222"
    },
    "kompatibilnost_matrika": {
        "1": [1, 3, 5],
        "2": [2, 4, 8]
    }
}
```

---

## GLOBALNO funkcije

**Pot:** `GLOBALNO/funkcije.php`

**Namen:** Numerološke funkcije za modul NumYra

### Funkcije

```php
// Konstante
NUMYRA_ABECEDA = [
    'a'=>1,'b'=>2,'c'=>3,'č'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9,
    'j'=>1,'k'=>2,'l'=>3,'m'=>4,'n'=>5,'o'=>6,'p'=>7,'q'=>8,'r'=>9,
    's'=>1,'š'=>1,'t'=>2,'u'=>3,'v'=>4,'w'=>5,'x'=>6,'y'=>7,'z'=>8,'ž'=>8
]

// Baza
numyra_baza(): array
numyra_pomen_stevila(int $stevilo): ?array

// Matematika
numyra_reduciraj(int $n, bool $ohrani_mojstrska = true): int
numyra_vrednost_znaka(string $znak): int
numyra_vsota_besedila(string $besedilo): int

// Glavne funkcije
numyra_izracunaj(string $besedilo): array
numyra_zivljenjska_pot(string $datum): int
numyra_glavni_cikli(string $datum): array
numyra_osebno_leto(string $datum, ?int $leto = null): array
numyra_sinastrija(string $besedilo1, string $besedilo2): array
numyra_iskalec_imen(array $kandidati, int $cilj): array
numyra_najdi_angelska(string $besedilo): array
```

---

## Sef (varovano)

**Pot:** `PODATKI/sef/` (ali `POT_SEF` iz okolja)

**Namen:** Varovan prostor za:
- API ključe
- Gesla
- Certifikate
- Druge skrivnosti

**Opomba:** Pot do sefa se določi v `pot.php`:
```php
$potSefOkolje = getenv('POT_SEF') ?: getenv('ASTRA_SEF_PATH') ?: '';
if ($potSefOkolje === '') {
    $potSefOkolje = POT_PODATKI . '/sef';
}
define('POT_SEF', rtrim($potSefOkolje, '/\\'));
```

---

## AI baze

**Pot:** `PODATKI/ai/`

**Namen:** AI-specifični podatki in modeli

---

## Inventar

**Pot:** `PODATKI/globalno/inventura/`

**Namen:** Inventura sistemских komponent

---

## Skladišče

**Pot:** `PODATKI/skladišče/`

**Namen:** Shranjevanje izvoženih in ustvarjenih vsebin

---

## TODO Refaktor

1. **Konsolidacija JSON** — Združi podobne strukture
2. **Validacija shem** — Dodaj JSON Schema za vse baze
3. **Tipizacija** — PHP razredi za dostop do podatkov
4. **Caching** — Implementiraj predpomnjenje za baze
5. **Migracije** — Sistem za nadgradnjo podatkovnih baz
6. **Dokumentacija** — Dokumentiraj strukturo vsake baze

---

## Avtor

**AstraMentalica Mojster**
