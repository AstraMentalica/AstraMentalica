# 🧠 CLAUDE DELOVNI DNEVNIK
## AstraMentalica · MODULI repozitorij

> **NAVODILO ZA VSAKEGA CLAUDEA:**
> Preden se lotiš česarkoli — preberi to datoteko do konca.
> Ko končaš nalogo ali se ustaviš — zapiši kaj si naredil in kaj ostane.
> Ne diraj v manifeste, module ali strukturo brez da razumeš kontekst spodaj.

---

## 📋 KONTEKST PROJEKTA

**Projekt:** AstraMentalica — mistična platforma z modularno arhitekturo  
**Repozitorij:** `AstraMentalica/AstraMentalica` (grana `main`)  
**Avtor:** Damir Šafarič  
**Datum zadnjega vpisa:** 2026-06-27  

---

## 🏗️ ARHITEKTURA (NE SPREMINJAJ BREZ RAZLOGA)

### Struktura modula
```
MODULI/ImeModula/
├── modul.php              ← edina vstopna točka
├── podatki/
│   ├── manifest.json      ← identiteta (kanonični format)
│   ├── api.json           ← HTTP poti
│   ├── izhod.json         ← vhod/odvisnosti
│   ├── modul.md           ← dokumentacija
│   └── manifest.webmanifest ← PWA (kjer je ima_prikaz: true)
├── cache/
└── temp/
```

### Kanonični manifest.json format
```json
{
  "_id": "imemodula",
  "_verzija": "1.0.0",
  "modul": { "id", "ime", "tip", "nivo", "verzija", "aktiviran",
             "vstopna", "opis", "status", "demo", "zacasen" },
  "dostop": { "minimalna_vloga", "plan", "javno_vidno", "placljivo",
              "otroski", "vidnost", "dovoljenja" },
  "cache": { "omogocen", "ttl" },
  "ui": { "ima_prikaz", "ikona", "barva", "kategorija",
          "dovoljene_postavitve", "tags", "jeziki" },
  "izvajanje": { "tip", "api_only", "interval", "ob_zagonu",
                 "prioriteta", "bootstrap" },
  "migracije": { "obstajajo", "zadnja" },
  "integriteta": { "checksum" },
  "log": { "omogocen", "nivo" },
  "cas": { "ustvarjen", "posodobljen", "zadnji_zagon" }
}
```

### Pravila (ZAKLENJENA)
- Modul pozna samo svojo mapo in `Modul_Bridge`
- Brez `require_once` na sistemske poti
- Brez `$_POST`, `$_GET`, `$_SESSION` direktno
- Brez HTML v `modul.php`
- Vse zunanje klice gre skozi `Modul_Bridge::`

---

## 📦 STANJE MODULOV (2026-06-25)

### V repozitoriju: 60 modulov
Vsi imajo kanonični `manifest.json` + `api.json` + `izhod.json` + `modul.md` + `manifest.webmanifest`

| Skupina | Moduli |
|---------|--------|
| **Stabilni** | Devorum, Energetica, Jyotir, Lapidaria, LiberUmbrae, Lunaris, Mystaia, MysticaMesoAmericana, NordicaMystica, NumerariumCosmicum, Numyra, Occultum, OraculumVisionis, Pranaymica, Tarot |
| **Testni** | AegypticaArcana, Aetheris, Animaris, BotanicaSacra, Celestara, CorpusMysticum, CosmicaScientia, QiVitalis, Sephirotica, Seraphica, Somnaris, Stelaris, Synera, Transmutaria, UmbraeCodex, ViaAnimae, VibraMystica |
| **Stabilen (poravnan)** | Aeternum, AlchymiaAurea, Angelarium, AuroraMystica, Chakrarium, Codex, CodexAntiqua, Crystallum, Daemontica, Djotis, GeometricaSacra, Herbarica, Hieroglyphicus, Kabbaloria, Labyrinthus, MedicinaOrientalis, Meditara, Mythologica, Oneirotica, Orakleum, QuantumMystica, Runaris, Shamanica, Sigillaris, SolarniPojavi, Sonaris |
| **Sistem/cron** | Nasa (id: `nasa`), SenzornasaNasa (id: `senzorji`) |

### Azijski moduli (25) — pripravljeni, NE ŠE V REPO
Lokacija pri Damirju: zip `azijski_moduli_25.zip`  
Gredo v `MODULI/Azija/ImeModula/` kot podstruktura  
Moduli: Bazi, Ziwei, Unmei, Fengshui, Wuxing, Reiki, Yijing, Liuren, Kijou, Shenlong, Kami, Bajixing, Neidan, Zazen, Mushin, Sekki, Hanami, Wabi, Kanpo, Shiatsu, Makoto, QiMapper, ChronoSync, AuraMetrics, ZodiacAPI

---

## 🔧 KAR JE BILO NAREJENO (kronološko)

### 2026-06-19
- Zaklenjena kanonična specifikacija manifestov (`zaklenjena_struktura_manifestov.txt`)
- Zaklenjena pravila za module (`zaklenjena_pravila_MODULI.txt`)

