📚 ASTRAMENTALICA – ZAKLJUČENA PRAVILA (v2 – USKLAJENA RAZLIČICA)
BREZ RUNTIME – VSA NESKLADJA MED VIRI ODPRAVLJENA

Ta datoteka je samodejno sestavljena iz usklajenih virnih dokumentov spodaj.
Če karkoli popravljaš, popravi v izvirnem dokumentu (glej kazalo), NE tukaj direktno,
sicer se sprememba pri naslednjem sestavljanju izgubi.

SEZNAM USKLAJITEV OPRAVLJENIH V TEJ RAZLIČICI (v2):
1. N1/N2/N3 nivoji poenoteni: N1=SISTEM/api.php (vstop), N2=storitve_svetov+kanali
   (business), N3=kernel/jedro (mehanika). Velja v VSEH dokumentih.
2. Imena funkcij povsod snake_case po vzorcu podrocje_akcija() (JEZIKOVNI_STANDARD.md).
   Vsi primeri v STANDARDI.md in MODULI.md popravljeni iz camelCase.
3. Runtime (16_upravljalec_runtime.php) dokončno izbrisan iz VSEH dokumentov,
   vključno z grafom odvisnosti v REGISTER.md.
4. ADAPTER/odzivi/ poenoteno na: adapter_odziv.php, adapter_napake.php,
   adapter_statusi.php (po TEMELJ.md).
5. Knjižnični modul poenoten na ime CorpusMysticum (MODULI/SVET/CorpusMysticum/).
   VIZIJA.md uskladen.
6. Tipkarska napaka "nastavi sejoUporabnika()" odpravljena v STANDARDI.md.
7. manifest.json poenoten na EN kanonični format (iz TEMELJ.md) v MODULI.md
8. POT_SEF je konfigurabilen; če ni nastavljen v okolju, pade nazaj na PODATKI/sef/.
   in RAZVOJNI_STANDARD.md.

KAZALO
1. USTAVA – Temeljna pravila
2. ARHITEKTURA – Struktura sistema
3. ARHITEKTURA NA KRATKO – Povzetek toka in map
4. OSNOVA – Sloji in zlato pravilo
5. STRUKTURA – Mapna struktura ADAPTER/SISTEM
6. KAKO DELUJE SISTEM – Kanali vs storitve_svetov
7. JEZIKOVNI STANDARD – Poimenovanje in jezik
8. STANDARDI KODE – Kako pišemo kodo
9. RAZVOJNI STANDARD – Kako pišemo kodo (dodatek)
10. MODULI – Kako dodamo nov modul
11. MODUL_BRIDGE – Orkestrator za razvoj modulov
12. TEMELJ – Popolna specifikacija (brez runtime)
13. REGISTER – Živi dokument projekta
14. VIZIJA – Namen in filozofija projekta


================================================================================
USTAVA – Temeljna pravila
================================================================================

# USTAVA PROJEKTA
> Dokument ki definira **nespremenljiva pravila** sistema.
> Vsak AI asistent, razvijalec ali orodje ki dela na tem projektu mora prebrati ta dokument najprej.
> Pravila v USTAVI se ne kršijo. Nikoli.

---

## 0. IDENTITETA PROJEKTA

- Projekt je pisan **izključno v slovenščini** — mape, datoteke, spremenljivke, funkcije, komentarji
- Izjema so: PHP ključne besede, HTML tagi, SQL rezervirane besede, standardne PHP konstante (`true`, `false`, `null`, `echo`, `return`...)
- Projekt je zasnovan za **dolgo življenje** — vsaka odločitev mora biti sprejeta z mislijo "bo to vzdrževal nekdo drug čez 5 let?"
- Projekt je **modularen** — vsak del dela neodvisno, skupaj delajo samo čez SISTEM

---

## 1. ZLATA PRAVILA (absolutna)

### 1.0 pot.php je absolutno sidro
```
pot.php je PRVA datoteka ki se naloži — vedno, povsod, brez izjeme.
Ona ve kje je root. Vse ostalo ve za poti samo čez njo.
```

- `pot.php` leži v **rootu projekta**
- Vsaka datoteka ki potrebuje karkoli vključi NAJPREJ `pot.php`
- `pot.php` se prilagodi glede na okolje (lokalno, staging, produkcija) — **sama**, brez da ji kdo pove
- **Ne vsebuje logike** — samo definira konstante `POT_*`
- **Se ne premika, ne preimenuje, ne briše**
- AI asistent jo **nikoli ne sme spremeniti** brez eksplicitne odobritve lastnika

```php
// Vsaka datoteka začne tako:
require_once dirname(__FILE__, /* N nivojev gor do root */) . '/pot.php';

// Potem vse poti čez konstante:
require_once POT_SISTEM . '/api.php';
```

### 1.1 En vhod
```
VSE gre čez SISTEM/api.php. Nobena mapa ne kliče direktno druge mape.
```

### 1.2 En smer
```
Tok gre VEDNO navzdol: N1 → N2 → N3
Nikoli navzgor. Nikoli mimo nivoja.
```

### 1.3 Izolacija
```
Globalne mape (GLOBALNO, MODULI, UPORABNIKI, ASTRA, VSEBINA, PODATKI)
se med seboj NE vidijo, NE kličejo, NE poznajo.
Edina skupna točka je SISTEM.
```

### 1.4 SISTEM ne renderira
```
SISTEM vrača samo podatke (PHP array, JSON).
HTML/CSS/JS generira VEDNO samo frontend (GLOBALNO, ali samostojni moduli).
```

### 1.5 Jedro je zaklenjeno
```
SISTEM/kernel/jedro/ se NE spreminja brez eksplicitnega razloga.
Vsaka sprememba jedra zahteva komentar: // SPREMEMBA: razlog + datum
```

---

## 2. FILOZOFIJA

### Preživeti mora brez avtorja
Vsaka datoteka mora biti razumljiva osebi, ki projekta nikoli ni videla.
To pomeni: jasna imena, komentarji kjer ni očitno, logična struktura.

### Ena stvar, ena datoteka
Vsaka datoteka dela **točno eno stvar**.
Če datoteka dela dve stvari — jo razdelimo.

### Podatki ločeni od logike, logika ločena od prikaza
```
podatki → SISTEM (logika) → frontend (prikaz)
```
Nikoli SQL v HTML. Nikoli HTML v PHP logiki.

### Napake so vredne, ne sramota
Vsaka napaka mora biti:
1. Ujeta (try/catch ali error handler)
2. Zabeležena (PODATKI/log/)
3. Prikazana smiselno (uporabniku sporočilo, ne stack trace)

---

## 3. KAJ JE PREPOVEDANO

| Prepovedano | Zakaj |
|---|---|
| `die()` ali `exit()` v logiki | Nenadzorovano ustavljanje |
| `$_GET`, `$_POST` direktno v logiki | Vedno najprej sanitizacija v N1 |
| SQL direktno v frontend | Kršitev izolacije |
| `require` med globalnimi mapami | Kršitev izolacije |
| Anglešk spremenljivke/funkcije | Kršitev jezikovnega standarda |
| Hardcoded poti (`/var/www/...`) | Neprenosljivost — vedno `POT_*` konstante |
| Komentarji v angleščini | Kršitev jezikovnega standarda |
| Funkcije daljše od 40 vrstic | Razdeliti na manjše |
| Datoteke daljše od 300 vrstic | Razdeliti po odgovornosti |

---

## 4. VERZIONIRANJE IN SPREMEMBE

### Vsaka datoteka ima glavo
```php
<?php
/**
 * DATOTEKA: ime_datoteke.php
 * NAMEN:    Kratko, kaj ta datoteka dela (1-2 vrstici)
 * NIVO:     1 / 2 / 3 (sistemski nivo)
 * ODVISNO:  seznam datotek ki jih kliče (ali "nič")
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 */
```

### Spremembe se beležijo
```php
// SPREMEMBA 1.1 [2026-02-15]: Dodana validacija gesla — Janez
// SPREMEMBA 1.2 [2026-03-01]: Popravek seje pri odjavi — AI/GPT
```

### Verzije
- `1.0` — osnovna različica
- `1.x` — manjše popravke, brez spremembe vmesnika
- `2.0` — sprememba vmesnika ali obnašanja (zahteva posodobitev odvisnih)

---

## 5. KDAJ SME AI DELATI SAMOSTOJNO

AI asistent (GPT, Claude, DeepSeek...) sme samostojno:
- ✅ Dodajati nove module (po MODULI.md)
- ✅ Pisati novo logiko v N2 (nikoli N3 brez odobritve)
- ✅ Pisati frontend v GLOBALNO
- ✅ Pisati vsebino v VSEBINA/
- ✅ Popravljati napake v obstoječih datotekah

AI asistent mora vprašati preden:
- ⛔ Spreminja karkoli v `SISTEM/kernel/jedro/`
- ⛔ Dodaja nove odvisnosti (knjižnice, paketi)
- ⛔ Spreminja strukturo baz
- ⛔ Briše datoteke
- ⛔ Spreminja `pot.php` ali `api.php`

---

## 6. PRIORITETE PRI KONFLIKTIH

Ko si pravili v konfliktu, zmaga višja prioriteta:

```
1. Varnost
2. Pravilnost (deluje kot mora)
3. Izolacija (arhitekturna pravila)
4. Berljivost
5. Zmogljivost
```

> Primer: če bi hitrejša koda zahtevala klic med dvema globalnima mapama — zmaga izolacija, koda ostane počasnejša.

---

*USTAVA.md — verzija 1.0 — temelj projekta*


================================================================================
ARHITEKTURA – Struktura sistema
================================================================================

# ARHITEKTURA SISTEMA ASTRAMENTALICA

## Temeljno pravilo

Celoten sistem uporablja enotno notranjo komunikacijo preko API formata.

Ne glede na izvor zahteve mora vsaka zahteva najprej skozi ADAPTER, kjer se pretvori v enotni sistemski format.

---

## TOK IZVAJANJA

```text
Zahteva
    ↓
index.php / api.php / ai.php / cli.php ...
    ↓
ADAPTER
    ↓
Enotni API format
    ↓
SISTEM/api.php
    ↓
Kernel/zaganjalnik.php
    ↓
01_upravljalec_svetov.php
    ↓
Poslovna logika
    ↓
Odziv
    ↓
ADAPTER
    ↓
Končni kanal
```

---

# ROOT MAPE

## ADAPTER

### Namen

Pretvorba različnih vhodov in izhodov v enotni sistemski format.

ADAPTER ne vsebuje poslovne logike.

ADAPTER ne pozna notranjosti:

- GLOBALNO
- UPORABNIKI
- MODULI
- VSEBINA
- PODATKI

### Naloge

- prepozna kanal
- normalizira zahtevo
- pošlje zahtevo v SISTEM
- prejme odziv
- pripravi odziv za končni kanal

---

## SISTEM

### Namen

Srce in motor celotnega projekta.

SISTEM vsebuje:

- kernel
- kernel/jedro
- storitve svetov
- administracijo

SISTEM je edini backend projekta.

---

## MODUL_BRIDGE

### Namen

Izvajalno in povezovalno okolje za module.

### V SISTEMU

- preveri whitelist
- preveri dostop
- registrira modul
- omogoči prikaz modula
- poveže modul s sistemom

### IZVEN SISTEMA

- demo konstante
- demo glava
- demo noga
- demo nastavitve

### Definicija

Modul_Bridge je izvajalno in povezovalno okolje modulov, ki omogoča delovanje modulov znotraj sistema ali samostojno izven sistema brez sprememb samega modula.


================================================================================
ARHITEKTURA NA KRATKO – Povzetek toka in map
================================================================================

# ARHITEKTURA SISTEMA ASTRAMENTALICA (BREZ RUNTIME)

---

## OSNOVNO PRAVILO

V sistem ne vstopa nihče neposredno.

Edina vstopna točka v SISTEM/ je enotna API zahteva, ki jo pripravi ADAPTER.

Edini vstop v SISTEM je dovoljen preko:
SISTEM/api.php

text

Vstopi iz socialnih omrežij so skozi:
ADAPTER/vhod_webhook/

text

Zasebni vstopi (CLI, zasebni AI, zasebni API, CRON) so v:
ADAPTER/vhod_zasebno/

text

Vsi vhodi (WEB, AI, CLI, API, TELEGRAM, FACEBOOK, WEBHOOK, CRON ...) morajo biti najprej prevedeni v enoten format zahteve.


---

## TOK IZVAJANJA
Zunanji svet
↓
index.php (edina javna vstopna točka)
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

text

---

## ADAPTER

### Namen

Prevajalnik zunanjega sveta.

### Naloge

- sprejema zahteve iz različnih virov
- normalizira podatke
- ustvari enoten format zahteve
- izbere ustrezen kanal
- posreduje zahtevo SISTEMU

### Ne vsebuje

- poslovne logike
- logike modulov
- logike uporabnikov
- logike svetov
- sistemske mehanike

### Po domače

ADAPTER je prevajalec med zunanjim svetom in sistemom.


---

## SISTEM

### Namen

Motor celotnega projekta.

SISTEM vsebuje vso sistemsko logiko in vse mehanizme, potrebne za delovanje projekta.

SISTEM je razdeljen na:
SISTEM/
├── api.php # edini vstop v sistem (N1)
├── kernel/ # sistemska mehanika (N3)
├── storitve_svetov/ # business logika (N2)
└── kanali/ # tehnični izhod (N2)

text

---

## SISTEM/kernel

### Namen

Mehanika motorja.

### Vsebuje

- varnost
- seje
- dovoljenja (RBAC)
- whitelist
- delo z bazami (upravljalec_baz.php)
- sistemske protokole
- sistemske pogodbe (kontrakte)
- sistemske dogodke
- sistemske mehanizme
- napake
- cache
- dogodke
- kavlje
- ponudnike
- middleware
- usmerjevalnik
- validacijo
- zagon
- pogon

### Ne vsebuje

- logike svetov
- logike modulov
- logike uporabnikov
- HTML
- renderjev
- business logike

### Po domače

Kernel skrbi, da motor sploh deluje.


---

## SISTEM/storitve_svetov

### Namen

Servisni sloj med svetovi in sistemom.

### Vsebuje

- funkcionalno logiko posameznih svetov
- povezavo med svetovi in sistemom

### Primer
UPORABNIKI (frontend)
↓
SISTEM/storitve_svetov/uporabniki/uporabnik_prijava.php
↓
SISTEM/kernel/jedro/ (mehanika)
↓
rezultat (podatki)
↓
nazaj skozi ADAPTER v kanal

text

### Po domače

Storitve_svetov so vozlišče funkcij, ki povezuje svetove z motorjem sistema.


---

## SISTEM/kanali

### Namen

Tehnična izvedba izhoda sistema.

### Vsebuje
SISTEM/kanali/
├── priprava.php # standardizacija izhoda (contract)
├── vrsta.php # čakalna vrsta (queue)
└── obdelava.php # worker (pošiljanje na kanale)

text

### Ne vsebuje

- business logike
- sistemske mehanike (to je v kernel/)
- HTML
- renderjev

### Po domače

Kanali skrbijo, kako gre odgovor iz sistema ven.


---

## GLOBALNO

### Namen

Skupni frontend sistema.

### Vsebuje
GLOBALNO/
├── frontend/ # JS, CSS, interakcije
├── render/ # SAMO prikaz (glava, noga, navigacija, domov, 404)
└── vmesnik/ # dizajn (css, teme)

text

### Ne vsebuje

- poslovne logike
- sistemske logike
- SQL
- session_start()
- direktnih klicev v SISTEM/

### Po domače

GLOBALNO je skupni videz in prikaz sistema.


---

## UPORABNIKI

### Namen

Uporabniški svet.

### Vsebuje
UPORABNIKI/
├── prikaz/uporabnik/ # uporabniške strani
├── prikaz/skrbnik/ # skrbniške strani
├── prikaz/sistem/ # prijava, registracija, odjava, profil
└── {id}/ # vsak uporabnik svojo mapo
├── profil.json
├── nastavitve.json
└── PASSPORT/ # osebni podatki

text

### Ne vsebuje

- registracijske logike
- prijavne logike
- dela z bazo

### Ta logika se izvaja v
SISTEM/storitve_svetov/uporabniki/

text

### Po domače

UPORABNIKI je uporabniški sandbox – samo prikaz.


---

## MODULI

### Namen

Modulski svet.

### Vsebuje
MODULI/
├── ORAKLEUM/
├── NEBO/
├── ZEMLJA/
├── SIMBOLI/
├── POTI/
├── SVET/
└── VIP/

text

Vsak modul:
MODULI/{kategorija}/{ImeModula}/
├── modul.php # glavna logika (API)
├── manifest.json # konfiguracija
├── podatki/ # podatki modula
└── .baza/ # lastna baza (če jo ima)

text

### Ne vsebuje

- sistemske logike
- direktnih klicev v SISTEM/
- direktnih klicev v GLOBALNO/
- direktnih klicev v druge module

### Logika modulov se izvaja v
SISTEM/storitve_svetov/moduli/

text

### Komunikacija
MODUL
↓
Modul_Bridge
↓
ADAPTER
↓
SISTEM/api.php

text

### Po domače

MODULI so izolirani funkcionalni sandboxi.


---

## VSEBINA

### Namen

Shramba vsebin.

### Vsebuje
VSEBINA/
├── javno/
├── faq/
├── branja/
└── manifest/

text

- MD datoteke
- besedila
- opise
- vsebinske predloge

### Ne vsebuje

