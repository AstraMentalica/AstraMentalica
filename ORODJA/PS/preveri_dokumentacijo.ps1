<#
.SYNOPSIS
    Preveri dokumentacijske datoteke v projektu.
.DESCRIPTION
    Analizira README, MD in druge dok. datoteke.
.EXAMPLE
    .\preveri_dokumentacijo.ps1
#>

# ============================================================
# AstraMentalica - Preverjanje dokumentacije
# ============================================================
# Namen: Analiza projektne dokumentacije
# Avtor: AstraMentalica Mojster
# Verzija: 1.0.0
# ============================================================

[CmdletBinding()]
param(
    [Parameter(Mandatory=$false)]
    [string]$PotProjekta = $PSScriptRoot | Split-Path -Parent
)

$BarvaInfo = "Cyan"
$BarvaUspeh = "Green"
$BarvaOpozorilo = "Yellow"

Write-Naslov "ASTRA MENTALICA - PREVERJANJE DOKUMENTACIJE"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor White

# Dokumentacijske datoteke
$DocDatoteke = @{
    "README.md" = "Glavni README"
    "AstraMentalica.md" = "Projektni pregled"
    "PROJEKT_PREGLED.md" = "Pregled projekta"
    "MODULI.md" = "Dokumentacija modulov"
    "AGENTI.md" = "Dokumentacija agentov"
    "KNJIGE.md" = "Knjige in baze"
    "ARHITEKTURA.md" = "Arhitektura"
    "TODO_REFAKTOR.md" = "TODO refaktor"
}

Write-Host ""
Write-Host "Obstoječa dokumentacija:" -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

foreach ($Ime in $DocDatoteke.Keys) {
    $PolnaPot = Join-Path $PotProjekta $Ime
    if (Test-Path $PolnaPot) {
        $Velikost = (Get-Item $PolnaPot).Length
        $Vsebina = Get-Content $PolnaPot -Raw
        
        $Vrstic = ($Vsebina -split "`n").Count
        $Besed = ($Vsebina -split "\s+").Count
        
        Write-Host "[✓] $Ime" -ForegroundColor $BarvaUspeh
        Write-Host "    → $($DocDatoteke[$Ime])" -ForegroundColor DarkGray
        Write-Host "    → $Vrstic vrstic, ~$Besed besed, $([math]::Round($Velikost/1KB, 1)) KB" -ForegroundColor DarkGray
    } else {
        Write-Host "[✗] $Ime - MANJKA" -ForegroundColor $BarvaOpozorilo
        Write-Host "    → $($DocDatoteke[$Ime])" -ForegroundColor DarkGray
    }
}

# Preveri README v modulih
Write-Host ""
Write-Host "README v modulih:" -ForegroundColor $BarvaInfo

$ReadmeVModulih = Get-ChildItem -Path "$PotProjekta\MODULI" -Filter "README.md" -Recurse -File -ErrorAction SilentlyContinue

if ($ReadmeVModulih) {
    Write-Host "Najdenih $($ReadmeVModulih.Count) README datotek v modulih" -ForegroundColor White
    foreach ($R in $ReadmeVModulih | Select-Object -First 5) {
        Write-Host "  → $($R.FullName.Replace($PotProjekta, ''))" -ForegroundColor DarkGray
    }
} else {
    Write-Host "Ni README datotek v modulih" -ForegroundColor $BarvaOpozorilo
}

# Preveri ORODJA mapo
Write-Host ""
Write-Host "Orodja:" -ForegroundColor $BarvaInfo

$OrodjaPot = Join-Path $PotProjekta "ORODJA"
if (Test-Path $OrodjaPot) {
    $PsScripts = (Get-ChildItem "$OrodjaPot\PS" -Filter "*.ps1" -ErrorAction SilentlyContinue).Count
    $PyScripts = (Get-ChildItem "$OrodjaPot\PY" -Filter "*.py" -ErrorAction SilentlyContinue).Count
    
    Write-Host "[✓] PowerShell skripte: $PsScripts" -ForegroundColor $BarvaUspeh
    Write-Host "[✓] Python skripte: $PyScripts" -ForegroundColor $BarvaUspeh
} else {
    Write-Host "[✗] ORODJA mapa manjka" -ForegroundColor $BarvaOpozorilo
}

Write-Host ""
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "Preverjanje končano!" -ForegroundColor $BarvaInfo
