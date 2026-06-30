# AstraMentalica — Arhitektura

## Pregled

AstraMentalica je zasnovan kot **strogo modularna večplastna arhitektura** z jasnim ločevanjem odgovornosti.

---

## Arhitekturni nivoji

```
┌─────────────────────────────────────────────────────────────────────┐
│  NIVO 0: SIDRO (POT)                                                │
│  ─────────────────────────────────────────────────────────────────  │
│  pot.php                                                            │
│  • Edina datoteka z __DIR__                                         │
│  • Definira vse POT_* konstante                                     │
│  • Ni logike, samo konstante                                         │
│  • SIDRO = absolute trust zone                                      │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│  NIVO 1: VSTOP                                                     │
│  ─────────────────────────────────────────────────────────────────  │
│  index.php (javna) / SISTEM/api.php (notranja)                      │
│  • Edina vstopna točka za zahteve                                   │
│  • "Nema" vstopna točka - ni logike                                 │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│  NIVO 2: ADAPTER                                                    │
│  ─────────────────────────────────────────────────────────────────  │
│  ADAPTER/adapter.php                                                │
│  • Normalizacija zahtev                                             │
│  • Middleware pipeline (CORS, IP blacklist, rate-limit)             │
│  • Kanal routing (splet, api, telegram, ai, cli)                    │
│  • Registracija izhodnih kanalov                                    │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│  NIVO 3: SISTEM                                                     │
│  ─────────────────────────────────────────────────────────────────  │
│  SISTEM/kernel/                                                     │
│  • Bootstrap (zaganjalnik.php)                                      │
│  • Jedro (15 faz)                                                   │
│  • Storitve (storitve_svetov/)                                      │
│  • Baze podatkov (baze/)                                            │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                    ┌───────────────┼───────────────┐
                    ▼               ▼               ▼
┌─────────────────────────┐ ┌─────────────┐ ┌─────────────────────────┐
│  NIVO 4: MODULI         │ │NIVO 4: AI   │ │NIVO 4: UPORABNIKI      │
│  ────────────────────   │ │─────────────│ │───────────────────────  │
│  MODULI/                │ │AI/          │ │UPORABNIKI/             │
│  • Modul_Bridge         │ │• asistenti  │ │• prijava/registracija  │
│  • 25+ modulov          │ │• arhitekti   │ │• profil                │
│  • Čista modularnost    │ │• varnost    │ │• passport (VIP)        │
└─────────────────────────┘ └─────────────┘ └─────────────────────────┘
```

---

## Jedro (Kernel) — 15 faz

**Pot:** `SISTEM/kernel/jedro/`

| Faz | Datoteka | Namen |
|-----|----------|-------|
| 1 | `01_upravljalec_svetov.php` | Upravljalec svetov |
| 2 | `02_napake.php` | Obravnava napak |
| 3 | `03_varnost.php` | Varnostni mehanizmi |
| 4 | `04_seja.php` | Upravljanje sej |
| 5 | `05_pravice.php` | RBAC pravice |
| 6 | `06_cache.php` | Predpomnilnik |
| 7 | `07_dogodki.php` | Event system |
| 8 | `08_kavlji.php` | Hook sistem |
| 9 | `09_ponudniki.php` | Providerji |
| 10 | `10_middleware.php` | Dodatni middleware |
| 11 | `11_usmerjevalnik.php` | Request routing |
| 12 | `12_validacija.php` | Input validacija |
| 13 | `13_api.php` | API komponente |
| 14 | `14_zagon.php` | Zagon sistema |
| 15 | `15_pogon.php` | Pogon izvajanja |

---

## Podatkovna plast

```
PODATKI/
├── baze/                    # Bazni adapterji
│   ├── adapter_json.php    # JSON adapter
│   ├── adapter_sqlite.php  # SQLite adapter
│   ├── adapter_mysql.php   # MySQL adapter
│   ├── interface/          # Vmesniki
│   ├── query_builder.php   # Query builder
│   └── upravljalec_baz.php # Upravljalec
│
├── json/                   # JSON datoteke
├── sqlite/                 # SQLite datoteke
├── mysql/                  # MySQL datoteke
│
├── registri/              # Sistemski registri
│   ├── moduli_register.json
│   └── rbac/              # RBAC pravila
│
├── sistem/                # Sistemski podatki
│   ├── dnevnik/          # Dnevniki
│   ├── dovoljenja/       # Dovoljenja
│   ├── analitika/        # Analitika
│   └── registri/        # Registri
│
├── uporabniki/           # Uporabniški podatki
├── globalno/             # Globalni podatki
├── ai/                   # AI podatki
├── inventar/            # Inventura
├── skladišče/          # Shranjevanje
├── sef/                 # Varovano (API ključi, gesla)
└── arhiv/               # Arhiv
```

---

## Adapter sistem

### Vhodni adapterji

**Pot:** `ADAPTER/`

| Mapa | Namen |
|------|-------|
| `middleware/` | Vmesna programska oprema |
| `vhod_webhook/` | Sprejemanje webhook-ov |
| `zahteve/` | Obdelava zahtev |

### Middleware pipeline

1. **CORS** — Cross-Origin Resource Sharing
2. **IP Blacklist** — Blokiranje IP naslovov
3. **Rate Limiting** — Omejevanje frekvence
4. **Dnevnik** — Beleženje zahtev

### Izhodni kanali

**Pot:** `ADAPTER/izhod_kanali/`

| Kanal | Namen |
|-------|-------|
| `KanalWeb.php` | Spletni odzivi |
| `KanalApi.php` | API odzivi |
| `KanalTelegram.php` | Telegram odzivi |
| `KanalFacebook.php` | Facebook odzivi |
| `KanalAi.php` | AI odzivi |
| `KanalCli.php` | CLI odzivi |