- poslovne logike
- sistemske logike

### Po domače

VSEBINA vsebuje samo vsebino.


---

## PODATKI

### Namen

Centralni podatkovni zalogovnik.

### Vsebuje
PODATKI/
├── sef/ # .env datoteke
├── registri/ # centralni registri (moduli, rbac, whitelist)
├── moduli/ # podatki modulov
├── uporabniki/ # sistemski podatki (identiteta, vloge)
├── globalno/ # inventura
├── sistem/ # tmp, dnevnik, vrsta, seja, predpomnilnik
├── baze/ # mysql, sqlite, json
└── analitika/ # statistika, meritve

text

- JSON podatke
- MySQL podatke
- SQLite podatke
- nastavitve
- dnevnike
- cache
- sistemske podatke
- uporabniške podatke

### Po domače

PODATKI so centralna shramba sistema.


---

## KLJUČNO PRAVILO
GLOBALNO, UPORABNIKI, MODULI in VSEBINA ne izvajajo poslovne logike.

text

Poslovna logika se izvaja v:
SISTEM/storitve_svetov/

text

Sistemska logika se izvaja v:
SISTEM/kernel/

text

Tehnični izhod se izvaja v:
SISTEM/kanali/

text

Vsi vhodi morajo skozi:
ADAPTER

text

Vsi odzivi se vračajo skozi:
ADAPTER/izhod_kanali/

text


---

## ABSOLUTNE PREPOVEDI
❌ Direktno klicanje SISTEM/ mimo index.php
❌ Modul vključuje SISTEM/
❌ Modul bere $_SESSION
❌ Modul piše v PODATKI/ direktno (razen z dovoljenjem v svojo mapo)
❌ Backend v MODULI/, GLOBALNO/, UPORABNIKI/
❌ Modul vpogleda v drug modul direktno
❌ RUNTIME v SISTEM/ (ne obstaja)

text


---

## ENOTNO POIMENOVANJE (funkcije)

| Področje | Funkcije |
|----------|----------|
| Varnost | `varnost_ocisti()`, `varnost_csrf_ustvari()`, `varnost_csrf_preveri()` |
| Cache | `cache_preberi()`, `cache_shrani()`, `cache_brisi()`, `cache_ocisti_vse()` |
| Dogodki | `na_dogodek()`, `spusti_dogodek()`, `zabelezi_dogodek_log()` |
| Kavlji | `dodaj_kavelj()`, `izvedi_kavelj()` |
| Ponudniki | `registriraj_ponudnika()`, `pridobi_ponudnika()` |
| Middleware | `dodaj_middleware()`, `izvedi_middleware()` |
| Usmerjevalnik | `registriraj_pot()`, `usmeri()` |
| Upravljalec | `upravljalec_whitelist()`, `upravljalec_register()`, `upravljalec_zazeni()` |
| API | `api_odgovor()`, `api_napaka()` |
| Baze | `baza_beri()`, `baza_pisi()`, `baza_obstaja()`, `baza_brisi()`, `baza_poisci()` |
| Most | `most_api_klic()`, `most_podatki_pisi()`, `most_podatki_beri()` |
| RBAC | `vloga_v_int()`, `trenutna_vloga()`, `ma_vlogo()`, `zahtevaj_vlogo()` |
| Seja | `seja_zacni()`, `seja_obnovi()`, `seja_je_prijavljen()`, `seja_unici()` |
| Izhod | `izhod_pripravi()`, `vrsta_dodaj()`, `vrsta_preberi()`, `obdelava_izvedi()` |


---

## CELOTNA MAPA STRUKTURA
ROOT/
├── index.php # EDINA JAVNA VSTOPNA TOČKA
├── pot.php # SIDRO – vse konstante
└── .htaccess # vse gre na index.php

ADAPTER/
├── adapter.php # EDINI VSTOP/IZSTOP
│
├── vhod_webhook/ # SKRITI URL-ji (samo jaz in servisi)
│ ├── adapter_facebook.php
│ ├── adapter_telegram.php
│ └── adapter_stripe.php
│
├── vhod_zasebno/ # SAMO JAZ (cron, AI, CLI)
│ ├── adapter_cron.php
│ ├── adapter_ai.php
│ ├── adapter_zasebni_api.php
│ └── adapter_cli.php
│
├── izhod_kanali/ # PRETVORBA IZHODA
│ ├── KanalWeb.php
│ ├── KanalApi.php
│ ├── KanalAi.php
│ ├── KanalCli.php
│ ├── KanalTelegram.php
│ └── KanalFacebook.php
│
├── middleware/ # FILTERJI
│ ├── auth.php
│ ├── csrf.php
│ ├── cors.php
│ ├── omejevalnik.php
│ ├── ip_blacklist.php
│ └── dnevnik.php
│
└── odzivi/ # PRIPRAVA IZHODA
├── adapter_odziv.php # pošiljanje izhoda na kanale
├── adapter_napake.php # napake → format
└── adapter_statusi.php # standardizirani statusni kodi

SISTEM/
├── api.php # N1 – EDINI VSTOP V SISTEM
│
├── kernel/ # N3 – SISTEMSKA MEHANIKA
│ ├── zaganjalnik.php # bootstrap
│ ├── env_loader.php # okolje (.env)
│ ├── nastavitve.php # globalne nastavitve
│ │
│ ├── jedro/ # čisto jedro (brez domenske logike)
│ │ ├── 01_upravljalec_svetov.php
│ │ ├── 02_napake.php
│ │ ├── 03_varnost.php
│ │ ├── 04_seja.php
│ │ ├── 05_pravice.php
│ │ ├── 06_cache.php
│ │ ├── 07_dogodki.php
│ │ ├── 08_kavlji.php
│ │ ├── 09_ponudniki.php
│ │ ├── 10_middleware.php
│ │ ├── 11_usmerjevalnik.php
│ │ ├── 12_validacija.php
│ │ ├── 13_api.php
│ │ ├── 14_zagon.php
│ │ └── 15_pogon.php
│ │ # 16_upravljalec_runtime.php – IZBRISAN (runtime ne obstaja)
│ │
│ └── baze/ # adapterji za baze
│ ├── upravljalec_baz.php # centralizirano branje/pisanje (EDINI DOSTOP)
│ ├── adapter_json.php
│ ├── adapter_mysql.php
│ └── adapter_sqlite.php
│
├── storitve_svetov/ # N2 – BUSINESS LOGIKA
│ ├── uporabniki/ # backend uporabniki
│ ├── moduli/ # backend modulov
│ ├── globalno/ # backend za prikaz
│ └── astra/ # backend za ASTRA
│
└── kanali/ # N2 – TEHNIČNI IZHOD
├── priprava.php # standardizacija izhoda (contract)
├── vrsta.php # čakalna vrsta (queue)
└── obdelava.php # worker (pošiljanje na kanale)

GLOBALNO/ # SAMO frontend (brez business logike)
├── frontend/ # JS, CSS, interakcije
├── render/ # SAMO prikaz (brez logike)
│ ├── glava.php
│ ├── noga.php
│ ├── navigacija.php
│ ├── domov.php
│ └── 404.html
└── vmesnik/ # dizajn
├── css
└── teme

UPORABNIKI/ # SAMO frontend (brez business logike)
├── prikaz/uporabnik/
│ ├── uporabnik_nastavitve.php
│ ├── uporabnik_passport.php
│ ├── uporabnik_meditacije.php
│ └── uporabnik_dnevnik.php
├── prikaz/skrbnik/
│ └── nastavitve.php
├── prikaz/sistem/
│ ├── uporabnik_prijava.php
│ ├── uporabnik_registracija.php
│ ├── uporabnik_odjava.php
│ └── uporabnik_profil.php
└── {id}/
├── profil.json
├── nastavitve.json
└── PASSPORT/
├── dnevnik.json
├── modrosti.json
├── odkritja.json
├── pot.json
├── simboli.json
├── sanje.json
└── meditacije.json

MODULI/ # izolirani domenski moduli
├── ORAKLEUM/
│ ├── Tarot/
│ └── OraculumVisionis/
├── NEBO/
│ ├── Stelaris/
│ ├── Lunaris/
│ ├── Jyotir/
│ └── Senzorji/
├── ZEMLJA/
│ ├── QiVitalis/
│ ├── Pranaymica/
│ ├── Energetica/
│ ├── BotanicaSacra/
│ ├── Lapidaria/
│ ├── VibraMystica/
│ └── Somnaris/
├── SIMBOLI/
│ ├── Numyra/
│ ├── NumerariumCosmicum/
│ ├── AegypticaArcana/
│ ├── NordicaMystica/
│ ├── MysticaMesoamericana/
│ ├── Sephirotica/
│ ├── Occultum/
│ └── Devorum/
├── POTI/
│ ├── Transmutaria/
│ ├── UmbraeCodex/
│ ├── LiberUmbrae/
│ ├── ViaAnimae/
│ ├── Animaris/
│ └── Seraphica/
├── SVET/
│ ├── CorpusMysticum/
│ ├── Aetheris/
│ ├── Celestara/
│ ├── Mystaia/
│ └── CosmicaScientia/
└── VIP/
└── Synera/

VSEBINA/ # statične MD vsebine
├── javno/
├── faq/
├── branja/
└── manifest/

PODATKI/ # centralni rezervoar sistema
├── sef/
│ ├── .env_sistem
│ ├── .env_api
│ └── .env_baza
├── registri/
│ ├── moduli_register.json
│ ├── rbac/
│ │ ├── vloge.json
│ │ └── pravila.json
│ ├── override/
│ │ └── {id}.json
│ ├── whitelist/
│ │ ├── whitelist_gost.json
│ │ ├── whitelist_S0.json
│ │ ├── whitelist_S1.json
│ │ ├── whitelist_S2.json
│ │ ├── whitelist_S3.json
│ │ ├── whitelist_S4.json
│ │ ├── whitelist_S5.json
│ │ └── whitelist_admin.json
│ ├── prepovedi.json
│ └── postavitev.json
├── moduli/
│ └── {ime_modula}/
│ ├── uporaba.json
│ ├── globalno.json
│ ├── cache.json
│ └── statistika.json
├── uporabniki/
│ └── (samo centralno, ne peskovnik)
├── globalno/inventura/
│ └── gradniki.json
├── sistem/
│ ├── tmp/
│ ├── dnevnik/
│ │ ├── sistem.log
│ │ ├── api.log
│ │ └── instalacija.log
│ ├── vrsta/
│ │ ├── izhod.json
│ │ ├── cron.json
│ │ └── interno.json
│ ├── seja/
│ └── predpomnilnik/
├── baze/
│ ├── mysql/
│ ├── sqlite/
│ └── json/
└── analitika/
├── statistika/
└── meritve/

ASTRA/ # nadzorni svet – samo S5/admin
├── nadzorni_center.php
└── admin_portal.php

text

---

## KONČNO
ADAPTER → SISTEM → ADAPTER → SISTEM → ADAPTER → ODZIV

text

**BREZ RUNTIME. BREZ IZJEM.**

================================================================================
OSNOVA – Sloji in zlato pravilo
================================================================================

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

================================================================================
STRUKTURA – Mapna struktura ADAPTER/SISTEM
================================================================================

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

================================================================================
KAKO DELUJE SISTEM – Kanali vs storitve_svetov
================================================================================

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

================================================================================
JEZIKOVNI STANDARD – Poimenovanje in jezik
================================================================================

================================================================================
ASTRAMENTALICA — JEZIKOVNI STANDARD v1 (ZAKLENJEN)
================================================================================

1. IDENTITETA SISTEMA
Primarni jezik: slovenščina.
Velja za: poslovno logiko, sistemske funkcije, helperje, komentarje,
CSS razrede, dokumentacijo, log sporočila, interne kontrakte, module,
konfiguracije.

================================================================================
2. IMENA FUNKCIJ
FORMAT: podrocje_akcija()
Primeri: shramba_beri(), seja_obnovi(), cache_pocisti(),
         middleware_registriraj(), odziv_preveri(), vrsta_dodaj()

================================================================================
3. IMENA DATOTEK
male_crke_z_podctajem.php
Primeri: upravljalec_baz.php, odziv.php, kanal_web.php

PREPOVEDANO: manager.php, utils.php, common.php, helper.php

================================================================================
4. IMENA RAZREDOV
PascalCase
Primeri: UpravljalecUporabnikov, SistemRuntime, CacheAdapter

SUFFIXI: *Adapter, *Runtime, *Provider, *Middleware, *Kontrakt, *DTO, *VO

================================================================================
5. IMENA MAP
ROOT: SISTEM/, ADAPTER/, MODULI/
MODULI/[PODROCJA]/ - VELIKE ČRKE (ZEMLJA, NEBO, ETER)
POSAMEZNI MODULI - PascalCase (BotanicaSacra, OrdoSolaris)
OSTALO - male črke (sredstva/, elementi/, protokoli/)

================================================================================
6. CSS STANDARD
CSS RAZREDI: kebab-case, slovensko
Primeri: .glavni-ovoj, .kartica-uporabnika, .modul-aktiven

FILES: reset.css, spremenljivke.css, sloji.css, kartica.css, navigacija.css

================================================================================
7. HEADER STANDARD (vsaka PHP datoteka)
================================================================================

<?php
    /**
     * ============================================================
     * POT: SISTEM/kernel/jedro/05_pravice.php
     * 📅 VERZIJA: v114 (9.6.2026 15:00)
     * ============================================================
     *
     * 🏛️ NIVO: KERNEL
     *
     * 📰 NAMEN:
     *     Upravljanje uporabniških pravic (RBAC).
     *
     * 🔧 JAVNE FUNKCIJE:
     *     - pravice_preveri_vlogo(int $potrebna): bool
     *     - pravice_ima_dovoljenje(string $dovoljenje): bool
     *     - pravice_registriraj_dovoljenje(string $ime, int $vloga): void
     *
     * 📡 ODVISNOSTI:
     *     - SISTEM/kernel/jedro/03_varnost.php
     *     - POT_PODATKI . '/sistem/registri/pravice.json'
     *
     * 🤝 SOODVISNOSTI:
     *     - SISTEM/kernel/jedro/04_seja.php
     *     - SISTEM/kernel/jedro/06_cache.php
     *
     * ⚡ UPORABA:
     *     - Kliče se iz middleware ali neposredno v storitvah.
     *
     * 🚫 PREPOVEDI:
     *     - Brez echo, print_r, var_dump
     *     - Brez die(), exit()
     *     - Brez direktnih poti (uporabi konstante!)
     *     - Brez direktnega branja $_SESSION
     *
     * 📌 STATUS:
     *     Stabilno
     *
     * 📅 ZGODOVINA:
     *     - v114: uskladitev s Header Standard v114
     *     - v113: dodane oznake in jezik
     *     - v112: prva implementacija
     *
     * 👤 AVTOR:
     *     AstraMentalica Mojster
     *
     * 🌐 JEZIK:
     *     sl
     *
     * 🏷️ OZNAKE:
     *     kernel, jedro, pravice, rbac
     * ============================================================
     */
    declare(strict_types=1);

    defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

================================================================================
8. PREVODI (angleško → slovensko)
================================================================================

loader → nalagalnik          request → zahteva          response → odziv
router → usmerjevalnik       session → seja             bootstrap → zagon
provider → ponudnik          hook → kavelj              event → dogodek
context → kontekst           logger → dnevnik           queue → vrsta
cache → predpomnilnik        contract → kontrakt        dispatcher → odprava
worker → izvajalec           firewall → pozarni_zid      token → zeton

================================================================================
9. ANGLEŠKO DOVOLJUJEM (brez prevoda)
================================================================================

debug, debugging, monitoring, hash, middleware, pipeline, csrf, jwt

✅ runtime: cache_pocisti(), middleware_registriraj(), pipeline_izvedi()
❌ business: predpomnilnik_pocisti(), ponudnik_nalozi()

================================================================================
10. NAMESPACE STANDARD
================================================================================

namespace AstraMentalica\Kernel;
namespace AstraMentalica\Storitve;
namespace AstraMentalica\Moduli;
namespace AstraMentalica\Adapter;

FILESYSTEM ≠ NAMESPACE
Filesystem: MODULI/ZEMLJA/BotanicaSacra
Namespace:  AstraMentalica\Moduli\Zemlja\BotanicaSacra

================================================================================
11. PREPOVEDANO
================================================================================

❌ magic helperji, doStuff(), utils_final_v2.php
❌ global side-effect bootstrapi, implicit runtime registracije
❌ skrite dependency povezave, "smart" abstrakcije brez potrebe
❌ __DIR__ izven pot.php, relativne poti (../)
❌ echo, var_dump, print_r v CELEM SISTEMU (SISTEM/kernel/, SISTEM/storitve_svetov/, SISTEM/api.php)
✅ Dovoljeno samo v GLOBALNO/render/ za prikaz HTML
❌ Business logika v GLOBALNO/
❌ GLOBALNO direktno vidi MODULI/, UPORABNIKI/, PODATKI/

================================================================================
12. KONČNO PRAVILO
================================================================================

Če odločitev: zmanjša magijo, poveča predvidljivost, izboljša debugging,
zmanjša coupling, poveča modularnost → potem je pravilna.

================================================================================
KONEC JEZIKOVNEGA STANDARDA
================================================================================

================================================================================
STANDARDI KODE – Kako pišemo kodo
================================================================================

# STANDARDI KODE
> Preberi najprej: USTAVA.md, ARHITEKTURA.md
> Ta dokument definira **kako pišemo kodo** — PHP, poimenovanje, komentarji, struktura.

