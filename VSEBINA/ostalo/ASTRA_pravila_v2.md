=== 00_temeljna_pravila.md ===

---
tip: pravilo
nivo: -1
naziv: TEMELJNA PRAVILA SISTEMA
velja_od: 2026-03-29
verzija: 3.4.0
---

# TEMELJNA PRAVILA SISTEMA

**Ta dokument je EDINI VIR za temeljna pravila sistema.**
**Nivo -1 – nad vsemi razen samega sebe.**

---

## 1. DETERMINISTIČNI VRSTNI RED IZVAJANJA DOKUMENTOV

Sistem bere in izvaja dokumente **STRIKTNO PO ŠTEVILČNEM VRSTNEM REDU**:

| Vrstni red | Dokument | Nivo | Vloga |
|------------|----------|------|-------|
| 1 | `00_temeljna_pravila.md` | -1 | TEMELJ – prepovedi, struktura, pravila |
| 2 | `00_standard_pisanja_pravil.md` | 0 | STANDARD – format in pisanje pravil |
| 3 | `01_vrstni_red_izvajanja.md` | -1 | IZVAJANJE – bootstrap, loader, router |
| 4 | `02_zaklep_jedra.md` | -1 | ZAKLEP – zaklenjen vrstni red jedra |
| 5 | `04_razsirljivost_sistema.md` | -1 | RAZŠIRLJIVOST – moduli, plugini, servisi |
| 6 | `05_struktura_pravil.md` | -1 | STRUKTURA – format in validacija pravil |
| 7+ | `06_*.md` do `17_*.md` | 2+ | PODROČJA – implementacija podrobnosti |

**Pravilo:** Nobeno pravilo ne sme spreminjati tega vrstnega reda.
**Pravilo:** Nobeno pravilo ne sme redefinirati že definiranega nivoja.
**Opomba:** `03_implementacijske_prioritete.md` ne obstaja – vsebina je bila integrirana v `01` in `02`.

---

## 2. HIERARHIJA NIVOJEV

| Nivo | Pomen | Primer |
|------|-------|--------|
| -1 | absolutni zaklep | 00, 01, 02, 04, 05 |
| 0 | standard pisanja | 00_standard_pisanja_pravil.md |
| 1 | globalno (rezervirano) | – |
| 2 | področje / implementacija | 06–17 |
| 3 | plugin | posamezni plugin dokumenti |
| 4 | servis | posamezni servis dokumenti |
| 5 | lokalno | projektno specifično |

**Pravilo:** Dokument z nižjim nivojem ima vedno prednost pred dokumentom z višjim nivojem.
**Pravilo:** Dokument z nižjo številko v isti nivojski skupini ima prednost pred dokumentom z višjo številko.

---

## 3. ABSOLUTNA PREPOVED KODE V PRAVILIH

**Izvršljiva koda v .md pravilih je ABSOLUTNO PREPOVEDANA.**

Pravila smejo vsebovati:
- psevdokodo (opisno, neizvršljivo)
- opise obnašanja
- JSON primere (kot podatkovne strukture, ne kot kodo)
- tabele in diagrame
- strukturne prikaze map in datotek

Pravila **NE SMEJO** vsebovati:
- `<?php` oznak ali katerihkoli jezikovnih oznak
- dejanskih funkcij kateregakoli programskega jezika
- izvršljivih klicev
- definicij razredov ali funkcij
- spremenljivk s `$`, `var`, `let`, `const`
- pogojnih stavkov (`if`, `switch`, `for`, `while`)
- definicij konstant s kodo

**Validator MORA blokirati vsako pravilo, ki vsebuje izvršljivo kodo.**

---

## 4. ABSOLUTNE PREPOVEDI (VELJAJO ZA VSE)

| Prepoved | Razlog |
|----------|--------|
| Router v bootstrap.php | Krši hierarhijo |
| Direkten klic modulov iz jedra | Jedro ne pozna modulov |
| EnvLoader po loggerju | EnvLoader MORA biti prvi |
| Sprememba vrstnega reda jedra | Jedro je zaklenjeno (glej 02) |
| Koda v pravilih | Pravila niso izvršljiva |
| Podvajanje informacije v dveh dokumentih | Enotni vir resnice |
| Jedro definira konstante | Bootstrap definira konstante |
| Jedro vključuje datoteke izven svojega imenika | Ohranja zaklenjenost |
| Jedro kliče module ali plugine direktno | Samo preko vmesnika |
| Loader vsebuje logiko, pogoje ali fallbacke | Loader je determinističen |
| Bootstrap spreminja vrstni red jedra | Jedro je fiksno (glej 02) |
| Bootstrap preskakuje datoteke jedra | Vse ali nič |
| Polovična inicializacija | Atomarni bootstrap |
| Fallback logika izven preveri_stanje.php | Samo ob namestitvi/nadgradnji |

---

## 5. DOLOČANJE POTI (PREMESTLJIV SISTEM)

Sistem je **popolnoma premestljiv**. Vse poti se določijo dinamično ob zagonu.

### 5.1. ROOT_PATH

`ROOT_PATH` je koren projekta – mapa, kjer se nahajajo vstopna datoteka, konfiguracijska datoteka in vse sistemske mape.

**Način določitve:** Izračuna se relativno iz lokacije loaderja z dvema nivojema navzgor.

### 5.2. Konstante poti

Vse ostale konstante se določijo relativno na `ROOT_PATH`.

**Opomba:** Spodnji seznam je opisni prikaz konvencije poimenovanja, ne definicija konstant v pravilu. Konstante definira bootstrap.

| Konstanta | Relativna pot |
|-----------|---------------|
| `SISTEM_PATH` | `ROOT_PATH/SISTEM` |
| `ASTRA_PATH` | `ROOT_PATH/ASTRA` |
| `DATOTEKE_PATH` | `ROOT_PATH/DATOTEKE` |
| `MODULI_PATH` | `ROOT_PATH/MODULI` |
| `PLUGINI_PATH` | `ROOT_PATH/PLUGINI` |
| `UPORABNIKI_PATH` | `ROOT_PATH/UPORABNIKI` |
| `GLOBALNO_PATH` | `ROOT_PATH/GLOBALNO` |
| `VSEBINA_PATH` | `DATOTEKE_PATH/vsebina` |
| `CACHE_PATH` | `DATOTEKE_PATH/cache` |
| `MEDIA_PATH` | `DATOTEKE_PATH/media` |

### 5.3. BASE_URL

`BASE_URL` se določi samodejno iz podatkov spletnega strežnika z možnostjo ročnega preglasitve v konfiguracijski datoteki `.env`.

**Pravilo:** Ročno nastavljena vrednost `BASE_URL` v `.env` ima vedno prednost pred samodejno določeno.

---

## 6. KONFLIKTNA STRATEGIJA

Globalna konfliktna strategija sistema je **priority**.

| Strategija | Opis |
|------------|------|
| `override` | zadnje izvedeno pravilo prepiše prejšnje |
| `merge` | združi izhode |
| `fail` | prekine in vrne napako `rule_conflict` |
| `priority` | izvede se tisto z višjo prioriteto (PRIVZETO) |

**Definicija:** Vrednost se shrani v konfiguracijski datoteki `spremenljivke.php`.

---

## 7. ANTI-FRAGMENTACIJSKO PRAVILO

**Če se nova funkcionalnost lahko doda v obstoječ dokument, se ne ustvarja nov dokument.**

Prepovedano je ustvarjanje:
- "patch" dokumentov
- "clarification" dokumentov
- "temporary" dokumentov
- dokumentov, ki urejajo vrstni red (razen v 01)
- dokumentov, ki delno zaklepajo jedro (razen v 02)

---

## 8. ANTI-KONFLIKTNO PRAVILO

**Novo pravilo ne sme spreminjati pomena obstoječega pravila.**

Če ga spreminja, mora biti obstoječe pravilo **posodobljeno**, ne dopolnjeno z novim.

---

## 9. ENOTNI VIR RESNICE

Vsaka informacija v sistemu ima **točno en avtoritativen vir**:

| Tema | Avtoritativen vir |
|------|-------------------|
| Vrstni red zagona (bootstrap) | `01_vrstni_red_izvajanja.md` |
| Zaklenjen vrstni red jedra | `02_zaklep_jedra.md` poglavje 3 |
| Struktura modula | `07_moduli.md` |
| Struktura plugina | `09_plugini.md` |
| Kavlji – seznam in prioritete | `06_kavlji.md` |
| Generator – komande | `16_generator.md` |
| Servisi – driverji | `10_servisi.md` |
| Format logov | `11_logiranje.md` |
| Format dogodkov | `12_stanje.md` |

**Pravilo:** Drugi dokumenti smejo na ta vir samo sklicevati, ne kopirati.

---

## 10. KAZEN ZA KRŠITEV

