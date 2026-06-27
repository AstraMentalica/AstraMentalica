✅ KONČNA PRAVILA ZA RAZVIJALCE (POTRJENA)
🎯 TEMELJNO PRAVILO
Modul je NEUMEN.

Modul pozna samo:

svojo mapo

Modul_Bridge

Modul ne ve:

kje je sidro (pot.php)

kje je sistem (SISTEM/)

za druge module

za baze

za seje

za HTTP

ZAKAJ? Da modul lahko kadarkoli odstraniš iz sistema in ga poganjaš samostojno preko Modul_Bridge demo podatkov.

🏗️ STRUKTURA MODULA
text
MODULI/ImeModula/
├── modul.php              ← edina vstopna točka (OBVEZNO)
└── podatki/               ← podatki modula (OBVEZNO)
    ├── manifest.json      ← sistemske nastavitve (OBVEZNO)
    ├── api.json           ← HTTP poti (OBVEZNO)
    ├── izhod.json         ← shema in pisanje (OBVEZNO)
    └── modul.md           ← dokumentacija (PRIZOROČLJIVO)
📋 KAJ MODUL SME
Stvar	Opis
require_once __DIR__ . '/modul.php'	Klic lastne logike
__DIR__ . '/podatki/'	Dostop do lastnih podatkov
__DIR__ . '/cache/'	Lastni cache (začasni podatki)
__DIR__ . '/temp/'	Lastne začasne datoteke
Modul_Bridge::klic($akcija, $podatki)	Klic sistema preko mostu
Modul_Bridge::podatki_beri($kljuc)	Branje demo/sistemskih podatkov
Modul_Bridge::podatki_pisi($kljuc, $vrednost)	Pisanje podatkov (z dovoljenjem)
Modul_Bridge::uporabnik_pridobi()	Dobi trenutnega uporabnika (ali demo)
Modul_Bridge::vloga_preveri($zahtevana)	Preveri vlogo (ali demo)
Modul_Bridge::modul_klic($modul, $akcija, $podatki)	Klic drugega modula preko Bridge
🚫 KAJ MODUL NE SME
Stvar	Zakaj
require_once '../../pot.php'	Modul ne ve za sidro
require_once POT_SISTEM . '/...'	Modul ne pozna sistemskih poti
__DIR__ . '/../'	Modul ne sme zapustiti svoje mape
__DIR__ . '/../../'	Modul ne sme zapustiti svoje mape
__DIR__ . '/../../../SISTEM'	Modul ne sme zapustiti svoje mape
Direkten klic v SISTEM/	Krši izolacijo
session_start()	Modul ne upravlja sej
$_SESSION direktno	Modul ne upravlja sej
$_POST, $_GET, $_REQUEST, $_COOKIE	Modul ne dostopa direktno do HTTP
global $db ali $GLOBALS	Modul ne uporablja globalnih spremenljivk
exec(), shell_exec(), proc_open()	Modul ne ustvarja niti (razen z dovoljenjem)
die(), exit() v API akcijah	Modul ne sme ustavljati sistema
HTML v modul.php	Modul vrača podatke, ne prikaz
Klicanje drugih modulov direktno	Moduli so izolirani – grejo preko Bridge
✅ DOVOLJENO ZA RAZVOJ
Stvar	Kdaj
echo, var_dump, print_r	Samo v razvoju (if (defined('DEBUG')))
file_put_contents()	Samo znotraj lastne mape modula
die(), exit()	Samo v CLI orodjih (cron.php, namesti.php)
🔌 KAKO MODUL KLIČE SISTEM
php
function modul_moj_akcija(string $akcija, array $podatki = []): array {
    // 1. Preveri vlogo preko Bridge-a
    if (!Modul_Bridge::vloga_preveri('S1')) {
        return ['napaka' => 'Nimaš dostopa'];
    }
    
    // 2. Dobi uporabnika preko Bridge-a
    $uporabnik = Modul_Bridge::uporabnik_pridobi();
    
    // 3. Sistemski podatki → preko Bridge-a
    $sistemskiPodatki = Modul_Bridge::podatki_beri('sistem/nastavitve');
    
    // 4. Lastni cache → direktno (znotraj mape)
    $cache = json_decode(file_get_contents(__DIR__ . '/cache/tmp.json'), true);
    
    // 5. Tvoja logika
    $rezultat = ...;
    
    // 6. Shrani podatke → preko Bridge-a (z dovoljenjem)
    Modul_Bridge::podatki_pisi('moj_modul/rezultat', $rezultat);
    
    // 7. Klic drugega modula → preko Bridge-a
    $koledar = Modul_Bridge::modul_klic('koledar', 'dogodki_pridobi', ['datum' => 'danes']);
    
    // 8. Vrni JSON
    return ['uspeh' => true, 'vsebina' => $rezultat];
}
🧪 KAKO MODUL DELUJE IZVEN SISTEMA
Ko modul odstraniš iz sistema, Modul_Bridge samodejno zagotovi demo okolje:

Funkcija	Demo podatek
Modul_Bridge::uporabnik_pridobi()	['id' => 0, 'ime' => 'Demo', 'vloga' => 60]
Modul_Bridge::vloga_preveri()	true (demo admin)
Modul_Bridge::podatki_beri()	Prazni demo podatki
Modul_Bridge::podatki_pisi()	Shrani v demo mapo
Modul_Bridge::modul_klic()	Vrne demo podatke za drug modul
Modul ne ve razlike med pravim sistemom in demo načinom.

⚠️ NASA IZJEMA – NE OBSTAJA
NASA nima posebnega statusa. Tudi NASA uporablja:

php
Modul_Bridge::podatki_pisi('nasa/snapshot.json', $podatki);
Bridge prepozna:

json
"dovoljenja": ["pisati_v_lastno_mapo"]
in dovoli.

Noben modul nima posebnega statusa.

📝 CHECKLIST ZA RAZVIJALCA
text
[ ] modul.php obstaja z vstopno funkcijo
[ ] modul.php nima HTML
[ ] modul.php nima require_once na sistemske poti
[ ] modul.php ne uporablja __DIR__ za izhod iz svoje mape
[ ] modul.php ne uporablja $_POST, $_GET, $_SESSION direktno
[ ] modul.php ne uporablja globalnih spremenljivk
[ ] modul.php kliče samo Modul_Bridge za zunanje stvari
[ ] modul.php uporablja file_put_contents() samo znotraj svoje mape
[ ] podatki/manifest.json je pravilen (kanoničen)
[ ] podatki/api.json ima poti
[ ] podatki/izhod.json ima shemo
[ ] Modul deluje samostojno (preko Modul_Bridge demo)
🚨 ČE KRŠIŠ PRAVILA
Kršitev	Kazen
require_once na sistemske poti	Modul zavrnjen
__DIR__ za izhod iz lastne mape	Modul zavrnjen
Direkten klic v SISTEM/	Modul zavrnjen
HTML v modul.php	Modul zavrnjen
session_start()	Modul zavrnjen
Direktno klicanje drugega modula	Modul zavrnjen
exec(), shell_exec() brez dovoljenja	Modul zavrnjen
🕊️ KONČNA MANTRA
Modul pozna samo:

svojo mapo

svoj manifest

svoj izhod

svoj api

Modul_Bridge

Modul ne pozna:

SIDRA

SISTEM/

baze

session

HTTP

drugih modulov

filesystema izven svoje mape

To je to. Pravila so zaklenjena. 🕊️