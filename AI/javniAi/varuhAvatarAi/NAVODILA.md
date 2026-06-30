# 🌟 Duhovni Varuhi – Navodila za zagon

## 1. Namestitev (samo prvič)

```bash
cd varuh_sistem
pip install -r requirements.txt
```

## 2. Vnesi svoj API ključ

Odpri `.env_varuh` in zamenjaj `sk-ant-VSTAVI_SVOJ_KLJUC` s svojim resničnim
ključem iz https://console.anthropic.com/settings/keys

```
ANTHROPIC_API_KEY_VARUH=sk-ant-api03-tvoj-resnicni-kljuc-tukaj
```

**Vsak modul ima lahko svoj `.env_ime_modula`** – sistem jih samodejno najde:
- `.env_varuh` → varuhi
- `.env_avatar` → avatar sistem (ko ga dodamo)
- `.env_knjiznica` → knjižnica (ko jo dodamo)
- `.env` → splošni, rezerva za vse

Vrstni red iskanja ključa za modul "varuh":
1. `ANTHROPIC_API_KEY_VARUH`
2. `VARUH_API_KEY`
3. `ANTHROPIC_API_KEY` (splošni, če nič drugega ni najdeno)

## 3. Zagon strežnika

```bash
python varuh_server.py
```

Videl boš:
```
╔══════════════════════════════════════════╗
║  🌟 DUHOVNI VARUHI – STREŽNIK           ║
║  Port: 5757                               ║
║  API:  ✅ nastavljen                      ║
╚══════════════════════════════════════════╝
```

Če pravi `❌ manjka ANTHROPIC_API_KEY` – preveri `.env_varuh`.

## 4. Odpri v brskalniku

```
http://localhost:5757
```

## 5. Uporaba

- Klikni varuha na levi (Stellarion je vedno odklenjen)
- Vpiši sporočilo ali pritisni 🎤 za govor
- Varuh ti odgovori v slovenščini in **na glas spregovori** (vklopljeno privzeto)
- Vsako sporočilo prinese točke → avatar napreduje skozi stopnje
- Verzi (ognjeni/vodni/srčni/zemeljski/zračni) se sami spremenijo glede na to,
  kako hitro klikaš/tipkaš

## Dodajanje novega varuha

Odpri `moduli_zavesti/varuhi.json` in dodaj nov vnos po vzoru obstoječih –
vsak varuh ima svojo `zavest.temelj`, `posebnosti`, `ni` pravila in
`magicna_zival`. Strežnika ni treba ponovno zaganjati za majhne popravke
besedila – samo za nove ključe v kodi.

## Pogoste težave

| Težava | Rešitev |
|---|---|
| "Napaka povezave" v klepetu | Preveri da je ključ v `.env_varuh` pravi in se začne s `sk-ant-` |
| Varuh ne govori | Preveri da brskalnik podpira Web Speech API (Chrome/Edge najboljše) |
| Slovenski glas ne obstaja | Brskalnik bo uporabil najbližji glas; na nekaterih OS ni pravega sl-SI glasu |
| Mikrofon ne dela | Dovoli dostop do mikrofona v brskalniku; HTTPS je potreben na produkciji |
| Port 5757 zaseden | Spremeni `PORT=` v `.env_varuh` |
