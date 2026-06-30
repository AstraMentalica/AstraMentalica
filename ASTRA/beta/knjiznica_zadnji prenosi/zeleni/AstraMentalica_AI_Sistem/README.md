# AstraMentalica AI Sistem

## Datoteke

| Datoteka | Opis |
|---|---|
| `ustvarjalno_okolje.html` | ASTRA admin + AI varuhi chat (rail navigacija) |
| `avatar_varuh_sistem.html` | Avatar + Duhovni Varuh + AI chat + glas (GLAVNA) |
| `zavest_varuhov_editor.html` | Originalni editor zavesti varuhov |
| `astramentalica_nadzorna_plosca.html` | Originalna nadzorna plošča |

## JSON podatki

| Datoteka | Opis |
|---|---|
| `varuhi.json` | Centralna baza vseh varuhov (9 varuhov + 9 živali) |
| `avatar_arhetipi.json` | Archetipi za razvoj avatarja (6 stebrov, 10 stopenj) |
| `moduli_varuhi.json` | Varuhi modulov z osebnostmi |
| `knjiznica.js` | Skupna JS logika knjižnice varuhov |

## API

Obe HTML datoteki kličeta Anthropic API direktno iz brskalnika.
Model: `claude-sonnet-4-6`

Za produkcijo: zamenjaj z DeepSeek prek `ASTRA/ai_proxy.php`:
```
POST https://api.deepseek.com/v1/chat/completions
Authorization: Bearer {DEEPSEEK_API_KEY iz PODATKI/sef/.env_api}
```

## Arhitekturna pravila (AstraMentalica v117)

- `pot.php` je absolutno sidro — `POT_*` konstante povsod
- `die()`/`exit()` so **prepovedani** — `http_response_code(403); return;`
- `media_storitev.php` je edina za poti medijev
- Jedro 01–16 zaklenjeno brez odobritve
- CSS v slovenščini — `.navigacija`, `.kartica`, `.gumb` (brez angleških prefiksov)
- API ključi samo v `PODATKI/sef/.env_api`