Če kateri koli dokument:
- vsebuje izvršljivo kodo
- podvaja informacijo iz drugega dokumenta
- krši vrstni red zagona
- nima veljavnega nivoja
- nima veljavnega hash-a v registru

→ **sistem se ne zažene** in vrne napako z navedbo datoteke in sklicevanjem na ta dokument.

---

**KONEC DOKUMENTA**


=== 00_standard_pisanja_pravil.md ===

---
tip: pravilo
nivo: 0
naziv: STANDARD PISANJA PRAVIL
velja_od: 2026-03-29
verzija: 1.1.0
---

# STANDARD PISANJA PRAVIL

## 1. NAMEN

Ta dokument določa standard za pisanje vseh pravil v sistemu.

Cilj je zagotoviti enotno strukturo, dosledno terminologijo, odsotnost podvajanja in konfliktov, razširljivost, strojno berljivost in stabilno nadaljnje razvijanje.

Ta standard velja za vsa nova in obstoječa pravila.

---

## 2. OBVEZNI HEADER

Vsako pravilo mora vsebovati header z naslednjimi obveznimi polji:

| Polje | Format | Opis |
|-------|--------|------|
| `tip` | vedno `pravilo` | tip dokumenta |
| `nivo` | celo število | hierarhična raven (glej tabelo nivojev) |
| `naziv` | VELIKE ČRKE | unikaten naziv brez številčenja in posebnih znakov |
| `velja_od` | YYYY-MM-DD | datum veljavnosti |
| `verzija` | X.Y.Z | semantično verzioniranje |

Polja so obvezna. Pravilo brez veljavnega headerja se ne registrira.

---

## 3. TABELA NIVOJEV

| Nivo | Pomen |
|------|-------|
| -1 | absolutni zaklep – jedro sistema |
| 0 | standard pisanja |
| 1 | globalno (rezervirano) |
| 2 | področje / implementacija |
| 3 | plugin |
| 4 | servis |
| 5 | lokalno |

---

## 4. POIMENOVANJE DATOTEK

Format: `NN_ime_pravila.md`

Pravila:
- dvomestna številka (00–99)
- male črke
- ločilo je podčrtaj
- brez šumnikov
- brez presledkov

Primer: `05_struktura_pravil.md`

---

## 5. STRUKTURA VSEBINE

Pravilo mora biti razdeljeno v jasno oštevilčene sekcije.

Priporočene sekcije:
- `## 1. NAMEN`
- `## 2. OBSEG`
- `## 3. DEFINICIJE`
- `## 4. PRAVILA`
- `## 5. PREPOVEDI`
- `## 6. REFERENCE`

Minimalno zahtevano: namen in vsaj eno poglavje s pravili.

---

## 6. NAČELA PISANJA

### 6.1. Ena odgovornost

Vsako pravilo pokriva **eno** logično področje. Ni dovoljeno združevati več sistemov, modulov ali funkcionalnosti v en dokument.

### 6.2. Brez podvajanja

Če informacija že obstaja v drugem dokumentu, se ne ponavlja – samo sklicuje.

### 6.3. Brez izvršljive kode

V pravilih ni dovoljeno pisati kode kateregakoli programskega jezika. Dovoljeno je opisno (psevdokoda, opis obnašanja, JSON kot podatkovna struktura, struktura map).

### 6.4. Brez implementacijskih detajlov

Pravila določajo **kaj**, ne **kako**. Prepovedano je navajati konkretne knjižnice, ogrodja ali implementacijske rešitve.

### 6.5. Deterministična formulacija

Obvezno se uporablja:
- `mora`, `je obvezno`, `ni dovoljeno`, `sistem zahteva`

Prepovedano je:
- `lahko`, `priporočljivo`, `običajno`, `po potrebi`

### 6.6. Generičnost

Pravila ne smejo vsebovati konkretnih imen modulov, pluginov, servisov ali projektov. Pravila morajo biti splošna in neodvisna od konkretne implementacije.

### 6.7. Brez runtime logike

Pravila ne vsebujejo pogojnih stavkov, razvejitev ali runtime pogojev. Opisujejo stanje, ne potek izvajanja.

### 6.8. Brez konfliktov

Novo pravilo ne sme redefinirati obstoječega ali spreminjati njegovega pomena. V primeru konflikta se posodobi izvorno pravilo.

---

## 7. VELJAVNOST PRAVILA

Pravilo je veljavno, če:
- ima popoln header
- ima namen
- je deterministično formulirano
- ne vsebuje izvršljive kode
- ne podvaja informacij iz drugega dokumenta
- ne ustvarja konflikta z obstoječim pravilom
- je splošno in generično

Neveljavno pravilo se ne vključi v sistem in ne vpliva na izvajanje.

---

## 8. PREPOVEDI (POVZETEK)

| Prepovedano | Razlog |
|-------------|--------|
| Podvajanje informacij | Enotni vir resnice |
| Izvršljiva koda | Pravila niso programska koda |
| Implementacijski detajli | Pravila določajo kaj, ne kako |
| Runtime logika | Pravila opisujejo stanje |
| Konkretna imena projektov | Pravila morajo biti generična |
| Nepopoln header | Brez headerja ni registracije |
| Nejasna terminologija | Determinizem je obvezen |

---

**KONEC DOKUMENTA**


=== 01_vrstni_red_izvajanja.md ===

---
tip: pravilo
nivo: -1
naziv: VRSTNI RED IZVAJANJA SISTEMA
velja_od: 2026-03-29
verzija: 4.0.0
---

# VRSTNI RED IZVAJANJA SISTEMA

**Ta dokument je EDINI VIR za vrstni red zagona in izvajanja sistema.**
**Nivo -1 – podrejen samo `00_temeljna_pravila.md`.**

---

## 1. HIERARHIJA ZAGONA

Sistem se zaganja v naslednjem fiksnem zaporedju:

```
1. index.php  (spletni vhod)  ALI  CLI  (ukazna vrstica)
2. SISTEM/loader.php
   2.1. Definiraj varnostno konstanto
   2.2. Poženi validator pravil
   2.3. Ob napaki validatorja → ustavi sistem (ERROR 500)
3. bootstrap.php
   3.1.  Definiraj ROOT_PATH (samodejno iz lokacije loaderja)
   3.2.  Definiraj vse ostale konstante poti (relativno na ROOT_PATH)
   3.3.  Določi BASE_URL (samodejno + možnost preglasitve iz .env)
   3.4.  Naloži generirana skladišča
   3.5.  Naloži .env (EnvLoader) ← MORA BITI PRED vsem ostalim jedrom
   3.6.  Naloži spremenljivke.php
   3.7.  Naloži varni_razredi.php
   3.8.  Naloži preveri_stanje.php (SAMO ob prvi namestitvi ali nadgradnji)
   3.9.  Naloži jedro (zaklenjen vrstni red iz 02_zaklep_jedra.md poglavje 3)
   3.10. Registrira servise
   3.11. Poženi kavelj system_init
   3.12. Naloži module (ModulLoader)
   3.13. Naloži plugine (PluginLoader)
4. router()  ← VEDNO PO bootstrapu, NIKOLI znotraj bootstrapa
```

**Absolutno pravilo:** Router se kliče po zaključku bootstrapa. Klicanje routerja znotraj bootstrapa je prepovedano.

**Absolutno pravilo:** Bootstrap je atomaren. Če katerikoli korak odpove, se nadaljnji koraki ne izvedejo in sistem se ustavi.

---

## 2. VALIDATOR PRAVIL

### 2.1. Kdaj se validator požene

| Faza | Lokacija | Akcija ob napaki |
|------|----------|------------------|
| Ob vsakem zagonu | SISTEM/loader.php | ERROR 500, sistem se ne zažene |
| Ob ukazu `--check` | generator.php | Izpiše napake, ne generira |

### 2.2. Kaj validator preverja

| # | Preverjanje | Vir |
|---|-------------|-----|
| 1 | Hash vsakega pravila | DATOTEKE/podatki/pravila_hash.json |
| 2 | Hierarhija nivojev | `00_temeljna_pravila.md` poglavje 2 |
| 3 | Prepoved izvršljive kode v .md | `00_temeljna_pravila.md` poglavje 3 |
| 4 | Prepoved podvajanja | `00_temeljna_pravila.md` poglavje 9 |
| 5 | Vrstni red zagona bootstrapa | Ta dokument poglavje 1 |
| 6 | Zaklenjen vrstni red jedra | `02_zaklep_jedra.md` poglavje 3 |
| 7 | Konfliktna strategija | `00_temeljna_pravila.md` poglavje 6 |

### 2.3. Hash registry

Lokacija: `DATOTEKE/podatki/pravila_hash.json`

Format (JSON podatkovna struktura):

```
{
  "version": "1.0.0",
  "last_validation": "YYYY-MM-DDTHH:MM:SSZ",
  "files": {
    "00_temeljna_pravila.md": "sha256:...",
    "01_vrstni_red_izvajanja.md": "sha256:...",
    "02_zaklep_jedra.md": "sha256:..."
  }
}
```

