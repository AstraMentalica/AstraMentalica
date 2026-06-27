# 🌍 Zemljevi (Svetovni zemljevid)

## 📋 Pregled

Zemljevi so vizualni navigacijski sistem za preklop med moduli v AstraMentalici. Omogoča uporabnikom, da se lažko premikajo med različnimi moduli z uporabo vizualnih kartič.

## 🏗️ Struktura

```
GLOBALNO/vmesnik/
├── elementi/
│   └── zemljevi.php          # PHP komponenta za prikaze
├── css/
│   └── zemljevi.css          # Stili (samo vmesnik spremenljivke)
└── js/
    └── zemljevi.js           # JavaScript za interaktivnost
```

## 🎯 Namembnost

- **Centralizirana navigacija**: Vsi moduli na enem mestu
- **Vizualni izbor**: Kartice z ikonami in barvami
- **Responsive**: Deluje na vseh velikostih zaslonov
- **Tema kompatibilnost**: Uporablja samo vmesnik spremenljivke

## 🔧 Uporaba

### Osnovna uporaba

```php
<?php
// Pridobi vse module
$moduli = zemljevi_pridobi_module();

// Prikaži zemljeve
zemljevi_prikazi($moduli, $aktivniModul, '?svet=MODULI&modul=');
?>
```

### Format modula

Za delovanje mora imeti vsak modul v mapi `MODULI/` eno od:
- `modul.php` - vstopna točka
- `index.php` - alternativa
- `manifest.json` - konfiguracija (opcijsko za barvo)

### manifest.json (opcijsko)

```json
{
    "ime": "Moj Modul",
    "barva": "#e8c84a",
    "ikona": "🎯"
}
```

## 🎨 Stili

Vsi stili uporabljajo samo spremenljivke iz `GLOBALNO/vmesnik/css/spremenljivke.css`:

- `--kartica` - ozadje kartice
- `--rob` - rob kartice
- `--zlata` - primarna barva
- `--besedilo` - barva besedila
- `--senca-m` - senčnost
- `--prehod-pocasen` - animacija

## 📱 Responsive

- **1200px+**: 4 stolpci
- **900px-1200px**: 3 stolpci
- **600px-900px**: 2 stolpci
- **<600px**: 1 stolpec

## ⚡ Funkcije

### PHP funkcije

- `zemljevi_prikazi(array $moduli, string $aktivniModul, string $url)` - Prikaže zemljeve
- `zemljevi_pridobi_module(): array` - Pridobi seznam vseh modulov

### JavaScript funkcije

- `Zemljevi.inicializiraj()` - Inicializira zemljeve
- `Zemljevi.oznaciAktivno()` - Označi aktivno kartico
- `Zemljevi.prikaziToast(sporocilo)` - Prikaže obvestilo

## 🔗 Integracija

V `render.php` dodaj:

```php
// CSS v <head>
<link rel="stylesheet" href="<?= $bazaUrl ?>/GLOBALNO/vmesnik/css/zemljevi.css">

// JS pred </body>
<script src="<?= $bazaUrl ?>/GLOBALNO/vmesnik/js/zemljevi.js"></script>
```

## 📝 Stran moduli_seznam.php

```php
<?php
$moduli = zemljevi_pridobi_module();
$aktivniModul = $_GET['modul'] ?? '';
?>

<h1>🌍 Moduli</h1>
<?php zemljevi_prikazi($moduli, $aktivniModul, '?svet=MODULI&modul='); ?>
```

## 🎯 Prednosti

1. **Centralizirano**: Vsi stili v enem mestu
2. **Enostavno**: Dodaj nov modul z dodajanjem mape
3. **Hitro**: Brez dodatnih poizvedb v bazo
4. **Uporabniku prijazno**: Vizualno navigacijo
5. **Responsive**: Deluje na vseh napravah

## 📊 Status

- ✅ PHP komponenta
- ✅ CSS stili
- ✅ JavaScript interaktivnost
- ✅ Integracija v render
- ✅ Stran moduli_seznam.php
- ✅ Dokumentacija

## 🚀 Prihodnje izboljšave

- [ ] Iskalnik modulov
- [ ] Filtri po kategorijah
- [ ] Priljubljeni moduli
- [ ] Zadnje obiskani
- [ ] Drag & drop razporeditev