---

## 1. JEZIK

**Vse je v slovenščini.** Brez izjem razen:
- PHP rezervirane besede: `echo`, `return`, `true`, `false`, `null`, `class`, `function`...
- HTML tagi: `<div>`, `<form>`, `<input>`...
- SQL rezervirane besede: `SELECT`, `WHERE`, `JOIN`...
- Globalne PHP konstante: `PHP_EOL`, `PHP_INT_MAX`...

```php
// ✅ pravilno
function geslo_preveri(string $geslo): bool {
    $dolzina = strlen($geslo);
    return $dolzina >= 8;
}

// ❌ napačno (angleško IN camelCase — glej razdelek 2 za pravilo imen funkcij)
function checkPassword(string $password): bool {
    $length = strlen($password);
    return $length >= 8;
}
```

---

## 2. POIMENOVANJE

### Datoteke in mape
```
mape/            → male_črke z podčrtajem (snake_case)
datoteke.php     → male_črke z podčrtajem (snake_case)
```

Primeri:
```
SISTEM/kernel/jedro/01_napake.php     ✅
MODULI/osnovni/Codex/modul.php        ✅  (ime modula je z veliko — lastno ime)
GLOBALNO/gradniki/gumb.php            ✅
GLOBALNO/gradniki/GlavniGumb.php      ❌  (ne CamelCase za datoteke)
```

### Funkcije
```
podrocje_akcija(), z glagolom v akciji (format po JEZIKOVNI_STANDARD.md)
```
```php
function pravice_preveri(): bool {}
function modul_nalozi(string $ime): array {}
function uporabnik_shrani(array $podatki): bool {}
function seja_vrni(): array {}
function cache_izbrisi(string $kljuc): void {}
```

### Spremenljivke
```
camelCase, opisno ime, brez okrajšav (spremenljivke ostanejo camelCase — pravilo snake_case velja za FUNKCIJE, ne za spremenljivke)
```
```php
$uporabniškoIme = 'Janez';      // ✅
$aktivnaSeja = true;             // ✅
$steviloPoskusov = 3;            // ✅

$un = 'Janez';                  // ❌ (okrajšava)
$active = true;                 // ❌ (angleško)
$n = 3;                         // ❌ (neopisno)
```

### Konstante
```
VELIKA_ČRKE z podčrtajem
```
```php
define('POT_KOREN', __DIR__);
define('RAZLIČICA', '1.0');
define('MAX_POSKUSOV', 5);
```

### Razredi (Classes)
```
PascalCase, samostalnik
```
```php
class UporabnikUpravnik {}
class ModulNalagalnik {}
class SejaUpravnik {}
```

### Metode v razredih
```
snake_case, glagol na začetku (enako kot funkcije — glej JEZIKOVNI_STANDARD.md)
```
```php
class UporabnikUpravnik {
    public function preveri_prijavo(): bool {}
    public function nalozi_profil(int $id): array {}
    private function sanitiziraj_vnos(string $vnos): string {}
}
```

---

## 3. STRUKTURA DATOTEKE

Vsaka PHP datoteka ima **obvezno glavo**:

# ASTRAMENTALICA HEADER STANDARD

Standard določa enotno glavo za vse PHP in JavaScript datoteke v sistemu AstraMentalica.

Glava ni namenjena zgolj dokumentaciji, temveč predstavlja arhitekturno pogodbo datoteke.

Iz glave mora biti takoj razvidno:

- kaj datoteka počne
- v katerem nivoju arhitekture deluje
- katere komponente uporablja
- s katerimi datotekami sodeluje
- česa ne sme početi
- kdo jo vzdržuje
- kakšna je zgodovina sprememb

## KANONIČNI REFERENČNI HEADER

To je uradni referenčni primer.

Vsaka PHP datoteka mora uporabljati enako strukturo, enak vrstni red in enako vizualno obliko.

Spremenijo se samo vrednosti posameznih polj.

```php
    <?php

    /**
     * ============================================================
     * POT: SISTEM/kernel/jedro/05_pravice.php
     * 📅 VERZIJA: v114 (9.6.2026 15:00)
     * ============================================================
     *
     * 🏛️ NIVO: SISTEM N3
     *
     * 📰 NAMEN:
     *     Upravljanje uporabniških pravic (RBAC).
     *
     * 🔧 JAVNE FUNKCIJE:
     *     - pravice_preveri_vlogo(int $potrebna): bool
     *     - pravice_ima_dovoljenje(string $dovoljenje): bool
     *     - pravice_registriraj_dovoljenje(string $ime, int $vloga): void
     *
     * 📡 ODVISNOSTI:
     *     - SISTEM/kernel/jedro/03_varnost.php
     *     - POT_PODATKI . '/sistem/registri/pravice.json'
     *
     * 🤝 SOODVISNOSTI:
     *     - SISTEM/kernel/jedro/04_seja.php
     *     - SISTEM/kernel/jedro/06_cache.php
     *
     * ⚡ UPORABA:
     *     - Kliče se iz middleware ali neposredno v storitvah.
     *
     * 🚫 PREPOVEDI:
     *     - Brez echo, print_r, var_dump
     *     - Brez die(), exit()
     *     - Brez direktnih poti (uporabi konstante!)
     *     - Brez direktnega branja $_SESSION
     *
     * 📌 STATUS:
     *     Stabilno
     *
     * 📅 ZGODOVINA:
     *     - v114: uskladitev s Header Standard v114
     *     - v113: dodane oznake in jezik
     *     - v112: prva implementacija
     *
     * 👤 AVTOR:
     *     AstraMentalica Mojster
     *
     * 🌐 JEZIK:
     *     sl
     *
     * 🏷️ OZNAKE:
     *     kernel, jedro, pravice, rbac
     * ============================================================
     */
    declare(strict_types=1);

    defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

```

 ## Za datoteke ki so del jedra (N3) — dodaj zaklenjenost:

```php
/**
 * DATOTEKA: 02_varnost.php
 * NAMEN:    Varnostne funkcije: hash, CSRF, sanitizacija, rate limit
 * NIVO:     3
 * ODVISNO:  01_napake.php
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 * ⚠ JEDRO: Ne spreminjaj brez razloga. Zapiši spremembo spodaj.
 */
```

## RAZŠIRJENI ELEMENTI

| Element | Kdaj |
|----------|----------|
| 🔧 JAVNE FUNKCIJE | Če datoteka vsebuje javne funkcije ali metode |
| 📡 ODVISNOSTI | Če uporablja druge komponente |
| 🤝 SOODVISNOSTI | Če sodeluje z drugimi datotekami istega sklopa |
| ⚡ UPORABA | Če namen uporabe ni samoumeven |
| ✅ DOVOLJENO | Samo GLOBALNO in razvojne module |
| 📅 ZGODOVINA | Priporočljivo za vse datoteke |

## ARHITEKTURNI NIVOJI

N1 - vstop (SISTEM/api.php)
N2 - business logika in tehnični izhod (SISTEM/storitve_svetov/, SISTEM/kanali/)
N3 - sistemska mehanika (SISTEM/kernel/, SISTEM/kernel/jedro/)

## SISTEM N3

Jedrne sistemske komponente.

Primeri:

- SISTEM/kernel/jedro/
- SISTEM/kernel/kontrakti/

Pravila:

- brez HTML
- brez renderiranja
- brez poslovne logike modulov

## STORITEV N2

Sistemske storitve.

Primeri:

- SISTEM/storitve_svetov/

Pravila:

- uporablja kernel
- izvaja sistemske procese
- ne vsebuje prikaza

## ADAPTER

Povezovalni sloj sistema.

Primeri:

- ADAPTER/izhod_kanali/
- ADAPTER/odzivi/

Pravila:

- brez business logike
- pretvorba vhodov in izhodov
- komunikacija med svetovi

## MODUL

Samostojna funkcionalna enota.

Primeri:

- MODULI/ORAKLEUM/
- MODULI/CRM/
- MODULI/ANALITIKA/

Pravila:

- komunikacija preko Modul_Bridge -> adapterja
- brez direktnega klica GLOBALNO/
- brez direktnega klica drugih modulov
- vračanje RESPONSE CONTRACT

## GLOBALNO

Pasivni prikazni sloj.

Primeri:

- GLOBALNO/render/

Pravila:

- brez poslovne logike
- brez dostopa do podatkovnih virov
- samo prikaz

## FRONTEND

Odjemalska logika.

Primeri:

- GLOBALNO/frontend/

Pravila:

- UI logika
- runtime funkcije
- komunikacija preko API

## ZAKLENJEN VRSTNI RED POLJ

1. POT
2. 📅 VERZIJA
3. 🏛️ NIVO
4. 📰 NAMEN
5. 🔧 JAVNE FUNKCIJE
6. 📡 ODVISNOSTI
7. 🤝 SOODVISNOSTI
8. ⚡ UPORABA
9. 🚫 PREPOVEDI
10. ✅ DOVOLJENO
11. 📌 STATUS
12. 📅 ZGODOVINA
13. 👤 AVTOR
14. 🌐 JEZIK
15. 🏷️ OZNAKE

## OBVEZNOST PO TIPU DATOTEKE

| Tip datoteke | Obvezni elementi |
|--------------|------------------|
| SISTEM/kernel/jedro/ | POT, VERZIJA, NIVO, NAMEN, FUNKCIJE, ODVISNOSTI, PREPOVEDI, STATUS, AVTOR, JEZIK, OZNAKE |
| SISTEM/storitve_svetov/ | POT, VERZIJA, NIVO, NAMEN, FUNKCIJE, ODVISNOSTI, PREPOVEDI, STATUS, AVTOR, JEZIK, OZNAKE |
| ADAPTER/kanali/ | POT, VERZIJA, NIVO, NAMEN, FUNKCIJE, ODVISNOSTI, PREPOVEDI, STATUS, AVTOR, JEZIK, OZNAKE |
| MODULI/.../ | POT, VERZIJA, NIVO, NAMEN, FUNKCIJE, ODVISNOSTI, SOODVISNOSTI, PREPOVEDI, STATUS, AVTOR, JEZIK, OZNAKE |
| GLOBALNO/render/ | POT, VERZIJA, NIVO, NAMEN, SOODVISNOSTI, PREPOVEDI, DOVOLJENO, STATUS, AVTOR, JEZIK, OZNAKE |
| GLOBALNO/frontend/ | POT, VERZIJA, NIVO, NAMEN, FUNKCIJE, ODVISNOSTI, PREPOVEDI, STATUS, AVTOR, JEZIK, OZNAKE |


## OBVEZNA PHP ZAŠČITA

Vsaka PHP datoteka mora neposredno za headerjem vsebovati:

    declare(strict_types=1);

    defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

## PRAVILO V ENI STAVKI

Vsaka PHP ali JavaScript datoteka v sistemu AstraMentalica mora vsebovati standardizirano glavo, ki določa njen arhitekturni nivo, namen, odvisnosti, omejitve in sledljivost ter s tem predstavlja preverljivo arhitekturno pogodbo sistema.

---

## 4. KOMENTARJI

### Kdaj komentirati
Komentiraj **zakaj**, ne **kaj**. Kar dela koda je vidno — zakaj je manj očitno.

```php
// ✅ dober komentar
// Omejimo na 5 poskusov ker po tem blokira IP (varnostna politika)
if ($število_poskusov > 5) {
    blokirajIP($ip);
}

// ❌ slab komentar (pove samo kaj koda počne — to je že vidno)
// Preverimo če je število poskusov večje od 5
if ($število_poskusov > 5) {
    blokirajIP($ip);
}
```

### Blok komentarji za funkcije
```php
/**
 * Preveri ali ima uporabnik pravico do akcije.
 *
 * @param int    $uporabnikId  ID uporabnika
 * @param string $akcija       Ime akcije (npr. 'uredi_profil')
 * @return bool  true = ima pravico, false = nima
 */
function ima_pravico(int $uporabnikId, string $akcija): bool {
    // ...
}
```

### Sekcije znotraj datoteke
```php
// ============================
// INICIALIZACIJA
// ============================

// ============================
// VALIDACIJA
// ============================

// ============================
// SHRAMBA
// ============================
```

### Začasna koda (TODO)
```php
// TODO: Dodati cache ko bo Redis na voljo — [2026-01-15]
// FIXME: Popraviti sanitizacijo posebnih znakov — [2026-02-01]
// HACK: Začasna rešitev — zamenjati z lastnim razredom
```

---

## 5. NAPAKE IN IZJEME

### Osnovno pravilo
```
Nikoli ne pustimo napake da "pade" tiho.
Vsaka napaka: ujeta → zabeležena → sporoči smiselno.
```

### Struktura try/catch
```php
try {
    $rezultat = shraniPodatke($podatki);
} catch (InvalidArgumentException $e) {
    // Pričakovana napaka — slab vnos od uporabnika
    napaka_zabelezi('OPOZORILO', $e->getMessage(), __FILE__, __LINE__);
    napaka_vrni('Neveljavni podatki: ' . $e->getMessage());
} catch (Exception $e) {
    // Nepričakovana napaka — sistemska težava
    napaka_zabelezi('NAPAKA', $e->getMessage(), __FILE__, __LINE__);
    napaka_vrni('Sistemska napaka. Poskusite znova.');
}
```

### Nikoli
```php
// ❌ nikoli
die('Napaka!');
exit(1);

// ❌ nikoli — pokažemo stack trace uporabniku
throw new Exception($e->getMessage());  // z originalnim stack trace

// ✅ pravilno
throw new Exception('Napaka pri shranjevanju profila.');
```

---

## 6. VARNOST — OBVEZNA PRAVILA

### Vhodni podatki
```php
// VSE kar pride od zunaj — sanitiziraj VEDNO, v N1 (prvi nivo -> SISTEM/), takoj
// Nikoli ne zaupaj $_GET, $_POST, $_COOKIE direktno

// ✅ pravilno (v api.php N1)
$ime = sanitizirajBesedilo($_POST['ime'] ?? '');
$id  = filtrirajInt($_GET['id'] ?? 0);

// ❌ napačno (direktna uporaba)
$ime = $_POST['ime'];
$id  = $_GET['id'];
```

### SQL (ko delaš direktno)
```php
// ✅ vedno prepared statements
$stavek = $baza->prepare('SELECT * FROM uporabniki WHERE id = :id');
$stavek->execute([':id' => $id]);

// ❌ nikoli konkatenacija
$poizvedba = "SELECT * FROM uporabniki WHERE id = " . $id;
```

### Seje
```php
// Seje upravljamo samo čez N3 (tretji nivo -> SISTEM/kernel/jedro/) /seja.php
// Nikoli session_start() direktno v logiki ali frontends
zaženiSejo();     // ✅ klic funkcije iz jedra
session_start();  // ❌ direktno
```

---

## 7. DOLŽINE IN MEJE

| Enota | Meja |
|---|---|
| Vrstice na datoteko | max 300 |
| Vrstice na funkcijo | max 40 |
| Parametri funkcije | max 4 (potem array) |
| Gnezdenje (indentacija) | max 4 nivoji |
| Dolžina vrstice | max 120 znakov |

Ko preseže mejo — razdeli.

---

## 8. INDENTACIJA IN FORMATIRANJE

- Indentacija: **4 presledki** (ne tab)
- Oklepaji na **isti vrstici** za funkcije/pogoje:
```php
// ✅
function geslo_preveri(string $geslo): bool {
    if (strlen($geslo) < 8) {
        return false;
    }
    return true;
}

// ❌
function geslo_preveri(string $geslo): bool
{
    if (strlen($geslo) < 8)
    {
        return false;
    }
}
```

- Presledek **pred in po** operatorjih:
```php
$vsota = $a + $b;          // ✅
$vsota=$a+$b;              // ❌
```

- Prazen prostor med logičnimi bloki:
```php
// ✅
$id = (int) $_GET['id'];

$uporabnik = uporabnik_najdi($id);

if (!$uporabnik) {
    napaka_vrni('Uporabnik ne obstaja.');
}

return $uporabnik;
```

---

## 9. VRAČANJE VREDNOSTI

Funkcije vračajo **dosleden tip**:

```php
// ✅ vedno array (prazen array = brez rezultatov, ne false)
function iskanje(string $iskanje): array {
    if (empty($iskanje)) return [];
    // ...
    return $rezultati;
}

// ✅ bool za operacije
function shrani(array $podatki): bool {
    // ...
    return true; // ali false ob napaki
}

// ✅ null samo ko ima semantičen pomen (ni najdeno)
function uporabnik_najdi(int $id): ?array {
    // ...
    return null; // uporabnik ne obstaja
}
```

---

## 10. PRIMER — CELOTNA DATOTEKA