---

## 3. LOADER DATOTEKE

### 3.1. index.php

**Vloga:** Edina vstopna točka za spletne zahteve.

**Vsebina:** Izključno klic loaderja. Nobene druge logike.

### 3.2. SISTEM/loader.php

**Vloga:** Nivo 1 – edina sistemska vstopna točka.

**Zaporedje:**
1. Definiraj varnostno konstanto (če še ni definirana)
2. Pokliči validator
3. Ob napaki validatorja → ustavi s kodo 500
4. Pokliči bootstrap.php
5. Pokliči router z URI

---

## 4. PRIORITETA PROGRAMIRANJA

Ta tabela določa vrstni red implementacije komponent:

| # | Komponenta | Ocenjeni čas |
|---|------------|--------------|
| 1 | index.php, loader.php, bootstrap.php, jedro.php (minimalen router) | 30 min |
| 2 | preveri_stanje.php (z version flag sistemom) | 45 min |
| 3 | logger.php, manager_napak.php | 30 min |
| 4 | manager_pravic.php, manager_seje.php | 45 min |
| 5 | hooks.php, modul_loader.php, plugin_loader.php | 60 min |
| 6 | manager_stanja.php | 45 min |
| 7 | manager_cache.php, servis.php | 30 min |
| 8 | izvajalnik.php, provider.php | 45 min |
| 9 | Generator (parser.php, generator.php, preverjalnik.php) | 90 min |
| 10 | Testi | 120 min |

---

## 5. preveri_stanje.php – PRAVILA OBNAŠANJA

| Pravilo | Opis |
|---------|------|
| Kdaj se požene | Samo ob prvi namestitvi ali nadgradnji – nikoli ob vsakem zagonu |
| .env obnašanje | Ne prepiše obstoječe .env datoteke – samo doda manjkajoče ključe |
| Mape | Ustvari samo manjkajoče mape, obstoječih ne dotakne |
| Sočasnost | Uporablja ekskluzivno zaklepanje datotek za preprečevanje race condition |
| Fallback | Edino dovoljeno mesto za fallback logiko v celotnem sistemu |

---

## 6. KONTROLNI SEZNAM PRED ZAGONOM

- [ ] index.php kliče loader.php in nič drugega
- [ ] .htaccess obstaja in pravilno usmerja zahteve
- [ ] SISTEM/loader.php nastavi varnostne glave
- [ ] SISTEM/sistem/bootstrap.php ima try-catch za vsak korak
- [ ] SISTEM/sistem/jedro/jedro.php definira router()
- [ ] SISTEM/sistem/preveri_stanje.php ne prepiše obstoječe .env
- [ ] Version flag uporablja ekskluzivno zaklepanje
- [ ] EnvLoader.php je naložen PRED jedrom (korak 3.5)
- [ ] Jedro se naloži po zaporedju iz `02_zaklep_jedra.md` poglavje 3
- [ ] Validator se požene PRED bootstrapom

---

## 7. PREPOVEDI V VRSTNEM REDU ZAGONA

| Prepoved | Razlog |
|----------|--------|
| Router znotraj bootstrapa | Router mora biti po bootstrapu |
| Jedro kliče bootstrap | Jedro je nižji nivo |
| Bootstrap kliče loader | Loader je višji nivo |
| EnvLoader po loggerju | EnvLoader mora biti pred jedrom |
| modul_loader.php kliče plugin_loader.php | Nič ne sme biti odvisno od plugina |
| Datoteka jedra vključuje datoteke izven svojega imenika | Ohranja zaklenjenost |
| Fallback logika izven preveri_stanje.php | Polovična inicializacija ni dovoljena |

---

**KONEC DOKUMENTA**


=== 02_zaklep_jedra.md ===

---
tip: pravilo
nivo: -1
naziv: ZAKLEP JEDRA
velja_od: 2026-03-29
verzija: 5.0.0
---

# ZAKLEP JEDRA

**Ta dokument določa ZAKLENJEN VRSTNI RED JEDRA in ABSOLUTNE PREPOVEDI JEDRA.**
**Nivo -1 – podrejen `00_temeljna_pravila.md` in `01_vrstni_red_izvajanja.md`.**

V primeru konflikta med tem dokumentom in `00` ali `01`:
→ zmaga dokument z **nižjo številko**.

V primeru konflikta med tem dokumentom in katerimkoli dokumentom z nivojem 0, 2, 3, 4 ali 5:
→ zmaga **ta dokument**.

---

## 1. VLOGA DOKUMENTA

Ta dokument je EDINI VIR za:
- zaklenjen vrstni red nalaganja jedra
- prepovedi znotraj jedra
- prepovedi znotraj loaderja
- prepovedi znotraj bootstrapa
- runtime zaklep
- izjeme

Ta dokument **ne opisuje** vrstnega reda bootstrapa – to je v `01_vrstni_red_izvajanja.md`.

---

## 2. ZAKLENJEN VRSTNI RED NALAGANJA JEDRA

**Ta vrstni red je obvezen. Ne sme se spreminjati.**

| # | Datoteka | Razlog vrstnega reda |
|---|----------|----------------------|
| 1 | EnvLoader.php | DEBUG nastavitev vpliva na logger – mora biti prva |
| 2 | logger.php | Odvisen od DEBUG iz EnvLoaderja |
| 3 | detektor_zank.php | Neodvisen od ostalih |
| 4 | manager_napak.php | Neodvisen od ostalih |
| 5 | manager_stanja.php | Neodvisen od ostalih |
| 6 | manager_seje.php | Neodvisen od ostalih |
| 7 | manager_pravic.php | Neodvisen od ostalih |
| 8 | manager_cache.php | Neodvisen od ostalih |
| 9 | hooks.php | Neodvisen od ostalih |
| 10 | servis.php | Neodvisen od ostalih |
| 11 | provider.php | Neodvisen od ostalih |
| 12 | izvajalnik.php | Neodvisen od ostalih |
| 13 | jedro.php | Definira router – mora biti pred loaderji |
| 14 | modul_loader.php | Odvisen od jedra |
| 15 | plugin_loader.php | Odvisen od jedra |

**Ta vrstni red se ne spreminja. Nikoli.**

---

## 3. PREPOVEDI V JEDRU

| Prepoved |
|----------|
| Jedro ne definira konstant – konstante definira bootstrap |
| Jedro ne vključuje datotek izven svojega imenika |
| Jedro ne kliče modulov direktno |
| Jedro ne kliče pluginov direktno |
| Jedro ne izvaja poslovne logike |
| Jedro ne vsebuje fallback logike |

Jedro:
- definira okolje
- ne pozna modulov
- ne pozna pluginov
- ne pozna poslovne logike

---

## 4. PREPOVEDI V LOADERJU

Pravila loaderja so definirana v `01_vrstni_red_izvajanja.md` poglavje 3 in poglavje 7.

Ta dokument jih ne podvaja.

---

## 5. PREPOVEDI V BOOTSTRAPU

| Prepoved |
|----------|
| Sprememba vrstnega reda jedra |
| EnvLoader po katerikoli drugi datoteki jedra |
| Router znotraj bootstrapa |
| Klic preveri_stanje.php ob vsakem zagonu |
| Preskakovanje datotek jedra |
| Dodajanje novih core datotek brez posodobitve tega dokumenta |

Bootstrap mora:
- definirati konstante
- inicializirati jedro po zaklenjenem vrstnem redu
- biti transakcijski (vse ali nič)

---

## 6. RUNTIME ZAKLEP IN PREPOVED DINAMIČNIH SPREMEMB

Jedro se naloži enkrat ob zagonu. Po bootstrapu se struktura jedra ne sme spremeniti.

**Prepovedano (pri načrtovanju):**
- dodajanje novih core datotek brez posodobitve tega dokumenta
- odstranjevanje core datotek brez posodobitve tega dokumenta
- sprememba vrstnega reda nalaganja

**Prepovedano (runtime):**
- pogojno ali dinamično nalaganje jedra
- ponovno nalaganje jedra
- redefinicija komponent jedra po bootstrapu

**Dovoljeno (runtime):**
- dodajanje modulov
- dodajanje pluginov
- registracija kavljev

---

## 7. PREPOVED FALLBACK LOGIKE

Jedro ne vsebuje fallback logike.

Če datoteka jedra manjka:
- bootstrap prekine izvajanje
- sistem se ne inicializira
- delna inicializacija ni dovoljena

Edino dovoljeno mesto za fallback logiko je `preveri_stanje.php` in samo ob prvi namestitvi ali nadgradnji. Glej `01_vrstni_red_izvajanja.md` poglavje 5.

---

## 8. ZAŠČITENI KAVLJI JEDRA

Naslednji kavlji so zaščiteni in rezervirani za jedro:

