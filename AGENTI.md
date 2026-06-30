# AstraMentalica — Agenti

## Pregled

Sistem AstraMentalica vsebuje več AI agentov, organiziranih v hierarhično strukturo. Agenti so razdeljeni v:
- **Javni agenti** — dostopni uporabnikom
- **Zasebni agenti** — sistemski in arhitekturni
- **Astra Mentor** — centralni AI asistent

---

## Hierarhija agentov

```
AI/
├── asistentAi.php              # Univerzalni spletni AI asistent
├── llama_helper.php           # Ollama/Llama 3.2 helper
├── varnost.php                # Varnostno sidro
├── varnost2.php               # Dodatna varnost
├── pot_ai.php                 # AI pot utility
├── README_llama.md            # Llama dokumentacija
├── .env.example               # Env spremenljivke
│
├── astraMentor/               # 🌟 Astra Mentor - Centralni AI
│   ├── AstraMentorIntegracija.php
│   ├── AstraMentorIntegration.php
│   ├── astraMentorGateway.php
│   ├── astraMentorHistory.php
│   ├── NalagalnikAi.php
│   ├── ObdelovalnikAi.php
│   ├── PomocnikAi.php
│   ├── PovezavaAi.php
│   ├── povezovalnikAi.php
│   └── deepseekBackend.php
│
├── zasebniAi/                 # Zasebni sistemski agenti
│   ├── arhitekturniAi/        # Arhitekturni AI (DeepSeek)
│   │   ├── deepseek_arhitekt.php
│   │   ├── deepseek_arhitekt (2).php
│   │   ├── deepseek_adapter.php
│   │   ├── deepseek_baza.php
│   │   ├── deepseek_integrator.php
│   │   ├── deepseek_koder.php
│   │   ├── deepseek_nacrtovalec.php
│   │   ├── deepseek_nacrtovalec2.php
│   │   ├── deepseek_nadzornik.php
│   │   ├── deepseek_nadzornik2.php
│   │   ├── deepseek_nadzornik3.php
│   │   ├── deepseek_revizor.php
│   │   ├── openclaw_coder.php
│   │   └── crestodian.php
│   │
│   ├── strukturniAI/          # Strukturirani AI pomočniki
│   │   ├── arhitekt_globalno.php
│   │   ├── arhitekt_moduli.php
│   │   ├── arhitekt_podatki.php
│   │   ├── arhitekt_sistem.php
│   │   ├── arhitekt_uporabniki.php
│   │   └── arhitekt_vsebina.php
│   │
│   ├── placenecAi/            # Plačane AI storitve
│   │   ├── ai_asistent.php
│   │   └── how_to.txt
│   │
│   └── sistemskiAi/           # (prazna mapa)
│
└── javniAi/                   # Javni dostopni agenti
    ├── komunikacijskiAi/     # Komunikacijski AI
    ├── PisarAi/              # Pisarniški AI asistent
    ├── varuhAvatarAi/        # Avatar varuh
    └── varuhModulAi/         # Modul varuh AI
```

---

## Astra Mentor (Centralni AI)

**Pot:** `AI/astraMentor/`

### Namen
Astra Mentor je centralni AI asistent platforme, ki nudi:
- Pomoč uporabnikom
- Smernice za module
- Generiranje vsebin
- Analizo podatkov

### Komponente

| Datoteka | Namen |
|----------|-------|
| `AstraMentorIntegracija.php` | Integracija z jedrom |
| `AstraMentorIntegration.php` | Alternativna integracija |
| `astraMentorGateway.php` | Vstopna točka |
| `astraMentorHistory.php` | Zgodovina pogovorov |
| `NalagalnikAi.php` | Nalaganje AI modelov |
| `ObdelovalnikAi.php` | Obdelava zahtev |
| `PomocnikAi.php` | Pomožne funkcije |
| `PovezavaAi.php` | Povezovanje s API-ji |
| `povezovalnikAi.php` | Alternativno povezovanje |
| `deepseekBackend.php` | DeepSeek backend adapter |

---

## Arhitekturni AI

**Pot:** `AI/zasebniAi/arhitekturniAi/`

### Namen
Arhitekturni AI se uporablja za:
- Načrtovanje sistema
- Analizo arhitekture
- Refaktoriranje kode
- Generiranje nove kode

### Specializirani arhitekti

| Datoteka | Namen |
|----------|-------|
| `deepseek_arhitekt.php` | Glavni arhitekt (DeepSeek) |
| `deepseek_arhitekt (2).php` | Alternativna verzija |
| `deepseek_nacrtovalec.php` | Načrtovalec komponent |
| `deepseek_nacrtovalec2.php` | Načrtovalec v2 |
| `deepseek_koder.php` | Koder asistenta |
| `deepseek_revizor.php` | Revizor kode |
| `deepseek_nadzornik.php` | Nadzornik procesov |
| `deepseek_nadzornik2.php` | Nadzornik v2 |
| `deepseek_nadzornik3.php` | Nadzornik v3 |
| `deepseek_adapter.php` | Adapter za AI providerje |
| `deepseek_baza.php` | Baza znanja |
| `deepseek_integrator.php` | Integrator komponent |
| `openclaw_coder.php` | OpenClaw koder |
| `crestodian.php` | Čuvaj arhitekture |

