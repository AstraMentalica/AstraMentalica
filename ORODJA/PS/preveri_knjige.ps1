<#
.SYNOPSIS
    Preveri knjige in podatkovne baze.
.DESCRIPTION
    Analizira JSON baze, knjiznice in numerološke baze.
.EXAMPLE
    .\preveri_knjige.ps1
#>

# ============================================================
# AstraMentalica - Preverjanje knjig in baz
# ============================================================
# Namen: Analiza podatkovnih baz in znanj
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
$BarvaNapaka = "Red"

Write-Naslov "ASTRA MENTALICA - PREVERJANJE KNJIG IN BAZ"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor White

# Knjiznice
Write-Host ""
Write-Host "Knjižnice (MODULI/Knjiznice/):" -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

$Knjiznice = Get-ChildItem -Path "$PotProjekta\MODULI\Knjiznice" -Directory -ErrorAction SilentlyContinue
foreach ($Knjiznica in $Knjiznice) {
    $Datoteke = Get-ChildItem $Knjiznica.FullName -Filter "*.php" -File -ErrorAction SilentlyContinue
    $Podatki = Test-Path (Join-Path $Knjiznica.FullName "podatki")
    $Readme = Test-Path (Join-Path $Knjiznica.FullName "README.md")
    
    $Status = if ($Podatki -and $Readme) { "✓" } elseif ($Podatki -or $Readme) { "⚠" } else { "✗" }
    $Barva = if ($Status -eq "✓") { $BarvaUspeh } elseif ($Status -eq "⚠") { $BarvaOpozorilo } else { $BarvaNapaka }
    
    Write-Host "[$Status] $($Knjiznica.Name)" -ForegroundColor $Barva
    Write-Host "    → $($Datoteke.Count) PHP datotek" -ForegroundColor DarkGray
    if ($Podatki) { Write-Host "    → podatki/ mapa obstaja" -ForegroundColor DarkGray }
    if ($Readme) { Write-Host "    → README.md obstaja" -ForegroundColor DarkGray }
}

# JSON baze
Write-Host ""
Write-Host "JSON podatkovne baze:" -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

$Baze = @(
    @{Pot="PODATKI\registri\moduli_register.json";Opis="Register modulov"}
    @{Pot="PODATKI\data_bus.json";Opis="Data bus"}
    @{Pot="PODATKI\frekvence.json";Opis="Frekvence"}
    @{Pot="PODATKI\kanonični_varuhi.json";Opis="Kanonični varuhi"}
    @{Pot="PODATKI\zbirka.json";Opis="Glavna zbirka"}
)

foreach ($Baza in $Baze) {
    $PolnaPot = Join-Path $PotProjekta $Baza.Pot
    if (Test-Path $PolnaPot) {
        try {
            $Vsebina = Get-Content $PolnaPot -Raw
            $Json = $Vsebina | ConvertFrom-Json
            $Velikost = (Get-Item $PolnaPot).Length
            
            Write-Host "[✓] $($Baza.Opis)" -ForegroundColor $BarvaUspeh
            Write-Host "    → $($Baza.Pot)" -ForegroundColor DarkGray
            Write-Host "    → $([math]::Round($Velikost/1KB, 1)) KB" -ForegroundColor DarkGray
        } catch {
            Write-Host "[✗] $($Baza.Opis) - NAPAKA JSON" -ForegroundColor $BarvaNapaka
        }
    } else {
        Write-Host "[✗] $($Baza.Opis) - MANJKA" -ForegroundColor $BarvaOpozorilo
    }
}

# Numerološka baza
Write-Host ""
Write-Host "Numerološka baza (NumYra):" -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

$NumYraPot = Join-Path $PotProjekta "GLOBALNO\numyra_baza\pomeni.json"
if (Test-Path $NumYraPot) {
    try {
        $Vsebina = Get-Content $NumYraPot -Raw
        $Json = $Vsebina | ConvertFrom-Json
        
        Write-Host "[✓] numyra_baza/pomeni.json" -ForegroundColor $BarvaUspeh
        
        if ($Json.stevila) {
            $SteviloPomenov = ($Json.stevila | Get-Member -MemberType NoteProperty).Count
            Write-Host "    → $SteviloPomenov številk s pomeni" -ForegroundColor DarkGray
        }
        
        if ($Json.angelska_stevila) {
            $Angelska = ($Json.angelska_stevila | Get-Member -MemberType NoteProperty).Count
            Write-Host "    → $Angelska angelskih številk" -ForegroundColor DarkGray
        }
    } catch {
        Write-Host "[✗] Napaka pri branju" -ForegroundColor $BarvaNapaka
    }
} else {
    Write-Host "[⚠] numyra_baza/pomeni.json MANJKA (uporablja fallback)" -ForegroundColor $BarvaOpozorilo
}

# Sef
Write-Host ""
Write-Host "Varovani podatki (sef):" -ForegroundColor $BarvaInfo

$SefPot = Join-Path $PotProjekta "PODATKI\sef"
if (Test-Path $SefPot) {
    $SefDatoteke = Get-ChildItem $SefPot -File -ErrorAction SilentlyContinue
    Write-Host "[✓] PODATKI/sef/ obstaja" -ForegroundColor $BarvaUspeh
    Write-Host "    → $($SefDatoteke.Count) datotek" -ForegroundColor DarkGray
} else {
    Write-Host "[i] PODATKI/sef/ še ne obstaja (bo ustvarjeno ob prvem zagonu)" -ForegroundColor $BarvaInfo
}

Write-Host ""
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "Preverjanje končano!" -ForegroundColor $BarvaInfo