```php
<?php
/**
 * DATOTEKA: prijava.php
 * NAMEN:    Logika prijave uporabnika — preveri credentials, zažene sejo
 * NIVO:     2
 * ODVISNO:  jedro/02_varnost.php, jedro/03_seja.php, baze/shramba.php
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 */

// ============================
// PREVERJANJE VNOSA
// ============================

/**
 * Preveri in sanitizira prijavne podatke.
 *
 * @param array $vnos  Surovi POST podatki
 * @return array       Čisti podatki ali prazno ob napaki
 */
function prijava_preveri_vnos(array $vnos): array {
    $email = sanitiziraj_email($vnos['email'] ?? '');
    $geslo = sanitiziraj_besedilo($vnos['geslo'] ?? '');

    if (empty($email) || empty($geslo)) {
        return [];
    }

    return ['email' => $email, 'geslo' => $geslo];
}

// ============================
// PRIJAVA
// ============================

/**
 * Izvede prijavo uporabnika.
 *
 * @param array $podatki  Čisti prijavni podatki
 * @return array          Rezultat: ['uspeh' => bool, 'sporocilo' => string]
 */
function uporabnik_prijavi(array $podatki): array {
    $podatki = prijava_preveri_vnos($podatki);

    if (empty($podatki)) {
        return ['uspeh' => false, 'sporocilo' => 'Manjkajoči podatki.'];
    }

    try {
        $uporabnik = uporabnik_najdi_po_emailu($podatki['email']);

        if (!$uporabnik || !varnost_preveri_hash($podatki['geslo'], $uporabnik['geslo_hash'])) {
            // Ne povemo ali je napaka email ali geslo (varnostno)
            return ['uspeh' => false, 'sporocilo' => 'Napačni prijavni podatki.'];
        }

        seja_zacni();
        seja_nastavi_uporabnika($uporabnik['id']);

        return ['uspeh' => true, 'sporocilo' => 'Prijava uspešna.'];

    } catch (Exception $e) {
        napaka_zabelezi('NAPAKA', $e->getMessage(), __FILE__, __LINE__);
        return ['uspeh' => false, 'sporocilo' => 'Sistemska napaka.'];
    }
}
```

---

*STANDARDI.md — verzija 1.0*


================================================================================
RAZVOJNI STANDARD – Kako pišemo kodo (dodatek)
================================================================================

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

================================================================================
MODULI – Kako dodamo nov modul
================================================================================

# MODULI — KAKO DODAŠ NOVEGA
> Preberi najprej: USTAVA.md, ARHITEKTURA.md, STANDARDI.md
> Ta dokument opisuje **postopek dodajanja novega modula** in kaj vsak modul mora imeti.

---

## 1. KAJ JE MODUL

Modul je **svet znanja** — zaključena enota ki dela eno stvar.

Primeri:
- `Codex` — enciklopedija
- `Vip` — premium vsebina
- `Glasba` — vtičnik za glasbo

Vsak modul je v svoji mapi in:
- **Ne ve** za druge module
- **Ne kliče** druge module direktno
- Komunicira z drugimi **samo čez SISTEM**

---

## 2. DVATIPA MODULA

### Tip A: Integriran (privzeto)
- Vrne podatke SISTEMU, SISTEM posreduje naprej
- Nima lastnega `index.php`
- GLOBALNO ali druga stran renderira prikaz

```
index.php → SISTEM/api.php → moduli_logika → Modul/modul.php → vrne array
```

### Tip B: Samostojen
- Ima lastni `index.php` in lastni render
- Primerni za: admin orodja, kompleksne interaktivne svetove
- Označi: `"samostojen": true` v manifest.json

```
/modul-url/ → MODULI/ImeModula/index.php → renderira sam
                     ↓ (če rabi podatke od SISTEMA)
              SISTEM/api.php → vrne podatke → modul renderira
```

---

## 3. STRUKTURA MODULA

```
MODULI/{kategorija}/{ImeModula}/
├── modul.php              # glavna logika (obvezno)
├── index.php              # vstopna stran (samo če tip B — samostojen)
├── podatki/
│   ├── manifest.json      # konfiguracija (obvezno)
│   ├── pravila.md         # AI navodila za ta modul (priporočeno)
│   ├── vsebina.md         # vsebina modula (če jo ima)
│   └── meta.json          # metapodatki (avtorji, verzija vsebine)
└── .baza/                 # lastna baza (samo če modul rabi trajne podatke)
    └── sqlite.db
```

## Kategorije (trenutno še ni v uporabi):
```
osnovni    # standardni moduli
premium     # plačljivi moduli
vticniki    # vtičniki (glasba, efekti...)
merilni
raziskovalni... 
```

---

## 4. manifest.json — KANONIČNI FORMAT (glej TEMELJ.md za polno specifikacijo)

```json
{
  "modul": {
    "id": "codex",
    "ime": "Codex",
    "tip": "prikazovalnik",
    "nivo": 2,
    "verzija": "1.0.0",
    "aktiviran": true,
    "vstopna": "modul.php",
    "opis": "Enciklopedija sveta — razlage pojmov, zakonov, entitet"
  },
  "dostop": {
    "minimalna_vloga": "S0",
    "plan": "osnova"
  },
  "vhod": {
    "potrebuje": [],
    "opcijsko": ["poizvedba"],
    "vir": "uporabnik"
  },
  "izhod": {
    "format": "json",
    "pise_v": ["modul_codex"]
  },
  "odvisnosti": {
    "bere_iz": []
  },
  "cache": {
    "omogocen": true,
    "ttl": 3600
  },
  "ui": {
    "ima_prikaz": true,
    "ikona": "📖",
    "barva": "#a78bfa"
  },
  "izvajanje": {
    "tip": "ui",
    "cron": false,
    "api_only": false
  },
  "log": {
    "omogocen": true,
    "nivo": "info"
  }
}
```

Možne vrednosti:
- `"dostop.minimalna_vloga"`: `"gost"` | `"S0"` ... `"S5"` | `"admin"`
- `"modul.tip"`: `"zbiralec"` | `"orakelj"` | `"transformator"` | `"prikazovalnik"` | `"cron"`
- `"izvajanje.tip"`: `"ui"` | `"cron"` | `"api_only"`
- `"modul.aktiviran"`: `true` | `false` (samostojnost modula se določa z `izvajanje.tip`, ne s posebnim poljem)

> **Opomba o uskladitvi:** ta format je avtoritativen za vse module. Če najdeš starejši primer
> z poljima `"različica"` ali `"pravice": "javno"` — to je zastarela oblika, uporabi format zgoraj.

---

## 5. modul.php — OBVEZNA STRUKTURA

```php
<?php
/**
 * DATOTEKA: modul.php
 * NAMEN:    Glavna logika modula Codex — iskanje in vračanje vnosov
 * NIVO:     2 (modulna logika)
 * ODVISNO:  SISTEM/sistem/baze/shramba.php
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 */

// ============================
// VSTOPNA TOČKA MODULA
// ============================

/**
 * Glavna funkcija modula — kliče jo SISTEM.
 * Vedno sprejme array akcij in vrne array rezultatov.
 *
 * @param string $akcija   Kaj modul mora narediti
 * @param array  $podatki  Parametri za akcijo
 * @return array           Rezultat (vedno array, nikoli HTML)
 */
function modul_codex_akcija(string $akcija, array $podatki = []): array {
    return match($akcija) {
        'iskanje'  => codex_iskanje($podatki),
        'vnos'     => codex_vrni_vnos($podatki),
        'seznam'   => codex_vrni_seznam($podatki),
        default    => ['napaka' => 'Neznana akcija: ' . $akcija]
    };
}

// ============================
// AKCIJE
// ============================

function codex_iskanje(array $podatki): array {
    $poizvedba = sanitiziraj_besedilo($podatki['poizvedba'] ?? '');

    if (empty($poizvedba)) {
        return ['rezultati' => [], 'skupaj' => 0];
    }

    // ... logika iskanja
    return ['rezultati' => $rezultati, 'skupaj' => count($rezultati)];
}

function codex_vrni_vnos(array $podatki): array {
    $id = filtriraj_int($podatki['id'] ?? 0);

    if ($id <= 0) {
        return ['napaka' => 'Neveljaven ID'];
    }

    // ... poišči vnos
    return $vnos ?? ['napaka' => 'Vnos ne obstaja'];
}

function codex_vrni_seznam(array $podatki): array {
    $stran = filtriraj_int($podatki['stran'] ?? 1);
    $velikost = min(filtriraj_int($podatki['velikost'] ?? 20), 100);

    // ... vrni paginiran seznam
    return [
        'vnosi'  => $vnosi,
        'stran'  => $stran,
        'skupaj' => $skupajVnosov
    ];
}
```

---

## 6. pravila.md — AI NAVODILA

Vsak modul ima datoteko `pravila.md` — navodila za AI asistenta ki dela na tem modulu.

```markdown
# Pravila za modul Codex

## Kaj ta modul dela
Enciklopedija — shranjuje in vrača razlage pojmov, zakonov, entitet.

## Podatkovni model
Vsak vnos ima: id, naslov, vsebina (Markdown), kategorija, datum

## Kaj sme AI delati
- Dodajati nove vnose v vsebina.md
- Posodabljati obstoječe vnose
- Dodajati nove akcije v modul.php

## Kaj AI ne sme
- Spremeniti strukture baze brez odobritve
- Dodajati odvisnosti od drugih modulov
- Pisati HTML v modul.php

## Posebnosti
- Vsebina je v Markdownu, ne HTML
- Iskanje je full-text, ne LIKE
```

---

## 7. POSTOPEK — DODAJANJE NOVEGA MODULA

### Korak 1: Ustvari mapo
```
MODULI/{kategorija}/{ImeModula}/
```

### Korak 2: Ustvari manifest.json
Zapolni vsa obvezna polja (glej razdelek 4).

### Korak 3: Ustvari modul.php
Sledite strukturi iz razdelka 5.
Minimalno: funkcija `modul{Ime}Akcija(string $akcija, array $podatki): array`

### Korak 4: Registracija v SISTEM
Dodaj v `SISTEM/generirano/moduli.php`:
```php
// To datoteko generira sistem — ne urejaj ročno
// Zaženi: SISTEM/orodja/generiraj_module.php
```

> **Opomba:** Generiranje moduli.php je avtomatsko. Zaženi `orodja/generiraj_module.php` po dodajanju modula.

### Korak 5: (Neobvezno) Ustvari bazo
Če modul rabi trajne podatke:
```
MODULI/{kategorija}/{ImeModula}/.baza/sqlite.db
```
Shemo definiraj v datoteki `.baza/shema.sql`.

### Korak 6: (Neobvezno) Ustvari index.php
Samo za tip B (samostojni moduli).

---

## 8. KAKO SISTEM POKLIČE MODUL

```php
// V moduli_logika.php (N2)
$rezultat = modul_codex_akcija('iskanje', ['poizvedba' => 'filozofija']);

// Sistem ne ve kako modul dela — samo pokliče in dobi array nazaj
```

Poimenovanje vstopne funkcije:
```
modul_{ime_modula}_akcija()

primeri:
modul_codex_akcija()
modul_glasba_akcija()    // <- za Glasba
modul_vip_akcija()
```

---

## 9. CHECKLIST — PRED AKTIVACIJO MODULA

```
[ ] manifest.json — vsa polja izpolnjena
[ ] modul.php — ima vstopno funkcijo z ustreznim imenom
[ ] modul.php — vrača vedno array, nikoli HTML
[ ] modul.php — ima glavo z DATOTEKA/NAMEN/NIVO/VERZIJA
[ ] pravila.md — napisana (vsaj kratko)
[ ] moduli.php — regeneriran (zaženi generator)
[ ] Baza — shema.sql obstaja (če ima_bazo: true)
[ ] Test — osnovna akcija vrne pričakovan array
```

---

*MODULI.md — verzija 1.0*


================================================================================
MODUL_BRIDGE – Orkestrator za razvoj modulov
================================================================================

================================================================================
MODULI/Modul_Bridge/ – ORKESTRATOR ZA RAZVOJ MODULOV
================================================================================

📌 NAMEN:
Modul_Bridge omogoča razvoj modulov BREZ celotnega ASTRAMENTALICA sistema.
- Če sistem obstaja → uporabi sistemske funkcije (jedro/sistemske_funkcije.php)
- Če sistem NE obstaja → uporabi mini sistem (embed/mini_sistem.php)
- Omogoča testiranje modulov z različnimi vlogami
- Generira nove module in stebelne datoteke
- Pakira module za prodajo


================================================================================
📁 CELOTNA STRUKTURA
================================================================================

MODULI/Modul_Bridge/
│
├── 📋 index.php                         # Glavni orkestrator (vstopna točka)
│
├── 🎯 orkestrator/                      # UPRAVLJANJE MODULOV
│   ├── upravljalec.php                  # Seznam, aktivacija, deaktivacija
│   ├── testnik.php                      # Testiranje modulov
│   └── pakirnik.php                     # Pakiranje za prodajo (ZIP)
│
├── 🔌 jedro/                            # POVEZAVA NA PRAVI SISTEM
│   ├── sistem_preveri.php               # Preveri ali sistem obstaja
│   └── sistemske_funkcije.php           # Klici v pravi sistem (če obstaja)
│
├── 📦 embed/                            # MINI SISTEM (samostojen – brez ASTRAMENTALICE)
│   ├── mini_sistem.php                  # Jedro mini sistema (bootstrap)
│   ├── mini_konstante.php               # Mini konstante (poti, vloge)
│   ├── mini_vloge.php                   # Mini RBAC (gost/uporabnik/admin)
│   ├── mini_seja.php                    # Mini session (PHP $_SESSION)
│   ├── mini_cache.php                   # Mini cache (v seji, ne v PODATKI)
│   ├── mini_baza.php                    # Demo baza (JSON v seji)
│   └── mini_izhod.php                   # HTML render (glava, noga, postavitev)
│
├── 🏭 generator/                        # GENERIRANJE NOVIH MODULOV
│   ├── generiraj_modul.php              # Ustvari nov modul (mapa + osnovne datoteke)
│   └── generiraj_stebelno.php           # Stebelna datoteka (uporabi embed – samostojen modul)
│
├── 🧪 demo/                             # TESTIRANJE IN DEMONSTRACIJA
│   └── vloge_demo.php                   # Testiranje vlog (preklop in prikaz dostopa)
│
└── 📦 stebelne/                         # GENERIRANI STEBELNI MODULI
    └── (sem se shranijo stebelni moduli – vsak v svoji mapi)


================================================================================
📋 DATOTEKA: index.php (Glavni orkestrator)
================================================================================

<?php
/**
 * ============================================================
 * MODUL BRIDGE – Orkestrator
 * POT: MODULI/Modul_Bridge/index.php
 * ============================================================
 * v111 (27.5.2026)
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Centralna točka za upravljanje, testiranje in generiranje modulov.
 *     Omogoča delo z moduli tako znotraj kot zunaj ASTRAMENTALICA sistema.
 * 
 * 🔧 FUNKCIJE:
 *     - Pregled vseh modulov
 *     - Aktivacija/deaktivacija modulov
 *     - Testiranje modulov z različnimi vlogami
 *     - Generator novih modulov
 *     - Pakiranje modulov za prodajo
 * 
 * ============================================================
 */

// Najprej preveri, ali smo znotraj ASTRAMENTALICA sistema
require_once __DIR__ . '/jedro/sistem_preveri.php';

// Če sistem obstaja, uporabi sistemske funkcije
// Če sistem NE obstaja, uporabi mini sistem
if (SISTEM_OBSTAJA) {
    require_once __DIR__ . '/jedro/sistemske_funkcije.php';
} else {
    require_once __DIR__ . '/embed/mini_sistem.php';
}

// Inicializacija (seja, vloga, cache)
bridge_inicijalizacija();

// Preusmeritev glede na akcijo
$akcija = $_GET['akcija'] ?? 'pregled';

switch ($akcija) {
    case 'pregled':
        bridge_prikazi_pregled();
        break;
    case 'testnik':
        bridge_prikazi_testnik();
        break;
    case 'generiraj':
        bridge_prikazi_generator();
        break;
    case 'pakiraj':
        bridge_pakiraj_modul();
        break;
    default:
        bridge_prikazi_pregled();
        break;
}


================================================================================
📋 DATOTEKA: jedro/sistem_preveri.php
================================================================================

<?php
/**
 * ============================================================
 * JEDRO: sistem_preveri.php
 * POT: MODULI/Modul_Bridge/jedro/sistem_preveri.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Preveri, ali ASTRAMENTALICA sistem obstaja.
 *     Uporabi se NAJPREJ, preden se karkoli drugega naloži.
 * 
 * 🔧 VRNE:
 *     define('SISTEM_OBSTAJA', true/false)
 * 
 * ============================================================
 */

// Poskusi najti pot.php (SIDRO ASTRAMENTALICE)
$poti = [
    __DIR__ . '/../../../../pot.php',           # Standard: MODULI/Modul_Bridge/jedro/ → root
    __DIR__ . '/../../../../../pot.php',        # En nivo globlje
    $_SERVER['DOCUMENT_ROOT'] . '/pot.php',     # Koren domene
];

$SISTEM_OBSTAJA = false;

foreach ($poti as $pot) {
    if (file_exists($pot)) {
        require_once $pot;
        $SISTEM_OBSTAJA = true;
        break;
    }
}

define('SISTEM_OBSTAJA', $SISTEM_OBSTAJA);


================================================================================
📋 DATOTEKA: jedro/sistemske_funkcije.php
================================================================================

<?php
/**
 * ============================================================
 * JEDRO: sistemske_funkcije.php
 * POT: MODULI/Modul_Bridge/jedro/sistemske_funkcije.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Klici v pravi ASTRAMENTALICA sistem (če obstaja).
 *     Bridge uporablja sistemske funkcije namesto mini sistema.
 * 
 * 🔧 FUNKCIJE:
 *     - bridge_inicijalizacija() – zagon seje, preverjanje vloge
 *     - bridge_prikazi_pregled() – prikaz modulov preko SISTEM/storitve_svetov/
 *     - bridge_prikazi_testnik() – testiranje preko sistema
 *     - bridge_prikazi_generator() – generator (sistemski)
 *     - bridge_pakiraj_modul() – pakiranje preko sistema
 * 
 * ============================================================
 */

