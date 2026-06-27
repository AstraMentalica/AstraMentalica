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
**Datum zadnjega vpisa:** 2026-06-25  

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
Gredo v `MODULI/Azija/` kot podstruktura  
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
- [ ] **Azijski moduli** — dodati `azijski_moduli_25.zip` vsebino v repo pod `MODULI/Azija/`
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

---

## ⚠️ PAZI NA

- **`Azija/` mapa** ima svojo `modul.php` — to je kontejner modul, ne briši
- **`SenzornasaNasa`** in **`Nasa`** — oba obstajata, verjetno sta isto, razčistiti
- **Duplikati so lokalno pri Damirju** (S-prefiks mape, stare kopije) — v repo jih ni, ne dodajaj jih nazaj
- **Deploy key** je nastavljen (SHA256:l5RGqVoJwNJ7YVOyRMKoB1SN1scR7cO9rYUqxSy1NVk) ampak SSH port 22 in 443 sta blokirana v Claude okolju — push mora narediti Damir

---

## 🗣️ KOMUNIKACIJSKI STIL Z DAMIROM

- Govori slovensko
- Direkten, brez odvečnih pojasnil
- Kadar ne veš — vprašaj, ne ugibaj
- Kadar vidiš problem — povej takoj, ne po tem ko si že kaj spremenil
- Pred vsakim commitom zapiši sem kaj boš naredil

---

*Zadnji zapis: Claude (seja 2026-06-25) — poravnava manifestov, azijski moduli, PWA*
