# Spretnost: preveri-module

## Namen
Preveri strukturo in prisotnost manifestov/modulskih podatkov v vseh modulih, ki so v mapi MODULI/. Namen je hitro identificirati manjkajoče mape `podatki`, manjkajoče `manifest.json` in netipično število datotek v `podatki`.

## Kdaj uporabiti
- Pred nameščanjem ali posodobitvijo modulov
- Pri avtomatiziranem preverjanju skladnosti modulov s kanonično strukturo
- Ko modul povzroča napake in želiš osnovno diagnostiko

## Vhod / Izhod
- Vhod: noben (skripta samodejno pregleda vse podmape v `MODULI/`)
- Izhod: seznam vrstic v formatu `STATUS|IME_MODULA|PODATKI_EXIST|STEVILO_DATOTEK|MANIFEST` kjer je `STATUS` eno izmed: OK, MISSING, WARN

## Postopek (PowerShell)
1. Odpri PowerShell v korenu projekta:

```powershell
cd D:\Projekti\Projekt_AstraMentalica\AstraMentalica
```

2. Zaženi to skripto (samodejno poišče vse module):

```powershell
$root = Join-Path (Get-Location) 'MODULI'
Get-ChildItem $root -Directory | ForEach-Object {
  $ime = $_.Name
  $podatki = Join-Path $_.FullName 'podatki'
  if (-not (Test-Path $podatki)) {
    Write-Output "MISSING|$ime|podatki_missing|0|manifest_missing"
    return
  }
  $files = Get-ChildItem $podatki -File -Recurse -ErrorAction SilentlyContinue
  $count = $files.Count
  $manifestPath = Join-Path $podatki 'manifest.json'
  $hasManifest = Test-Path $manifestPath
  $status = if ($count -lt 1) { 'WARN' } else { 'OK' }
  Write-Output ("$status|$ime|podatki_ok|{0}|{1}" -f $count, ($hasManifest -as [string]))
}
```

# Spretnost: preveri-module

## Namen
Preveri strukturo in prisotnost manifestov/modulskih podatkov v vseh modulih, ki so v mapi MODULI/. Namen je hitro identificirati manjkajoče mape `podatki`, manjkajoče `manifest.json` in netipično število datotek v `podatki`.

## Kdaj uporabiti
- Pred nameščanjem ali posodobitvijo modulov
- Pri avtomatiziranem preverjanju skladnosti modulov s kanonično strukturo
- Ko modul povzroča napake in želiš osnovno diagnostiko

## Vhod / Izhod
- Vhod: noben (skripta samodejno pregleda vse podmape v `MODULI/`)
- Izhod: seznam vrstic v formatu `STATUS|IME_MODULA|PODATKI_EXIST|STEVILO_DATOTEK|MANIFEST` kjer je `STATUS` eno izmed: OK, MISSING, WARN

## Postopek (PowerShell)
1. Odpri PowerShell v korenu projekta:

```powershell
cd D:\Projekti\Projekt_AstraMentalica\AstraMentalica
```

2. Zaženi to skripto (samodejno poišče vse module):

```powershell
$root = Join-Path (Get-Location) 'MODULI'
Get-ChildItem $root -Directory | ForEach-Object {
  $ime = $_.Name
  $podatki = Join-Path $_.FullName 'podatki'
  if (-not (Test-Path $podatki)) {
    Write-Output "MISSING|$ime|podatki_missing|0|manifest_missing"
    return
  }
  $files = Get-ChildItem $podatki -File -Recurse -ErrorAction SilentlyContinue
  $count = $files.Count
  $manifestPath = Join-Path $podatki 'manifest.json'
  $hasManifest = Test-Path $manifestPath
  $status = if ($count -lt 1) { 'WARN' } else { 'OK' }
  Write-Output ("$status|$ime|podatki_ok|{0}|{1}" -f $count, ($hasManifest -as [string]))
}
```

3. Prilepi izpis v klepet z agentom ali shrani v datoteko:

```powershell
# Shrani v datoteko
.\scripts\preveri-moduli-output.txt

# ali za neposredno uporabo
.
```

## Kaj agent naredi s podatki
- Prebere izpis in pripravi povzetek: število modulov OK, seznam modulov z manjkajočimi `podatki/`, modulov brez `manifest.json`, in tiste z nenavadnim številom datotek.
- Če želiš, lahko agent dodatno preveri vse `manifest.json` datoteke glede na obvezna polja (npr. `modul.id`, `modul.ime`, `modul.verzija`).

## Merila kakovosti
- Modul šteje za osnovno veljavnega, če ima mapo `podatki` in vsaj 1 datoteko v njej.
- Za popolno skladnost priporočamo prisotnost `podatki/manifest.json` z vsaj polji `modul.id`, `modul.ime`, `modul.verzija`.

## Primer interpretacije izpisa
- `OK|Tarot|podatki_ok|12|True` → modul `Tarot` ima 12 datotek, manifest prisoten.
- `MISSING|Xyz|podatki_missing|0|manifest_missing` → modul `Xyz` nima mape `podatki`.
- `WARN|Foo|podatki_ok|0|False` → mapa `podatki` obstaja, vendar brez datotek ali brez manifesta.

## Kako uporabiti v Cline (agent)
- V klepetu vpiši: "Uporabi spretnost 'preveri-module'" ali prilepi izpis PowerShell skripte.
- Agent bo analiziral izpis in vrnil kratek povzetek in seznam akcij (npr. "Ustvari manjkajoči manifest za: X, Y").

## Dodatno (neobvezno)
- Če želite avtomatsko validacijo manifestov, agent lahko požene naslednjo preverjevalno funkcijo (JSON schema check) — to lahko dodamo po potrebi.

---

Avtor: Avtomatsko generirano/posodobljeno spretnostno navodilo za lokalno uporabo.