---

## Strukturirani AI

**Pot:** `AI/zasebniAi/strukturniAI/`

### Namen
Strukturirani AI pomočniki za specifična področja:

| Datoteka | Namen | Področje |
|----------|-------|----------|
| `arhitekt_globalno.php` | Arhitekt GLOBALNO | Frontend |
| `arhitekt_moduli.php` | Arhitekt MODULI | Moduli |
| `arhitekt_podatki.php` | Arhitekt PODATKI | Podatkovna plast |
| `arhitekt_sistem.php` | Arhitekt SISTEM | Sistemsko jedro |
| `arhitekt_uporabniki.php` | Arhitekt UPORABNIKI | Uporabniška plast |
| `arhitekt_vsebina.php` | Arhitekt VSEBINA | Vsebinska plast |

---

## Llama Helper

**Pot:** `AI/llama_helper.php`

### Namen
Helper za komunikacijo z lokalnim Ollama/Llama 3.2 HTTP endpointom.

### Funkcije

```php
llama_config(): array
    // Vrne konfiguracijo:
    // - url: Ollama URL (privzeto: http://localhost:11434)
    // - path: API pot (privzeto: /api/chat)
    // - key: API ključ (neobvezen)
    // - model: Izbran model
    // - models_env: Seznam modelov iz env
    // - selected_file: Pot do datoteke z izbranim modelom

llama_set_selected_model(string $model): bool
    // Nastavi izbrani model

llama_chat(array $messages, ?string $model = null,
           float $temperature = 0.2, int $max_tokens = 1024): string
    // Pošlje sporočila modelu in vrne odgovor
```

### Okoljske spremenljivke

| Spremenljivka | Privzeto | Opis |
|---------------|----------|------|
| `OLLAMA_URL` | `http://localhost:11434` | Ollama URL |
| `OLLAMA_CHAT_PATH` | `/api/chat` | API pot |
| `OLLAMA_API_KEY` | (prazno) | API ključ |
| `OLLAMA_MODEL` | `llama-3.2` | Privzeti model |
| `OLLAMA_MODELS` | (prazno) | Seznam modelov (comma-separated) |

---

## Univerzalni AI Asistent

**Pot:** `AI/asistentAi.php`

### Namen
Spletni vmesnik za univerzalnega AI asistenta z podporo za:
- Blog pisanje
- Analize
- Kreativno pisanje
- Lektoriranje
- Kodiranje
- Prevajanje
- Debugging

### Uporaba

```bash
# Klic direktno
php AI/asistentAi.php
```

### Agenti

| Agent | Namen |
|-------|-------|
| `bloger` | Blog pisec - članki in vsebine |
| `analitik` | Analitik - analize in pregledi |
| `kreativec` | Kreativec - kreativno pisanje |
| `urejevalec` | Urejevalec - lektoriranje |

---

## Javni AI Agenti

**Pot:** `AI/javniAi/`

### Namen
Javni agenti za uporabnike:

| Mapa | Namen |
|------|-------|
| `komunikacijskiAi/` | Komunikacijski AI |
| `PisarAi/` | Pisarniški AI |
| `varuhAvatarAi/` | Avatar varuh |
| `varuhModulAi/` | Modul varuh AI |

---

## Varnost

**Pot:** `AI/varnost.php`, `AI/varnost2.php`

### Namen
Varnostno sidro za AI komponento:
- Preprečevanje nepooblaščenega dostopa
- Validacija AI zahtev
- Rate limiting za AI klic
- Input sanitization

### Uporaba

```php
if (!defined('AI_VSTOP')) {
    $v = __DIR__ . '/varnost.php';
    if (file_exists($v)) require_once $v;
}
```

---

## ai_proxy.php

**Pot:** `AI/ai_proxy.php`

### Namen
Proxy za zunanje AI API-je:
- OpenAI
- Anthropic
- Google (API Hub)
- Drugi ponudniki

---

## TODO Refaktor

1. **Konsolidacija** — Združi podvojene DeepSeek datoteke (arhitekt (2), nadzornik 2/3...)
2. **Standardizacija** — Enoten vmesnik za vse agente
3. **Dokumentacija** — Dodaj PHPDoc za vse funkcije
4. **Tipi** — Dodaj striktne tipe
5. **Error handling** — Izboljšaj obravnavo napak
6. **Caching** — Dodaj predpomnjenje za AI odgovore

---

## Avtor

**AstraMentalica Mojster**