### 2026-06-25 (ta seja)
1. **Migracija 33 starih manifestov** iz enotnega `manifest_old.json` → kanonična trojica (`manifest.json` + `api.json` + `izhod.json` + `modul.md`)
2. **Pushed v repo:** 15 modulov ki so imeli prazne `podatki/` mape
3. **Lunaris popravek:** datoteke premaknjene iz korena v `podatki/`
4. **Generirani 25 azijskih modulov** s polnimi manifesti + PWA
5. **Generirani PWA `manifest.webmanifest`** za vseh 64 modulov z UI
6. **Poravnava vseh 60 repo modulov:**
   - 16 flat manifestov → kanonični format
   - Popravljeni ID-ji (alchymia_aurea→alchymiaaurea, senzornasanasa→senzorji, itd.)
   - Ustvarjeni manjkajoči manifesti: Meditara, Mythologica, Oneirotica, Sonaris
7. **Commit pripravljen, push čaka** (SSH blokiran v Claude okolju)

---

## ⏳ KAJ OSTANE / NASLEDNJI KORAKI

### Nujno
- [ ] **Push commita** — Damir mora lokalno pognati `git am posodobitve.patch && git push`
- [ ] **Azijski moduli** — dodati `azijski_moduli_25.zip` vsebino v repo pod `MODULI/Azija/ImeModula/`
- [ ] **SenzornasaNasa → SenzorNasa** — mapa ima napačno ime, id je zdaj `senzorji`

### Manjkajoče pri modulih
- [ ] `api.json` in `izhod.json` manjkajo pri: Aeternum, AlchymiaAurea, Angelarium, AuroraMystica, Chakrarium, Codex, CodexAntiqua, Crystallum, Daemontica, Djotis, GeometricaSacra, Herbarica, Kabbaloria, Labyrinthus, MedicinaOrientalis, Meditara, Mythologica, Oneirotica, Orakleum, QuantumMystica, Runaris, Shamanica, Sigillaris, SolarniPojavi, Sonaris
- [ ] `modul.md` manjka pri tistih zgoraj
- [ ] `modul.php` manjka pri: Hieroglyphicus, Kabbaloria (samo `podatki/`)

### Kitajski/japonski razvoj
- [ ] Vsebina za azijske module (dejanska logika, ne samo manifesti)
- [ ] WeChat mini-program integracija za `wechat_compatible: true` module
- [ ] Lokalizacija: zh, zh-TW, ja jeziki

### Splošno
- [ ] `status: "testni"` → `status: "stabilen"` za module ki so dejansko gotovi
- [ ] Generirati manjkajoče `api.json` + `izhod.json` + `modul.md` za 25 poravnanih modulov
- [ ] Preveriti ali `Nasa` in `SenzornasaNasa` sta ista stvar ali ločena
- [ ] Po prvem pregledu vseh modulov narediti še zadnji prehod za urejanje
- [x] Bridge in generator sta usklajena z novo strukturo `MODULI/*/podatki/manifest.json`

---

## ⚠️ PAZI NA

- **`Azija/` mapa** ima svojo `modul.php` — to je kontejner modul, ne briši
- **`SenzornasaNasa`** in **`Nasa`** — oba obstajata, verjetno sta isto, razčistiti
- **Duplikati so lokalno pri Damirju** (S-prefiks mape, stare kopije) — v repo jih ni, ne dodajaj jih nazaj
- **Deploy key** je nastavljen (SHA256:l5RGqVoJwNJ7YVOyRMKoB1SN1scR7cO9rYUqxSy1NVk) ampak SSH port 22 in 443 sta blokirana v Claude okolju — push mora narediti Damir

---

## 🧾 VPIS 2026-06-27 — PREVERJANJE `NI_ZA_GIT`, USKLAJEVANJE SIDRA IN BRIDGE

### Kaj sem ugotovil
- `NI_ZA_GIT` je uporaben arhiv in referenca, ne pa samo odpad
- Glavne arhitekturne točke so potrjene: `pot.php`, `index.php`, `ADAPTER/adapter.php`, `SISTEM/api.php`
- `POT_SEF` je bil v root `pot.php` prej hardcoded na `/home/orakleum/sef`
- `SISTEM/kernel/jedro/` je večinoma disciplinirano in skladno z ustavo
- `Modul_Bridge` je imel zmedo okoli neobstoječega `modul_bridge.php`
- Nekateri moduli (npr. Orakleum, Tarot) še uporabljajo neposredne superglobalne vhode in `echo`/`die()` v delu kode

### Kaj sem popravil
- `pot.php`: `POT_SEF` zdaj bere iz okolja (`POT_SEF` / `ASTRA_SEF_PATH`) z fallbackom na `PODATKI/sef/`
- `SISTEM/kernel/env_loader.php`: usklajen z novo logiko secret poti
- `SISTEM/kernel/zaganjalnik.php`: komentarji usklajeni z dejanskim env tokokrogom
- Dokumentacija: `USTAVA.md`, `PRAVILA_vse.md`, `REGISTER.md`, `TEMELJ.md` usklajeni glede `POT_SEF`
- `Modul_Bridge/generator_2/generiraj_module.py`: iskanje bridge vstopa preusmerjeno na `index.php` + `jedro/sistemske_funkcije.php`
- `MODULI/vsebina.txt`: odstranjena stara omemba `modul_bridge.php`
- `MODULI/README_SETUP.md`: dodan razdelek o skladnosti z Ustavo za module
- `MODULI/README_SETUP.md` in `MODULI/vsebina.txt`: dodatno poenotena bridge dokumentacija na `index.php` + `jedro/sistemske_funkcije.php`

