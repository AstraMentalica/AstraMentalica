# Uporabniški sistem AstraMentalica

## 📋 Pregled

Sistem za upravljanje uporabnikov z modernim glassmorphism vmesnikom, Google OAuth integracijo in predvajanjem frekvenc ter ambientne glasbe.

## 🎯 Implementirane funkcionalnosti

### 1. Registracija uporabnikov
- **Datoteka**: `UPORABNIKI/prikaz/uporabnik/uporabnik_registracija.php`
- **Funkcionalnosti**:
  - Standardna registracija z e-pošto in geslom
  - Google OAuth integracija (pripravljeno za implementacijo)
  - Validacija vnosov
  - Avtomatska prijava po registraciji
  - Glassmorphism UI design

### 2. Prijava uporabnikov
- **Datoteka**: `UPORABNIKI/prikaz/uporabnik/uporabnik_prijava.php`
- **Funkcionalnosti**:
  - Prijava z e-pošto in geslom
  - Google OAuth gumb
   - Google prijava ne daje višjih pravic; uporablja iste vloge in RBAC kot navadna prijava
  - Glassmorphism UI design
  - Preusmeritev na profil po prijavi

### 3. Nastavitve uporabnika

> Google OAuth je samo prijavni kanal. Ne spreminja vloge, ne odklepa dodatnih pravic in ne zaobide RBAC pravil.
- **Datoteka**: `UPORABNIKI/prikaz/uporabnik/uporabnik_nastavitve.php`
- **Funkcionalnosti**:
  - Urejanje profila (ime)
  - Spreminjanje gesla
  - **Predvajanje meditacijskih frekvenc**:
    - 432 Hz - Naravna frekvenca
    - 528 Hz - Zdravljenje
    - 639 Hz - Ljubezen
    - 741 Hz - Zaznava
    - 852 Hz - Duhovnost
    - 963 Hz - Vizija
  - **Ambientna glasba**:
    - Dež
    - Ocean
    - Gozd
    - Ogenj
    - Veter
    - Zvočna skleda
  - Glasnostni regulator
  - Glasba se predvaja preko Web Audio API

### 4. Navigacija
- **Datoteka**: `GLOBALNO/postavitev/osnova/navigacija.php`
- **Spremembe**:
  - Dodan link "Nastavitve" v navigacijsko vrstico
  - Pokaže se samo za prijavljene uporabnike
  - Aktivno stanje ko je uporabnik na strani nastavitve

## 🎨 Design - Glassmorphism

Vsi uporabniški vmesniki uporabljajo sodobni **glassmorphism** dizajn:

- **Zahajajoči ozadja** (backdrop blur)
- **Prosojne plasti** (rgba barve)
- **Zabrisani robovi** (border-radius: 12-20px)
- **Svetleče obrobe** (border: 1px solid rgba(255,255,255,0.2))
- **Gradienti**: `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`
- **Animirano ozadje** s plavajočimi točkami

## 🔧 Tehnične specifikacije

### PHP Struktura
- Uporablja obstoječi sistem sej (`seja_je_prijavljen()`, `seja_pridobi()`)
- Integracija z bazo podatkov (`baza_beri()`, `baza_zapisi()`, `baza_posodobi()`)
- Dogodki (`dogodek_sprozi()`) za sledenje aktivnostim
- Validacija vnosov na strežniški strani

### JavaScript Audio
- **Web Audio API** za generiranje frekvenc
- **OscillatorNode** za točne frekvence (432-963 Hz)
- **BiquadFilterNode** za ambientne zvoke
- **GainNode** za nadzor glasnosti
- Ne zahteva zunanjih audio datotek

### Google OAuth
- Pripravljen integracija za Google prijavo
- Potrebuje `GOOGLE_CLIENT_ID` v environment spremenljivkah
- Callback URL: `?svet=UPORABNIKI&pot=prijava&google_callback=1`

## 📁 Struktura datotek

```
UPORABNIKI/
├── prikaz/
│   └── uporabnik/
│       ├── uporabnik_registracija.php        # Registracija
│       ├── uporabnik_prijava.php             # Prijava
│       ├── uporabnik_nastavitve.php          # Nastavitve + audio
│       ├── uporabnik_nastavitve_napredno.php # Napredne nastavitve
│       ├── uporabnik_profil_stran.php        # Profil
│       ├── uporabnik_moduli.php              # Upravljanje modulov
│       └── google_oauth_callback.php         # Google OAuth
└── README.md                                 # Ta datoteka

GLOBALNO/postavitev/osnova/
└── navigacija.php                            # Posodobljena navigacija
```

## 🚀 Namestitev in konfiguracija

### 1. Nastavite Google OAuth (opcijsko)

V `.env` ali environment spremenljivke dodajte:

```env
GOOGLE_CLIENT_ID=VAŠ_GOOGLE_CLIENT_ID
GOOGLE_CLIENT_SECRET=VAŠ_GOOGLE_CLIENT_SECRET
GOOGLE_REDIRECT_URI=https://vas-domen.com/?svet=UPORABNIKI&pot=prijava
```

### 2. Dostop do strani

- **Registracija**: `?svet=UPORABNIKI&pot=registracija`
- **Prijava**: `?svet=UPORABNIKI&pot=prijava`
- **Nastavitve**: `?svet=UPORABNIKI&pot=nastavitve` (zahteva prijavo)
- **Profil**: `?svet=UPORABNIKI&pot=profil` (zahteva prijavo)

## 🎵 Uporaba frekvenc

1. Pojdite na **Nastavitve** (⚙️)
2. Izberite želeno frekvenco ali ambientni zvok
3. Prilagodite glasnost
4. Uporabite gumbe za predvajanje:
   - ⏮️ - Prejšnja frekvenca
   - ▶️/⏸️ - Predvajaj/Pavza
   - ⏭️ - Naslednja frekvenca

## 🔒 Varnost

- Gesla so hashirana z `password_hash()` (BCRYPT)
- Preverjanje sej na vsaki zahtevi
- CSRF zaščita (pripravljeno za implementacijo)
- Validacija vseh vnosov
- Sanitizacija izhoda z `htmlspecialchars()`

## 🎯 Naslednji koraki (priporočeno)

1. **Google OAuth**:
   - ✅ Callback že implementiran
   - Nastavite `GOOGLE_CLIENT_ID` in `GOOGLE_CLIENT_SECRET` v `.env`

2. **Dodaj pošiljanje e-pošte**:
   - Potrditvena e-pošta za registracijo
   - Obnovitev gesla

3. **Razširi nastavitve**:
   - ✅ Priljubljene frekvence
   - ✅ Glasnostni regulator
   - ✅ Obvestila in zasebnost
   - ✅ Jezik in časovna zona

4. **Moduli**:
   - ✅ Upravljanje modulov
   - ✅ Aktivacija/deaktivacija
   - ✅ Kategorizacija
   - ✅ Statistika uporabe

## 📝 Opombe

- Sistem uporablja obstoječo bazo podatkov `uporabniki`
- Uporabniške nastavitve se shranjujejo v `UPORABNIKI/{id}/nastavitve.json`
- Audio se predvaja lokalno v brskalniku (ne potrebuje strežnika)
- Design je odziven (responsive) za mobilne naprave

AstraMentalica Mojster  
Verzija: 1.0.0 (24.6.2026)