- `system_init`
- `pred_router`
- `po_router`
- `bootstrap_ready`
- vsi kavlji, ki se začnejo s predpono `core_`

**Plugin ali modul ne sme registrirati kavlja z imenom iz tega seznama ali s predpono `core_`.**

Za celoten seznam kavljev glej `06_kavlji.md`.

---

## 9. IMMUTABLE REGISTRY

Funkcija `router` se definira enkrat ob nalaganju jedra.

Če funkcija ob definiciji že obstaja, se obstoječa ohrani in nova se ne registrira.

Redefinicija core funkcij po bootstrapu ni dovoljena.

---

## 10. KONSTANTE

Jedro ne definira konstant.

Bootstrap definira konstante pred nalaganjem jedra.

Jedro sme konstante:
- brati
- uporabljati

Jedro ne sme konstant:
- ustvarjati
- spreminjati

---

## 11. KAZALO DOKUMENTOV

| Dokument | Nivo | Vloga |
|----------|------|-------|
| 00_temeljna_pravila.md | -1 | Temelj |
| 00_standard_pisanja_pravil.md | 0 | Standard pisanja |
| 01_vrstni_red_izvajanja.md | -1 | Vrstni red zagona in bootstrapa |
| 02_zaklep_jedra.md | -1 | Zaklep jedra – ta dokument |
| 04_razsirljivost_sistema.md | -1 | Razširljivost |
| 05_struktura_pravil.md | -1 | Struktura pravil |
| 06_kavlji.md | 2 | Kavlji |
| 07_moduli.md | 2 | Moduli |
| 08_vmesniki.md | 2 | Vmesniki |
| 09_plugini.md | 3 | Plugini |
| 10_servisi.md | 4 | Servisi |
| 11_logiranje.md | 2 | Logiranje |
| 12_stanje.md | 2 | Stanje |
| 13_varnost.md | 2 | Varnost |
| 14_testiranje.md | 2 | Testiranje |
| 15_verzioniranje.md | 2 | Verzioniranje |
| 16_generator.md | 2 | Generator |
| 17_roadmap.md | – | Idejni dokument (ni pravilo) |

---

**KONEC DOKUMENTA – JEDRO JE ZAKLENJENO**


=== 04_razsirljivost_sistema.md ===

---
tip: pravilo
nivo: -1
naziv: RAZŠIRLJIVOST SISTEMA
velja_od: 2026-03-29
verzija: 3.0.0
---

# RAZŠIRLJIVOST SISTEMA

**Ta dokument določa ARHITEKTURO RAZŠIRLJIVOSTI – plasti, smer klicev in mehanizme.**
**Podrobnosti posameznih komponent so v svojih dokumentih (07, 09, 10, 06).**

---

## 1. PET PLASTI SISTEMA

| Plast | Vloga | Lokacija | Način spremembe |
|-------|-------|----------|-----------------|
| 1. JEDRO + SERVISI | Core mehanizmi | SISTEM/sistem/jedro/ | ZAKLENJENO |
| 2. PRAVILA | Sistemska pravila | ASTRA/razvoj/pravila/ | Ročno |
| 3. VMESNIKI | Vhod/izhod, kavlji, API | SISTEM/sistem/generirano/ | Generirano |
| 4. MODULI | Vsebinski moduli | MODULI/Modul_*/ | Generirano |
| 5. PLUGINI | Razširitve brez prikaza | PLUGINI/Plugin_Ime/ | Autodiscovery |

---

## 2. SMER KLICEV

Klic poteka izključno od višje plasti proti nižji:

```
PLUGINI → VMESNIKI → MODULI → PRAVILA → JEDRO/SERVISI
```

| Tip klica | Dovoljen |
|-----------|----------|
| Višja plast → nižja plast | DA |
| Nižja plast → višja plast | NE |
| Ista plast → ista plast (direktno) | NE – mora preko vmesnika |
| Krožne odvisnosti | NE |

---

## 3. MODULI

Za strukturo, format `modul.json`, lifecycle in data providerje glej `07_moduli.md`.

Ključna načela:
- Modul se generira iz pravil
- Modul ima navigacijo
- Modul bere podatke samo preko providerja
- Modul ne komunicira z drugim modulom direktno

---

## 4. PLUGINI

Za strukturo, format `plugin.json`, lifecycle in autodiscovery glej `09_plugini.md`.

Ključna načela:
- Plugin se zazna samodejno (autodiscovery)
- Plugin nima navigacije
- Plugin ne prikazuje vsebine
- Plugin ne sme registrirati zaščitenih kavljev jedra (seznam v `02_zaklep_jedra.md` poglavje 9)

---

## 5. SERVISI

Za lokacijo, driverje in zamenjavo driverjev glej `10_servisi.md`.

Ključno načelo: Zamenjava driverja ne zahteva spremembe kode modula ali plugina – samo spremembo konfiguracijske vrednosti.

---

## 6. KAVLJI

Za popoln seznam kavljev, prioritete in registracijo glej `06_kavlji.md`.

Ključno načelo: Kavlji so edini mehanizem za razširjanje obnašanja jedra brez spremembe jedra.

---

## 7. GENERATOR

Za komande generatorja in delovni tok glej `16_generator.md`.

Ključno načelo: Sistem se razširja s pravili, ne s kodo. Iz pravil generator ustvari kodo.

---

## 8. NAČELA RAZŠIRLJIVOSTI

| Načelo | Opis |
|--------|------|
| Odprtost | Vsak modul ali plugin se doda brez spremembe jedra |
| Zamenjljivost | Vsak driver ali provider se zamenja brez spremembe logike |
| Neodvisnost | Moduli ne vedo drug za drugega |
| Determinizem | Vrstni red nalaganja je vedno enak |
| Izolacija | Napaka v pluginu ne sme povzročiti odpovedi jedra |

---

**KONEC DOKUMENTA**


=== 05_struktura_pravil.md ===

---
tip: pravilo
nivo: -1
naziv: STRUKTURA PRAVIL
velja_od: 2026-03-29
verzija: 2.1.0
---

# STRUKTURA PRAVIL

**Ta dokument je EDINI VIR za strukturo in izvajanje pravil.**
**Format pisanja pravil je v `00_standard_pisanja_pravil.md`.**

---

## 1. OBVEZNI ELEMENTI PRAVILA

Vsako pravilo mora imeti definirane naslednje elemente:

| Element | Opis |
|---------|------|
| `input_schema` | JSON shema pričakovanega vhoda |
| `output_schema` | JSON shema pričakovanega izhoda |
| `priority` | Celo število od 1 do 1000 (1 = najvišja) |
| `timeout_ms` | Maksimalen čas izvajanja v milisekundah |
| `dependencies` | Seznam ID-jev pravil ali kavljev, od katerih je odvisno |
| `parallel` | Označba ali se pravilo sme izvajati vzporedno |
| `tests` | Seznam testnih primerov |

---

## 2. PREDLOGA

Vsa pravila se ustvarjajo iz predloge v `templates/rule_template.json`.

Pravilo brez veljavne predloge se ne registrira v sistem.

---

## 3. REGISTRACIJA

Pravila se registrirajo ob zagonu sistema.

Sistem med registracijo:
- preveri veljavnost headerja
- preveri prisotnost vseh obveznih elementov
- preveri hash v registru
- preveri odsotnost konfliktov

Če katerikoli korak registracije ne uspe → sistem se ne zažene.

---

## 4. IZVAJANJE PRAVILA

Vrstni red izvajanja za vsako pravilo:

1. Vsi kavlji `pred_pravilom` se izvedejo po prioriteti
2. Pravilo se izvede
3. Vsi kavlji `po_pravilu` se izvedejo po prioriteti

**Posebno pravilo:** Če kateri koli kavelj `pred_pravilom` vrne vrednost `null`, se pravilo ne izvede in se preskoči.

Za podrobnosti o kavljih glej `06_kavlji.md`.

---

## 5. PRIORITETE PRAVIL

| Vrednost | Pomen |
|----------|-------|
| 1 | Najvišja prioriteta – izvede se prvo |
| 500 | Privzeta prioriteta |
| 1000 | Najnižja prioriteta – izvede se zadnje |

Ko čaka več pravil:
- najprej gre tisto z najvišjo prioriteto (najnižja številka)
- pri enaki prioriteti gre tisto, ki je bilo prej registrirano

---

## 6. REGISTER PRAVIL

Lokacija: `DATOTEKE/podatki/pravila_registry.json`

Register vsebuje za vsako pravilo:
- identifikator
- hash
- prioriteto
- status (aktivno / neaktivno)
- odvisnosti

---

**KONEC DOKUMENTA**


=== 06_kavlji.md ===

---
tip: pravilo
nivo: 2
naziv: KAVLJI
velja_od: 2026-03-29
verzija: 2.1.0
---

# KAVLJI

**Ta dokument je EDINI VIR za seznam kavljev, prioritete in registracijo.**

---