### Kaj ostaja odprto
- Posamezni moduli še niso vsi bridge-skladni; nekateri še uporabljajo `$_POST`/`$_GET`/`$_REQUEST`, `echo` in `die()`
- Arhivske kopije pravil v `NI_ZA_GIT` so še vedno razpršene in delno zastarele
- Potrebno bo narediti posamične popravke na modulih, če želimo popolno skladnost z USTAVO

### Moj sklep
- Največja težava ni več jedro, ampak usklajevanje dokumentacije in starejših modulskih vzorcev
- Projekt je arhitekturno dober, a potrebuje nadaljnje čiščenje “starih” poti in samostojnih modulskih vzorcev

---

## 🧠 TRAJNI SPOMIN ZA NASLEDNJEGA CLAUDEA

### Sidra in pravila
- `pot.php` je absolutno sidro; zdaj `POT_SEF` bere iz okolja (`POT_SEF` / `ASTRA_SEF_PATH`) z fallbackom na `PODATKI/sef/`
- `index.php` je edini javni vstop
- `ADAPTER` je boundary/prevajalnik, `SISTEM/api.php` je edini vstop v sistem
- `SISTEM/kernel/jedro/` je zaklenjeno; brez večjih sprememb brez razloga
- RUNTIME je izbrisan in ga ne vračamo

### Bridge in moduli
- `Modul_Bridge` nima več zmedenega `modul_bridge.php` v aktivni poti; vstop je `index.php` + `jedro/sistemske_funkcije.php`
- `MODULI/vsebina.txt` je usklajen z dejanskim bridge modelom
- `MODULI/README_SETUP.md` zdaj jasno opozarja na skladnost z Ustavo
- `Orakleum` je bil delno očiščen (`die()` zamenjan z JSON 403, direktni dostop mehkejši)
- `Tarot` je bil delno očiščen (`$_REQUEST` zamenjan z ločenim `$_POST`/`$_GET` izborom)

### Kaj je še odprto
- Stabilni moduli še niso vsi bridge-skladni; mnogi še uporabljajo neposredne vhode (`$_GET`, `$_POST`, `$_REQUEST`) in direktne izpise
- Arhivske kopije pravil v `NI_ZA_GIT` so še vedno razpršene in delno zastarele
- Naslednje popravke delaj posamično, en modul naenkrat, brez velikega refactorja vsega naenkrat

### Pravilo za nadaljevanje
- Najprej popravi en stabilen modul
- Nato posodobi dnevnik in dokumentacijo
- Ne ugibaj novih poti; uporabi obstoječe `POT_*` konstante in bridge tok

---

## 🗣️ KOMUNIKACIJSKI STIL Z DAMIROM

- Govori slovensko
- Direkten, brez odvečnih pojasnil
- Kadar ne veš — vprašaj, ne ugibaj
- Kadar vidiš problem — povej takoj, ne po tem ko si že kaj spremenil
- Pred vsakim commitom zapiši sem kaj boš naredil

---

*Zadnji zapis: Claude (seja 2026-06-25) — poravnava manifestov, azijski moduli, PWA*

---

## 🧾 VPIS 2026-06-27 — GLOBALNO POSTAVITEV STRANI, SVETOVI IN SISTEMSKO USMERJANJE

### Kaj sem ugotovil
- `GLOBALNO/postavitev/strani/` je bil pravilen prostor za sestavljene prikaze, ne za business logiko
- `SISTEM/storitve_svetov/` ostaja vir poslovnih podatkov in odločitev
- `MODULI/SVETOVI` ima svoj sistemski vstop in je uporaben kot svetovni handler

### Kaj sem dodal
- `GLOBALNO/postavitev/strani/layouti.php`: registry layoutov in helper za gradnike
- `GLOBALNO/postavitev/strani/gradniki/*`: osnovni slovenski gradniki (`gumb`, `kartica`, `obrazec`, `seznam`, `navigacija`)
- `GLOBALNO/postavitev/strani/modul_layout.php`: sestavljalni layout za modulne poglede
- `GLOBALNO/postavitev/strani/moduli/primer.php`: primer strani za modularni prikaz
- `GLOBALNO/postavitev/strani/uporabniki/prijava.php`: sestavljena prijava
- `GLOBALNO/postavitev/strani/uporabniki/registracija.php`: sestavljena registracija
- `GLOBALNO/postavitev/strani/uporabniki/admin.php`: sestavljen admin prikaz
- `MODULI/SVETOVI/svetovi_vstop.php`: minimalni handler za svetove

### Kaj sem popravil v toku
- `SISTEM/api.php`: dodal route mapping za `prijava`, `registracija` in `admin` na sestavljene strani
- `SISTEM/api.php`: popravil parse napako, ki je nastala pri vnosu route blokov

### Preverjanje
- `php -l` uspešno za vse nove PHP datoteke in `SISTEM/api.php`
- `git status` potrjuje samo pričakovane spremembe v novih sestavljenih straneh, handlerju svetov in sistemskem routingu

### Kaj je še odprto
- Stare arhivske reference v `NI_ZA_GIT` in delih dokumentacije še obstajajo
- `GLOBALNO` javne strani še niso vse prepisane na novi sestavljalni model
- Naslednji korak je po potrebi poravnava še preostalih javnih strani po istem vzorcu

