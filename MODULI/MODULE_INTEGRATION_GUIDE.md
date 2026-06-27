# Vodnik za integracijo modulov

## 📋 Pregled

Ta vodnik opisuje kako vzpostaviti in integrirati module v AstraMentalico sistem.

## 🏗️ Struktura modula

Vsak modul v MODULI/ mapi mora imeti:

```
MODULI/IME_MODULA/
├── podatki/
│   ├── manifest.json      # Obvezno - metapodatki modula
│   ├── api.json           # API definicija
│   └── izhod.json         # Izhodni konfiguracija
├── modul.php              # Obvezno - vstopna točka
├── modul_ime_funkcije.php # Funkcije
├── modul_ime_config.php   # Konfiguracija
└── modul_ime_jsonbaza.php # JSON baza (če potrebuje)
```

## 📝 Manifest struktura

```json
{
    "_id": "ime_modula",
    "_verzija": "1.0.0",
    "modul": {
        "id": "ime_modula",
        "ime": "Prikazno ime",
        "tip": "zbiralec|interaktivni|enciklopedija|divinacija",
        "nivo": 1,
        "verzija": "1.0.0",
        "aktiviran": true,
        "vstopna": "modul.php",
        "opis": "Opis modula",
        "status": "testni|stable|production",
        "demo": false,
        "zacasen": false
    },
    "dostop": {
        "minimalna_vloga": "S0",
        "plan": "osnova|premium",
        "javno_vidno": true,
        "placljivo": false,
        "otroski": false,
        "vidnost": "vsi|prijavljeni|admin",
        "dovoljenja": ["branje"]
    },
    "cache": {
        "omogocen": true,
        "ttl": 3600
    },
    "ui": {
        "ima_prikaz": true,
        "ikona": "🎵",
        "barva": "#c084fc",
        "kategorija": "ZEMLJA|NEBESNA|MITOLOGIJA|KNJIGE|VEDIŽEVANJE|KRISTALI|KABALA",
        "tags": ["tag1", "tag2"],
        "jeziki": ["sl"]
    },
    "izvajanje": {
        "tip": "ui|api|oba",
        "api_only": false,
        "interval": null,
        "ob_zagonu": false,
        "prioriteta": 50,
        "bootstrap": null
    }
}
```

## 🚀 Postopek registracije

### 1. Pripravi modul

Preveri da ima modul:
- ✅ Mapa v `MODULI/IME_MODULA/`
- ✅ Datoteko `modul.php` (vstopna točka)
- ✅ Datoteko `podatki/manifest.json`

### 2. Zaženi setup skripto

```bash
# Preko browserja
http://tvoja-domena.com/MODULI/moduli_setup.php

# Ali preko CLI
php MODULI/moduli_setup.php
```

Setup skripta bo:
1. Preverila obstoj modula
2. Preverila veljavnost manifesta
3. Registrirala modul v sistem
4. Prikazala rezultate

### 3. Aktiviraj modul za uporabnika

```php
// V uporabnikovi kodi
$uporabnikoviModuli[] = [
    'id' => 'vibramystica',
    'ime' => 'VibraMystica',
    'aktiviran' => true,
    'aktivirano' => time()
];
```

## 🧪 Testiranje modula

### Test 1: Preveri registracijo

```php
<?php
// test_modul.php
require_once 'MODULI/Modul_Bridge/jedro/sistem_preveri.php';
require_once 'MODULI/Modul_Bridge/jedro/sistemske_funkcije.php';

bridge_inicijalizacija();

$moduli = _bridge_moduli_iz_map();
echo "Registriranih modulov: " . count($moduli) . "\n";

foreach ($moduli as $modul) {
    echo "- {$modul['manifest']['modul']['ime']} ({$modul['manifest']['_id']})\n";
}
```

### Test 2: Preveri delovanje

```php
<?php
// test_modul_akcija.php
$modul_id = 'vibramystica';

// Preveri ali je registriran
if (modul_je_registriran($modul_id)) {
    echo "Modul $modul_id je registriran\n";
    
    // Pridobi manifest
    $manifest = modul_pridobi($modul_id);
    print_r($manifest);
} else {
    echo "Modul $modul_id NI registriran\n";
}
```

## 📦 Postopek za posamezne module

### VibraMystica (Zvok/Frekvence)

**Status**: ✅ Pripravljen za registracijo

**Namestitev**:
1. Preveri da obstaja `MODULI/VibraMystica/modul.php`
2. Preveri da obstaja `MODULI/VibraMystica/podatki/manifest.json`
3. Zaženi `MODULI/moduli_setup.php`
4. Modul se bo registriral samodejno

**Uporaba**:
```php
// V uporabnikovem modulu
$odziv = modul_vibramystica_akcija('info', []);
```

### Energetica (Čakre/Energija)

**Status**: ✅ Pripravljen za registracijo

**Namestitev**: Enaka kot VibraMystica

### Celestara (Zvezde/Astronomija)

**Status**: ✅ Pripravljen za registracijo

**Namestitev**: Enaka kot VibraMystica

### Tarot (Vedeževanje)

**Status**: ✅ Pripravljen za registracijo

**Namestitev**: Enaka kot VibraMystica

### Ostali moduli

**Runaris, CodexDamiris, Oracle, Lapidaria, Devorum, Kabbaloria**

Vsi so pripravljeni in bodo registrirani skupaj z setup skripto.

## 🔧 Odpravljanje težav

### Težava: "Modul ni registriran"

**Rešitev**:
1. Preveri ali obstaja `MODULI/IME/modul.php`
2. Preveri ali obstaja `MODULI/IME/podatki/manifest.json`
3. Preveri ali ima manifest `_id` polje
4. Zaženi setup skripto znova

### Težava: "Manifest ni veljaven JSON"

**Rešitev**:
1. Preveri sintakso v `manifest.json`
2. Uporabi JSON validator
3. Popravi napake

### Težava: "Modul.php ne obstaja"

**Rešitev**:
1. Preveri pot do `modul.php`
2. Preveri ali je datoteka berljiva
3. Preveri permissions

## 📊 Monitoring

### Preveri status modulov

```php
<?php
// status_modulov.php
$moduli = modul_pridobi_vse();

echo "Registriranih modulov: " . count($moduli) . "\n";

foreach ($moduli as $ime => $manifest) {
    $status = $manifest['status'] ?? 'neznan';
    $aktiviran = $manifest['aktiviran'] ? 'DA' : 'NE';
    echo "- {$manifest['ime']}: $status (aktiviran: $aktiviran)\n";
}
```

## 🎯 Naslednji koraki

1. **Zaženi setup**: `php MODULI/moduli_setup.php`
2. **Testiraj module**: Uporabi test skripte
3. **Aktiviraj za uporabnike**: Dodaj v uporabnikove module
4. **Integriraj v UI**: Poveži z uporabniškim vmesnikom

## 📚 Dodatne informacije

- [Modul_Bridge dokumentacija](MODULI/Modul_Bridge/)
- [Sistem registracija](SISTEM/storitve_svetov/moduli/modul_registracija.php)
- [Manifest primer](MODULI/VibraMystica/podatki/manifest.json)