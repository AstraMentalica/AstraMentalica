Če kanali vsebujejo BACKEND LOGIKO → so v SISTEM/

Če kanali samo PRIPRAVIJO IZHOD (formatiranje) → so v ADAPTER/

PRAVILNA RAZDELITEV
Komponenta	Kam spada	Zakaj
Business logika kanalov	SISTEM/storitve_svetov/	To je backend
Izhodni format (JSON/HTML/XML)	ADAPTER/izhod_kanali/	To je pretvorba, ne logika
Priprava odziva	ADAPTER/odzivi/	To je pretvorba, ne logika
Čakalna vrsta (queue)	SISTEM/kanali/vrsta.php	To je backend (upravljanje)
Worker/obdelava	SISTEM/kanali/obdelava.php	To je backend (izvajanje)

storitve_svetov/	Business logika (prijava, registracija, profil...)	DOMENSKA LOGIKA
kanali/	Tehnična izvedba izhoda (queue, worker, format)	TEHNIČNA LOGIKA

SISTEM/
├── api.php                    # N1 - vstop
├── kernel/                    # N3 - mehanika
│   ├── zaganjalnik.php
│   ├── env_loader.php
│   ├── nastavitve.php
│   └── jedro/
│       ├── 01_upravljalec_svetov.php
│       ├── 02_napake.php
│       ├── 03_varnost.php
│       ├── 04_seja.php
│       ├── 05_pravice.php
│       ├── 06_cache.php
│       ├── 07_dogodki.php
│       ├── 08_kavlji.php
│       ├── 09_ponudniki.php
│       ├── 10_middleware.php
│       ├── 11_usmerjevalnik.php
│       ├── 12_validacija.php
│       ├── 13_api.php
│       ├── 14_zagon.php
│       └── 15_pogon.php
├── storitve_svetov/           # N2 - business logika
│   ├── uporabniki/
│   ├── moduli/
│   ├── globalno/
│   └── astra/
└── kanali/                    # N2 - tehnični izhod
    ├── priprava.php
    ├── vrsta.php
    └── obdelava.php