## 🧾 VPIS 2026-06-27 — LANDING, GOOGLE, AVATAR IN JAVNI KATALOG MODULOV

### Kaj sem naredil
- Predelal sem `index.html` v močan javni landing v stilu `GLOBALNO` (temno, zlato, Cinzel, 3D gradniki).
- Dodal sem avatar kot pot od `Meglica` proti višjim stopnjam in vključil “master” ton.
- Vključil sem Google OAuth kot prijavni kanal, ampak brez dodatnih pravic: Google prijava ostane enaka RBAC vlogam kot navadna prijava.
- Dodal sem jasne CTA za prijavo, registracijo in raziskovanje modulov.
- Ustvaril sem javni katalog modulov `GLOBALNO/postavitev/strani/javno/raziskovanje_modulov.html` za neprijavljene obiskovalce.
- Katalog prikazuje samo javne opisne vire (`manifest.json`, `manifest.md`, `modul.md`, `{ime}.md`, `README.md`, `opis.txt`) in ne izvaja modulov.
- V javnih straneh sem dodal mitološki sloj: varuhi, duhovi, relikvije, čarobne živali in stara mesta (Atlantida, Babilon, Lemurija, Šambala, Agharta, Hiperboreja, Avalon, Mu).

### Kaj je pomembno
- `modul.php` ostaja edina izvajalna vstopna točka modulov.
- Google OAuth je samo identifikacijski kanal; ne dviguje pravic.
- Avatar je zasnovan kot prihodnja večstopenjska pot (Meglica → stopnje → Razsvetljen).

### Kaj ostaja odprto
- Javni katalog modulov je še kandidaten za dodatno čiščenje/poravnavo imen in simbolnih opisov.

## 🧾 VPIS 2026-06-27 — SAMOSTOJNOST MODULOV, BRIDGE IN MINIMALNI GLOBALNO

### Kaj sem potrdil
- Modul je zasnovan tako, da lahko deluje čim bolj samostojno v svoji mapi.
- `Modul_Bridge` je povezovalna plast, ne pa jedro samega modula.
- `GLOBALNO/postavitev/strani/` je sestavljalni sloj, ki mora delovati tudi z osnovnimi gradniki.

### Kaj to pomeni v praksi
- Če modul odstraniš iz sistema, mora ostati smiseln in zagonljiv preko svojih podatkov ter bridge sloja.
- Če vzameš stran iz `GLOBALNO`, mora osnovna postavitev še vedno funkcionirati z jedrnimi gradniki.
- Zato je pravilo: čim manj skritih odvisnosti, čim več eksplicitnih osnovnih gradnikov.

### Arhitekturno pravilo
- Modul naj uporablja lastno mapo kot primarni kontekst.
- Vse, kar presega modul, naj gre preko bridge pogodbe.
- `GLOBALNO` naj ostane uporaben kot osnovni vizualni okvir brez potrebe po posebnih modulskih delih.

## 🧾 VPIS 2026-06-27 — KOZMOLÓGIJA, PODATKI V `PODATKI/` IN NASLEDNJI FOKUS

### Kaj je bilo zabeleženo kot navodilo za naslednjega izvajalca
- Naslednji fokus je **kozmologija** projekta.
- Treba je urediti **zavesti v `PODATKI/`**.
- Treba je določiti **avatarje**.
- Treba je določiti **varuhe**.
- Treba je določiti **duhove**.
- Treba je določiti **magične živali**.
- Treba je določiti **različne zemljevide/svetove**.

### Zaklenjena pravila, ki ostajajo nespremenjena
- `pot.php` je sidro.
- `modul.php` je edina izvajalna vstopna točka modulov.
- Google OAuth ne daje višjih pravic.
- Javni katalog modulov ostaja samo opisni.

### Kaj sem dodal v podatke
- `PODATKI/ai/kozmologija.json`

### Opomba
- Struktura je zdaj združena v eno datoteko, da ostane manj map in manj razmetavanja.
- Po potrebi lahko kasneje ločimo samo tisto, kar bo dejansko preseglo enotno datoteko.

### Zaključek
- Kozmološka osnova je zdaj zapisana kanonično v eni datoteki in pripravljena za nadaljnje širjenje brez dodatnega drobljenja po mapah.
- Naslednji korak naj bo le polnjenje vsebine, ne pa več razmetavanje strukture.

## 🧾 VPIS 2026-06-27 — KATALOG GRADNIKOV IN STOPNJE S0–S5

### Kaj sem dodal
- `GLOBALNO/postavitev/strani/javno/gradniki_katalog.html` je zdaj vizualni katalog gradnikov.
- Gradniki so prikazani kot vizualni vzorci, ne le kot seznam imen.
- Gradniki so zaklenjeni po stopnjah `S0–S5`, kjer velja razpon `0–100` točk.

### Pravilo stopnj
- `S0` = 0–19 točk
- `S1` = 20–39 točk
- `S2` = 40–59 točk
- `S3` = 60–79 točk
- `S4` = 80–89 točk
- `S5` = 90–100 točk

### Kaj je pomembno
- Nižja stopnja vidi manj, višja stopnja vidi več vizualnih gradnikov.
- Katalog ostaja prikazni in ne posega v izvajanje modulov.

