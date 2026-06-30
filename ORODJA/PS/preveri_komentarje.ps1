<#
.SYNOPSIS
    Preveri komentarje v PHP in JS datotekah.
.DESCRIPTION
    Analizira komentarje glede na standarde projekta.
.EXAMPLE
    .\preveri_komentarje.ps1
#>

# ============================================================
# AstraMentalica - Preverjanje komentarjev
# ============================================================
# Namen: Analiza dokumentacije v kodi
# Avtor: AstraMentalica Mojster
# Verzija: 1.0.0
# ============================================================

[CmdletBinding()]
param(
    [Parameter(Mandatory=$false)]
    [string]$PotProjekta = $PSScriptRoot | Split-Path -Parent,
    
    [Parameter(Mandatory=$false)]
    [switch]$Strict
)

$BarvaInfo = "Cyan"
$BarvaNapaka = "Red"
$BarvaOpozorilo = "Yellow"

$SeznamRezultatov = @{
    SkupajDatotek = 0
    SDokumentiranimi = 0
    SNedokumentiranimi = 0
    NPravilnih = 0
    NKoncnih = 0
}

Write-Naslov "ASTRA MENTALICA - PREVERJANJE KOMENTARJEV"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor White

$PhpDatoteke = Get-ChildItem -Path $PotProjekta -Filter "*.php" -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch "\\.venv\\|\\vendor\\|\\node_modules\\" }

Write-Host "Analiziranje $($PhpDatoteke.Count) PHP datotek..." -ForegroundColor $BarvaInfo

# Vzorci za preverjanje
$VzorecHeader = '# ============================================================'
$VzorecFunkcija = 'function\s+\w+\s*\('
$VzorecPHPDoc = '/\*\*\s*\n.*?\*/\s*\n'

foreach ($Datoteka in $PhpDatoteke) {
    $SeznamRezultatov.SkupajDatotek++
    $Vsebina = Get-Content $Datoteka.FullName -Raw -ErrorAction SilentlyContinue
    $RelativnaPot = $Datoteka.FullName.Replace($PotProjekta, "").TrimStart("\")
    
    # Preveri header standard
    $ImaHeader = $Vsebina -match $VzorecHeader
    
    # Poišči funkcije brez PHPDoc
    $Funkcije = [regex]::Matches($Vsebina, $VzorecFunkcija)
    $DokumentiraneFunkcije = [regex]::Matches($Vsebina, $VzorecPHPDoc)
    
    if (-not $ImaHeader -and $Strict) {
        Write-Host "[⚠] Brez header: $RelativnaPot" -ForegroundColor $BarvaOpozorilo
        $SeznamRezultatov.SNedokumentiranimi++
    } else {
        $SeznamRezultatov.SDokumentiranimi++
    }
    
    # Preveri TODO/FIXME/XTODO komentarje
    $Todos = [regex]::Matches($Vsebina, '(TODO|FIXME|XTODO|HACK|XXX):')
    if ($Todos.Count -gt 0) {
        Write-Host "[i] $RelativnaPot - $($Todos.Count) TODO/FIXME" -ForegroundColor $BarvaInfo
    }
}

# preveri ključne datoteke
Write-Host ""
Write-Host "Ključne datoteke:" -ForegroundColor $BarvaInfo

$KljučneDatoteke = @(
    "pot.php",
    "index.php",
    "SISTEM\api.php",
    "ADAPTER\adapter.php",
    "MODULI\Modul_Bridge\modul_bridge.php",
    "SISTEM\kernel\zaganjalnik.php"
)

foreach ($Kljucna in $KljučneDatoteke) {
    $PolnaPot = Join-Path $PotProjekta $Kljucna
    if (Test-Path $PolnaPot) {
        $Vsebina = Get-Content $PolnaPot -Raw
        $ImaHeader = $Vsebina -match $VzorecHeader
        $ImaDatum = $Vsebina -match 'VERZIJA:'
        
        $Status = if ($ImaHeader -and $ImaDatum) { "✓" } elseif ($ImaHeader) { "⚠" } else { "✗" }
        $Barva = if ($Status -eq "✓") { "Green" } elseif ($Status -eq "⚠") { "Yellow" } else { "Red" }
        
        Write-Host "[$Status] $Kljucna" -ForegroundColor $Barva
    }
}

# ============================================================
# POVZETEK
# ============================================================
Write-Host ""
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "POVZETEK" -ForegroundColor $BarvaInfo
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "Skupaj datotek: $($SeznamRezultatov.SkupajDatotek)"
Write-Host "Dokumentiranih: $($SeznamRezultatov.SDokumentiranimi)" -ForegroundColor Green
Write-Host "Nedokumentiranih: $($SeznamRezultatov.SNedokumentiranimi)" -ForegroundColor Yellow
