<#
.SYNOPSIS
    Izdela celotno poročilo o projektu.
.DESCRIPTION
    Zažene vse preverjalnike in združi rezultate v eno poročilo.
.EXAMPLE
    .\izdelaj_porocilo.ps1
.EXAMPLE
    .\izdelaj_porocilo.ps1 -PotProjekta "D:\Projekti\AstraMentalica" -IzhodnaPot "D:\Porocila"
#>

# ============================================================
# AstraMentalica - Izdelava poročila
# ============================================================
# Namen: Generiranje celotnega poročila
# Avtor: AstraMentalica Mojster
# Verzija: 1.0.0
# ============================================================

[CmdletBinding()]
param(
    [Parameter(Mandatory=$false)]
    [string]$PotProjekta = $PSScriptRoot | Split-Path -Parent,
    
    [Parameter(Mandatory=$false)]
    [string]$IzhodnaPot = "$PSScriptRoot\..\POROCILA"
)

$BarvaInfo = "Cyan"
$BarvaUspeh = "Green"
$BarvaOpozorilo = "Yellow"
$BarvaNapaka = "Red"

Write-Naslov "ASTRA MENTALICA - IZDELAVA POROCILA"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor White
Write-Host "Izhodna pot: $IzhodnaPot" -ForegroundColor White
Write-Host "Datum: $(Get-Date -Format 'dd.MM.yyyy HH:mm:ss')" -ForegroundColor White

# Ustvari izhodno mapo
if (-not (Test-Path $IzhodnaPot)) {
    New-Item -ItemType Directory -Path $IzhodnaPot -Force | Out-Null
}

# Inicializiraj poročilo
$Porocilo = @"
# ASTRA MENTALICA - Celotno porocilo projekta
==============================================
Datum: $(Get-Date -Format 'dd.MM.yyyy HH:mm:ss')
Pot: $PotProjekta

## 1. STRUKTURA PROJEKTA

"@

# Število datotek
$PhpDatoteke = Get-ChildItem -Path $PotProjekta -Filter "*.php" -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch "\\.venv\\|\\vendor\\|\\node_modules\\" }

$JsonDatoteke = Get-ChildItem -Path $PotProjekta -Filter "*.json" -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch "\\node_modules\\" }

$JsDatoteke = Get-ChildItem -Path $PotProjekta -Include "*.js", "*.jsx" -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch "\\node_modules\\|\\.venv\\" }

$Porocilo += @"

| Tip | Število |
|-----|---------|
| PHP datotek | $($PhpDatoteke.Count) |
| JSON datotek | $($JsonDatoteke.Count) |
| JS datotek | $($JsDatoteke.Count) |

### Glavne mape

"@

$GlavneMape = @("ADAPTER", "AI", "ASTRA", "GLOBALNO", "MODULI", "PODATKI", "SISTEM", "UPORABNIKI", "VSEBINA")
foreach ($Mapa in $GlavneMape) {
    $PolnaPot = Join-Path $PotProjekta $Mapa
    if (Test-Path $PolnaPot) {
        $SteviloDatotek = (Get-ChildItem -Path $PolnaPot -Recurse -File -ErrorAction SilentlyContinue).Count
        $Porocilo += "| $Mapa | $SteviloDatotek datotek |`n"
    }
}

$Porocilo += @"

## 2. MODULI

"@

# Moduli
$Moduli = Get-ChildItem -Path "$PotProjekta\MODULI\Univerzalno" -Directory -ErrorAction SilentlyContinue
$Porocilo += "Skupaj modulov: $($Moduli.Count)`n`n"

foreach ($Modul in $Moduli) {
    $ModulneDatoteke = Get-ChildItem $Modul.FullName -Filter "*.php" -File -ErrorAction SilentlyContinue
    $Porocilo += "- **$($Modul.Name)**: $($ModulneDatoteke.Count) datotek`n"
}

$Porocilo += @"

## 3. KNJIZNICE

"@

$Knjiznice = Get-ChildItem -Path "$PotProjekta\MODULI\Knjiznice" -Directory -ErrorAction SilentlyContinue
foreach ($Knjiznica in $Knjiznice) {
    $Datoteke = Get-ChildItem $Knjiznica.FullName -Filter "*.php" -File -ErrorAction SilentlyContinue
    $Porocilo += "- **$($Knjiznica.Name)**: $($Datoteke.Count) datotek`n"
}