---

## Storitve svetov

**Pot:** `SISTEM/storitve_svetov/`

```
storitve_svetov/
├── ai/                   # AI storitve
│   └── ...              # AI providerji
├── glasovni_servis/     # Glasovne storitve
├── globalno/            # Globalne storitve
├── moduli/              # Modulske storitve
├── postavitev/          # Postavitvene storitve
└── uporabniki/          # Uporabniške storitve
    ├── uporabnik_prijava.php
    ├── uporabnik_registracija.php
    ├── uporabnik_profil.php
    ├── uporabnik_google_oauth.php
    └── ...
```

---

## Modul Bridge

**Pot:** `MODULI/Modul_Bridge/modul_bridge.php`

### Arhitekturni principi

1. **Loose coupling** — moduli ne poznajo en drugega
2. **Centraliziran dostop** — vse komunikacija preko Bridge-a
3. **Demo način** — moduli delujejo samostojno brez polnega sistema
4. **Fallback** — avtomatska degradacija

### Komunikacijski vzorci

```
MODUL_A ──────► Modul_Bridge::modul_klic() ──────► MODUL_B
                    │
                    ▼
            ┌───────────────┐
            │   Jedro      │
            │  (sistem)    │
            └───────────────┘
                    │
                    ▼
            ┌───────────────┐
            │   Baza       │
            │  (podatki)   │
            └───────────────┘
```

---

## AI arhitektura

```
AI/
├── astraMentor/         # Centralni AI mentor
│   ├── gateway         # Vstopna točka
│   ├── integracija     # Jedro integracija
│   ├── history         # Zgodovina
│   └── backend         # DeepSeek backend
│
├── zasebniAi/          # Zasebni agenti
│   ├── arhitekturniAi  # Arhitekturni AI
│   ├── strukturniAI    # Strukturirani pomočniki
│   ├── placenecAi      # Plačane storitve
│   └── sistemskiAi     # Sistemski AI
│
├── javniAi/            # Javni agenti
│   ├── komunikacijskiAi
│   ├── PisarAi
│   ├── varuhAvatarAi
│   └── varuhModulAi
│
├── varnost.php         # AI varnost
├── llama_helper.php    # Ollama helper
└── ai_proxy.php        # Zunanji API proxy
```

---

## Varnostna arhitektura

```
VARNOST
    │
    ├── SISTEM_VARNOST       # Globalna varovalka
    │
    ├── Adapter middleware   │
    │   ├── CORS            # Cross-origin
    │   ├── IP Blacklist    # Blokiranje
    │   ├── Rate Limit      # Omejevanje
    │   └── Dnevnik         # Beleženje
    │
    ├── Kernel varnost      │
    │   ├── CSRF           # CSRF zaščita
    │   ├── AES            # Šifriranje
    │   ├── JWT            # Žetoni
    │   └── RBAC          # Pravice
    │
    └── AI varnost         │
        └── varnost.php    # AI varnostno sidro
```

---

## Request lifecycle

```
1. UPORABNIK pošlje zahtevo
            │
            ▼
2. index.php (edina javna vstopna)
            │
            ▼
3. pot.php naloži konstante
            │
            ▼
4. adapter.php
   ├── CORS middleware
   ├── IP Blacklist
   ├── Rate Limiting
   └── Kanal routing
            │
            ▼
5. sistem_izvedi()
   ├── _sistem_bootstrap()
   │   └── zaganjalnik_izvedi()
   │       ├── Nalozi jedro (15 faz)
   │       ├── Nalozi baze
   │       └── Nalozi knjiznice
   │
   └── API routing / stran
            │
            ▼
6. Modul_Bridge klic
            │
            ▼
7. adapter_poslji_odziv()
   └── Kanal izpis
            │
            ▼
8. UPORABNIK prejme odziv
```

---

## Dizajn principi

### 1. Ena vstopna točka
- `index.php` je edina javna datoteka
- Vse zahteve gredo skozi adapter

### 2. Ločevanje odgovornosti
- Adapter: samo routing
- Sistem: samo logika
- Moduli: samo funkcionalnost

### 3. Centralizirana konfiguracija
- `pot.php` definira vse poti
- `moduli_register.json` definira module
- RBAC definira pravice

### 4. Fail-safe design
- Demo način za module
- Graceful degradation
- Varovalke na vsakem nivoju

### 5. Stroga modularnost
- Moduli so neodvisni
- Komunikacija samo preko Bridge-a
- Brez direktnih odvisnosti

---

## Razširitev sistema

### Dodajanje novega modula

1. Ustvari mapo v `MODULI/Univerzalno/`
2. Dodaj `modul.php` po predlogi
3. Registriraj v `moduli_register.json`
4. Dodaj akcije

### Dodajanje novega kanala

1. Ustvari `Kanal*.php` v `ADAPTER/izhod_kanali/`
2. Registriraj v `adapter_zagon()`
3. Dodaj routing logiko

### Dodajanje nove faze jedra

1. Dodaj datoteko v `SISTEM/kernel/jedro/`
2. Poimenuj po vzorcu `XX_ime.php`
3. Dodaj v `$GLOBALS['ZAGANJALNIK_FAZE']`

---

## TODO Arhitekturne izboljšave

1. **Tipizacija** — Dodaj PHP 8 striktne tipe povsod
2. **Event sourcing** — Event-driven architecture za jedro
3. **Service container** — DI container za storitve
4. **Caching layer** — Centraliziran cache sistem
5. **Async jobs** — Background job processing
6. **API versioning** — Versioning za API endpoint-e

---

## Avtor

**AstraMentalica Mojster**
