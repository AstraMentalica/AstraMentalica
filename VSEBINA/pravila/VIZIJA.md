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
