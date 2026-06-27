To je osnovna ustava sistema. Če je nova koda v nasprotju s temi pravili, je nova koda napačna.

================================================================================
1. TEMELJNO PRAVILO
================================================================================

Edina javna vstopna točka sistema je:

index.php

Vsi javni dostopi:

WEB
API
AJAX
FORME

vstopajo skozi:

index.php

Neposreden dostop do:

SISTEM/
GLOBALNO/
UPORABNIKI/
MODULI/
VSEBINA/
PODATKI/

ni dovoljen.

================================================================================
2. SIDRO
================================================================================

Edina datoteka, ki določa poti sistema:

pot.php

Pravila:

pot.php uporablja __DIR__
vse ostale datoteke uporabljajo samo konstante

Prepovedano:

__DIR__
dirname()
realpath()

izven:

pot.php

================================================================================
3. SLOJI SISTEMA
================================================================================

SIDRO
│
└── pot.php

BOUNDARY
│
└── ADAPTER/

KERNEL
│
└── SISTEM/kernel/

BUSINESS
│
└── SISTEM/storitve_svetov/

KANALI
│
└── SISTEM/kanali/

FRONTEND RUNTIME
│
└── GLOBALNO/frontend/

RENDER
│
└── GLOBALNO/render/

VIZUALNI
│
└── GLOBALNO/vmesnik/

SANDBOX
│
└── MODULI/

USER SPACE
│
└── UPORABNIKI/

ADMIN
│
└── ASTRA/

STORAGE
│
└── PODATKI/

CONTENT
│
└── VSEBINA/

================================================================================
4. ADAPTER
================================================================================

ADAPTER je prevajalnik.

Njegova naloga:

vse vhode prevesti v enotni API jezik

ADAPTER ne vsebuje:

business logike
uporabnikov
modulov
frontenda

ADAPTER samo:

sprejme vhod
normalizira vhod
ustvari zahtevo
pošlje zahtevo v SISTEM/api.php

================================================================================
5. EDINA POT DO SISTEMA
================================================================================

Nihče ne sme dostopati neposredno do:

SISTEM/kernel/
SISTEM/storitve_svetov/
SISTEM/kanali/

Edina dovoljena pot:

index.php
↓
ADAPTER
↓
SISTEM/api.php

To pravilo je absolutno.

================================================================================
6. SISTEM/api.php
================================================================================

To je edina vstopna točka sistema.

Naloge:

sprejme standardizirano zahtevo
preveri zahtevo
po potrebi zažene sistem
usmeri zahtevo
vrne odgovor

Ne vsebuje:

HTML
CSS
renderjev
frontend logike

================================================================================
7. ZAGON SISTEMA
================================================================================

Sistem zažene izključno:

SISTEM/kernel/zaganjalnik.php

Nikoli:

adapter
frontend

Pravilen tok:

SISTEM/api.php
↓
SISTEM/kernel/zaganjalnik.php
↓
SISTEM/kernel/jedro/01-15
↓
SISTEM/storitve_svetov/
↓
SISTEM/kanali/

================================================================================
8. JEDRO
================================================================================

Mapa:

SISTEM/kernel/jedro/

Jedro vsebuje samo sistemske mehanizme.

Primer:

napake
varnost
seje
pravice
cache
dogodki
kavlji
ponudniki
middleware
validacija
api
zagon
pogon
kontrakti

Jedro ne vsebuje:

registracije
prijave
profilov
košaric
vsebin
modulov
uporabnikov

================================================================================
9. KANALI
================================================================================

Mapa:

SISTEM/kanali/

Kanali so tehnična izvedba izhoda.

Kanali ne vsebujejo business logike.

Kanali ne vsebujejo sistemske mehanike (to je v kernel/).

Dovoljeno:

priprava.php (standardizacija izhoda)
vrsta.php (čakalna vrsta)
obdelava.php (worker)

Prepovedano:

registracija uporabnika
profil uporabnika
moduli
vsebina
svetovi
business logika

================================================================================
10. BUSINESS LOGIKA
================================================================================

Business logika obstaja samo tukaj:

SISTEM/storitve_svetov/

Nikjer drugje.

================================================================================
11. SVETOVI
================================================================================

Frontend svet:

GLOBALNO/
UPORABNIKI/
ASTRA/

Backend svet:

SISTEM/storitve_svetov/globalno/
SISTEM/storitve_svetov/uporabniki/
SISTEM/storitve_svetov/astra/

Poimenovanje:

svet
↓
entiteta
↓
dejanje

Primer:

SISTEM/storitve_svetov/uporabniki/uporabnik_prijava.php
SISTEM/storitve_svetov/uporabniki/uporabnik_registracija.php
SISTEM/storitve_svetov/uporabniki/uporabnik_profil.php

================================================================================
12. MODULI
================================================================================

Modul ni del sistema.

Modul je izoliran sandbox.

Modul ne pozna:

SISTEM
ADAPTER
GLOBALNO
UPORABNIKI
ASTRA
VSEBINA
PODATKI
SIDRO

Modul ne uporablja:

__DIR__

Modul ne uporablja:

require_once POT_SISTEM

Modul ne pozna nobene sistemske poti.

================================================================================
13. MODUL_BRIDGE
================================================================================

Edina stvar, ki jo modul pozna:

Modul_Bridge

Komunikacija:

MODUL
↓
Modul_Bridge
↓
ADAPTER
↓
SISTEM/api.php

Neposredna komunikacija:

MODUL
↓
SISTEM

je prepovedana.

================================================================================
14. MODUL IZVEN SISTEMA
================================================================================