$Porocilo += @"

## 4. JSON BAZE

"@

$Baze = @("registri\moduli_register.json", "data_bus.json", "frekvence.json", "kanonični_varuhi.json")
foreach ($Baza in $Baze) {
    $PolnaPot = Join-Path $PotProjekta "PODATKI\$Baza"
    if (Test-Path $PolnaPot) {
        $Velikost = (Get-Item $PolnaPot).Length
        $Porocilo += "- **$Baza**: $([math]::Round($Velikost/1KB, 1)) KB`n"
    } else {
        $Porocilo += "- **$Baza**: MANJKA`n"
    }
}

$Porocilo += @"

## 5. PHP SINTAKSA

"@

# Preveri PHP sintakso
$SkupajNapak = 0
foreach ($Datoteka in $PhpDatoteke) {
    $Rezultat = php -l $Datoteka.FullName 2>&1
    if ($LASTEXITCODE -ne 0) {
        $SkupajNapak++
    }
}

$Porocilo += "Napake v PHP: $SkupajNapak`n"

$Porocilo += @"

## 6. PODVOJENE DATOTEKE

"@

# Poišči (2) datoteke
$Podvojitve2 = Get-ChildItem -Path $PotProjekta -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.Name -match '\s*\(2\)\.' -and $_.FullName -notmatch '\\.git\\' }

$Porocilo += "Podvojitve (2): $($Podvojitve2.Count)`n`n"

foreach ($Datoteka in $Podvojitve2) {
    $Porocilo += "- $($Datoteka.Name)`n"
}

$Porocilo += @"

## 7. VARNOST

"@

$Porocilo += "| Preverjanje | Status |`n|-----------|--------|`n"
$Porocilo += "| .gitignore | $(if (Test-Path (Join-Path $PotProjekta ".gitignore")) { "✓" } else { "✗" }) |`n"
$Porocilo += "| SISTEM_VARNOST | $(if (Select-String -Path "$PotProjekta\pot.php" -Pattern "SISTEM_VARNOST" -Quiet) { "✓" } else { "✗" }) |`n"

$Porocilo += @"

## 8. DOKUMENTACIJA

"@

$DocDatoteke = @("README.md", "AstraMentalica.md", "PROJEKT_PREGLED.md", "MODULI.md", "AGENTI.md", "KNJIGE.md", "ARHITEKTURA.md", "TODO_REFAKTOR.md")
foreach ($Doc in $DocDatoteke) {
    $Obstaja = if (Test-Path (Join-Path $PotProjekta $Doc)) { "✓" } else { "✗" }
    $Porocilo += "| $Doc | $Obstaja |`n"
}

$Porocilo += @"

## 9. OROODJA

"@

$PsScripts = (Get-ChildItem "$PotProjekta\ORODJA\PS" -Filter "*.ps1" -ErrorAction SilentlyContinue).Count
$PyScripts = (Get-ChildItem "$PotProjekta\ORODJA\PY" -Filter "*.py" -ErrorAction SilentlyContinue).Count

$Porocilo += "| Tip | Število |`n|------|---------|`n"
$Porocilo += "| PowerShell | $PsScripts |`n"
$Porocilo += "| Python | $PyScripts |`n"

$Porocilo += @"

## 10. POVZETEK

- PHP datotek: $($PhpDatoteke.Count)
- JSON datotek: $($JsonDatoteke.Count)
- Modulov: $($Moduli.Count)
- Knjižnic: $($Knjiznice.Count)
- PHP napak: $SkupajNapak
- Podvojitev (2): $($Podvojitve2.Count)

---
*Poročilo generirano: $(Get-Date -Format 'dd.MM.yyyy HH:mm:ss')*
"@

# Shrani poročilo
$DatotekaPorocila = Join-Path $IzhodnaPot "AstraMentalica_porocilo_$(Get-Date -Format 'yyyyMMdd_HHmmss').md"
$Porocilo | Out-File -FilePath $DatotekaPorocila -Encoding UTF8

Write-Host ""
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "POROCILO IZDELANO!" -ForegroundColor $BarvaUspeh
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host ""
Write-Host "Poročilo shranjeno: $DatotekaPorocila" -ForegroundColor White
Write-Host ""
Write-Host "Odpiram poročilo..." -ForegroundColor $BarvaInfo

# Odpri poročilo
Start-Process $DatotekaPorocila