## 1. ŠTIRJE OSNOVNI KAVLJI

| Kavelj | Kdaj se sproži | Vrne |
|--------|---------------|------|
| `pred_pravilom` | Neposredno pred izvajanjem pravila | Spremenjen vhod ali `null` (prekini izvajanje) |
| `po_pravilu` | Neposredno po izvajanju pravila | Spremenjen izhod |
| `ob_napaki` | Ob napaki v pravilu ali kavlju | `continue`, `abort` ali `retry` |
| `ob_spremembi` | Ob vsaki spremembi stanja | Potrditev ali napaka |

---

## 2. SISTEMSKI KAVLJI

| Kavelj | Kdaj se sproži |
|--------|---------------|
| `system_init` | Ob zagonu sistema, po nalaganju jedra |
| `pred_router` | Pred vsakim spletnim zahtevkom |
| `po_router` | Po določitvi poti, pred prikazom |
| `pred_prikaz` | Tik pred prikazom strani |
| `po_prikaz` | Po prikazu strani |
| `pred_generiranje` | Preden generator začne |
| `po_generiranju` | Po zaključku generiranja |
| `head_assets` | Pri sestavljanju glave HTML dokumenta |
| `footer_assets` | Tik pred zapiralno oznako telesa |
| `routes` | Ob nalaganju in sestavljanju poti |
| `preveri_pravice` | Ob vsakem preverjanju pravic |
| `bootstrap_ready` | Po zaključku bootstrapa |

---

## 3. ZAŠČITENI KAVLJI JEDRA

Naslednji kavlji so zaklenjeni in rezervirani za jedro:
- `system_init`
- `pred_router`
- `po_router`
- `bootstrap_ready`
- vsi kavlji s predpono `core_`

Plugin ali modul ne sme registrirati kavlja z imenom iz tega seznama ali s predpono `core_`.

---

## 4. PRIORITETE

Prioriteta določa vrstni red izvajanja kavljev iste vrste.

| Vrednost | Pomen |
|----------|-------|
| `-100` do `-1` | Visoka prioriteta – izvede se prej |
| `0` | Privzeta prioriteta |
| `1` do `100` | Nizka prioriteta – izvede se kasneje |

**Pravilo:** Kavlji se izvajajo od najmanjše vrednosti k največji (`-50` → `0` → `50`).

---

## 5. VERIŽENJE

Kavlji iste vrste se izvajajo v verigi:
- vsak kavelj prejme izhod prejšnjega kot vhod
- če kavelj vrne napako, se veriga prekine
- ob prekinitvi se sproži kavelj `ob_napaki`

---

## 6. REGISTRACIJA KAVLJA

Kavlji se registrirajo v `plugin.json` ali `modul.json` z naslednjimi podatki:

```
{
  "hooks": [
    { "hook": "ime_kavlja", "priority": 0 }
  ]
}
```

Vsak registriran kavelj mora imeti:
- veljavno ime (ne sme biti zaščiteni kavelj jedra)
- prioriteto (privzeto 0)

---

**KONEC DOKUMENTA**


=== 07_moduli.md ===

---
tip: pravilo
nivo: 2
naziv: MODULI
velja_od: 2026-03-29
verzija: 2.0.0
---

# MODULI

**Ta dokument je EDINI VIR za strukturo in delovanje modulov.**

---

## 1. STRUKTURA MAPE MODULA

```
MODULI/Modul_Ime/
├── modul.json       ← meta podatki (obvezno)
├── init.php         ← lifecycle metode (obvezno)
├── src/             ← izvorna koda modula
└── migracije/       ← migracijske skripte (če obstajajo)
```

---

## 2. modul.json – OBVEZNA POLJA

Format (JSON podatkovna struktura):

```
{
  "ime": "Modul_Codex",
  "verzija": "1.0.0",
  "min_sistem": "1.0.0",
  "max_sistem": "2.0.0",
  "opis": "Kratek opis modula",
  "provider": "filesystem",
  "skladisca": ["DATOTEKE", "MEDIA"],
  "navigacija": [
    { "title": "Naziv menija", "url": "/pot", "role": "S2" }
  ],
  "odvisnosti": []
}
```

Polja `ime`, `verzija`, `min_sistem`, `max_sistem` in `provider` so obvezna.

---

## 3. DATA PROVIDER

Modul ne bere podatkov direktno. Bere izključno preko providerja.

| Provider | Vloga |
|----------|-------|
| `filesystem` | Bere .md, .json, .txt datoteke |
| `database` | Bere iz podatkovne baze |
| `api` | Bere iz zunanjega API-ja |
| `ai` | Generira vsebino z umetno inteligenco |

Zamenjava providerja pomeni isto logiko modula z drugačnim virom podatkov.

---

## 4. MULTI STORAGE

Modul sme používat več skladišč hkrati:

| Skladišče | Vloga |
|-----------|-------|
| `DATOTEKE` | Vsebina (generirana ali ročna) |
| `MEDIA` | Slike, videi, priponke |
| `CACHE` | Začasne datoteke |

---

## 5. TEMPLATE OVERRIDE

Sistem išče predlogo za prikaz modula v naslednjem vrstnem redu:

1. `GLOBALNO/layout/moduli/ime_modula.php` – če obstaja, se ta uporabi
2. `GLOBALNO/layout/osnova.php` – privzeti layout

---

## 6. LIFECYCLE

V `init.php` mora biti definiran razred z naslednjimi metodami:

| Metoda | Kdaj se kliče |
|--------|---------------|
| `install()` | Ob prvi namestitvi modula |
| `update($staraVerzija, $novaVerzija)` | Ko se verzija modula spremeni |
| `uninstall()` | Ob odstranitvi modula |
| `enable()` | Ko se modul omogoči |
| `disable()` | Ko se modul onemogoči |

Stanje modulov se hrani v `DATOTEKE/podatki/stanje_modulov.json`.

---

## 7. NAPREDEK UPORABNIKA

### 7.1. Struktura napredka (JSON podatkovna struktura)

```
{
  "cikel": "identifikator_cikla",
  "user_id": "identifikator_uporabnika",
  "current_day": 2,
  "completed_days": [1],
  "started_at": "YYYY-MM-DD",
  "last_opened": "YYYY-MM-DD",
  "streak": 2,
  "completed_cycle": false,
  "unlocked_premium": false
}
```

### 7.2. Pravila streaka

- Streak se poveča, če uporabnik odpre cikel vsak dan zaporedoma
- Streak se ponastavi, če uporabnik zamudi en dan
- Streak se beleži samo za aktivne cikle

---

**KONEC DOKUMENTA**


=== 08_vmesniki.md ===

---
tip: pravilo
nivo: 2
naziv: VMESNIKI
velja_od: 2026-03-29
verzija: 2.0.0
---

# VMESNIKI

**Ta dokument določa VMESNIKE – generirane vhodno-izhodne točke sistema.**

---

## 1. GENERIRANE DATOTEKE

Vmesniki se generirajo v `SISTEM/sistem/generirano/`:

| Datoteka | Vsebina |
|----------|---------|
| `moduli.php` | Register vseh aktivnih modulov |
| `plugini.php` | Register vseh aktivnih pluginov |
| `skladisca.php` | Struktura skladišč |
| `navigacija.php` | Meniji in navigacijske povezave |
| `pravila.php` | Register pravil |
| `kavlji.php` | Register kavljev |

Generirane datoteke se ne urejajo ročno. Vsaka ročna sprememba se prepiše ob naslednjem generiranju.

---

## 2. API

Vsak API vmesnik ima fiksno shemo.

Sprememba sheme pomeni breaking change in zahteva novo glavno verzijo.

Vsi API klici se logirajo: kdo je klical, kaj je klical, kdaj in kakšen je bil rezultat.

---

## 3. ROUTER – URL STRUKTURA

### 3.1. Definirani URL vzorci

| URL | Pomen |
|-----|-------|
| `/` | Začetna stran |
| `/codex` | Seznam vseh codex zapisov |
| `/codex/{id}` | Posamezen codex zapis |
| `/cikli` | Seznam vseh ciklov |
| `/cikel/{slug}` | Prvi dan cikla |
| `/cikel/{slug}/{dan}` | Določen dan cikla |
| `/branja` | Seznam branj |
| `/branje/{id}` | Posamezno branje |
| `/manifesti` | Seznam manifestov |
| `/manifest/{id}` | Posamezen manifest |
| `/admin` | Administrativni vmesnik |
| `/uporabnik` | Profil uporabnika |
| `/premium` | Premium vsebina |
| `/health` | Endpoint za preverjanje stanja sistema |

### 3.2. Logika routerja (opisno)

Router prejme URI in:
1. Očisti URI (odstrani odvečne poševnice)
2. Razdeli URI na segmente
3. Na podlagi prvega segmenta pokliče ustrezno funkcijo prikaza
4. Če segment ne ustreza nobenemu vzorcu → prikaže napako 404

