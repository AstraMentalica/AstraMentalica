# AstraMentalica — Projektni pregled

## Sistemske informacije
- **Verzija:** v117 (18.6.2026)
- **Jezik:** Slovenščina (sl)
- **Platforma:** PHP 8+ / Web
- **Lokacija:** `D:\Projekti\Projekt_AstraMentalica\AstraMentalica`

---

## Arhitekturna shema

```
┌─────────────────────────────────────────────────────────────────────┐
│                         UPORABNIK / ODjemalec                       │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         INDEX.PHP (Edina javna vstopna točka)       │
│                         ────────────────────────────────────────    │
│                         - "Nema" vstopna točka                       │
│                         - Preusmeri v ADAPTER                        │
│                         - Ni logike, ni renderiranja                 │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         POT.PHP (Absolutno sidro)                   │
│                         ────────────────────────────────────────    │
│                         - EDINA datoteka z __DIR__                   │
│                         - Definira vse POT_* konstante              │
│                         - RBAC vloge (0-100)                         │
│                         - Sistemske nastavitve                       │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         ADAPTER / adapter.php                        │
│                         ────────────────────────────────────────    │
│                         - Normalizacija zahtev                        │
│                         - Kanal routing (splet, api, telegram...)    │
│                         - Middleware: CORS, IP blacklist, rate-limit │
│                         - Registracija izhodnih kanalov              │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         SISTEM / api.php                             │
│                         ────────────────────────────────────────    │
│                         - Edina vstopna točka v sistem               │
│                         - Bootstrap zagon                            │
│                         - API routing                                │
│                         - Google OAuth callback                      │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                    ┌───────────────┼───────────────┐
                    ▼               ▼               ▼
┌─────────────────────────┐ ┌─────────────┐ ┌─────────────────────┐
│   SISTEM/kernel/        │ │SISTEM/      │ │   PODATKI/          │
│   ─────────────────     │ │storitve_    │ │   ──────────────    │
│   jedro/                 │ │svetov/      │ │   baze/             │
│   - 01_upravljalec      │ │             │ │   - json/           │
│   - 02_napake           │ │uporabniki/  │ │   - sqlite/         │
│   - 03_varnost          │ │ai/          │ │   - mysql/           │
│   - 04_seja             │ │moduli/      │ │                     │
│   - 05_pravice          │ │globalno/    │ │   registri/         │
│   - 06_cache            │ │postavitev/  │ │   sistem/           │
│   - 07_dogodki          │ │glasovni/    │ │   uporabniki/       │
│   - 08_kavlji           │ │             │ │   globalno/         │
│   - 09_ponudniki        │ │             │ │   ai/               │
│   - 10_middleware       │ │             │ │                     │
│   - 11_usmerjevalnik    │ │             │ │   inventar/         │
│   - 12_validacija       │ │             │ │   skladišče/        │
│   - 13_api              │ │             │ │   sef/              │
│   - 14_zagon            │ │             │ │                     │
│   - 15_pogon            │ │             │ │                     │
└─────────────────────────┘ └─────────────┘ └─────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         MODULI / Modul_Bridge                        │
│                         ────────────────────────────────────────    │
│                         - Centralni most za module                    │
│                         - Modul_Bridge::vloga_preveri()              │
│                         - Modul_Bridge::uporabnik_pridobi()          │
│                         - Modul_Bridge::podatki_beri/pisi()          │
│                         - Modul_Bridge::modul_klic()                │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         MODULI (25+ modulov)                         │
│                         ────────────────────────────────────────    │
│   Univerzalno/                                                          │
│   ├── Tarot/         - Arhetipske karte                             │
│   ├── Synera/        - Sinergija zavesti                            │
│   ├── CorpusMysticum/- Duhovna knjižnica                            │
│   ├── Lunaris/       - Lunina modrost                               │
│   ├── VibraMystica/  - Vibracijska mistika                         │
│   ├── Sonaris/       - Zvočna harmonija                            │
│   ├── Transmutaria/  - Transmutacija energije                       │
│   └── ...                                                            │
│                                                                      │
│   Knjiznice/                                                          │
│   ├── Orakleum/      - Mistični orakelj                             │
│   ├── Codex/         - Starodavna modrost                            │
│   ├── CodexAntiqua/  - Antični zapiski                              │
│   ├── UmbraeCodex/   - Senčna knjižnica                             │
│   └── OraculumVisionis/- Vizijski orakelj                           │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Struktura mapa

| Mapa | Namen |
|------|-------|
| **ADAPTER/** | Prevajalnik med zunanjim svetom in jedrom |
| **AI/** | AI pot + varnostno sidro + asistenti |
| **ASTRA/** | Auth + upravljanje + admin center |
| **GLOBALNO/** | Frontend (layout, JS, CSS, zvoki, vmesnik) |
| **MODULI/** | 25+ samostojnih modulov |
| **PODATKI/** | JSON/SQLite/MySQL baze, cache, logi, registri |
| **SISTEM/** | Backend jedro (varnost, baza, avtentikacija) |
| **UPORABNIKI/** | Prijava, registracija, profil, VIP passport |
| **VSEBINA/** | FAQ, gradiva, pravila, ponudba |
| **ORODJA/** | Validacijska orodja (PowerShell + Python) |

---

## RBAC Vloge

| Vloga | Vrednost | Opis |
|-------|----------|------|
| GOST | 0 | Neprijavljen uporabnik |
| S0 | 10 | Registriran uporabnik |
| S1 | 20 | Aktivni uporabnik |
| S2 | 30 | Napredni uporabnik |
| S3 | 40 | VIP uporabnik |
| S4 | 50 | Premium uporabnik |
| S5 | 60 | Premium+ uporabnik |
| ADMIN | 100 | Administrator |

---

## Kanali (Adapter routing)

| Kanal | Trigger | Namen |
|-------|---------|-------|
| **splet** | privzeti | Spletna aplikacija |
| **api** | `/api/` v URI | REST API |
| **ai** | `/ai/` v URI ali X-AI-AGENT header | AI agenti |
| **telegram** | "telegram" v URI | Telegram bot |
| **facebook** | "facebook" v URI | Facebook Messenger |
| **cli** | PHP_SAPI === 'cli' | Ukazna vrstica |

---

## Registrirani moduli (moduli_register.json)

```json
{
    "NEBO/Stelaris":  { "vloga_min": 20, "nivo": 2, "tip": "sestavljalec" },
    "NEBO/Lunaris":   { "vloga_min": 10, "nivo": 1, "tip": "zbiralec" },
    "NEBO/Jyotir":    { "vloga_min": 20, "nivo": 2, "tip": "sestavljalec" },
    "ZEMLJA/QiVitalis":{ "vloga_min": 10, "nivo": 1, "tip": "zbiralec" },
    "ZEMLJA/Pranaymica":{ "vloga_min": 10, "nivo": 1, "tip": "zbiralec" },
    "SIMBOLI/Numyra":  { "vloga_min": 10, "nivo": 1, "tip": "zbiralec" },
    "SIMBOLI/NordicaMystica": { "vloga_min": 10, "nivo": 1, "tip": "zbiralec" },
    "POTI/Transmutaria":{ "vloga_min": 10, "nivo": 1, "tip": "zbiralec" },
    "SVET/CorpusMysticum":{ "vloga_min": 30, "nivo": 3, "tip": "izvajalec" },
    "ORAKLEUM/Tarot":  { "vloga_min": 30, "nivo": 3, "tip": "izvajalec" },
    "VIP/Synera":      { "vloga_min": 40, "nivo": 3, "tip": "izvajalec" }
}
```

---

## Varnostni mehanizmi

- **SISTEM_VARNOST** konstanta (varovalka)
- CSRF zaščita
- AES šifriranje (PODATKI/sef/)
- JWT žetoni
- IP blacklist (ADAPTER/middleware/ip_blacklist.php)
- Prepared statements (SQL)
- Rate limiting (ADAPTER/middleware/omejevalnik.php)
- RBAC kontrola dostopa

---

## AI Integracija

| Pot | Namen |
|-----|-------|
| **AI/astraMentor/** | Astra Mentor - glavni AI asistent |
| **AI/zasebniAi/arhitekturniAi/** | Arhitekturni AI (DeepSeek) |
| **AI/zasebniAi/strukturniAI/** | Strukturne AI pomožnike |
| **AI/zasebniAi/placanecAi/** | Plačane AI storitve |
| **AI/javniAi/** | Javni AI asistenti |
| **AI/llama_helper.php** | Ollama/Llama 3.2 integracija |
| **AI/varnost.php** | AI varnostno sidro |

---

## Knjižnice (Moduli/Knjiznice)

| Ime | Namen |
|-----|-------|
| **Orakleum** | Mistični orakelj z API |
| **Codex** | Starodavna modrost z API |
| **CodexAntiqua** | Antični zapiski |
| **UmbraeCodex** | Senčna knjižnica |
| **OraculumVisionis** | Vizijski orakelj |

---

## Podatkovne poti (pot.php konstante)

```
POT_KOREN          → Root mapa projekta
POT_SISTEM         → /SISTEM
POT_GLOBALNO       → /GLOBALNO
POT_MODULI         → /MODULI
POT_UPORABNIKI     → /UPORABNIKI
POT_PODATKI        → /PODATKI
POT_VSEBINA        → /VSEBINA
POT_ASTRA          → /ASTRA
POT_ADAPTER        → /ADAPTER
POT_AI             → /AI
POT_KERNEL         → /SISTEM/kernel
POT_JEDRO          → /SISTEM/kernel/jedro
POT_BAZE           → /SISTEM/kernel/baze
POT_STORITVE       → /SISTEM/storitve_svetov
POT_KANALI         → /SISTEM/kanali
```

---

## Ključne datoteke

| Datoteka | Namen |
|----------|-------|
| `index.php` | Edina javna vstopna točka |
| `pot.php` | Absolutno sidro - vse POT_* konstante |
| `ai_proxy.php` | AI proxy za zunanje API klic |
| `ADAPTER/adapter.php` | Glavni adapter - routing zahtev |
| `SISTEM/api.php` | Sistem API - edini vstop v jedro |
| `SISTEM/kernel/zaganjalnik.php` | Bootstrap jedra |
| `MODULI/Modul_Bridge/modul_bridge.php` | Centralni most za module |
| `GLOBALNO/funkcije.php` | Numerološke funkcije (NumYra) |
| `GLOBALNO/asistent/asistent.php` | AI asistent |

---

## Sledi spremembam

- Verzija: v117 (18.6.2026)
- Zadnja večja sprememba: Združitev pot.php in pot (2).php, dodan POT_AI
- Arhitekturni standard: Header Standard v116

---

## TODO za Copilot optimizacijo

1. **Konfiguracija .claude/** - Posodobi .claude/ mape za boljše razumevanje projekta
2. **Tipi za PHP** - Dodaj stub datoteke za PHP tipe kjer manjkajo
3. **Inline dokumentacija** - Zagotovi dosledno dokumentiranje funkcij
4. **Modul standardizacija** - Vsi moduli naj sledijo isti strukturi
5. **Skupna knjižnica** - Extractiraj skupne funkcije (odziv_uspeh, odziv_napaka)

---

## Avtor

**AstraMentalica Mojster**
