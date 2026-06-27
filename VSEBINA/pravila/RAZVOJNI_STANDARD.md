================================================================================
ASTRAMENTALICA — RAZVOJNI STANDARD
================================================================================

1. OSNOVNO PRAVILO
Koda mora biti: berljiva, predvidljiva, stabilna, modularna, brez magije,
brez skritih side-effectov, brez "pametnih bližnjic".

================================================================================
2. INCLUDE / REQUIRE
================================================================================

DOVOLJENO:
require_once POT_SISTEM . '/kernel/env_loader.php';
require_once POT_KERNEL . '/jedro/01_upravljalec_svetov.php';

PREPOVEDANO:
require '../../../nekaj.php';

PRAVILO: Vse poti uporabljajo konstante iz pot.php. Nikoli direktnih poti.

================================================================================
3. HTML STANDARD
================================================================================

HTML JE PASIVEN – ne dela business logike.

DOVOLJENO v GLOBALNO/render/:
<?= $podatki['ime'] ?>

PREPOVEDANO v GLOBALNO/render/:
if ($_SESSION['vloga'] > 50)   # Business logika ne sodi v render!

================================================================================
4. JAVASCRIPT STANDARD
================================================================================

PREPOVEDAN inline JS:
❌ <button onclick="test()">

DOVOLJENI samo event listenerji:
✅ document.addEventListener('click', ...)

================================================================================
5. API STANDARD
================================================================================

API vedno vrne JSON, nikoli HTML/warningov/stack trace.

OBVEZEN FORMAT:
{
    "status": "success|error",
    "sporocilo": "",
    "vsebina": {},
    "napake": []
}

================================================================================
6. MODUL STANDARD
================================================================================

MODUL JE GLUP:
- Nima svojega HTML (razen če ima eksplicitno dovoljenje v manifestu)
- Vrača SAMO JSON (API)
- Render (HTML) zagotovi GLOBALNO/render/ preko Modul_Bridge -> adapterja

MODUL VSEBUJE:
- modul.php (edina vstopna točka – API)
- manifest.json (definira pravice, odvisnosti, dovoljenja)

MODUL NE SME:
- manipulirati session
- direktno nalagati GLOBALNO/
- direktno pisati v PODATKI/ (razen svoje mape preko dovoljenja)
- izvajati redirect
- imeti HTML v modul.php

MODUL IMA LAHKO (samo za razvoj):
- razvoj/ mapo s testnim HTML (ignorira se v produkciji)

================================================================================
7. MANIFEST STANDARD (glej kanonični format v MODULI.md, razdelek 4, in TEMELJ.md)
================================================================================

Vsi manifesti modulov uporabljajo ENO obliko (definirano v MODULI.md):
polja `modul.{id,ime,tip,nivo,verzija,aktiviran,vstopna,opis}`,
`dostop.{minimalna_vloga,plan}`, `vhod`, `izhod`, `odvisnosti`, `cache`, `ui`,
`izvajanje.{tip,cron,api_only}`, `log`.

Primer (Stelaris):
{
    "modul": {
        "id": "stelaris",
        "ime": "Stelaris",
        "tip": "zbiralec",
        "nivo": 1,
        "verzija": "7.2.0",
        "aktiviran": true,
        "vstopna": "modul.php",
        "opis": "Astrološki modul"
    },
    "dostop": { "minimalna_vloga": "S1", "plan": "osnova" },
    "vhod": { "potrebuje": [], "opcijsko": ["latitude", "longitude"], "vir": "uporabnik" },
    "izhod": { "format": "json", "pise_v": ["modul_stelaris"] },
    "odvisnosti": { "bere_iz": [] },
    "cache": { "omogocen": true, "ttl": 3600, "skupina": "stelaris" },
    "ui": { "ima_prikaz": true, "ikona": "🌌", "barva": "#818cf8" },
    "izvajanje": { "tip": "ui", "cron": false, "api_only": false },
    "log": { "omogocen": true, "nivo": "info" }
}

> **Opomba o uskladitvi:** starejša oblika s poljima `"oznaka"`, `"vloga": 20` (int) ali
> `"rbac.zahtevana_vloga"` je zastarela. Vedno uporabi obliko zgoraj — `dostop.minimalna_vloga`
> kot string ("S0".."S5", "gost", "admin"), pretvorjen v int šele preko `vloga_v_int()`.

================================================================================
8. ERROR STANDARD
================================================================================

Napake vedno:
Exception → 02_napake.php → dnevnik → odziv

PREPOVEDANO:
die($napaka);

================================================================================
9. LOG STANDARD
================================================================================

FORMAT:
[2026-05-12 18:55:00] [ERROR] [MODUL: Stelaris] Sporočilo...

NIVOJI: DEBUG, INFO, WARNING, ERROR, CRITICAL

================================================================================
10. DEBUG STANDARD
================================================================================

DEBUG JE CENTRALEN:
debug_zapisi()
debug_prikazi()

PREPOVEDANO:
var_dump(), print_r(), echo v runtime kodi

================================================================================
11. VARNOSTNI STANDARD
================================================================================

VEDNO:
- sanitize input
- csrf preverjanje
- escape output
- validate data
- whitelist moduli
- whitelist poti

NIKOLI:
- dynamic include user input
- eval()
- raw SQL string concat
- direct filesystem access brez preverjanja

================================================================================
12. PERFORMANCE STANDARD
================================================================================

NE:
- 20x branje iste datoteke
- recursive include chaos
- reload manifestov nonstop

DA:
- cache
- registry
- lazy loading
- shared context

================================================================================
13. MODUL BRIDGE – RAZMERJE
================================================================================

Modul_Bridge je edina točka, ki jo modul pozna.

TOK, KO SISTEM OBSTAJA:
pot.php (SIDRO) → Modul_Bridge (dobi konstante) → preko adapterja → SISTEM/api.php → modul vrača JSON → GLOBALNO/render/ izriše

TOK, KO SISTEM NE OBSTAJA (stebelni modul):
Modul_Bridge (lastne mini_* funkcije) → modul vrača JSON → Modul_Bridge/mini_izhod izriše

VLOGA Modul_Bridge:
- Razbremeni adapter
- Pripravi modulu glavo in nogo (ali iz GLOBALNO ali iz lastnega mini sistema)
- Zagotovi konstante (preko pot.php, če obstaja)
- Lahko generira nov SISTEM (stebelni modul s svojim sidrom)

Modul_Bridge NIMA:
- Dostopa do SISTEM (razen preko Modul_Bridge -> adapterja, ko sistem obstaja)
- Dostopa do GLOBALNO (razen preko Modul_Bridge -> adapterja)

================================================================================
14. KONČNO PRAVILO
================================================================================

Če rešitev:
- skriva flow
- oteži debug
- povečuje magijo
- briše meje layerjev
- meša odgovornosti
- oteži selitev sistema

potem NI dovoljena v ASTRAMENTALICA arhitekturi.

================================================================================
KONEC RAZVOJNEGA STANDARDA
================================================================================