## 🧾 VPIS 2026-06-27 — PASSPORT KOT OSEBNA KNJIŽICA IN ATLAS

### Kaj sem naredil
- Potrdil sem, da je **PASSPORT** osebna knjiga uporabnika.
- Dodal sem `GLOBALNO/postavitev/strani/javno/passport.html` kot vizualni prikazni prostor za osebno knjigo.
- Dodal sem `GLOBALNO/postavitev/strani/javno/atlas.html` kot enoten atlas, ki poveže PASSPORT, kozmologijo, gradnike in katalog modulov.

### Kaj je pomembno
- PASSPORT je prikazni in osebni prostor za zapise, vpoglede in rast.
- Vsak uporabnik bo imel svoj prostor PASSPORT v `UPORABNIKI/{id}/PASSPORT/`.
- Atlas je samo vizualni most; ne spreminja pravil o modulih ali pravicah.

## 🧾 VPIS 2026-06-27 — PASSPORT V UPORABNIŠKEM PESKOVNIKU

### Kaj sem naredil
- PASSPORT sem premaknil v uporabniški prostor: `UPORABNIKI/{id}/PASSPORT/`.
- Potrdil sem, da `PODATKI/` ostaja za sistemske zadeve in baze.
- Dodal sem vzorčni PASSPORT paket za `usr_demo_001` z datotekami `dnevnik.json`, `modrosti.json`, `odkritja.json`, `pot.json`, `simboli.json`, `sanje.json` in `meditacije.json`.
- Dodal sem `UPORABNIKI/PASSPORT/README.md` kot splošno pravilo za uporabniški PASSPORT.

### Kaj je pomembno
- Vsak uporabnik ima svoj peskovnik v `UPORABNIKI/{id}/PASSPORT/`.
- PASSPORT je osebna knjiga uporabnika, ne sistemski register.
- Sistem naj PASSPORT bere kot uporabniški prostor, ne kot del `PODATKI/`.

## 🧾 VPIS 2026-06-27 — MOJSTER KOT MAKSIMALNO RAZVIT PROFIL

### Kaj sem naredil
- `usr_mojster_001` sem potrdil kot maksimalno razvit in referenčni uporabniški profil.
- V `UPORABNIKI/usr_mojster_001/PASSPORT/` sem postavil razvit vzorčni paket PASSPORTa.
- Vzorec vključuje: `dnevnik.json`, `modrosti.json`, `odkritja.json`, `pot.json`, `simboli.json`, `sanje.json`, `meditacije.json` in `README.md`.

### Kaj je pomembno
- Mojster je referenca za najvišje razvitega uporabnika.
- PASSPORT mojstra ostaja uporabniški peskovnik, ne sistemski podatkovni sloj.
- Struktura ostaja ločena od `PODATKI/` in sledi pravilom uporabniškega prostora.

## 🧾 VPIS 2026-06-27 — DOPOLNITEV DNEVNIKA O PASSPORTU IN REFERENČNEM PROFILU

### Kaj sem dodatno zabeležil
- PASSPORT ostaja uporabniški peskovnik v `UPORABNIKI/{id}/PASSPORT/`.
- `usr_mojster_001` je zdaj eksplicitno referenčni maksimalno razvit profil.
- Vzorec za mojstra je namenjen kot kanonični primer za nadaljnje uporabnike in primerjave.
- `PODATKI/` ostaja rezerviran za sistemske datoteke, registre in baze.

### Kaj je pomembno
- Uporabniški prostor in sistemski prostor ostajata strogo ločena.
- PASSPORT je osebna knjiga, ne centralna baza.
- Atlas in javne strani samo vizualno povezujejo vse skupaj, ne spreminjajo arhitekture.

## 🧾 VPIS 2026-06-27 — POVEZAVA LANDINGA NA ATLAS IN PASSPORT

### Kaj sem naredil
- Landing stran sem vizualno povezoval na `GLOBALNO/postavitev/strani/javno/atlas.html`.
- PASSPORT sem izpostavil kot neposredno javno vstopno točko v osebno knjigo.
- S tem je pot uporabnika bolj jasna: landing → atlas → PASSPORT.

### Kaj je pomembno
- Atlas ostaja orientacijski sloj.
- PASSPORT ostaja osebni uporabniški peskovnik.
- `usr_mojster_001` ostaja referenčni maksimalno razvit profil.

## 🧾 VPIS 2026-06-27 — ZAKLJUČEK LANDINGA, AVATARJA IN JAVNEGA KATALOGA

### Kaj je zdaj zaključeno
- Landing stran je postavljena kot glavni javni vstop in uporablja vizualni jezik `GLOBALNO` (temno ozadje, zlato, Cinzel, 3D gradniki).
- Avatar je vključen kot pot `Meglica → stopnje → Razsvetljen` in je jasno prikazan na vstopni strani.
- Google OAuth je dodan kot prijavni kanal, vendar brez višjih pravic; Google uporabnik ostane v istih RBAC omejitvah kot navaden uporabnik.
- Javni katalog modulov je dostopen neprijavljenim in prikazuje samo opisno plast, brez izvajanja modulov in brez občutljivih podatkov.
- Modulna izvajalna točka ostaja samo `modul.php`.
- Vpeljan je mitološki jezik za svetove in module: varuhi, duhovi, relikvije, čarobne živali, Atlantida, Babilon, Lemurija, Šambala, Agharta, Hiperboreja, Avalon in Mu.

