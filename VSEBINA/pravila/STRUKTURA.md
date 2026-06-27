ROOT/
├── index.php              ← EDINA JAVNA VSTOPNA TOČKA
├── pot.php                ← SIDRO (konstante poti)
└── .htaccess              ← vse gre na index.php


ADAPTER/
├── adapter.php                    ← EDINI VSTOP/IZSTOP
│
├── vhod_webhook/                  ← SKRITI URL-ji (samo jaz in servisi)
│   ├── adapter_facebook.php
│   ├── adapter_telegram.php
│   └── adapter_stripe.php
│
├── vhod_zasebno/                  ← SAMO JAZ (cron, AI, CLI)
│   ├── adapter_cron.php
│   ├── adapter_ai.php
│   ├── adapter_zasebni_api.php
│   └── adapter_cli.php
│
├── izhod_kanali/                  ← PRETVORBA IZHODA
│   ├── KanalWeb.php
│   ├── KanalApi.php
│   ├── KanalAi.php
│   ├── KanalCli.php
│   ├── KanalTelegram.php
│   └── KanalFacebook.php
│
├── middleware/                    ← FILTERJI
│   ├── auth.php
│   ├── csrf.php
│   ├── cors.php
│   ├── omejevalnik.php
│   ├── ip_blacklist.php
│   └── dnevnik.php
│
└── odzivi/                        ← PRIPRAVA IZHODA
    ├── adapter_odziv.php          # pošiljanje izhoda na kanale
    ├── adapter_napake.php         # napake → format
    └── adapter_statusi.php        # standardizirani statusni kodi


.htaccess
───────────────────────────────────────────────────────────────────────────────
RewriteEngine On

# Webhooki (skriti URL-ji – samo ti in zunanji servisi vesta)
RewriteRule ^facebook-webhook$ ADAPTER/vhod_webhook/adapter_facebook.php [L]
RewriteRule ^telegram-webhook$ ADAPTER/vhod_webhook/adapter_telegram.php [L]

# Vse ostalo (splet, API, AI) gre na index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
TOK IZVAJANJA
text
Zahteva
    ↓
index.php (edini javni vstop)
    ↓
ADAPTER/adapter.php (normalizacija v ENOTNI API FORMAT)
    ↓
SISTEM/api.php (edini vstop v sistem)
    ↓
SISTEM/kernel/zaganjalnik.php (bootstrap)
    ↓
SISTEM/kernel/jedro/01-15 (sistemska mehanika)
    ↓
SISTEM/storitve_svetov/ (business logika)
    ↓
SISTEM/kanali/ (tehnični izhod: priprava, vrsta, obdelava)
    ↓
ADAPTER/odzivi/adapter_odziv.php (pošiljanje na kanale)
    ↓
ODZIV
ADAPTER → SISTEM → ADAPTER → SISTEM → ADAPTER → ODZIV

text
ADAPTER (normalizacija)
    ↓
SISTEM (obdelava)
    ↓
ADAPTER (serializacija)
    ↓
SISTEM (kanali/vrsta)
    ↓
ADAPTER (izhod)
    ↓
ODZIV
POMEMBNO
Vsi vhodi gredo skozi ADAPTER – web, api, cli, webhook, cron

Vsi izhodi gredo skozi ADAPTER – web, api, telegram, facebook

SISTEM nikoli ne echo-a – vrača samo podatke

ADAPTER nima business logike – samo pretvorba