---

## 4. SCHEDULER – ČASOVNIKI

### 4.1. Podprte vrste

Sistem podpira:
- **Odložene akcije** – izvedba po preteku določenega časa
- **Periodična pravila** – izvedba v rednih intervalih
- **Časovno pogojena pravila** – izvedba ob določeni uri ali datumu

### 4.2. Izračun trenutnega dne cikla (opisno)

Trenutni dan = število polnih dni od začetnega datuma cikla + 1.

### 4.3. Pravila odpiranja dni cikla

| Pogoj | Status dne |
|-------|-----------|
| Začetni datum je v prihodnosti | Zaklenjen |
| Dan je večji od trenutnega dne | Zaklenjen |
| Dan je premium in uporabnik nima dostopa | Zaklenjen |
| Nobeden od zgornjih pogojev ne velja | Odklenjen |

---

## 5. KONFIGURACIJA

Konfiguracija je ločena od kode in prihaja iz:
- okoljskih spremenljivk
- datoteke `.env`
- centralne konfiguracijske storitve

Vsaka sprememba konfiguracije sproži kavelj `ob_spremembi`.

---

## 6. ZUNANJE INTEGRACIJE

Vsaka zunanja integracija mora imeti definirano:
- **timeout** – maksimalen čas čakanja na odgovor
- **retry** – število ponovitev ob neuspešnem klicu
- **circuit breaker** – mehanizem za zaustavitev klicev ob odpovedi zunanje storitve

Vsak zunanji vhod se validira pred vstopom v sistem.

---

## 7. CLI

Za komande generatorja (CLI) glej `16_generator.md`.

---

**KONEC DOKUMENTA**


=== 09_plugini.md ===

---
tip: pravilo
nivo: 3
naziv: PLUGINI
velja_od: 2026-03-29
verzija: 2.0.0
---

# PLUGINI

**Ta dokument je EDINI VIR za strukturo in delovanje pluginov.**

---

## 1. STRUKTURA MAPE PLUGINA

```
PLUGINI/Plugin_Ime/
├── plugin.json      ← meta podatki (obvezno)
├── init.php         ← lifecycle metode (obvezno)
├── src/             ← izvorna koda plugina
└── migracije/       ← migracijske skripte (če obstajajo)
```

---

## 2. plugin.json – OBVEZNA POLJA

Format (JSON podatkovna struktura):

```
{
  "ime": "Plugin_SEO",
  "verzija": "1.0.0",
  "min_sistem": "1.0.0",
  "max_sistem": "2.0.0",
  "opis": "Kratek opis plugina",
  "hooks": [
    { "hook": "head_assets", "priority": -10 },
    { "hook": "pred_prikaz", "priority": 0 }
  ],
  "servisi": ["servis_seo"],
  "odvisnosti": []
}
```

Polja `ime`, `verzija`, `min_sistem` in `max_sistem` so obvezna.

---

## 3. AUTODISCOVERY

Sistem samodejno zazna plugin, ko:

| Pogoj | Posledica |
|-------|-----------|
| Obstaja `plugin.json` | Plugin se registrira |
| Obstaja `init.php` | Požene se ob zagonu |
| Obstaja mapa `migracije/` | Migracije se poženejo ob prvi namestitvi |

Ni potrebne nobene dodatne konfiguracije. Zadostuje postavitev mape v `PLUGINI/`.

---

## 4. LIFECYCLE

| Metoda | Kdaj se kliče |
|--------|---------------|
| `install()` | Ob prvi namestitvi plugina |
| `update($staraVerzija, $novaVerzija)` | Ko se verzija plugina spremeni |
| `uninstall()` | Ob odstranitvi plugina |
| `enable()` | Ko se plugin omogoči |
| `disable()` | Ko se plugin onemogoči |

---

## 5. RAZLIKA MED MODULOM IN PLUGINOM

| Lastnost | Modul | Plugin |
|----------|-------|--------|
| Prikazuje vsebino | DA | NE |
| Ima navigacijo | DA | NE |
| Uporablja data providerje | DA | NE |
| Način zaznavanja | Generirano | Autodiscovery |
| Hierarhični nivo | 2 | 3 |

---

## 6. OMEJITVE PLUGINOV

Plugin ne sme:
- registrirati zaščitenih kavljev jedra (seznam v `02_zaklep_jedra.md` poglavje 9 in `06_kavlji.md` poglavje 3)
- direktno klicati modulov
- direktno klicati jedra
- imeti navigacije

---

**KONEC DOKUMENTA**


=== 10_servisi.md ===

---
tip: pravilo
nivo: 4
naziv: SERVISI
velja_od: 2026-03-29
verzija: 2.0.0
---

# SERVISI

**Ta dokument je EDINI VIR za strukturo servisov in zamenjavo driverjev.**

---

## 1. LOKACIJA

```
SISTEM/sistem/servisi/
├── servis_cache.php
├── servis_mail.php
├── servis_ai.php
├── servis_queue.php
└── servis_db.php
```

---

## 2. ZAMENJAVA DRIVERJA

Driver za vsak servis se določi v konfiguracijski datoteki `.env`.

| Servis | Konfiguracijska ključ | Primeri vrednosti |
|--------|----------------------|-------------------|
| Cache | `CACHE_DRIVER` | `file`, `redis`, `memcached` |
| Mail | `MAIL_DRIVER` | `smtp`, `mailgun`, `sendgrid` |
| AI | `AI_DRIVER` | `openai`, `anthropic`, `local` |
| Queue | `QUEUE_DRIVER` | `sync`, `redis`, `rabbitmq` |
| Baza | `DB_DRIVER` | `mysql`, `postgresql`, `sqlite` |

**Pravilo:** Zamenjava driverja ne zahteva spremembe kode modulov ali pluginov – samo spremembo konfiguracijske vrednosti.

---

## 3. NAČELA SERVISOV

| Načelo | Opis |
|--------|------|
| Izolacija | Servis je neodvisen od modulov in pluginov |
| Zamenjljivost | Vsak driver je zamenljiv brez spremembe logike |
| Enoten vmesnik | Vsi driverji istega servisa imajo enak vmesnik |
| Konfigurabilnost | Vse nastavitve prihajajo iz `.env` |

---

## 4. REGISTRACIJA SERVISOV

Servisi se registrirajo med bootstrapom v koraku 3.10 (glej `01_vrstni_red_izvajanja.md` poglavje 1).

Plugin sme deklarirati lastne servise v `plugin.json` pod ključem `servisi`.

---

**KONEC DOKUMENTA**


=== 11_logiranje.md ===

---
tip: pravilo
nivo: 2
naziv: LOGIRANJE
velja_od: 2026-03-29
verzija: 2.1.0
---

# LOGIRANJE

**Ta dokument je EDINI VIR za format in pravila logiranja.**
**Format dogodkov event sourcinga je v `12_stanje.md`.**

---

## 1. VRSTE LOGOV IN LOKACIJE

| Vrsta | Datoteka | Vsebina |
|-------|----------|---------|
| Sistemski logi | `DATOTEKE/podatki/system.log` | debug, info, warning, error, critical |
| Revizijska sled | `DATOTEKE/podatki/audit.log` | kdo je kaj storil in kdaj |
| Dogodki stanja | `DATOTEKE/podatki/events.log` | event sourcing – glej `12_stanje.md` |

---

## 2. FORMAT SISTEMSKEGA LOGA

Vsak zapis v `system.log` ima naslednjo strukturo (JSON):

```
{
  "timestamp": "YYYY-MM-DDTHH:MM:SSZ",
  "level": "info",
  "layer": "jedro",
  "component": "ime_komponente",
  "message": "Opis dogajanja",
  "request_id": "unikatni_id_zahtevka"
}
```

---

## 3. FORMAT REVIZIJSKEGA LOGA

Vsak zapis v `audit.log` mora vsebovati:
- čas akcije
- identiteto izvajalca (uporabnik ali sistem)
- vrsto akcije
- prizadeti objekt ali entiteto
- rezultat akcije

---

## 4. NIVOJI LOGIRANJA

| Nivo | Kdaj se uporablja |
|------|------------------|
| `debug` | Podrobnosti za razvijalce – samo v razvojnem okolju |
| `info` | Normalno delovanje, uspešne akcije |
| `warning` | Nepričakovano stanje, ki ne ustavi sistema |
| `error` | Napaka, ki onemogoča določeno funkcijo |
| `critical` | Napaka, ki ustavi sistem ali zahteva takojšnje ukrepanje |

---

## 5. PRAVILA LOGIRANJA

| Pravilo | Opis |
|---------|------|
| DEBUG logi | Samo v razvojnem in testnem okolju, nikoli v produkciji |
| Request ID | Vsak zahtevek dobi unikaten ID, ki se vleče skozi vse loge |
| Občutljivi podatki | Gesla, žetoni in osebni podatki se ne logirajo |
| Rotacija | Logoteke se rotirajo, da ne zapolnijo diska |