function bridge_inicijalizacija(): void {
    // Uporabi pravo ASTRAMENTALICA sejo
    if (function_exists('seja_zacni')) {
        seja_zacni();
    }
}

function bridge_prikazi_pregled(): void {
    // Uporabi SISTEM/storitve_svetov/moduli/
    require_once POT_STORITVE . '/moduli/moduli_pregled.php';
    $moduli = moduli_pridobi_vse();
    include __DIR__ . '/../prikaz/pregled.php';
}

// ... ostale funkcije kličejo sistemske ekvivalente


================================================================================
📋 DATOTEKA: embed/mini_sistem.php
================================================================================

<?php
/**
 * ============================================================
 * EMBED: mini_sistem.php
 * POT: MODULI/Modul_Bridge/embed/mini_sistem.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Jedro mini sistema – uporabi se, ko ASTRAMENTALICA sistem NE OBSTAJA.
 *     Omogoča popolnoma samostojno delovanje Bridge-a.
 * 
 * 🔧 ODVISNOSTI:
 *     - mini_konstante.php
 *     - mini_vloge.php
 *     - mini_seja.php
 *     - mini_cache.php
 *     - mini_baza.php
 *     - mini_izhod.php
 * 
 * ============================================================
 */

require_once __DIR__ . '/mini_konstante.php';
require_once __DIR__ . '/mini_vloge.php';
require_once __DIR__ . '/mini_seja.php';
require_once __DIR__ . '/mini_cache.php';
require_once __DIR__ . '/mini_baza.php';
require_once __DIR__ . '/mini_izhod.php';

// Inicializacija mini sistema
function mini_inicijalizacija(): void {
    mini_seja_zacni();
    
    // Privzeta vloga, če ni nastavljena
    if (!mini_je_prijavljen()) {
        mini_prijavi_gosta();
    }
}

// Bridge funkcije za mini sistem
function bridge_inicijalizacija(): void {
    mini_inicijalizacija();
}

function bridge_prikazi_pregled(): void {
    $moduli = mini_moduli_pridobi_vse();
    mini_izhod_glava('Pregled modulov');
    include __DIR__ . '/../prikaz/pregled.php';
    mini_izhod_noga();
}

// ... ostale bridge funkcije za mini sistem


================================================================================
📋 DATOTEKA: embed/mini_konstante.php
================================================================================

<?php
/**
 * ============================================================
 * EMBED: mini_konstante.php
 * POT: MODULI/Modul_Bridge/embed/mini_konstante.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Mini konstante za samostojno delovanje Bridge-a.
 * 
 * ============================================================
 */

define('MINI_ROOT', __DIR__ . '/../../');
define('MINI_MODULI', MINI_ROOT . '/');
define('MINI_BRIDGE', __DIR__ . '/../');

// RBAC vloge (ujemanje s pravim sistemom)
define('MINI_VLOGA_GOST', 0);
define('MINI_VLOGA_UPORABNIK', 10);
define('MINI_VLOGA_ADMIN', 60);

// Cache TTL
define('MINI_CACHE_TTL', 3600);


================================================================================
📋 DATOTEKA: embed/mini_vloge.php
================================================================================

<?php
/**
 * ============================================================
 * EMBED: mini_vloge.php
 * POT: MODULI/Modul_Bridge/embed/mini_vloge.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Mini RBAC sistem za samostojno delovanje.
 * 
 * 🔧 FUNKCIJE:
 *     - mini_preveri_vlogo($zahtevana) → bool
 *     - mini_dodeli_vlogo($vloga) → void
 *     - mini_pridobi_uporabnika() → array
 * 
 * ============================================================
 */

function mini_preveri_vlogo(int $zahtevana): bool {
    $uporabnik = mini_pridobi_uporabnika();
    return $uporabnik['vloga'] >= $zahtevana;
}

function mini_dodeli_vlogo(int $vloga): void {
    $_SESSION['mini_uporabnik']['vloga'] = $vloga;
}

function mini_pridobi_uporabnika(): array {
    return $_SESSION['mini_uporabnik'] ?? [
        'id' => 0,
        'ime' => 'Gost',
        'vloga' => MINI_VLOGA_GOST
    ];
}

function mini_prijavi_gosta(): void {
    $_SESSION['mini_uporabnik'] = [
        'id' => 0,
        'ime' => 'Gost',
        'vloga' => MINI_VLOGA_GOST
    ];
}


================================================================================
📋 DATOTEKA: embed/mini_seja.php
================================================================================

<?php
/**
 * ============================================================
 * EMBED: mini_seja.php
 * POT: MODULI/Modul_Bridge/embed/mini_seja.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Mini session management za samostojno delovanje.
 * 
 * ============================================================
 */

