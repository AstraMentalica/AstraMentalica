# 🌍 Elementalni Svetovi - AstraMentalica

## Pregled

Sistem Elementalnih Svetov je nova plast nad obstoječim modulnim sistemom.
Vsak svet predstavlja enega od 5 elementov in združuje sorodne module.

## 🌊 5 Elementalnih Svetov

| Svet | Element | Simbol | Barva | Moduli |
|------|---------|--------|-------|--------|
| `VODA` | 💧 Voda | 🌊 | `#2196f3` | Lunaris, Energetica, Meditara, Somnaris, Sonaris, VibraMystica, Sreiki, Sshiatsu |
| `ZRAK` | 🌬️ Zrak | 💨 | `#00bcd4` | Kabbaloria, CodexDamiris, CodexVerba, Oracle, OraculumVisionis, Oneirotica, Mythologica, Devorum, LiberUmbrae |
| `ETER` | ✨ Eter | 🌟 | `#9c27b0` | Aetheris, QuantumMystica, AuroraMystica, SchronoSync, SenzorNasa, Sephirotica, Angelarium, Seraphica |
| `ZEMLJA` | 🌍 Zemlja | 🌿 | `#4caf50` | Runaris, Lapidaria, Herbarica, BotanicaSacra, GeometricaSacra, AlchymiaAurea + vsi Azijski moduli (24) |
| `OGENJ` | 🔥 Ogenj | 🔥 | `#ff5722` | AlchymiaAurea, Transmutaria, SolarniPojavi, AuroraMystica, Shamanica, Animaris |

## 🏗️ Struktura

```
MODULI/SVETOVI/
├── README.md                          # Ta datoteka
├── svetovi_registracija.php           # Registracija svetov
├── svetovi_handler.php                # Glavni handler za svetove
├── voda/
│   └── svet_voda.php                  # Voda svet
├── zrak/
│   └── svet_zrak.php                  # Zrak svet
├── eter/
│   └── svet_eter.php                  # Eter svet
├── zemlja/
│   └── svet_zemlja.php                # Zemlja svet
└── ogenj/
    └── svet_ogenj.php                 # Ogenj svet
```

## 🔧 Uporaba

### Dostop preko URL:
```
?svet=VODA
?svet=ZRAK
?svet=ETER
?svet=ZEMLJA
?svet=OGENJ
```

### Dostop iz navigacije:
```php
// V navigaciji dodaj:
<a href="?svet=VODA">💧 Voda</a>
<a href="?svet=ZRAK">🌬️ Zrak</a>
<a href="?svet=ETER">✨ Eter</a>
<a href="?svet=ZEMLJA">🌍 Zemlja</a>
<a href="?svet=OGENJ">🔥 Ogenj</a>
```

### Dostop iz kode:
```php
$aktivniSvet = $_GET['svet'] ?? 'GLOBALNO';
$svetHandler = new SvetHandler();
$svetHandler->prikaziSvet($aktivniSvet);