---

**KONEC DOKUMENTA**


=== 12_stanje.md ===

---
tip: pravilo
nivo: 2
naziv: STANJE
velja_od: 2026-03-29
verzija: 2.0.0
---

# STANJE

**Ta dokument je EDINI VIR za upravljanje stanja in format dogodkov.**

---

## 1. CENTRALNO STANJE

Stanje sistema je centralno shranjeno v jedru.

Pravila, moduli in plugini stanja ne spreminjajo direktno. Vsaka sprememba stanja gre skozi event sourcing mehanizem.

---

## 2. EVENT SOURCING

Vsaka sprememba stanja se zabeleži kot **dogodek**.

Vsak dogodek vsebuje:
- čas nastanka (ISO 8601)
- vir spremembe (pravilo, kavelj, uporabnik ali sistem)
- staro vrednost
- novo vrednost
- kontekst (razlog spremembe)

Trenutno stanje sistema = replay vseh dogodkov od zadnjega posnetka (snapshot).

---

## 3. FORMAT DOGODKA

Vsak dogodek v `events.log` ima naslednjo strukturo (JSON):

```
{
  "timestamp": "YYYY-MM-DDTHH:MM:SSZ",
  "event": "komponenta.akcija",
  "source": "Vir_spremembe",
  "user": "identifikator_uporabnika",
  "data": {
    "kljuc": "vrednost"
  }
}
```

---

## 4. STANDARD POIMENOVANJA DOGODKOV

Format: `{komponenta}.{akcija}`

| Komponenta | Dovoljene akcije |
|------------|-----------------|
| `modul_ime` | `created`, `updated`, `deleted` |
| `system` | `started`, `shutdown`, `config_changed` |
| `user` | `logged_in`, `logged_out`, `registered` |
| `rule` | `executed`, `failed`, `timeout` |
| `plugin` | `installed`, `updated`, `uninstalled` |

---

## 5. SHRANJEVANJE DOGODKOV

Dogodki se shranjujejo v `DATOTEKE/podatki/events.log` v načinu append-only.

Append-only pomeni: zapisov se ne briše in ne ureja. Vsaka sprememba je nov zapis.

---

## 6. TRANSAKCIONALNOST

Vsaka sprememba stanja je:
- **Atomarna** – bodisi uspe v celoti bodisi se ne zgodi nič
- **Rollback** – ob napaki se vse spremembe razveljavijo
- **Pre-commit** – dogodek se zapiše v log pred izvedbo spremembe

---

## 7. SOČASNOST (CONCURRENCY)

Sistem uporablja optimistično sočasnost:

- Vsako stanje ima polje `version` (celo število)
- Pred posodobitvijo se preveri, da se `version` ni spremenila od branja
- Če se je `version` spremenila → pride do konflikta → sistem poskusi znova (retry)
- Ob prekoračitvi števila ponovitev → napaka

---

**KONEC DOKUMENTA**


=== 13_varnost.md ===

---
tip: pravilo
nivo: 2
naziv: VARNOST
velja_od: 2026-03-29
verzija: 2.0.0
---

# VARNOST

**Ta dokument določa VARNOSTNE MEHANIZME sistema.**

---

## 1. TRI STOPNJE VARNOSTI

| Stopnja | Mehanizem | Opis |
|---------|-----------|------|
| 1 | Validacija vhodov | Vsak zunanji vhod se preveri pred vstopom v sistem |
| 2 | Omejitev vpliva | Timeout, omejitev pomnilnika, omejitev dostopa do datotek |
| 3 | Samodejna obnovitev | Ob napaki sistem poskusi vzpostaviti normalno delovanje |

---

## 2. PREPREČEVANJE DIREKTNEGA DOSTOPA

Vse datoteke v `SISTEM/sistem/` morajo ob klicu preveriti prisotnost varnostne konstante.

Ob odsotnosti konstante se zahtevek zavrne s kodo 403.

Edina izjema je `SISTEM/loader.php`, ki je javna vstopna točka.

---

## 3. BELE IN ČRNE LISTE

Bele in črne liste se definirajo v `ASTRA/razvoj/pravila/varnost.md` in generirajo v `SISTEM/sistem/generirano/`.

Jedro preverja belo in črno listo pred vsako izvedbo pravila ali kavlja.

---

## 4. MODEL PRAVIC (RBAC)

### 4.1. Vloge

| Vloga | Pomen | Pravice |
|-------|-------|---------|
| `S0` | Sistem | Vse pravice |
| `S1` | Administrator | Sistem, moduli, plugini, uporabniki |
| `S2` | Urednik | Vsebina, komentarji |
| `S3` | Uporabnik | Branje vsebine, ustvarjanje komentarjev |
| `S4` | Gost | Samo javna vsebina |
| `S5` | Blokiran | Brez pravic |

### 4.2. Preverjanje pravic

Sistem pred vsako zaščiteno akcijo preveri, ali ima trenutni uporabnik ustrezno vlogo.

Pravice se preverjajo hierarhično: višja vloga vključuje pravice nižjih vlog.

---

## 5. OMEJEVANJE ZAHTEVKOV (RATE LIMITING)

| Vloga | Omejitev |
|-------|----------|
| Gost | 30 zahtevkov na minuto |
| Uporabnik | 60 zahtevkov na minuto |
| VIP | 120 zahtevkov na minuto |
| Administrator | Brez omejitve |

Ob prekoračitvi se zahtevek zavrne s kodo 429.

---

## 6. ZAŠČITA PRED CSRF

Vsak obrazec, ki sproži spremembo stanja, mora vsebovati CSRF žeton.

Sistem pred obdelavo zahtevka preveri veljavnost žetona. Ob neskladnosti se zahtevek zavrne.

---

## 7. SANITIZACIJA VHODA

Vsi zunanji vhodi (POST, GET, API) se sanitizirajo pred vstopom v poslovno logiko.

Sanitizacija pomeni: odstranitev nevarnih znakov, normalizacija kodiranja, omejitev dolžine.

---

## 8. OMEJITVE VIROV

| Parameter | Omejitev |
|-----------|----------|
| Pomnilnik na zahtevek | 128 MB |
| Maksimalni čas izvajanja | 5000 ms |
| Maksimalna globina dogodkov | 100 |
| Maksimalna dolžina verige kavljev | 50 |

Ob prekoračitvi katerekoli omejitve se zahtevek prekine in zabeleži napaka.

---

**KONEC DOKUMENTA**


=== 14_testiranje.md ===

---
tip: pravilo
nivo: 2
naziv: TESTIRANJE
velja_od: 2026-03-29
verzija: 2.1.0
nadomešča: 13_testiranje.md verzija 1.0.0
---

# TESTIRANJE

**Ta dokument določa TESTIRANJE sistema.**

---

## 1. OBVEZNI TESTI

| Vrsta | Obvezno za | Lokacija |
|-------|------------|----------|
| Enotni testi | parser, generator, preverjalnik, jedro, logger | `ASTRA/razvoj/testi/enotni/` |
| Integracijski testi | modul_loader, plugin_loader, router, kavlji | `ASTRA/razvoj/testi/integracijski/` |
| Robni testi | neveljavni vhodi, manjkajoči moduli, krožne odvisnosti | `ASTRA/razvoj/testi/robni/` |
| Obremenitveni testi | predpomnilnik, obremenitev, sočasni uporabniki | `ASTRA/razvoj/testi/obremenitveni/` |

---

## 2. STRUKTURA TESTOV

```
ASTRA/razvoj/testi/
├── enotni/
│   ├── test_parser.php
│   ├── test_generator.php
│   ├── test_preverjalnik.php
│   ├── test_jedro.php
│   └── test_logger.php
├── integracijski/
│   ├── test_modul_loader.php
│   ├── test_plugin_loader.php
│   ├── test_router.php
│   └── test_kavlji.php
├── robni/
│   ├── test_neveljavni_vhodi.php
│   ├── test_manjkajoci_moduli.php
│   └── test_kroznih_odvisnosti.php
└── obremenitveni/
    ├── test_load_100_users.php
    └── test_cache_stress.php
```

---

## 3. KDAJ SE TESTI POŽENEJO

| Dogodek | Akcija |
|---------|--------|
| Ob ukazu `--check` generatorja | Testi se preverijo pred generiranjem |
| Po spremembi pravila | Avtomatsko testiranje prizadete komponente |
| Pred združitvijo v glavno vejo | Vsi testi morajo uspeti |

---

## 4. TESTNA OKOLJA

| Okolje | Namen | DEBUG | Kateri testi |
|--------|-------|-------|--------------|
| Razvoj (localhost) | Aktivni razvoj | Vklopljen | Vsi |
| Staging (test.domena) | Predprodukcijsko testiranje | Vklopljen | Vsi razen obremenitvenih |
| Produkcija (domena) | Živo okolje | Izklopljen | Samo enotni in integracijski |

