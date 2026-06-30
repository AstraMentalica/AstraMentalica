<#
.SYNOPSIS
    Preveri PHP sintakso v projektu AstraMentalica.
.DESCRIPTION
    Analizira vse PHP datoteke in preveri sintaktične napake.
    Uporablja PHP lint za preverjanje.
.EXAMPLE
    .\preveri_php_sintakso.ps1
.EXAMPLE
    .\preveri_php_sintakso.ps1 -PotProjekta "D:\Projekti\AstraMentalica" -Strict
#>

# ============================================================
# AstraMentalica - Preverjanje PHP sintakse
# ============================================================
# Namen: Validacija PHP datotek za Copilot optimizacijo
# Avtor: AstraMentalica Mojster
# Verzija: 1.0.0
# ============================================================

[CmdletBinding()]
param(
    [Parameter(Mandatory=$false)]
    [string]$PotProjekta = $PSScriptRoot | Split-Path -Parent,
    
    [Parameter(Mandatory=$false)]
    [switch]$Strict,
    
    [Parameter(Mandatory=$false)]
    [switch]$PopraviNeveljavne
)

# Barve za izpis
$BarvaUspeh = "Green"
$BarvaNapaka = "Red"
$BarvaOpozorilo = "Yellow"
$BarvaInfo = "Cyan"

# Inicializacija
$SkupajPreverjenih = 0
$SkupajNapak = 0
$SeznamNapak = @()
$SeznamOpozoril = @()

function Write-Naslov {
    param([string]$Besedilo)
    Write-Host ""
    Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
    Write-Host $Besedilo -ForegroundColor $BarvaInfo
    Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
}

function Write-PhpSporocilo {
    param(
        [string]$Besedilo,
        [string]$Pot,
        [string]$Tip = "Info"
    )
    switch ($Tip) {
        "Uspeh" { 
            Write-Host "[✓] " -ForegroundColor $BarvaUspeh -NoNewline
            Write-Host "$Besedilo" 
            if ($Pot) { Write-Host "         $Pot" -ForegroundColor DarkGray }
        }
        "Napaka" { 
            Write-Host "[✗] " -ForegroundColor $BarvaNapaka -NoNewline
            Write-Host "$Besedilo"
            if ($Pot) { Write-Host "         $Pot" -ForegroundColor DarkGray }
        }
        "Opozorilo" { 
            Write-Host "[⚠] " -ForegroundColor $BarvaOpozorilo -NoNewline
            Write-Host "$Besedilo"
            if ($Pot) { Write-Host "         $Pot" -ForegroundColor DarkGray }
        }
        default { 
            Write-Host "[i] " -ForegroundColor $BarvaInfo -NoNewline
            Write-Host "$Besedilo"
        }
    }
}

# Preveri ali PHP obstaja
$PhpPot = Get-Command php -ErrorAction SilentlyContinue
if (-not $PhpPot) {
    Write-Naslov "NAMESTITEV PHP"
    Write-Host "PHP ni najden v PATH!" -ForegroundColor $BarvaNapaka
    Write-Host "Namestite PHP in dodajte pot v sistemsko spremenljivko PATH." -ForegroundColor $BarvaInfo
    Write-Host ""
    Write-Host "Lahko ročno preverite datoteke z:" -ForegroundColor $BarvaOpozorilo
    Write-Host "  php -l datoteka.php" -ForegroundColor $BarvaInfo
    exit 1
}

Write-Naslov "ASTRA MENTALICA - PREVERJANJE PHP SINTAKSE"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor White
Write-Host "PHP verzija: $(php -v | Select-Object -First 1)" -ForegroundColor White
Write-Host "Čas: $(Get-Date -Format 'dd.MM.yyyy HH:mm:ss')" -ForegroundColor White

# Preveri ali pot obstaja
if (-not (Test-Path $PotProjekta)) {
    Write-PhpSporocilo "Pot ne obstaja: $PotProjekta" -Tip "Napaka"
    exit 1
}

# Najdi vse PHP datoteke
Write-Host ""
Write-Host "Iskanje PHP datotek..." -ForegroundColor $BarvaInfo

# Izključi mape ki jih ne preverjamo
$IzkluceneMape = @(".venv", "node_modules", ".git", "vendor")
$IzklucenaPot vzorca = $IzkluceneMape -join "|"

$PhpDatoteke = Get-ChildItem -Path $PotProjekta -Filter "*.php" -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch $IzklucenaPot vzorca }

Write-Host "Najdenih $($PhpDatoteke.Count) PHP datotek" -ForegroundColor White

# Preveri vsako datoteko
Write-Host ""
Write-Host "Preverjanje sintakse..." -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