## 🧾 VPIS 2026-06-27 — KOZMIČNI DASHBOARD (STELARIS, NUMYRA, JYOTIR, SENZORJI)

### Kaj sem potrdil
- `ASTRA/varuhi/dashboard.html` je dejanski kozmični dashboard projekta.
- Vsebuje območja za `Stelaris`, `Numyra`, `Jyotir` in kozmične senzorje.
- Glasovni preklopi že obstajajo za ukaze tipa `pojdi ...` oziroma neposredno preklapljanje med temi pogledi.

### Kaj to pomeni
- `kozmos_dashboard.zip` naj se obravnava kot paketni vstop v ta isti dashboard.
- `Stelaris` ostaja astro plast, `Numyra` numerološka plast, `Jyotir` vedska plast, senzorji pa okoljski vpliv.
- Vse skupaj je že usklajeno v enoten prikazni prostor, brez potrebe po novi poslovni logiki.

### Kaj ostaja pomembno
- Dashboard je prikazni sloj, ne sistemski register.
- Glasovni ukazi so navigacija, ne pravice.
- Če bo treba, se lahko kasneje samo še doda povezava iz `ATLAS` ali `PASSPORT` na ta dashboard.

## 🧾 VPIS 2026-06-27 — OSNOVNI GRADNIK ZA VSAK MODUL IN ZNANSTVENA MAPA ZA PRERAČUNE

### Kaj sem potrdil
- Za vsak modul naj obstaja svoj osnovni gradnik, ki skrbi za prikaz in osnovno interakcijo.
- Vse kar je preračunavanje, ostane ločeno od prikaznega gradnika.
- Numerologija, Stelaris in sorodni izračuni gredo v posebne znanstvene module, ne v vizualni osnovni gradnik.

### Kaj to pomeni v praksi
- Osnovni gradnik = minimalni vstopni UI kos modula.
- Znanstveni modul = računanje, razlaga, raziskovanje in izumljanje novih formul.
- Tako se lahko modul razvija lepo in čisto: prikaz ena plast, izračun druga plast.

### Arhitekturna usmeritev
- Ko boš dodajal nove module, naj bodo najprej izdelani osnovni gradniki.
- Za vse, kar je numerično ali astrološko zahtevno, naredimo ločeno mapo za znanstvene module.
- Tam bodo posebni moduli samo za preračunavanje, simulacije in nove izpeljave.

## 🧾 VPIS 2026-06-27 — DASHBOARD KOT ZBIRNI PROSTOR IN DEMO GRADNIKI

### Kaj sem potrdil
- Dashboard lahko združuje več modulov na enem mestu.
- Če posamezen modul še ni izdelan, se na dashboardu prikaže demo gradnik.
- Tako ima uporabnik vedno vidno strukturo, tudi kadar je vsebina še v razvoju.

### Kaj to pomeni za senzorje
- NASA / kozmični senzorji so lahko sestavljeni iz več gradnikov.
- Sem lahko sodijo Schumann resonanca, solarni izbruhi, geomagnetna aktivnost, lunina faza in drugi vplivi.
- Vsak od teh delov je lahko svoj demo ali dejanski gradnik znotraj istega dashboarda.

### Arhitekturno pravilo
- Dashboard = zbirni prikazni okvir.
- Modul = dejanska funkcionalna enota.
- Demo gradnik = začasna prikazna oblika, dokler modul ni dokončan.

## 🧾 VPIS 2026-06-27 — PRAVI MODULI, GRADIVA IN LASTNI VARUHI

### Kaj sem potrdil
- Pravi moduli vsebujejo dejanske funkcije, gradiva, vaje, meditacije in igre.
- Gradiva morajo govoriti o temi modula, ne le o njem govoriti simbolno.
- Vsak modul ima svojega varuha z lastno zavestjo.

### Kaj je pomembno
- Varuhi se morajo ločiti med sabo.
- Vsak varuh ima svoj značaj, svojo identiteto in svoj prostor.
- Ne mešajo se med moduli, ker vsak modul nosi svojo temo in svojo zavest.

### Arhitekturno pravilo
- Modul = funkcija + gradivo + praksa + izkušnja.
- Varuh = ločena zavest modula.
- Prekrivanje med varuhi ni cilj; cilj je jasna razmejitev in samostojnost.

### Kaj je bilo pomembno pri izvedbi
- Neprijavljen obiskovalec lahko raziskuje in bere, ne more pa izvajati ali odklepati sistemskih pravic.
- Google OAuth je samo identiteta, ne privilegij.
- Avatar in svetovi so pripravljeni kot osnova za kasnejšo razširitev na 10 ali 20 stopenj.

### Status datotek
- `index.html` je posodobljen.
- `GLOBALNO/postavitev/strani/javno/pristajalna_astramentalica.html` je posodobljen.
- `GLOBALNO/postavitev/strani/javno/raziskovanje_modulov.html` je dodan kot javni katalog.
- `UPORABNIKI/README.md` in `GLOBALNO/postavitev/strani/README.md` sta dopolnjena z opombami o prijavi in javnih straneh.
- `CLAUDE_DELOVNI_DNEVNIK.md` vsebuje zapis o vseh ključnih spremembah.