---

## 5. PRAVILA TESTIRANJA

| Pravilo | Opis |
|---------|------|
| Neodvisnost | Vsak test je neodvisen od ostalih |
| Ponovljivost | Test vrne vedno enak rezultat pri enakem vhodu |
| Enostavnost | En test preverja eno stvar |
| Kritičnost | Neuspešen test blokira deployment |

---

**KONEC DOKUMENTA**


=== 15_verzioniranje.md ===

---
tip: pravilo
nivo: 2
naziv: VERZIONIRANJE
velja_od: 2026-03-29
verzija: 2.0.0
---

# VERZIONIRANJE

**Ta dokument je EDINI VIR za pravila verzioniranja in migracije.**

---

## 1. SEMANTIČNO VERZIONIRANJE (X.Y.Z)

| Del verzije | Kdaj se poveča | Primeri |
|-------------|---------------|---------|
| X – glavna | Breaking change, sprememba jedra, nekompatibilna sprememba API | 1.0.0 → 2.0.0 |
| Y – podrazličica | Nova pravila, novi kavlji, novi moduli, novi plugini | 1.0.0 → 1.1.0 |
| Z – popravek | Popravek napake brez spremembe vmesnika | 1.0.0 → 1.0.1 |

---

## 2. LOKACIJA VERZIJE JEDRA

Verzija jedra se hrani v datoteki `SISTEM/verzija` kot navadno besedilo.

---

## 3. VERZIJA MODULOV IN PLUGINOV

Verzija modula ali plugina se hrani v `modul.json` oziroma `plugin.json` pod ključema `verzija`, `min_sistem` in `max_sistem`.

---

## 4. PREVERJANJE KOMPATIBILNOSTI

| Primerjava | Rezultat |
|------------|----------|
| Verzija sistema znotraj razpona min–max | Naloži se |
| Verzija sistema pod minimalno | Blokada, modul/plugin se ne naloži |
| Verzija sistema nad maksimalno (brez `strict`) | Opozorilo, naloži se |
| Verzija sistema nad maksimalno (z `strict: true`) | Blokada |

---

## 5. CHANGELOG

Vsaka nova verzija sistema ima zapis v `ASTRA/razvoj/changelog.md`.

Format zapisa:

```
## X.Y.Z (YYYY-MM-DD)

### Dodano
- opis novosti

### Popravljeno
- opis popravka

### Odstranjeno
- opis odstranjenega
```

---

## 6. MIGRACIJE

### 6.1. Kdaj je migracija potrebna

Migracija je obvezna, ko:
- se spremeni format ID-jev
- se preimenuje mapa ali datoteka s podatki
- se dodajo nova obvezna polja v konfiguracijo
- se preimenuje zbirka ali ključ v podatkovni bazi

### 6.2. Lokacija migracijskih skript

```
MODULI/Modul_Ime/migracije/
├── 1.0.0_to_1.1.0.php
└── 1.1.0_to_1.2.0.php

PLUGINI/Plugin_Ime/migracije/
└── 1.0.0_to_1.1.0.php
```

### 6.3. Struktura migracijske skripte (opisno)

Vsaka migracijska skripta mora vsebovati:
- metodo za nadgradnjo (`up`) – izvede spremembe
- metodo za vrnitev (`down`) – razveljavi spremembe

Obe metodi sta obvezni.

Migracije se izvajajo v naraščajočem vrstnem redu verzij.

---

**KONEC DOKUMENTA**


=== 16_generator.md ===

---
tip: pravilo
nivo: 2
naziv: GENERATOR
velja_od: 2026-03-29
verzija: 2.0.0
---

# GENERATOR

**Ta dokument je EDINI VIR za generator – komande, strukturo in delovni tok.**

---

## 1. LOKACIJA ORODIJ

```
ASTRA/razvoj/orodja/
├── parser.php          ← razčlenjuje .md pravila v strukturo
├── generator.php       ← generira kodo iz razčlenjene strukture
└── preverjalnik.php    ← preverja veljavnost pravil
```

---

## 2. KOMANDE

| Komanda | Opis |
|---------|------|
| `generator.php --full` | Generira vse (moduli, plugini, kavlji, navigacija) |
| `generator.php --modul=ImeModula` | Generira samo določen modul |
| `generator.php --plugin=ImePlugina` | Generira samo določen plugin |
| `generator.php --check` | Samo preveri pravila brez generiranja |

---

## 3. KAJ GENERATOR USTVARI

| Vhodno pravilo | Generirana izhod |
|----------------|-----------------|
| `00_temeljna_pravila.md` | Struktura celotnega sistema |
| `07_moduli.md` | `SISTEM/sistem/generirano/moduli.php` |
| `09_plugini.md` | `SISTEM/sistem/generirano/plugini.php` |
| `06_kavlji.md` | `SISTEM/sistem/generirano/kavlji.php` |
| Vse `*.md` v navodilih | Navigacija in povezovalne skripte |

---

## 4. DELOVNI TOK GENERATORJA

Generator deluje v naslednjem zaporedju:

1. Prebere vse datoteke v `ASTRA/razvoj/pravila/` in `ASTRA/razvoj/navodila/`
2. Za vsako pravilo pokliče parser, ki vrne strukturirano obliko
3. Na podlagi tipa in plasti pravila izbere ustrezen generator
4. Generator ustvari ustrezne datoteke
5. Preverjalnik potrdi veljavnost generiranih datotek
6. Pri ukazu `--check` se korak 4 preskoči – samo preverja, ne generira

---

## 5. TIPI GENERATORJEV

| Generator | Ustvari |
|-----------|---------|
| MapaGenerator | Strukturo map za module in plugine |
| PhpGenerator | PHP datoteke v `SISTEM/sistem/generirano/` |
| JsonGenerator | Konfiguracijske JSON datoteke |
| KonfigGenerator | Konfiguracijske datoteke okolja |

---

## 6. PREVERJANJE PRED GENERIRANJEM

Preden generator ustvari karkoli, preveri:
- Ali so vsa pravila veljavna (hash, header, nivo)
- Ali so vse odvisnosti izpolnjene
- Ali so prioritete veljavne

---

## 7. PREVERJANJE PO GENERIRANJU

Po generiranju preverjalnik potrdi:
- Ali se generirana koda lahko naloži brez napak
- Ali ni krožnih odvisnosti
- Ali so vse potrebne datoteke ustvarjene

---

## 8. PARSER (OPISNO)

Parser prebere `.md` datoteko pravila in iz nje izvleče:
- hierarhično plast (layer)
- tip pravila
- strukturo map
- seznam datotek za generiranje
- konfiguracijske vrednosti
- seznam funkcij in obnašanj

Rezultat parserja je strukturirana oblika, ki jo generator uporabi za ustvarjanje kode.

---

**KONEC DOKUMENTA**


=== 17_roadmap.md ===

---
tip: ideja
status: v-teku
ustvarjeno: 2026-03-29
---

# ROADMAP – IDEJE ZA PRIHODNOST

**Ta dokument NI del pravil sistema. Je idejni dokument za načrtovanje.**
**Ne vpliva na delovanje sistema. Ni podvržen validaciji.**

---

## Q2 2026 – Stabilen sistem z osnovnimi funkcijami

- [x] Arhitektura po plasteh (5 plasti)
- [x] Generator iz pravil
- [x] Plugin sistem (autodiscovery)
- [x] Modul sistem (generiran)
- [ ] Parser za oznake vsebine
- [ ] Cache sistem (napredna implementacija)
- [ ] Prvi moduli: Codex, Cikli

---

## Q3 2026 – Uporabniška izkušnja in vsebina

- [ ] Admin vmesnik (UI/UX)
- [ ] WYSIWYG editor
- [ ] SEO (meta oznake, sitemap)
- [ ] Iskalnik po vsebini
- [ ] Napredek skozi cikle (streak, completed_days)
- [ ] Uporabniški profil
- [ ] Prve teme (predloge)
- [ ] Prva vsebina: cikli, codex zapisi

---

## Q4 2026 – Monetizacija in skaliranje

- [ ] Plačilni sistem
- [ ] Premium vsebina z zaklepanjem
- [ ] Paketi (brezplačni, osnovni, pro)
- [ ] Registracija z email potrditvijo
- [ ] Pozabljeno geslo
- [ ] Afiliacijski program
- [ ] PWA (namestljiva spletna aplikacija)

---

## Q1 2027 – Skupnost in rast

- [ ] Komentarji z odgovori
- [ ] Ocenjevanje vsebine
- [ ] Newsletter
- [ ] RSS feed
- [ ] API za zunanje integracije

---

## Q2 2027 – Avtomatizacija in širitev

- [ ] Webhooks
- [ ] Import/Export
- [ ] Podpora za več jezikov
- [ ] White-label licenca

---

**KONEC DOKUMENTA**