foreach ($Datoteka in $PhpDatoteke) {
    $SkupajPreverjenih++
    $RelativnaPot = $Datoteka.FullName.Replace($PotProjekta, "").TrimStart("\")
    
    # PHP lint preverjanje
    $Rezultat = php -l $Datoteka.FullName 2>&1
    
    if ($LASTEXITCODE -eq 0) {
        Write-PhpSporocilo "OK" -Pot $RelativnaPot -Tip "Uspeh"
    } else {
        $SkupajNapak++
        $Napaka = $Rezultat -replace "PHP Notice:", "" -replace "PHP Warning:", ""
        
        # Parse napake za lepši izpis
        if ($Rezultat -match "Parse error|syntax error") {
            $SeznamNapak += [PSCustomObject]@{
                Datoteka = $RelativnaPot
                Napaka = $Napaka
                Tip = "Sintaktična napaka"
            }
            Write-PhpSporocilo "NAPAKA: $Napaka" -Pot $RelativnaPot -Tip "Napaka"
        } elseif ($Strict) {
            $SeznamOpozoril += [PSCustomObject]@{
                Datoteka = $RelativnaPot
                Napaka = $Napaka
                Tip = "Opozorilo"
            }
            Write-PhpSporocilo "OPOZORILO: $Napaka" -Pot $RelativnaPot -Tip "Opozorilo"
        } else {
            Write-PhpSporocilo "OK (z opozorili)" -Pot $RelativnaPot -Tip "Opozorilo"
        }
    }
    
    # Progress indicator za velike projekte
    if ($SkupajPreverjenih % 50 -eq 0) {
        Write-Host "  [$SkupajPreverjenih/$($PhpDatoteke.Count)] preverjenih..." -ForegroundColor DarkGray
    }
}

# ============================================================
# POVZETEK
# ============================================================
Write-Host ""
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "POVZETEK PREVERJANJA" -ForegroundColor $BarvaInfo
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host ""
Write-Host "Skupaj preverjenih:  $SkupajPreverjenih" -ForegroundColor White
Write-Host "Brez napak:          $($SkupajPreverjenih - $SkupajNapak)" -ForegroundColor $BarvaUspeh
Write-Host "S napakami:          $SkupajNapak" -ForegroundColor $BarvaNapaka

if ($SeznamNapak.Count -gt 0) {
    Write-Host ""
    Write-Host "DATOTEKE Z NAPAKAMI:" -ForegroundColor $BarvaNapaka
    Write-Host ("-" * 50)
    $SeznamNapak | ForEach-Object {
        Write-Host "  $($_.Datoteka)" -ForegroundColor $BarvaNapaka
        Write-Host "    $($_.Napaka)" -ForegroundColor DarkGray
    }
}

if ($SeznamOpozoril.Count -gt 0 -and $Strict) {
    Write-Host ""
    Write-Host "OPOZORILA:" -ForegroundColor $BarvaOpozorilo
    Write-Host ("-" * 50)
    $SeznamOpozoril | Select-Object -First 10 | ForEach-Object {
        Write-Host "  $($_.Datoteka): $($_.Napaka)" -ForegroundColor DarkGray
    }
    if ($SeznamOpozoril.Count -gt 10) {
        Write-Host "  ... in $($SeznamOpozoril.Count - 10) več opozoril" -ForegroundColor DarkGray
    }
}

# Shrani poročilo
$PorociloPot = Join-Path $PSScriptRoot "porocilo_php_sintaksa_$(Get-Date -Format 'yyyyMMdd_HHmmss').txt"
$Porocilo = @"
ASTRA MENTALICA - Poročilo PHP sintakse
=========================================
Datum: $(Get-Date -Format 'dd.MM.yyyy HH:mm:ss')
Pot: $PotProjekta

POVZETEK
--------
Skupaj preverjenih: $SkupajPreverjenih
Brez napak: $($SkupajPreverjenih - $SkupajNapak)
S napakami: $SkupajNapak

NAPAKE
------
$($SeznamNapak | ForEach-Object { "$($_.Datoteka): $($_.Napaka)" } | Out-String)

"@

# Shrani poročilo (samo če so napake)
if ($SkupajNapak -gt 0 -or $SeznamOpozoril.Count -gt 0) {
    $Porocilo | Out-File -FilePath $PorociloPot -Encoding UTF8
    Write-Host ""
    Write-Host "Poročilo shranjeno: $PorociloPot" -ForegroundColor $BarvaInfo
}

Write-Host ""
if ($SkupajNapak -eq 0) {
    Write-Host "✓ Vse PHP datoteke so sintaktično pravilne!" -ForegroundColor $BarvaUspeh
} else {
    Write-Host "✗ Najdenih $SkupajNapak datotek z napakami!" -ForegroundColor $BarvaNapaka
}
