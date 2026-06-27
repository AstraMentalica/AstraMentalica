# MODULI - Setup in vzpostavitev

## 🚀 Hitri začetek

### 1. Testiraj module
```bash
php MODULI/test_moduli.php
```

### 2. Registriraj module
```bash
php MODULI/moduli_setup.php
```

Ali preko browserja:
```
http://tvoja-domena.com/MODULI/moduli_setup.php
```

### 3. Aktiviraj module za uporabnike
```
?svet=UPORABNIKI&pot=moduli
```

## 📦 Kateri moduli so pripravljeni?

### ✅ Pripravljeni za registracijo:

1. **VibraMystica** - Zvočna mistika, frekvence, mantre
2. **Energetica** - Čakre, barve, energija
3. **Celestara** - Zvezde, ozvezdja, planeti
4. **Tarot** - Tarot vedeževanje
5. **Runaris** - Runeska mitologija
6. **CodexDamiris** - Digitalna knjiga modrosti
7. **Oracle** - Sistemsko vedeževanje
8. **Lapidaria** - Kristalna enciklopedija
9. **Devorum** - Miti in arhetipi
10. **Kabbaloria** - Kabala, Drevo življenja

## 🛠️ Struktura orodij

```
MODULI/
├── moduli_setup.php          # ✅ Registracija vseh modulov
├── test_moduli.php           # ✅ Testiranje modulov
├── MODULE_INTEGRATION_GUIDE.md  # ✅ Podroben vodnik
└── README_SETUP.md           # ✅ Ta datoteka
```

## 📋 Postopek vzpostavitve

### Korak 1: Preveri module
```bash
php MODULI/test_moduli.php
```

Izpiše:
- Koliko modulov je najdenih
- Kateri imajo veljaven manifest
- Kateri imajo modul.php
- Status registracije

### Korak 2: Registriraj module
```bash
php MODULI/moduli_setup.php
```

Izpiše:
- Uspešno registrirane
- Napake
- Preskočene (že registrirane)

### Korak 3: Aktiviraj za uporabnike
1. Pojdi na `?svet=UPORABNIKI&pot=moduli`
2. Klikni "Aktiviraj" na želene module
3. Moduli so shranjeni v `UPORABNIKI/{id}/moduli.json`

## 🔍 Diagnostika

### Preveri registracijo
```php
<?php
require_once 'MODULI/Modul_Bridge/jedro/sistem_preveri.php';
require_once 'MODULI/Modul_Bridge/jedro/sistemske_funkcije.php';
bridge_inicijalizacija();

$moduli = _bridge_moduli_iz_map();
echo "Moduli v mapi: " . count($moduli) . "\n";

if (function_exists('modul_pridobi_vse')) {
    $registrirani = modul_pridobi_vse();
    echo "Registriranih: " . count($registrirani) . "\n";
}
```

### Preveri posamezen modul
```php
<?php
$modul_id = 'vibramystica';

if (modul_je_registriran($modul_id)) {
    echo "Modul $modul_id JE registriran\n";
    $manifest = modul_pridobi($modul_id);
    print_r($manifest);
} else {
    echo "Modul $modul_id NI registriran\n";
}
```

## 🎯 Prvi modul za testiranje

Priporočam **VibraMystica** ker:
- ✅ Ima popoln manifest
- ✅ Ima delujoč modul.php
- ✅ Ima demo funkcionalnost
- ✅ Ne zahteva zunanjih odvisnosti

### Testiranje VibraMystica:

1. **Registriraj**:
   ```bash
   php MODULI/moduli_setup.php
   ```

2. **Aktiviraj**:
   - Pojdi na `?svet=UPORABNIKI&pot=moduli`
   - Klikni "Aktiviraj" pri VibraMystica

3. **Odpri**:
   - Klikni "Odpri" 
   - Ali poveži direktno: `?svet=MODULI_CEL&modul=vibramystica`

## 📊 Struktura modula (primer)

```
VibraMystica/
├── podatki/
│   ├── manifest.json      ✅ Metapodatki
│   ├── api.json           ✅ API
│   └── izhod.json         ✅ Izhod
├── modul.php              ✅ Vstopna točka
├── modul_vibramystica_funkcije.php  ✅ Funkcije
├── modul_vibramystica_pravila.php   ✅ Pravila
└── modul_vibramystica_jsonbaza.php  ✅ Baza
```

## 🐛 Odpravljanje težav

### "Modul ni registriran"
```bash
# 1. Preveri obstoj datotek
ls MODULI/VibraMystica/modul.php
ls MODULI/VibraMystica/podatki/manifest.json

# 2. Preveri JSON sintakso
php -r "print_r(json_decode(file_get_contents('MODULI/VibraMystica/podatki/manifest.json'), true));"

# 3. Ponovno registriraj
php MODULI/moduli_setup.php
```

### "Manifest ni veljaven"
- Preveri `_id` polje
- Preveri `modul` objekt
- Preveri `aktiviran` boolean
- Uporabi JSON validator

### "modul.php ne deluje"
- Preveri PHP sintakso: `php -l MODULI/VibraMystica/modul.php`
- Preveri odvisnosti (Modul_Bridge)
- Preveri permissions

## 📚 Dokumentacija

- [MODULE_INTEGRATION_GUIDE.md](MODULE_INTEGRATION_GUIDE.md) - Podroben vodnik
- [UPORABNIKI/README.md](../UPORABNIKI/README.md) - Uporabniški sistem
- [MODULI/Modul_Bridge/](../Modul_Bridge/) - Bridge dokumentacija

## 🎓 Naslednji koraki

1. **Zaženi test**: `php MODULI/test_moduli.php`
2. **Registriraj**: `php MODULI/moduli_setup.php`
3. **Aktiviraj**: Preko UI `?svet=UPORABNIKI&pot=moduli`
4. **Testiraj**: Odpri modul in preveri delovanje

## ️ Nasveti

- Začni z **VibraMystica** (najbolj zanesljiv)
- Testiraj vsak modul posebej
- Uporabljaj `test_moduli.php` za diagnostiko
- Shrani backup pred registracijo

## 📞 Podpora

- Preveri logove v `SISTEM/logi/`
- Uporabi `test_moduli.php` za diagnostiko
- Preveri manifest.json sintakso

---

**Verzija**: 1.0.0 (24.6.2026)  
**Avtor**: AstraMentalica Mojster