function mini_seja_zacni(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function mini_seja_unici(): void {
    session_destroy();
}

function mini_je_prijavljen(): bool {
    return isset($_SESSION['mini_uporabnik']) && $_SESSION['mini_uporabnik']['id'] > 0;
}


================================================================================
📋 DATOTEKA: embed/mini_cache.php
================================================================================

<?php
/**
 * ============================================================
 * EMBED: mini_cache.php
 * POT: MODULI/Modul_Bridge/embed/mini_cache.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Mini cache (shranjevanje v seji, ne v PODATKI/).
 * 
 * 🔧 FUNKCIJE:
 *     - mini_cache_shrani($kljuc, $vrednost, $ttl)
 *     - mini_cache_preberi($kljuc)
 *     - mini_cache_brisi($kljuc)
 * 
 * ============================================================
 */

function mini_cache_shrani(string $kljuc, $vrednost, int $ttl = MINI_CACHE_TTL): bool {
    if (!isset($_SESSION['mini_cache'])) {
        $_SESSION['mini_cache'] = [];
    }
    
    $_SESSION['mini_cache'][$kljuc] = [
        'vrednost' => $vrednost,
        'potek' => time() + $ttl
    ];
    
    return true;
}

function mini_cache_preberi(string $kljuc) {
    if (!isset($_SESSION['mini_cache'][$kljuc])) {
        return null;
    }
    
    $item = $_SESSION['mini_cache'][$kljuc];
    if ($item['potek'] < time()) {
        unset($_SESSION['mini_cache'][$kljuc]);
        return null;
    }
    
    return $item['vrednost'];
}

function mini_cache_brisi(string $kljuc): bool {
    if (isset($_SESSION['mini_cache'][$kljuc])) {
        unset($_SESSION['mini_cache'][$kljuc]);
        return true;
    }
    return false;
}


================================================================================
📋 DATOTEKA: embed/mini_baza.php
================================================================================

<?php
/**
 * ============================================================
 * EMBED: mini_baza.php
 * POT: MODULI/Modul_Bridge/embed/mini_baza.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Demo baza (JSON v seji) za samostojno delovanje.
 * 
 * 🔧 FUNKCIJE:
 *     - mini_baza_beri($tabela) → array
 *     - mini_baza_zapisi($tabela, $podatki) → bool
 * 
 * ============================================================
 */

function mini_baza_beri(string $tabela): array {
    if (!isset($_SESSION['mini_baza'][$tabela])) {
        return [];
    }
    return $_SESSION['mini_baza'][$tabela];
}

function mini_baza_zapisi(string $tabela, array $podatki): bool {
    $_SESSION['mini_baza'][$tabela] = $podatki;
    return true;
}

function mini_baza_dodaj(string $tabela, array $vrstica): int {
    if (!isset($_SESSION['mini_baza'][$tabela])) {
        $_SESSION['mini_baza'][$tabela] = [];
    }
    
    $id = count($_SESSION['mini_baza'][$tabela]) + 1;
    $vrstica['id'] = $id;
    $_SESSION['mini_baza'][$tabela][] = $vrstica;
    
    return $id;
}


================================================================================
📋 DATOTEKA: embed/mini_izhod.php
================================================================================

<?php
/**
 * ============================================================
 * EMBED: mini_izhod.php
 * POT: MODULI/Modul_Bridge/embed/mini_izhod.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     HTML render za mini sistem (glava, noga, postavitev).
 * 
 * 🔧 FUNKCIJE:
 *     - mini_izhod_glava($naslov)
 *     - mini_izhod_noga()
 *     - mini_izhod_postavitev($vsebina)
 * 
 * ============================================================
 */

function mini_izhod_glava(string $naslov = 'Modul Bridge'): void {
    ?>
    <!DOCTYPE html>
    <html lang="sl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($naslov) ?> | Modul Bridge</title>
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            body { font-family: system-ui, sans-serif; background: #0a0a1a; color: #d4c5a9; padding: 2rem; }
            .container { max-width: 1200px; margin: 0 auto; }
            h1 { color: #e8c84a; margin-bottom: 1rem; }
            .card { background: rgba(255,255,255,0.05); border-radius: 15px; padding: 1.5rem; margin-bottom: 1.5rem; }
            .btn { display: inline-block; background: #e8c84a; color: #0a0a1a; padding: 0.5rem 1rem; border-radius: 25px; text-decoration: none; margin-right: 0.5rem; }
            .btn-secondary { background: rgba(255,255,255,0.1); color: #d4c5a9; }
            .modul-item { background: rgba(255,255,255,0.03); padding: 1rem; border-radius: 10px; margin-bottom: 0.5rem; }
            .dostop-da { color: #4caf50; }
            .dostop-ne { color: #f44336; }
        </style>
    </head>
    <body>
    <div class="container">
        <h1>🔧 Modul Bridge</h1>
        <div class="card">
            <a href="?akcija=pregled" class="btn">📦 Pregled modulov</a>
            <a href="?akcija=testnik" class="btn">🧪 Testnik vlog</a>
            <a href="?akcija=generiraj" class="btn">🏭 Generiraj modul</a>
        </div>
    <?php
}

function mini_izhod_noga(): void {
    ?>
    </div>
    </body>
    </html>
    <?php
}


================================================================================
📋 DATOTEKA: generator/generiraj_modul.php
================================================================================

<?php
/**
 * ============================================================
 * GENERATOR: generiraj_modul.php
 * POT: MODULI/Modul_Bridge/generator/generiraj_modul.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Ustvari nov modul (mapa + osnovne datoteke).
 * 
 * 🔧 UPORABA:
 *     POST zahteva z parametri: kategorija, ime, opis
 * 
 * ============================================================
 */

function generiraj_modul(string $kategorija, string $ime, string $opis): array {
    $pot_modula = MINI_MODULI . '/' . $kategorija . '/' . $ime . '/';
    
    // Ustvari mapo
    if (!mkdir($pot_modula, 0755, true)) {
        return ['uspeh' => false, 'napaka' => 'Ne morem ustvariti mape'];
    }
    
    // Ustvari manifest.json
    $manifest = [
        'ime' => $ime,
        'oznaka' => strtolower($ime),
        'verzija' => '1.0.0',
        'opis' => $opis,
        'avtor' => 'Modul Bridge',
        'status' => 'razvoj',
        'vloga' => 0,
        'kanali' => ['web'],
        'vstop' => ['web' => 'modul.php']
    ];
    
    file_put_contents($pot_modula . 'manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
    
    // Ustvari modul.php
    $modul_php = '<?php
/**
 * ' . $ime . ' - modul.php
 * Generirano z Modul Bridge
 */

// Vhodna točka modula
$zahteva = json_decode(file_get_contents("php://input"), true) ?? $_GET;

$odziv = [
    "status" => "uspeh",
    "sporocilo" => "' . $ime . ' modul deluje",
    "vsebina" => [],
    "napake" => []
];

header("Content-Type: application/json");
echo json_encode($odziv);
';
    file_put_contents($pot_modula . 'modul.php', $modul_php);
    
    return ['uspeh' => true, 'pot' => $pot_modula];
}


================================================================================
📋 DATOTEKA: generator/generiraj_stebelno.php
================================================================================

<?php
/**
 * ============================================================
 * GENERATOR: generiraj_stebelno.php
 * POT: MODULI/Modul_Bridge/generator/generiraj_stebelno.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Ustvari stebelno datoteko (samostojen modul, ki uporabi embed/mini_sistem.php).
 *     Stebelni modul je popolnoma samostojen – deluje brez ASTRAMENTALICE.
 * 
 * 🔧 UPORABA:
 *     POST zahteva z parametri: ime, opis
 * 
 * ============================================================
 */

function generiraj_stebelno(string $ime, string $opis): array {
    $pot_stebelnega = MINI_BRIDGE . '/stebelne/' . $ime . '/';
    
    // Ustvari mapo
    if (!mkdir($pot_stebelnega, 0755, true)) {
        return ['uspeh' => false, 'napaka' => 'Ne morem ustvariti mape'];
    }
    
    // Ustvari index.php (samostojni zaganjalnik)
    $index = '<?php
/**
 * ' . $ime . ' - Stebelni modul
 * Generirano z Modul Bridge
 * 
 * SAMOSTOJNI MODUL – deluje brez ASTRAMENTALICE
 */

require_once __DIR__ . \'/../../embed/mini_sistem.php\';

mini_inicijalizacija();

$modul = [
    "ime" => "' . $ime . '",
    "opis" => "' . $opis . '",
    "verzija" => "1.0.0"
];

mini_izhod_glava($modul["ime"]);
?>
<div class="card">
    <h2><?= htmlspecialchars($modul["ime"]) ?></h2>
    <p><?= htmlspecialchars($modul["opis"]) ?></p>
    <p><strong>Status:</strong> Stebelni modul – deluje samostojno</p>
</div>
<?php
mini_izhod_noga();
';
    file_put_contents($pot_stebelnega . 'index.php', $index);
    
    return ['uspeh' => true, 'pot' => $pot_stebelnega];
}


================================================================================
📋 DATOTEKA: demo/vloge_demo.php
================================================================================

<?php
/**
 * ============================================================
 * DEMO: vloge_demo.php
 * POT: MODULI/Modul_Bridge/demo/vloge_demo.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Testiranje modulov z različnimi vlogami.
 *     Omogoča preklop med gost/uporabnik/admin in takojšen prikaz dostopa.
 * 
 * 🔧 UPORABA:
 *     ?vloga=0 (gost), ?vloga=10 (uporabnik), ?vloga=60 (admin)
 * 
 * ============================================================
 */

define('BRIDGE_VARNOST', true);
require_once __DIR__ . '/../embed/mini_sistem.php';

// Preklop vloge
if (isset($_GET['vloga'])) {
    $vloga = (int)$_GET['vloga'];
    mini_dodeli_vlogo($vloga);
}

$trenutni = mini_pridobi_uporabnika();

// Seznam modulov
$moduli = glob(__DIR__ . '/../../Modul_*', GLOB_ONLYDIR);

?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Testnik vlog | Modul Bridge</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: system-ui, sans-serif; background: #0a0a1a; color: #d4c5a9; padding: 2rem; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #e8c84a; margin-bottom: 1rem; }
        .card { background: rgba(255,255,255,0.05); border-radius: 15px; padding: 1.5rem; margin-bottom: 1.5rem; }
        .current-vloga { background: rgba(232,200,74,0.2); border-left: 4px solid #e8c84a; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .vloga-buttons { display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap; }
        .vloga-btn { padding: 0.5rem 1rem; border-radius: 25px; background: rgba(255,255,255,0.1); color: #d4c5a9; text-decoration: none; }
        .vloga-btn.active { background: #e8c84a; color: #0a0a1a; }
        .modul-item { background: rgba(255,255,255,0.03); padding: 1rem; border-radius: 10px; margin-bottom: 0.5rem; }
        .dostop-da { color: #4caf50; }
        .dostop-ne { color: #f44336; }
        iframe { width: 100%; height: 500px; border: 1px solid #2a2a4a; border-radius: 10px; margin-top: 1rem; }
    </style>
</head>
<body>
<div class="container">
    <h1>🧪 Testnik vlog</h1>
    
    <div class="current-vloga">
        <strong>🔑 Trenutna vloga:</strong> 
        <?php if ($trenutni['vloga'] >= MINI_VLOGA_ADMIN): ?>
            👑 Administrator (vloga: <?= $trenutni['vloga'] ?>)
        <?php elseif ($trenutni['vloga'] >= MINI_VLOGA_UPORABNIK): ?>
            👤 Uporabnik (vloga: <?= $trenutni['vloga'] ?>)
        <?php else: ?>
            🚪 Gost (vloga: <?= $trenutni['vloga'] ?>)
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h2>🎚️ Preklop vloge</h2>
        <div class="vloga-buttons">
            <a href="?vloga=<?= MINI_VLOGA_GOST ?>" class="vloga-btn <?= $trenutni['vloga'] == MINI_VLOGA_GOST ? 'active' : '' ?>">🚪 Gost</a>
            <a href="?vloga=<?= MINI_VLOGA_UPORABNIK ?>" class="vloga-btn <?= $trenutni['vloga'] == MINI_VLOGA_UPORABNIK ? 'active' : '' ?>">👤 Uporabnik</a>
            <a href="?vloga=<?= MINI_VLOGA_ADMIN ?>" class="vloga-btn <?= $trenutni['vloga'] == MINI_VLOGA_ADMIN ? 'active' : '' ?>">👑 Admin</a>
        </div>
    </div>
    
    <div class="card">
        <h2>📦 Moduli</h2>
        <?php foreach ($moduli as $modul):
            $ime = basename($modul);
            $minVloga = MINI_VLOGA_GOST;
            $modulJson = $modul . '/manifest.json';
            if (file_exists($modulJson)) {
                $json = json_decode(file_get_contents($modulJson), true);
                $minVlogaStr = $json['vloga'] ?? '0';
                $minVloga = (int)$minVlogaStr;
            }
            $dostop = $trenutni['vloga'] >= $minVloga;
        ?>
        <div class="modul-item">
            <strong><?= htmlspecialchars($ime) ?></strong><br>
            Zahtevana vloga: <?= $minVloga >= MINI_VLOGA_ADMIN ? 'Admin' : ($minVloga >= MINI_VLOGA_UPORABNIK ? 'Uporabnik' : 'Gost') ?>
            | Dostop: <?php if ($dostop): ?>
                <span class="dostop-da">✅ Dovoljen</span>
                <a href="<?= $modul ?>/modul.php" target="_blank">🔗 Odpri</a>
            <?php else: ?>
                <span class="dostop-ne">❌ Zavrnjen</span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    
    <a href="../index.php" class="btn" style="display: inline-block; margin-top: 1rem;">← Nazaj na Bridge</a>
</div>
</body>
</html>


================================================================================
📋 PRIKAZ MAPE: stebelne/ (generirani stebelni moduli)
================================================================================

stebelne/
├── (prvi_modul)/
│   └── index.php          # Samostojni zaganjalnik (uporabi embed/mini_sistem.php)
├── (drugi_modul)/
│   └── index.php
└── ...


================================================================================
✅ POVZETEK – KDAJ SE KAJ UPORABI
================================================================================

| Komponenta                    | Kdaj se uporabi                          | Odvisnosti                    |
|-------------------------------|------------------------------------------|-------------------------------|
| jedro/sistem_preveri.php      | Vedno (najprej) – preveri sistem         | Nobenih                       |
| jedro/sistemske_funkcije.php  | Ko sistem OBSTAJA                        | SISTEM/kernel/                |
| embed/mini_sistem.php         | Ko sistema NI (demo, stebelne)           | SAMO mini_* (vse v embed/)    |
| generator/generiraj_modul.php | Ko uporabnik želi nov modul              | mini_sistem ali sistemski     |
| generator/generiraj_stebelno.php| Ko uporabnik želi samostojen modul      | embed/mini_sistem.php         |
| demo/vloge_demo.php           | Ko uporabnik želi testirati dostop       | embed/mini_sistem.php         |

================================================================================
KONEC MODUL BRIDGE STRUKTURE
================================================================================


================================================================================
TEMELJ – Popolna specifikacija (brez runtime)
================================================================================

🌌 ASTRAMENTALICA — POPOLNA SPECIFIKACIJA (BREZ RUNTIME)
================================================================================

🔒 KLJUČNE ODLOČITVE
✔ Vloga v podatkih = INT (obvezno)
✔ RBAC = STRING (ostane)
✔ Pretvorba = vloga_v_int()
✔ Dostop = ma_vlogo()
✔ PODATKI = edini vir resnice
✔ upravljalec_baz.php = edini dostop
✔ ADAPTER = edini izhod (IO)
✔ SISTEM nikoli ne echo-a
✔ MODULI nimajo dostopa do SISTEM ali PODATKI
✔ RUNTIME NE OBSTAJA – IZBRISAN IZ SISTEMA


================================================================================
📁 1. MAPA STRUKTURA (popolna, enotna – BREZ RUNTIME)
================================================================================

root/
├── pot.php                         # SIDRO – vse konstante
├── index.php                       # EDINI javni VSTOP
├── .htaccess                       # zaščita
│
├── ADAPTER/
│   ├── vhod_zasebno/               # sistemski ai, cli, cron ..
│   │   ├── adapter_ai.php
│   │   └── adapter_cli.php
│   │
│   ├── vhod_webhook/               # tuja omrezja
│   │   ├── adapter_facebook.php
│   │   └── adapter_telegram.php
│   │
│   ├── izhod_kanali/               # distribucijski kanali (SAMO PRETVORBA)
│   │   ├── KanalWeb.php            # HTML pretvorba (usmeri v GLOBALNO/render/render.php)
│   │   ├── KanalApi.php            # JSON pretvorba
│   │   ├── KanalFacebook.php
│   │   ├── KanalAi.php
│   │   ├── KanalCli.php
│   │   └── KanalTelegram.php
│   │
│   ├── middleware/
│   │   ├── cors.php
│   │   ├── auth.php
│   │   ├── dnevnik.php
│   │   ├── ip_blacklist.php
│   │   └── omejevalnik.php
│   │
│   ├── odzivi/
│   │   ├── adapter_napake.php
│   │   ├── adapter_odziv.php
│   │   └── adapter_statusi.php
│   │
│   └── adapter.php                 # EDINI vstop/izstop
│
├── SISTEM/
│   ├── api.php                     # N1 vstopna točka (edini vstop v sistem)
│   │
│   ├── storitve_svetov/            # BUSINESS LAYER (N2)
│   │   ├── uporabniki/             # backend uporabniki
│   │   ├── moduli/                 # backend modulov
│   │   ├── globalno/               # backend za prikaz
│   │   └── astra/                  # backend za ASTRA
│   │
│   ├── kanali/                     # TEHNIČNI IZHOD (N2) – IZVEDBA
│   │   ├── priprava.php            # priprava izhoda (contract)
│   │   ├── vrsta.php               # čakalna vrsta (queue)
│   │   └── obdelava.php            # worker
│   │
│   └── kernel/                     # SISTEMSKA MEHANIKA (N3)
│       ├── zaganjalnik.php         # bootstrap sistema
│       ├── env_loader.php          # okolje (.env)
│       ├── nastavitve.php          # globalne nastavitve
│       │
│       ├── jedro/                  # čisto jedro (brez domenske logike)
│       │   ├── 01_upravljalec_svetov.php
│       │   ├── 02_napake.php
│       │   ├── 03_varnost.php
│       │   ├── 04_seja.php
│       │   ├── 05_pravice.php
│       │   ├── 06_cache.php
│       │   ├── 07_dogodki.php
│       │   ├── 08_kavlji.php
│       │   ├── 09_ponudniki.php
│       │   ├── 10_middleware.php
│       │   ├── 11_usmerjevalnik.php
│       │   ├── 12_validacija.php
│       │   ├── 13_api.php
│       │   ├── 14_zagon.php
│       │   └── 15_pogon.php
│       │   # 16_upravljalec_runtime.php – IZBRISAN (runtime ne obstaja)
│       │
│       └── baze/                   # adapterji za baze
│           ├── upravljalec_baz.php # centralizirano branje/pisanje (EDINI DOSTOP)
│           ├── adapter_json.php
│           ├── adapter_mysql.php
│           └── adapter_sqlite.php
│
├── MODULI/                         # izolirani domenski moduli
│   ├── ORAKLEUM/
│   │   ├── Tarot/
│   │   │   ├── modul.php
│   │   │   └── podatki/
│   │   │       ├── manifest.json
│   │   │       └── karte/          # 78 slik kart
│   │   └── OraculumVisionis/
│   ├── NEBO/
│   │   ├── Stelaris/
│   │   ├── Lunaris/
│   │   ├── Jyotir/
│   │   └── Senzorji/
│   ├── ZEMLJA/
│   │   ├── QiVitalis/
│   │   ├── Pranaymica/
│   │   ├── Energetica/
│   │   ├── BotanicaSacra/
│   │   ├── Lapidaria/
│   │   ├── VibraMystica/
│   │   └── Somnaris/
│   ├── SIMBOLI/
│   │   ├── Numyra/
│   │   ├── NumerariumCosmicum/
│   │   ├── AegypticaArcana/
│   │   ├── NordicaMystica/         # rune
│   │   ├── MysticaMesoamericana/   # majevski koledar
│   │   ├── Sephirotica/            # kabala
│   │   ├── Occultum/               # sigili
│   │   └── Devorum/                # miti in bogovi
│   ├── POTI/
│   │   ├── Transmutaria/           # alkimija
│   │   ├── UmbraeCodex/            # delo s senco
│   │   ├── LiberUmbrae/            # knjiga senc
│   │   ├── ViaAnimae/              # pot duše
│   │   ├── Animaris/               # šamanizem
│   │   └── Seraphica/              # angeli
│   ├── SVET/
│   │   ├── CorpusMysticum/         # knjižnica
│   │   ├── Aetheris/               # forum
│   │   ├── Celestara/              # blog
│   │   ├── Mystaia/                # trgovina
│   │   └── CosmicaScientia/        # kvantna mistika
│   └── VIP/
│       └── Synera/                 # S3+ vloga
│
├── GLOBALNO/                       # SAMO frontend (brez business logike)
│   ├── frontend/                   # JS, CSS, interakcije
│   ├── render/                     # SAMO prikaz (brez logike!)
│   │   ├── glava.php
│   │   ├── noga.php
│   │   ├── navigacija.php          # postavitve
│   │   ├── domov.php
│   │   └── 404.html
│   └── vmesnik/                    # dizajn
│       ├── css
│       └── teme
│
├── UPORABNIKI/                     # SAMO frontend (brez business logike)
│   ├── prikaz/uporabnik/
│   │   ├── uporabnik_nastavitve.php
│   │   ├── uporabnik_passport.php
│   │   ├── uporabnik_meditacije.php
│   │   └── uporabnik_dnevnik.php
│   ├── prikaz/skrbnik/
│   │   └── nastavitve.php
│   │
│   ├── prikaz/sistem/
│   │   ├── uporabnik_prijava.php
│   │   ├── uporabnik_registracija.php
│   │   ├── uporabnik_odjava.php
│   │   └── uporabnik_profil.php    # skupni prikaz profila
│   │
│   └── {id}/                       # vsak uporabnik svojo mapo
│       ├── profil.json             # id, vloga, plan, status
│       ├── nastavitve.json         # tema, jezik, lokacija
│       └── PASSPORT/               # 🕊️ SVETI GRAL
│           ├── dnevnik.json
│           ├── modrosti.json
│           ├── odkritja.json
│           ├── pot.json
│           ├── simboli.json
│           ├── sanje.json
│           └── meditacije.json
│
├── PODATKI/                        # centralni rezervoar sistema
│   ├── sef/
│   │   ├── .env_sistem
│   │   ├── .env_api
│   │   └── .env_baza
│   │
│   ├── registri/                   # CENTRALNI REGISTRI
│   │   ├── moduli_register.json    # whitelist vseh modulov
│   │   ├── rbac/
│   │   │   ├── vloge.json          # kaj sme posamezna vloga
│   │   │   └── pravila.json        # splošna pravila sistema
│   │   ├── override/
│   │   │   └── {id}.json           # dovoli / blokiraj
│   │   ├── whitelist/              # cachirani whitelist-i
│   │   │   ├── whitelist_gost.json
│   │   │   ├── whitelist_S0.json
│   │   │   ├── whitelist_S1.json
│   │   │   ├── whitelist_S2.json
│   │   │   ├── whitelist_S3.json
│   │   │   ├── whitelist_S4.json
│   │   │   ├── whitelist_S5.json
│   │   │   └── whitelist_admin.json
│   │   ├── prepovedi.json          # globalne blokade
│   │   └── postavitev.json         # layout / sistemske nastavitve
│   │
│   ├── moduli/                     # podatki modulov
│   │   └── {ime_modula}/
│   │       ├── uporaba.json
│   │       ├── globalno.json
│   │       ├── cache.json
│   │       └── statistika.json
│   │
│   ├── uporabniki/                 # sistemski podatki (identiteta, vloge)
│   │   └── (samo centralno, ne peskovnik)
│   │
│   ├── globalno/inventura/
│   │   └── gradniki.json           # vsi gradniki + RBAC dostop
│   │
│   ├── sistem/
│   │   ├── tmp/
│   │   ├── dnevnik/
│   │   │   ├── sistem.log
│   │   │   ├── api.log
│   │   │   └── instalacija.log
│   │   ├── vrsta/                  # podatki za čakalno vrsto
│   │   │   ├── izhod.json
│   │   │   ├── cron.json
│   │   │   └── interno.json
│   │   ├── seja/
│   │   └── predpomnilnik/
│   │
│   ├── baze/
│   │   ├── mysql/
│   │   ├── sqlite/
│   │   └── json/
│   │
│   └── analitika/
│       ├── statistika/
│       └── meritve/
│
├── VSEBINA/                        # statične MD vsebine
│   ├── javno/
│   ├── faq/
│   ├── branja/
│   └── manifest/
│
└── ASTRA/                          # nadzorni svet – samo S5/admin
    ├── nadzorni_center.php
    └── admin_portal.php


================================================================================
📜 2. pot.php (popoln, enoten)
================================================================================

<?php
// pot.php – SIDRO, edine konstante sistema
defined('SIDRO_AKTIVNO') or define('SIDRO_AKTIVNO', true);

// ============================================================
// 1. OSNOVNE POTI
// ============================================================
define('POT_KOREN', __DIR__);

define('POT_SISTEM',     POT_KOREN . '/SISTEM');
define('POT_MODULI',     POT_KOREN . '/MODULI');
define('POT_GLOBALNO',   POT_KOREN . '/GLOBALNO');
define('POT_UPORABNIKI', POT_KOREN . '/UPORABNIKI');
define('POT_PODATKI',    POT_KOREN . '/PODATKI');
define('POT_VSEBINA',    POT_KOREN . '/VSEBINA');
define('POT_ASTRA',      POT_KOREN . '/ASTRA');

// ============================================================
// 2. PODATKI PODMAPE
// ============================================================
define('PODATKI_ENV',        POT_PODATKI . '/sef');
define('PODATKI_MODULI',     POT_PODATKI . '/moduli');
define('PODATKI_UPORABNIKI', POT_PODATKI . '/uporabniki');
define('PODATKI_INVENTURA',  POT_PODATKI . '/globalno/inventura');
define('PODATKI_ANALITIKA',  POT_PODATKI . '/analitika');
define('PODATKI_STATISTIKA', POT_PODATKI . '/analitika/statistika');
define('PODATKI_MERITVE',    POT_PODATKI . '/analitika/meritve');

// ============================================================
// 3. REGISTRI (centralni registri, RBAC, override, whitelist)
// ============================================================
define('PODATKI_REGISTRI',   POT_PODATKI . '/registri');

// ============================================================
// 4. SISTEMSKE PODMAPE (PODATKI/sistem/)
// ============================================================
define('PODATKI_SISTEM',     POT_PODATKI . '/sistem');
define('PODATKI_LOG',        POT_PODATKI . '/sistem/dnevnik');
define('PODATKI_CACHE',      POT_PODATKI . '/sistem/predpomnilnik');
define('PODATKI_TMP',        POT_PODATKI . '/sistem/tmp');
define('PODATKI_VRSTA',      POT_PODATKI . '/sistem/vrsta');

// ============================================================
// 5. BAZE ADAPTERJI
// ============================================================
define('PODATKI_BAZE',       POT_PODATKI . '/baze');
define('PODATKI_JSON',       POT_PODATKI . '/baze/json');
define('PODATKI_SQLITE',     POT_PODATKI . '/baze/sqlite');
define('PODATKI_MYSQL',      POT_PODATKI . '/baze/mysql');

// ============================================================
// 6. KERNEL POTI
// ============================================================
define('POT_KERNEL',         POT_SISTEM . '/kernel');
define('POT_JEDRO',          POT_KERNEL . '/jedro');
define('POT_BAZE',           POT_KERNEL . '/baze');
define('POT_STORITVE',       POT_SISTEM . '/storitve_svetov');
define('POT_KANALI',         POT_SISTEM . '/kanali');

// ============================================================
// 7. URL KONSTANTE
// ============================================================
$prot = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$mapa = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

define('KOREN_URL',     $prot . '://' . $host . $mapa);
define('GLOBALNO_URL',  KOREN_URL . '/GLOBALNO');
define('MODULI_URL',    KOREN_URL . '/MODULI');
define('ASTRA_URL',     KOREN_URL . '/ASTRA');

// ============================================================
// 8. SISTEMSKE NASTAVITVE
// ============================================================
define('RAZVOJNI_NACIN', true);
define('SISTEM_VERZIJA', '5.0.0');
define('IME_APLIKACIJE', 'AstraMentalica');
define('CASOVNA_CONA',   'Europe/Ljubljana');

// ============================================================
// 9. RBAC VLOGE (integer vrednosti)
// ============================================================
define('VLOGA_GOST',   0);
define('VLOGA_S0',    10);
define('VLOGA_S1',    20);
define('VLOGA_S2',    30);
define('VLOGA_S3',    40);
define('VLOGA_S4',    50);
define('VLOGA_S5',    60);
define('VLOGA_ADMIN', 100);

// ============================================================
// 10. VAROVALKA
// ============================================================
define('SISTEM_VARNOST', true);


================================================================================
👤 3. ENOTNI MODEL UPORABNIKA
================================================================================

OBVEZNO:
{
  "id": 123,
  "vloga": 40,
  "plan": "premium",
  "status": "aktiven"
}

❗ "S3" NE SME obstajati v podatkih – vedno INT!


================================================================================
🔐 4. PRAVICE (05_pravice.php – FINAL)
================================================================================

<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/05_pravice.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3
 *
 * 📰 NAMEN:
 *     Upravljanje uporabniških pravic (RBAC).
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - vloga_v_int(string|int $vloga): int
 *     - trenutna_vloga(): int
 *     - ma_vlogo(string|int $zahtevana): bool
 *     - zahtevaj_vlogo(string|int $vloga): void
 *
 * 📡 ODVISNOSTI:
 *     - POT_JEDRO . '/04_seja.php'
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *     - Brez direktnih poti (uporabi konstante!)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kernel, jedro, pravice, rbac
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function vloga_v_int(string|int $vloga): int
{
    if (is_int($vloga)) {
        return $vloga;
    }

    return match(strtoupper($vloga)) {
        'GOST'  => VLOGA_GOST,
        'S0'    => VLOGA_S0,
        'S1'    => VLOGA_S1,
        'S2'    => VLOGA_S2,
        'S3'    => VLOGA_S3,
        'S4'    => VLOGA_S4,
        'S5'    => VLOGA_S5,
        'ADMIN' => VLOGA_ADMIN,
        default => VLOGA_GOST
    };
}

function trenutna_vloga(): int
{
    return (int)($_SESSION['vloga'] ?? VLOGA_GOST);
}

function ma_vlogo(string|int $zahtevana): bool
{
    return trenutna_vloga() >= vloga_v_int($zahtevana);
}

function zahtevaj_vlogo(string|int $vloga): void
{
    if (!ma_vlogo($vloga)) {
        throw new Exception('Dostop zavrnjen');
    }
}


================================================================================
💾 5. UPRAVLJALEC BAZ (upravljalec_baz.php – EDINI DOSTOP DO PODATKOV)
================================================================================

<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/baze/upravljalec_baz.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3
 *
 * 📰 NAMEN:
 *     Centralizirano branje/pisanje podatkov. EDINI DOSTOP DO PODATKOV.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - baza_pot(string $tip, string $ime, ?int $uporabnik = null): string
 *     - baza_beri(string $tip, string $ime, ?int $uporabnik = null): array
 *     - baza_pisi(string $tip, string $ime, array $data, ?int $uporabnik = null): bool
 *     - baza_obstaja(string $tip, string $ime, ?int $uporabnik = null): bool
 *     - baza_brisi(string $tip, string $ime, ?int $uporabnik = null): bool
 *     - baza_poisci(string $tip, array $kriteriji, ?int $uporabnik = null): array
 *
 * 📡 ODVISNOSTI:
 *     - POT_PODATKI konstante
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kernel, baze, podatki, shramba
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function baza_pot(string $tip, string $ime, ?int $uporabnik = null): string
{
    return match($tip) {
        'sistem' =>
            PODATKI_SISTEM . '/' . $ime . '.json',

        'modul' =>
            PODATKI_MODULI . '/' . $ime . '/globalno.json',

        'uporabnik' =>
            PODATKI_UPORABNIKI . '/uporabnik_' .
            ($uporabnik ?? throw new Exception('Manjka ID uporabnika')) .
            '/' . $ime . '.json',

        'uporabnik_modul' =>
            PODATKI_UPORABNIKI . '/uporabnik_' .
            ($uporabnik ?? throw new Exception('Manjka ID uporabnika')) .
            '/moduli/' . $ime . '.json',

        'register' =>
            PODATKI_REGISTRI . '/' . $ime . '.json',

        default =>
            throw new Exception('Neznan tip podatkov: ' . $tip)
    };
}

function baza_beri(string $tip, string $ime, ?int $uporabnik = null): array
{
    $pot = baza_pot($tip, $ime, $uporabnik);

    if (!file_exists($pot)) {
        return [];
    }

    $vsebina = file_get_contents($pot);
    if ($vsebina === false) {
        return [];
    }

    return json_decode($vsebina, true) ?? [];
}

function baza_pisi(string $tip, string $ime, array $data, ?int $uporabnik = null): bool
{
    $pot = baza_pot($tip, $ime, $uporabnik);

    $mapa = dirname($pot);
    if (!is_dir($mapa)) {
        if (!mkdir($mapa, 0755, true)) {
            return false;
        }
    }

    return file_put_contents($pot, json_encode($data, JSON_PRETTY_PRINT)) !== false;
}

function baza_obstaja(string $tip, string $ime, ?int $uporabnik = null): bool
{
    return file_exists(baza_pot($tip, $ime, $uporabnik));
}

function baza_brisi(string $tip, string $ime, ?int $uporabnik = null): bool
{
    $pot = baza_pot($tip, $ime, $uporabnik);

    if (!file_exists($pot)) {
        return true;
    }

    return unlink($pot);
}

function baza_poisci(string $tip, array $kriteriji, ?int $uporabnik = null): array
{
    $podatki = baza_beri($tip, 'podatki', $uporabnik);

    if (empty($podatki) || !isset($podatki['vnosi'])) {
        return [];
    }

    $rezultati = [];
    foreach ($podatki['vnosi'] as $vnos) {
        $ujemanje = true;
        foreach ($kriteriji as $kljuc => $vrednost) {
            if (($vnos[$kljuc] ?? null) !== $vrednost) {
                $ujemanje = false;
                break;
            }
        }
        if ($ujemanje) {
            $rezultati[] = $vnos;
        }
    }

    return $rezultati;
}


================================================================================
🔁 6. IZHODNI SISTEM
================================================================================

📋 SISTEM/kanali/priprava.php – PRIPRAVA IZHODA

<?php
/**
 * ============================================================
 * POT: SISTEM/kanali/priprava.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2
 *
 * 📰 NAMEN:
 *     Priprava izhoda v standardiziran format.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - izhod_pripravi(array $data): array
 *
 * 📡 ODVISNOSTI:
 *     - Nobenih
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kanali, izhod, priprava
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function izhod_pripravi(array $data): array
{
    return [
        'tip' => $data['tip'] ?? 'generic',
        'kanali' => $data['kanali'] ?? ['web'],
        'vsebina' => $data['vsebina'] ?? [],
        'meta' => $data['meta'] ?? [
            'cas' => time(),
            'sistem' => SISTEM_VERZIJA
        ]
    ];
}


📋 SISTEM/kanali/vrsta.php – ČAKALNA VRSTA

<?php
/**
 * ============================================================
 * POT: SISTEM/kanali/vrsta.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2
 *
 * 📰 NAMEN:
 *     Upravljanje čakalne vrste za izhod.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - vrsta_dodaj(array $item): bool
 *     - vrsta_preberi(?string $tip = null): array
 *     - vrsta_stevilo(?string $tip = null): int
 *     - vrsta_pocisti(?string $tip = null): bool
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kernel/baze/upravljalec_baz.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kanali, vrsta, queue
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function vrsta_dodaj(array $item): bool
{
    $vrsta = baza_beri('sistem', 'vrsta/izhod');
    
    if (!isset($vrsta['postavke'])) {
        $vrsta = ['postavke' => []];
    }
    
    $item['id'] = uniqid('vrsta_', true);
    $item['cas'] = time();
    
    $vrsta['postavke'][] = $item;
    
    return baza_pisi('sistem', 'vrsta/izhod', $vrsta);
}

function vrsta_preberi(?string $tip = null): array
{
    $vrsta = baza_beri('sistem', 'vrsta/izhod');
    
    if (empty($vrsta['postavke'])) {
        return [];
    }
    
    if ($tip === null) {
        return $vrsta['postavke'];
    }
    
    return array_filter($vrsta['postavke'], function($item) use ($tip) {
        return ($item['tip'] ?? '') === $tip;
    });
}

function vrsta_stevilo(?string $tip = null): int
{
    return count(vrsta_preberi($tip));
}

function vrsta_pocisti(?string $tip = null): bool
{
    if ($tip === null) {
        return baza_pisi('sistem', 'vrsta/izhod', ['postavke' => []]);
    }
    
    $vrsta = baza_beri('sistem', 'vrsta/izhod');
    
    if (empty($vrsta['postavke'])) {
        return true;
    }
    
    $vrsta['postavke'] = array_filter($vrsta['postavke'], function($item) use ($tip) {
        return ($item['tip'] ?? '') !== $tip;
    });
    
    return baza_pisi('sistem', 'vrsta/izhod', $vrsta);
}


📋 SISTEM/kanali/obdelava.php – WORKER

<?php
/**
 * ============================================================
 * POT: SISTEM/kanali/obdelava.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM N2
 *
 * 📰 NAMEN:
 *     Obdelava čakalne vrste – pošiljanje na kanale.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - obdelava_izvedi(?string $tip = null): array
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kanali/vrsta.php
 *     - ADAPTER/izhod_kanali/*
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kanali, obdelava, worker
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function obdelava_izvedi(?string $tip = null): array
{
    $postavke = vrsta_preberi($tip);
    $rezultati = [];
    
    foreach ($postavke as $postavka) {
        $kanali = $postavka['kanali'] ?? ['web'];
        
        foreach ($kanali as $kanal) {
            $rezultat = _poslji_na_kanal($kanal, $postavka);
            $rezultati[] = [
                'kanal' => $kanal,
                'id' => $postavka['id'] ?? 'unknown',
                'uspeh' => $rezultat
            ];
        }
    }
    
    // Počisti obdelane postavke
    vrsta_pocisti($tip);
    
    return $rezultati;
}

function _poslji_na_kanal(string $kanal, array $postavka): bool
{
    $mapa_kanalov = [
        'web' => POT_ADAPTER . '/izhod_kanali/KanalWeb.php',
        'api' => POT_ADAPTER . '/izhod_kanali/KanalApi.php',
        'telegram' => POT_ADAPTER . '/izhod_kanali/KanalTelegram.php',
        'facebook' => POT_ADAPTER . '/izhod_kanali/KanalFacebook.php',
        'ai' => POT_ADAPTER . '/izhod_kanali/KanalAi.php',
        'cli' => POT_ADAPTER . '/izhod_kanali/KanalCli.php'
    ];
    
    $pot = $mapa_kanalov[$kanal] ?? null;
    
    if (!$pot || !file_exists($pot)) {
        return false;
    }
    
    try {
        require_once $pot;
        
        // Pričakujemo funkcijo kanal_{ime}_poslji()
        $funkcija = 'kanal_' . $kanal . '_poslji';
        if (!function_exists($funkcija)) {
            return false;
        }
        
        return $funkcija($postavka);
    } catch (Exception $e) {
        return false;
    }
}


📋 ADAPTER/izhod_kanali/KanalWeb.php – WEB IZHOD

<?php
/**
 * ============================================================
 * POT: ADAPTER/izhod_kanali/KanalWeb.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER IO
 *
 * 📰 NAMEN:
 *     Pretvorba izhoda v HTML preko GLOBALNO/render/.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - kanal_web_poslji(array $postavka): bool
 *
 * 📡 ODVISNOSTI:
 *     - GLOBALNO/render/render.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez SQL
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, kanal, web, html
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function kanal_web_poslji(array $postavka): bool
{
    $stran = $postavka['vsebina']['stran'] ?? 'domov';
    $podatki = $postavka['vsebina']['podatki'] ?? [];
    
    require_once POT_GLOBALNO . '/render/render.php';
    _render_vsebina($stran, $podatki);
    
    return true;
}


📋 ADAPTER/izhod_kanali/KanalApi.php – API IZHOD

<?php
/**
 * ============================================================
 * POT: ADAPTER/izhod_kanali/KanalApi.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER IO
 *
 * 📰 NAMEN:
 *     Pretvorba izhoda v JSON.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - kanal_api_poslji(array $postavka): bool
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, kanal, api, json
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function kanal_api_poslji(array $postavka): bool
{
    header('Content-Type: application/json');
    
    $odziv = [
        'status' => $postavka['vsebina']['status'] ?? 'success',
        'podatki' => $postavka['vsebina']['podatki'] ?? [],
        'napaka' => $postavka['vsebina']['napaka'] ?? null
    ];
    
    echo json_encode($odziv, JSON_PRETTY_PRINT);
    return true;
}


📋 ADAPTER/adapter.php – EDINI VSTOP/IZSTOP

<?php
/**
 * ============================================================
 * POT: ADAPTER/adapter.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER N1
 *
 * 📰 NAMEN:
 *     EDINI vstop/izstop sistema. Normalizira vhod in pošlje izhod.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_izvedi(): void
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/api.php
 *     - ADAPTER/odzivi/adapter_odziv.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez SQL
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, vstop, izstop, io
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function adapter_izvedi(): void
{
    // 1. NORMALIZACIJA VHODA
    $akcija = $_POST['akcija'] ?? $_GET['akcija'] ?? '';
    $podatki = $_POST['podatki'] ?? $_GET['podatki'] ?? [];
    
    // 2. POŠLJI V SISTEM
    require_once POT_SISTEM . '/api.php';
    
    try {
        $odziv = _sistem_api_route($akcija, $podatki);
    } catch (Exception $e) {
        $odziv = [
            'status' => 'error',
            'sporocilo' => $e->getMessage()
        ];
    }
    
    // 3. PRIPRAVI IZHOD
    require_once POT_KANALI . '/priprava.php';
    $izhod = izhod_pripravi([
        'tip' => $akcija,
        'kanali' => ['web'],
        'vsebina' => $odziv,
        'meta' => [
            'akcija' => $akcija,
            'cas' => time()
        ]
    ]);
    
    // 4. POŠLJI IZHOD
    require_once POT_ADAPTER . '/odzivi/adapter_odziv.php';
    adapter_poslji_izhod($izhod);
}


📋 ADAPTER/odzivi/adapter_odziv.php – POŠILJANJE IZHODA

<?php
/**
 * ============================================================
 * POT: ADAPTER/odzivi/adapter_odziv.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ADAPTER IO
 *
 * 📰 NAMEN:
 *     Pošiljanje izhoda na ustrezne kanale.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - adapter_poslji_izhod(array $izhod): void
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kanali/vrsta.php
 *     - SISTEM/kanali/obdelava.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     adapter, odziv, izhod
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function adapter_poslji_izhod(array $izhod): void
{
    // Če je API kanal, pošlji direktno
    if (in_array('api', $izhod['kanali'] ?? [])) {
        require_once POT_ADAPTER . '/izhod_kanali/KanalApi.php';
        kanal_api_poslji($izhod);
        return;
    }
    
    // Sicer daj v vrsto
    require_once POT_KANALI . '/vrsta.php';
    vrsta_dodaj($izhod);
    
    // Obdelaj vrsto
    require_once POT_KANALI . '/obdelava.php';
    obdelava_izvedi();
}


================================================================================
🔴 7. KRITIČNI POPRAVKI
================================================================================

✔ api.php – zaščita
defined('SISTEM_VARNOST') or define('SISTEM_VARNOST', true);

✔ index.php – zaščita
$pot = preg_replace('/[^a-z0-9_-]/i', '', $_GET['pot'] ?? '');

✔ ID generator
'id' => empty($uporabniki)
    ? 1
    : max(array_column($uporabniki, 'id')) + 1,

✔ baza_json.php – IZBRISAN (krši izolacijo)
✔ 09_ponudniki.php – odstranjen die()
✔ 16_upravljalec_runtime.php – IZBRISAN (runtime ne obstaja)


================================================================================
🧩 8. MANJKAJOČE FUNKCIJE
================================================================================

function api_napaka(string $msg): void
{
    echo json_encode([
        'status' => 'error',
        'napaka' => $msg
    ]);
    exit;
}

function modul_preveri(string $modul): void
{
    $register = baza_beri('register', 'moduli_register');
    if (!isset($register[$modul]) || !($register[$modul]['aktiviran'] ?? false)) {
        throw new Exception('Modul ni dovoljen');
    }
}

function uporabnik_pot(int $id, string $datoteka): string
{
    return POT_UPORABNIKI . '/' . $id . '/' . $datoteka . '.json';
}


================================================================================
👤 9. REGISTRACIJA (FINAL)
================================================================================

$user_dir = POT_UPORABNIKI . '/' . $user_id;

mkdir($user_dir, 0755, true);
mkdir($user_dir . '/PASSPORT', 0755, true);

file_put_contents($user_dir . '/profil.json', json_encode([
    'id' => $user_id,
    'vloga' => VLOGA_S0,
    'plan' => 'osnova',
    'status' => 'aktiven'
], JSON_PRETTY_PRINT));


================================================================================
🌉 10. ADAPTER (FINAL – BREZ CURL)
================================================================================

function most_api_klic(string $akcija, array $podatki = []): array
{
    $staraPost = $_POST;
    $stariGet = $_GET;

    $_POST = ['akcija' => $akcija, 'podatki' => $podatki];
    $_GET = [];

    ob_start();
    require_once POT_ADAPTER . '/adapter.php';
    adapter_izvedi();
    $izhod = ob_get_clean();

    $_POST = $staraPost;
    $_GET = $stariGet;

    return json_decode($izhod, true) ?? ['status' => 'error', 'napaka' => 'Ni odgovora'];
}


================================================================================
⚙️ 11. OBVEZNO ZA IMPLEMENTACIJO
================================================================================

VRSTA (queue):
- vrsta_dodaj()     ✅
- vrsta_preberi()   ✅
- vrsta_stevilo()   ✅
- vrsta_pocisti()   ✅

KANALI:
- SISTEM/kanali/obdelava.php  ✅
- ADAPTER/izhod_kanali/       ✅

REGISTRI:
- PODATKI/registri/moduli_register.json  ✅
- PODATKI/registri/rbac/vloge.json       ✅


================================================================================
🔁 12. TOK SISTEMA (ZAKLENJEN)
================================================================================

index.php
    ↓
ADAPTER/adapter.php (normalizacija vhoda)
    ↓
SISTEM/api.php (edini vstop v sistem)
    ↓
SISTEM/kernel/zaganjalnik.php (bootstrap)
    ↓
SISTEM/kernel/jedro/01-15 (jedro – BREZ RUNTIME)
    ↓
SISTEM/storitve_svetov/ (business logika)
    ↓
SISTEM/kanali/ (tehnični izhod: priprava, vrsta, obdelava)
    ↓
ADAPTER/izhod_kanali/ (pretvorba: HTML, JSON, ...)
    ↓
ADAPTER/odzivi/adapter_odziv.php (pošiljanje)
    ↓
Končni izhod


================================================================================
🔒 KONČNO STANJE
================================================================================

brez konfliktov ✔
brez podvojenih funkcij ✔
RBAC pravilen ✔
upravljalec_baz.php stabilen ✔
adapter kontrolira IO ✔
sistem razširljiv ✔


================================================================================
🧱 ENA IN EDINA RESNICA
================================================================================

To je:
- konsistentno
- izvedljivo
- produkcijsko pripravljeno
- BREZ RUNTIME

================================================================================

================================================================================
REGISTER – Živi dokument projekta
================================================================================

# REGISTER PROJEKTA
> **Živi dokument** — izpolnjuje se sproti, ob vsaki spremembi.
> Vsak izvajalec (AI ali razvijalec) ki doda, spremeni ali zbriše karkoli — TAKOJ vpiše sem.
> Brez tega dokumenta naslednji izvajalec ne ve kaj obstaja.

---

## NAVODILO ZA IZVAJALCE

Ko narediš karkoli na projektu:
1. Poišči pravi razdelek spodaj
2. Dodaj/posodobi vnos
3. Zapiši datum in kdo je naredil
4. Če razdelka ni — ga dodaj po vzorcu

Format datuma: `LLLL-MM-DD`
Format avtorja: `ime` / `GPT` / `Claude` / `DeepSeek` / `AI`

---

## 1. DATOTEKE

Vsaka datoteka v projektu ima tukaj vnos.

### Format vnosa:
```
### pot/do/datoteke.php
- **Namen:** kaj dela
- **Nivo:** 0 (root) / 1 (SISTEM/) / 2 (SISTEM/kernel/) / 3 (SISTEM/kernel/jedro/)
- **Verzija:** 1.0
- **Avtor:** ime | datum
- **Odvisno od:** seznam datotek ki jih ta datoteka kliče
- **Kličejo jo:** seznam datotek ki kličejo to datoteko
- **Izvozi:** seznam funkcij/konstant ki jih ponuja navzven
- **Opombe:** posebnosti, opozorila, TODO
```

---

### pot.php
- **Namen:** Absolutno sidro — definira vse POT_* konstante
- **Nivo:** 0
- **Verzija:** —
- **Avtor:** — | —
- **Odvisno od:** nič
- **Kličejo jo:** vse datoteke v projektu (prva vrstica)
- **Izvozi:** `POT_KOREN`, `POT_SISTEM`, `POT_GLOBALNO`, `POT_MODULI`, `POT_UPORABNIKI`, `POT_ASTRA`, `POT_VSEBINA`, `POT_PODATKI`
- **Opombe:** ⚠ Ne premikaj, ne preimenuj, ne briši

---

*(sem se dodajajo datoteke ko nastajajo)*

---

## 2. KONSTANTE

Vse `define()` konstante v projektu.

| Konstanta | Vrednost / Opis | Definirana v | Datum |
|---|---|---|---|
| `POT_KOREN` | Absolutna pot do root/ | `pot.php` | — |
| `POT_SISTEM` | `.../SISTEM/` | `pot.php` | — |
| `POT_GLOBALNO` | `.../GLOBALNO/` | `pot.php` | — |
| `POT_MODULI` | `.../MODULI/` | `pot.php` | — |
| `POT_UPORABNIKI` | `.../UPORABNIKI/` | `pot.php` | — |
| `POT_ASTRA` | `.../ASTRA/` | `pot.php` | — |
| `POT_VSEBINA` | `.../VSEBINA/` | `pot.php` | — |
| `POT_PODATKI` | `.../PODATKI/` | `pot.php` | — |

---

## 3. FUNKCIJE

Vse PHP funkcije v projektu — globalne in javne metode razredov.

### Format vnosa:
```
| funkcija() | Kaj dela | Parametri | Vrne | Datoteka | Avtor | Datum |
```

| Funkcija | Namen | Parametri | Vrača | Datoteka | Avtor | Datum |
|---|---|---|---|---|---|---|
| *(sem se dodajajo ko nastajajo)* | | | | | | |

---

## 4. RAZREDI

Vsi PHP razredi v projektu.

### Format vnosa:
```
### ImeRazreda
- **Namen:** kaj dela
- **Datoteka:** pot/do/datoteke.php
- **Metode:** seznam javnih metod
- **Avtor:** ime | datum
```

*(sem se dodajajo ko nastajajo)*

---

## 5. BAZE IN SHEME

Vse baze in njihove tabele/sheme.

### Format vnosa:
```
### ime_baze (tip: sqlite/postgres/...)
- **Lokacija:** pot/do/baze.db
- **Uporablja jo:** seznam modulov/datotek
- **Tabele:** seznam tabel
- **Avtor:** ime | datum
```

---

### Json osnovne Baze in Sistemske baze (načrtovane)

| Baza | Tip | Lokacija | Namen | Status |
|---|---|---|---|---|
| Seje/cache | json | `PODATKI/sistem/seja/` | Sistemske seje | aktivno |
| Seje/cache | SQLite | `PODATKI/.baze/sqlite/` | Sistemske seje | načrtovano |
| Astra | SQLite | `ASTRA/.baze/astra.sqlite` | Admin podatki | načrtovano |
| moduli | json | `PODATKI/moduli/[ime_modula]/` | sistemski modulski podatki | aktivno |
| Skupni moduli | PostgreSQL | `MODULI/.baze/postgres/` | Relacije med moduli | načrtovano |
| Analitika | ClickHouse | `PODATKI/.baze/clickhouse/` | Obiskovalci, eventi | načrtovano |
| Uporabniki | json | `UPORABNIKI/uporabniki/[id_uporabnika]]/` | osebni up. podatki | aktivno |
| Uporabniki | json | `PODATKI/uporabniki/[id_uporabnika]]/` | sistemski up. podatki | aktivno |
| Uporabniki | Cassandra | `PODATKI/.baze/cassandra/` | Neomejeni uporabniki | načrtovano |

---

## 6. API KLICI / POTI

Vse poti ki jih `api.php` sprejema.

### Format vnosa:
```
| METHOD | /pot | Kaj dela | N2 handler | Pravice | Avtor | Datum |
```

| Metoda | Pot | Namen | Handler | Pravice | Avtor | Datum |
|---|---|---|---|---|---|---|
| *(sem se dodajajo ko nastajajo)* | | | | | | |

---

## 7. MODULI

Vsi moduli v projektu.

| Ime | Kategorija | Tip | Baza | Pravice | Status | Avtor | Datum |
|---|---|---|---|---|---|---|---|
| Aeternum | osnovni | integriran | sqlite | javno | načrtovano | — | — |

---

## 8. ODVISNOSTI MED DATOTEKAMI

Graf kdo kliče koga. Posodobi ko dodaš novo odvisnost.

```
pot.php
    ← vse datoteke v projektu

SISTEM/api.php (N1)
    → SISTEM/kernel/zaganjalnik.php

SISTEM/kernel/zaganjalnik.php (N2)
    → SISTEM/kernel/jedro/01_upravljalec_svetov.php
    → SISTEM/kernel/jedro/02_napake.php
    → SISTEM/kernel/jedro/03_varnost.php
    → SISTEM/kernel/jedro/04_seja.php
    → SISTEM/kernel/jedro/05_pravice.php
    → SISTEM/kernel/jedro/06_cache.php
    → SISTEM/kernel/jedro/07_dogodki.php
    → SISTEM/kernel/jedro/08_kavlji.php
    → SISTEM/kernel/jedro/09_ponudniki.php
    → SISTEM/kernel/jedro/10_middleware.php
    → SISTEM/kernel/jedro/11_usmerjevalnik.php
    → SISTEM/kernel/jedro/12_validacija.php
    → SISTEM/kernel/jedro/13_api.php
    → SISTEM/kernel/jedro/14_zagon.php
    → SISTEM/kernel/jedro/15_pogon.php
    # 16_upravljalec_runtime.php – IZBRISAN, runtime ne obstaja (glej TEMELJ.md)
```

*(dopolnjuj ko nastajajo nove odvisnosti)*

---

## 9. ZNANE TEŽAVE IN TODO

Aktivne težave in načrtovane izboljšave.

### Format:
```
| ID | Tip | Opis | Datoteka | Prioriteta | Avtor | Datum |
```

| ID | Tip | Opis | Datoteka | Prioriteta | Avtor | Datum |
|---|---|---|---|---|---|---|
| — | — | *(sem se dodajajo)* | — | — | — | — |

**Tipi:** `BUG` / `TODO` / `HACK` / `FIXME` / `IDEJA`
**Prioritete:** `visoka` / `srednja` / `nizka`

---

## 10. SPREMEMBE — DNEVNIK

Kronološki zapis vseh sprememb na projektu.

### Format:
```
### LLLL-MM-DD — Avtor
- **Dodano:** ...
- **Spremenjeno:** ...
- **Odstranjeno:** ...
- **Opombe:** ...
```

---

### 2026-01-01 — Začetek projekta
- **Dodano:** Dokumentacija (USTAVA, ARHITEKTURA, STANDARDI, MODULI, VIZIJA, FAZE, REGISTER)
- **Opombe:** Faza 1 zaključena. Naslednje: Faza 2 — jedro sistema.

---

*(sem se dodajajo vnosi ko nastajajo)*

---

## NAVODILO ZA AI ASISTENTA

Ko dobiš nalogo in jo zaključiš, **obvezno posodobi REGISTER.md**:

```
1. Razdelek 1 (Datoteke)     → dodaj vsako novo datoteko
2. Razdelek 2 (Konstante)    → dodaj vsako novo konstanto
3. Razdelek 3 (Funkcije)     → dodaj vsako novo funkcijo
4. Razdelek 4 (Razredi)      → dodaj vsak nov razred
5. Razdelek 5 (Baze)         → dodaj vsako novo bazo/tabelo
6. Razdelek 6 (API)          → dodaj vsako novo pot
7. Razdelek 8 (Odvisnosti)   → posodobi graf
8. Razdelek 9 (TODO)         → dodaj znane težave
9. Razdelek 10 (Dnevnik)     → zapiši kaj si naredil
```

Če tega ne narediš — naslednji izvajalec dela v temi.

---

*REGISTER.md — verzija 1.0 — živi dokument*


================================================================================
VIZIJA – Namen in filozofija projekta
================================================================================

# VIZIJA PROJEKTA
> Ta dokument je za **AI asistente** ki delajo na projektu.
> Preberi ga preden karkoli narediš — da razumeš *kaj gradiš* in *zakaj*.
> Tehnična pravila so v USTAVA.md, ARHITEKTURA.md, STANDARDI.md.

---

## KAJ JE TO

**Kozmična šola zavesti** — spletna platforma kjer uporabniki raziskujejo ezoterična znanja,
spoznavajo sebe, in gradijo svojo osebno knjigo skozi pot.

Ni to klasična spletna stran. Je **živ ekosistem** kjer:
- Vsebina raste sama (AI piše module, poglavja, razlage)
- Uporabnik s svojo potjo sooblikuje sistem
- Avtor (lastnik) ima svoj Codex ki je srce vsega
- Skupnost (forumi, blogi, cikli) vrača energijo nazaj v vsebino

---

## SRCE SISTEMA — CODEX

Lastnik ima svojo knjigo znanja: **Codex**.

```
Codex (lastnikova knjiga)
    ↓ iz njega nastajajo
Moduli (ezoterična znanja: astrologija, numerologija, djotiš...)
    ↓ moduli sprožijo
Forumi, blogi, cikli
    ↓ iz skupnosti se dobivajo ideje
Nove vsebine → nazaj v Codex
```

Codex ni statičen — **raste**. Ko skupnost postavi vprašanje, ko cikel razkrije vzorec,
ko forum odpre temo — to postane novo poglavje. AI pomaga pisati, lastnik usmerja.

---

## UPORABNIKOVA POT

Vsak uporabnik med raziskovanjem **gradi svojo osebno knjigo**.

Knjiga nastaja **avtomatsko** — iz:
- Odgovorov na vprašanja v modulih
- Modulov ki jih je obiskal
- Tarot ciklov ki jih je sledil
- Zapiskov ki jih je dodal

Uporabnik ne piše knjige — **živi jo**. Sistem jo gradi v ozadju.

```
Uporabnik obišče modul Astrologija
    → sistem zabeleži
    → postavi vprašanje: "Kateri element prevladuje v tebi?"
    → odgovor gre v uporabnikovo knjigo
    → knjiga postane ogledalo njegove poti
```

---

## MODULI — PLANETI VESOLJA

Vsak modul je svet znanja. Za začetek ~50, nato neomejeno.

Moduli pokrivajo ezoterična področja:
- Astrologija (zahodna, vedska/djotiš)
- Numerologija
- Tarot
- Kabala
- Hermetika
- Simboli, arhetípi, sanje
- ... in vse kar Codex odpre

Vsak modul:
- Ima svojo vsebino (AI jo piše, lastnik usmerja)
- Ima svoja vprašanja (ki gradijo uporabnikovo knjigo)
- Je del večjega vesolja (vizualno: planet, zvezda, portal)
- Lahko raste (nova poglavja, nove razlage)

---

## TAROT CIKLI

Lastnik objavlja **5-dnevne cikle** — vsak dan nova karta, nova tema, novo ozaveščanje.

```
Dan 1-5: Brezplačno za vse (v živo)
Dan 6+:  Brezplačno samo za tiste ki SLEDIJO V REALNEM ČASU
         Plačljivo za arhiv / kasnejši dostop
```

Cikel ima namen: uporabnik v 5 dneh spozna problem in ga ozavesti.
Poglobitev (dnevi 6+) je za tiste ki so pripravljeni iti globlje.

---

## KNJIŽNICA CORPUSMYSTICUM

Posebni modul — **knjižnica** ki je srce znanja (v MODULI/SVET/CorpusMysticum/).

Ima dve verziji:
- **Starodavna** — za odrasle, globoka, mistična, za resne bralce
- **Otroška** — za zgodbice, lahkotna, za večerno poslušanje

Funkcionalnosti:
- Glasovno branje (sistem bere besedilo na glas)
- Glasovno zapisovanje (uporabnik govori → sistem zapiše)
- Iskanje po vsebini
- Dodajanje zapiskov in beležk
- AI piše novo vsebino po navodilih

---

## AI V SISTEMU — VLOGE

V tem projektu AI ni samo orodje. Je **soavtor in graditelj**.

### GPT — arhitekt in strateg
- Analizira kaj manjka
- Planira naslednje korake
- Pregleduje kvaliteto vsebine
- Odloča kaj je za napisati

### DeepSeek — pisec
- Piše vsebino modulov
- Piše poglavja Codexa
- Piše razlage, vprašanja, zgodbe
- Dela po navodilih GPT-ja

### JSON — jezik med njima
Vsa vsebina ki jo AI ustvari pride v standardiziranem JSON formatu.
Sistem jo prebere in uvozi v pravi modul.
*(Format bo definiran v AI_PIPELINE.md — ko bo jedro sistema vzpostavljeno)*

### Claude — dokumentacija in arhitektura
- Vzdržuje standarde in dokumentacijo
- Pomaga pri arhitekturnih odločitvah
- Ne piše vsebine modulov

---

## KAJ AI ASISTENT MORA RAZUMETI

Preden narediš karkoli na tem projektu:

**1. Kontekst je duhovni, ne samo tehnični.**
Besede, struktura, izkušnja — vse mora služiti razvoju zavesti.
Modul o astrologiji ni "seznam planet" — je pot samospoznavanja.

**2. Sistem je živ.**
Ne gradiš statičnih strani. Gradiš organizem ki raste.
Vsaka odločitev mora omogočati rast, ne jo omejevati.

**3. Uporabnik je na poti.**
Vsaka interakcija je del njegove zgodbe.
Vprašanja so pomembna kot odgovori. Izkušnja je važnejša od informacije.

**4. Lastnik je usmerjevalec, ne programer.**
Sistem mora delovati sam. AI gradi, lastnik usmerja smer.
Nobena odločitev ne sme zahtevati lastnikovega posredovanja za rutinske stvari.

**5. Najprej jedro, potem vse ostalo.**
```
Faza 1: Dokumentacija ← (to je zdaj)
Faza 2: Jedro sistema (pot.php, api.php, zaganjalnik, baze)
Faza 3: En modul pravilno (CorpusMysticum — knjižnica, glej KNJIŽNICA AETERNUM zgoraj)
Faza 4: AI pipeline (GPT + DeepSeek + JSON)
Faza 5: 3D vesolje (Three.js)
Faza 6: Glasovno upravljanje
```
Ne preskakuj faz. Ne gradiš hiše na pesku.

---

## STIL IN ESTETIKA

Vizualno: **kozmično, mistično, globoko.**
- Temno ozadje (vesolje, noč)
- Zlati in modri odtenki
- Organske oblike (spirale, orbite, svetloba)
- Nič plastičnega, nič generičnega

Dve postavitvi:
- **Klasična** — za tiste ki raje berejo, strukturirano
- **Moderna/3D** — za tiste ki radi raziskujejo, kozmično vesolje

Jezik: **slovenščina** — topla, poetična, ne akademska.

---

*VIZIJA.md — verzija 1.0 — beri pred vsem drugim*


================================================================================
KONEC DOKUMENTA – PRAVILA_vse.md v2 (usklajena različica)
================================================================================