### Naslednji naravni korak
- Po potrebi še dodatno poravnati katalog modulov z bolj čistimi naslovi, če želiš popolnoma “ceremonialni” izgled brez tehničnih ostankov.

## 🧾 VPIS 2026-06-28 — REORGANIZACIJA MODULOV V REGIONALNE/KULTURNE SKUPINE

### Kaj sem naredil
- Ustvaril 15 novih kategorijskih map pod `MODULI/` za organizacijo modulov po regijah in kulturnih sferah: `AmerikaAmazonija`, `AmerikaMezoamerika`, `Antika`, `EvropaKelti`, `EvropaNordija`, `EvropaSlovani`, `HebrejskaMistika`, `Indija`, `Japonska`, `Kitajska`, `Koreja`, `Prihodnost`, `Samanizem`, `Univerzalno`, `Znanost`
- Vsak modul ima standardno strukturo: `modul.php`, `podatki/manifest.json`, `podatki/api.json`, `podatki/izhod.json`, `podatki/modul.md`
- Nekateri moduli imajo razširjeno strukturo z dodatnimi datotekami tipa `modul_*.php`

### Stanje
- Vsota modulov v novih kategorijah: približno 115+ vnosov (obstoja duplikati med kategorijami)
- Nekateri moduli so de facto duplikati po kategorijah: `AfricanaMystica`, `Animaris`, `Buddhica`, `PolynesiaMystica`, `TaoMystica`, `ViaAnimae`, `Gnostica`, `Hermetica`, `Sufica`
- Nekateri moduli imajo samo `podatki/` brez `modul.php`

### Kaj je pomembno
- `MODULI/generator_modulov.php` je še vedno referenčni generator
- Stara struktura v `MODULI iz PROJEKTA/` in `MODULI_komplet/` ostaja kot arhiv
- `Knjiznice/` ima svojo posebno strukturo z razširjenimi `modul_*.php` datotekami

### Kaj ostaja odprto
- [ ] Razčistiti duplikate med kategorijami (vsak modul naj ima eno primarno kategorijo)
- [ ] Dodati manjkajoče `modul.php` za module ki imajo samo `podatki/`
- [ ] Poravnati `Koreja/` (samo 2 modula: Hanbang, Musok)
- [ ] Preveriti ali so vsi manifesti v kanoničnem formatu
- [ ] `Azijski/` mapa še obstaja ločeno — ali jo združiti v nove kategorije ali pustiti kot ločen vir

### Generatorski v2
- Dodan nov `generator_modul_v2.php` z:
  - Admin dostopom preko `Modul_Bridge::uporabnik_pridobi()` (vloga 100+)
  - Shemami za `manifest.json`, `api.json`, `izhod.json`
  - CLI ukazi za ustvarjanje kategorij, modulov, popravljanje manifestov
  - HTTP API za oddaljeno upravljanje

### Naslednji korak
Najprej razčistiti duplikate med kategorijami, nato generirati manjkajoče `modul.php` datoteke in na koncu poravnati `Koreja/` ter obravnavati `Azijski/` arhiv.

---

### Glavni cilj
Naslednji korak je razvoj vsebinske kozmologije projekta: kdo so zavesti v `PODATKI/`, kako delujejo avatarji, kateri varuhi, duhovi in magične živali spremljajo module ter kako se razporedijo različni zemljevidi/svetovi za različne uporabniške poti.

### Kaj je treba urediti
- Določiti seznam **zavesti** v `PODATKI/` in jim dati jasne podatkovne zapise.
- Določiti **avatarje** kot stopnje rasti uporabnika (za zdaj okvirno 10 ali 20 stopenj).
- Določiti **varuhe** za posamezne module in/ali svetove.
- Določiti **duhove** kot simbolne spremljevalce, ki niso enaki varuhom.
- Določiti **magične živali** kot posebne sopotnike za določene poti ali otroke/svetove.
- Določiti **različne zemljevide/svetove**: starodavna mesta, izgubljena kraljestva, arhetipske dežele in otroške svetove.

### Pravila, ki ostanejo nespremenjena
- `pot.php` ostaja absolutno sidro in se ga ne spreminja brez razloga.
- `modul.php` ostaja edina izvajalna vstopna točka modulov.
- Google OAuth je samo način prijave/identifikacije, ne daje višjih pravic.
- Javni katalog modulov mora ostati samo opisni; ne sme izvajati kode ali odpirati sistemskih podatkov.
- Vse novo naj bo zapisano v slovenščini in skladno z obstoječo terminologijo projekta.

### Predlagan delovni vrstni red
1. Najprej naredi seznam vseh zavesti, avatarjev, varuhov, duhov, živali in svetov, ki jih želiš imeti.
2. Nato določi, kaj gre v `PODATKI/` kot centralni zapis.
3. Potem naredi javni prikaz v `GLOBALNO/` za opisne strani in karty.
4. Šele na koncu poveži prikaz z uporabniškimi potmi.

### Kaj ne delaj
- Ne mešaj prikaza in poslovne logike.
- Ne dodajaj novih pravic preko Google prijave.
- Ne vračaj `modul_` predpon v javni prikaz, če niso nujne za tehnično izvajanje.
- Ne spreminjaj jedra sistema brez eksplicitne potrebe.