Če modul ni nameščen:

SIDRO ne obstaja

Bridge ustvari:

demo konstante
demo okolje
demo uporabnika
demo pravice
demo nastavitve
demo glavo
demo nogo

Modul ne sme vedeti razlike med:

lokalni razvoj
produkcijski sistem

================================================================================
15. MODUL V SISTEMU
================================================================================

Če je modul nameščen:

Modul_Bridge
↓
SIDRO
↓
ADAPTER
↓
SISTEM/api.php

Bridge uporablja dejanske sistemske storitve.

Modul deduje:

GLOBALNO/render
GLOBALNO/vmesnik

Modul nima lastnega globalnega layouta.

================================================================================
16. VSEBINA
================================================================================

Mapa:

VSEBINA/

Ni frontend.

Ni aplikacija.

Ni modul.

Je skladišče vsebin.

Primer:

MD
TXT
JSON
predloge
članki
dokumentacija

Prikazujejo jih:

GLOBALNO
UPORABNIKI
MODULI

================================================================================
17. PODATKI
================================================================================

Mapa:

PODATKI/

Vse trajne shrambe.

Primer:

baze
cache
logi
uploadi
manifesti
nastavitve
ključavnice
seje

================================================================================
18. VARNOST
================================================================================

Varnost ni naloga modula.

Varnost ni naloga renderja.

Varnost ni naloga frontenda.

Varnost izvaja:

SISTEM/kernel/

================================================================================
19. ZLATO PRAVILO
================================================================================

Če neka koda obide:

ADAPTER
↓
SISTEM/api.php

je arhitekturno napačna.

Ne glede na to, ali trenutno deluje.

To je najpomembnejše pravilo celotnega sistema.

================================================================================
20. MODUL POZNA IZKLJUČNO MODUL_BRIDGE
================================================================================

Modul nikoli ne komunicira neposredno z:

SISTEM
ADAPTER
GLOBALNO
UPORABNIKI
ASTRA
PODATKI
pot.php

Edina dovoljena povezava:

Modul
↓
Modul_Bridge

Bridge je edina pogodba med modulom in sistemom.

================================================================================
21. MODUL MORA DELOVATI TUDI IZVEN SISTEMA
================================================================================

Modul mora biti možno razvijati, testirati in distribuirati brez AstraMentalice.

Če sistem ne obstaja:

Modul_Bridge simulira:

konstante
konfiguracijo
dovoljenja
glavo
nogo
razvojno okolje

Modul ne sme vedeti, ali teče v produkcijskem sistemu ali v razvojnem okolju.

================================================================================
22. MODUL SE REGISTRIRA PREKO MANIFESTA
================================================================================

Pogoj za registracijo modula je veljaven manifest.

Če manifest ni popoln:

modul ni registriran.

Registracija modula ni odvisna od njegove poslovne logike.

Registracija preverja izključno:

identiteto modula
verzijo
odvisnosti
deklarirane zahteve
deklarirana dovoljenja

================================================================================
23. AKTIVACIJA JE LOČENA OD REGISTRACIJE
================================================================================

Registracija:

sistem modul prepozna.

Aktivacija:

sistem modul omogoči.

Modul je lahko:

registriran
neaktiven

ali:

registriran
aktiven

Aktivacija nikoli ne pomeni registracije.

================================================================================
24. DVOJNI UPORABNIŠKI PODATKI SO NAMERNI
================================================================================

PODATKI/uporabniki/

vsebuje sistemske podatke:

identiteta
prijava
vloge
dovoljenja
sistemske evidence

UPORABNIKI/podatki/

vsebuje osebne uporabniške podatke:

PASSPORT
dnevniki
sanje
meditacije
osebne nastavitve
osebna zgodovina

Gre za dve različni domeni.

Združevanje ni dovoljeno.

================================================================================
25. SISTEM/API.PHP JE EDINI SISTEMSKI PREHOD
================================================================================

SISTEM/api.php

je edina vstopna točka v sistem.

Ne glede na kanal:

WEB
API
CLI
AI
WEBHOOK
CRON

vsi pridejo do sistema preko:

ADAPTER
↓
SISTEM/api.php

================================================================================
26. KERNEL API NI JAVNI API
================================================================================

SISTEM/kernel/jedro/13_api.php

ni javni API.

To je notranji jedrni API.

Uporabljajo ga izključno jedrne komponente sistema.

Nikoli ni dostopen iz:

ADAPTER
MODULOV
GLOBALNO
UPORABNIKI
ASTRA

Javni sistemski API ostaja izključno:

SISTEM/api.php

================================================================================
27. BOOTSTRAP POMENI ZGOLJ PRIPRAVO
================================================================================

Bootstrap ne pomeni zagona sistema.

Bootstrap pomeni:

pripravo
registracijo
inicializacijo definicij

Zagon sistema izvaja izključno:

SISTEM/kernel/zaganjalnik.php

Vsi bootstrap_* ostanki se postopoma preimenujejo v:

zagon_registracija
zagon_dogodkov
zagon_grafa
zagon_odkrivanje

zaradi jasnejše semantike.

================================================================================
28. SISTEMSKA RESNICA JE ENA
================================================================================

Če obstajata dve datoteki z enakim namenom:

ena mora postati avtoritativna,
druga mora postati izvedena ali pa se odstrani.

V sistemu ne smeta obstajati dve resnici za:

uporabnike
module
dovoljenja
konfiguracijo
registracijo
poti

Vedno obstaja en avtoritativni vir.

================================================================================
KONEC DOKUMENTA
================================================================================