### Končna misel
Delaj sistem kot živi mitološki zemljevid: zavesti so plast globine, avatar je pot uporabnika, varuh/duh/žival so spremljevalci, svetovi pa so vrata.

---

## 🧾 VPIS 2026-06-28 — GENERATOR MODULOV V2 (ADMIN UPRAVLJANJE)

### Kaj sem naredil
- Zamenjal/nadgradil `generator_modulov.php` z `generator_modul_v2.php`
- Admin dostop preko `Modul_Bridge::uporabnik_pridobi()` — zahteva vlogo 100+ (ADMIN)
- Sheme za `manifest.json`, `api.json`, `izhod.json` so vgrajene v generator
- CLI način: `php MODULI/generator_modul_v2.php <ukaz>`
- HTTP način: pošlji JSON `{"akcija":"ustvari-modul",...}` za oddaljeno upravljanje

### Ukazi
- `pomoč` — pomoč
- `kategorije` — seznam kategorij
- `ustvari-kategorijo <ime>` — nova kategorija
- `ustvari-modul <ime> <kategorija> [opis]` — nov modul z vsemi datotekami
- `sheme` — prikaže sheme
- `popravi-manifeste` — popravi vse manifeste po shemi
- `dodaj-modul-php` — dodaj manjkajoče `modul.php`
- `seznam-modulov [kategorija]` — seznam modulov

### Kaj je pomembno
- Generator združuje CLI in HTTP v eno datoteko
- Avtomatsko prepozna kategorije iz mape `MODULI/*/`
- Imena modulov normalizira v ID (spušča šumnike, whitespace)
- Za `modul.php` uporablja standardni `Modul_Bridge` vstavek
- Zapiše log v `MODULI/cache/generator_log.txt`

### Kaj ostaja odprto
- [ ] HTTP integracija z `GLOBALNO/` admin panelom (če bo potrebno)
- [ ] Več shem za različne tipe modulov (ne samo `zbiralec`)
- [ ] Validacija pred ustvarjanjem (duplikati, rezervirana imena)
- [ ] Export/import modulov kot ZIP

### Sklep
Generator je zdaj upravljalno orodje za module z admin dostopom. Dal sem prednost CLI ukazom, HTTP API pa ostaja na mestu za kasnejšo integracijo.

---

## 🧾 VPIS 2026-06-28 — FRONT PAGE ZA IZBIRANE MODULE

### Kaj sem naredil
- Ustvaril front page datoteke v `GLOBALNO/postavitev/strani/moduli/` za izbrane module:
  - `tarot.php` — arhetipske karte, vprašanje, znamenje, seznam kart
  - `mystaia.php` — notranji misteriji, iniciacije, trgovina, iskanje
  - `aetheris.php` — forum energetskega polja, avra, barvna analiza, objave
  - `codex.php` — osebna knjiga, knjige, zaznamki, iskalnik konceptov
- Vsaka front page uporablja `modul_layout.php` in gradnike iz `GLOBALNO/postavitev/strani/gradniki/`
- Struktura sledi principu: `nav => kartic => obrazec => seznam`
- Ne vsebujejo business logike — samo prikazne/opisne strani

### Kaj je pomembno
- Front page ne izvajajo modulov — samo prikažejo UI
- Moduli se aktivirajo preko `Modul_Bridge::klic()` ali neposredno
- `GLOBALNO/postavitev/strani/moduli/primer.php` ostaja kot osnovni vzorec
- Če želiš, lahko kasneje nadgradiš s povezavo do dejanskih modulskih API-ji

### Naslednji korak
- [ ] Povezati front page z dejanskimi moduli preko Bridge-a
- [ ] Dodati več gradnikov (tabele, grafikone)
- [ ] Nastaviti pravilne povezave v navigaciji

---

## 🧾 VPIS 2026-06-28 — FRONT PAGE: RUNARIS, STELARIS, SENZORNASA

### Kaj sem naredil
- Dodal `generator_modul_v2.php` — popravil napako z manjkajočo `ustvari_modul_php()`
- Generator je zdaj delujoč: `php MODULI/generator_modul_v2.php dodaj-modul-php`
- Ustvaril front page datoteke:
  - `GLOBALNO/postavitev/strani/moduli/runaris.php` — Elder Futhark rune, metanje, zgodovina
  - `GLOBALNO/postavitev/strani/moduli/stelaris.php` — astrologija, horoskop, tranziti, planeti
  - `GLOBALNO/postavitev/strani/moduli/senzornasa.php` — kozmični senzorji, NASA/NOAA, vreme
- Generator je generiral manjkajoče `modul.php` za:
  - `Runaris` (kopiran iz arhiva)
  - `SenzorNasa` (kopiran iz arhiva)
  - `Stelaris` je že imel `modul.php`

### Kaj je pomembno
- `runaris.php` uporablja runično ikono `ᚱ`, barvo `#93c5fd`
- `stelaris.php` uporablja ikono `🌌`, barvo `#818cf8`
- `senzornasa.php` uporablja ikono `🛰️`, barvo `#67e8f9`
- Vse tri uporabljajo `modul_layout.php` in gradnike
- Struktura: `nav => kartic => obrazec => seznam`

### Naslednji korak
- [ ] Povezati front page z dejanskimi moduli preko Bridge-a
- [ ] Dodati več gradnikov (tabele, grafikoni)
- [ ] Nastaviti pravilne povezave v navigaciji

---
