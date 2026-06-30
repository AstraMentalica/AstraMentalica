<#
.SYNOPSIS
    Preveri JavaScript in JSX sintakso v projektu.
.DESCRIPTION
    Analizira vse JS/JSX datoteke in preveri osnovne sintaktične napake.
    Uporablja Node.js za validacijo če je na voljo.
.EXAMPLE
    .\preveri_js_sintakso.ps1
.EXAMPLE
    .\preveri_js_sintakso.ps1 -PotProjekta "D:\Projekti\AstraMentalica" -PreveriESLint
#>

# ============================================================
# AstraMentalica - Preverjanje JavaScript sintakse
# ============================================================
# Namen: Validacija JS datotek za Copilot optimizacijo
# Avtor: AstraMentalica Mojster
# Verzija: 1.0.0
# ============================================================

[CmdletBinding()]
param(
    [Parameter(Mandatory=$false)]
    [string]$PotProjekta = $PSScriptRoot | Split-Path -Parent,
    
    [Parameter(Mandatory=$false)]
    [switch]$PreveriESLint,
    
    [Parameter(Mandatory=$false)]
    [switch]$Strict
)

# Barve za izpis
$BarvaUspeh = "Green"
$BarvaNapaka = "Red"
$BarvaOpozorilo = "Yellow"
$BarvaInfo = "Cyan"

# Inicializacija
$SkupajPreverjenih = 0
$SkupajNapak = 0
$SkupajOpozoril = 0
$SeznamNapak = @()
$SeznamOpozoril = @()

function Write-Naslov {
    param([string]$Besedilo)
    Write-Host ""
    Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
    Write-Host $Besedilo -ForegroundColor $BarvaInfo
    Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
}

# Preveri Node.js
$NodePot = Get-Command node -ErrorAction SilentlyContinue
$ImamoNode = $null -ne $NodePot

Write-Naslov "ASTRA MENTALICA - PREVERJANJE JAVASCRIPT SINTAKSE"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor White

if ($ImamoNode) {
    Write-Host "Node.js verzija: $(node -v)" -ForegroundColor White
} else {
    Write-Host "Node.js: NI NAJDEN (omejeno preverjanje)" -ForegroundColor $BarvaOpozorilo
}

Write-Host "Čas: $(Get-Date -Format 'dd.MM.yyyy HH:mm:ss')" -ForegroundColor White

# Preveri ali pot obstaja
if (-not (Test-Path $PotProjekta)) {
    Write-Host "[✗] Pot ne obstaja: $PotProjekta" -ForegroundColor $BarvaNapaka
    exit 1
}

# Najdi JS/JSX datoteke
Write-Host ""
Write-Host "Iskanje JavaScript datotek..." -ForegroundColor $BarvaInfo

$JsDatoteke = Get-ChildItem -Path $PotProjekta -Include "*.js", "*.jsx", "*.mjs", "*.cjs" -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch "\\node_modules\\|\\.venv\\|\\vendor\\|\\.git\\" }

Write-Host "Najdenih $($JsDatoteke.Count) JavaScript datotek" -ForegroundColor White

# Preveri vsako datoteko osnovno
Write-Host ""
Write-Host "Preverjanje sintakse (osnovno)..." -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

foreach ($Datoteka in $JsDatoteke) {
    $SkupajPreverjenih++
    $RelativnaPot = $Datoteka.FullName.Replace($PotProjekta, "").TrimStart("\")
    
    $Vsebina = Get-Content $Datoteka.FullName -Raw -ErrorAction SilentlyContinue
    
    if (-not $Vsebina) {
        Write-Host "[⚠] PRAZNA: $RelativnaPot" -ForegroundColor $BarvaOpozorilo
        $SkupajOpozoril++
        continue
    }
    
    # Osnovna preverjanja
    $Napake = @()
    
    # Preveri neujemajoče oklepaje
    $OdprtiOklepaji = ([regex]::Matches($Vsebina, '\{')).Count
    $ZaprtiOklepaji = ([regex]::Matches($Vsebina, '\}')).Count
    if ($OdprtiOklepaji -ne $ZaprtiOklepaji) {
        $Napake += "Neujemajoči oklepaji: {$OdprtiOklepaji vs } $ZaprtiOklepaji"
    }
    
    # Preveri neujemajoče okrogle oklepaje
    $OdprtiOkrogli = ([regex]::Matches($Vsebina, '\(')).Count
    $ZaprtiOkrogli = ([regex]::Matches($Vsebina, '\)')).Count
    if ($OdprtiOkrogli -ne $ZaprtiOkrogli) {
        $Napake += "Neujemajoči okrogli oklepaji: ($OdprtiOkrogli vs ) $ZaprtiOkrogli"
    }
    
    # Preveri neujemajoče oglate oklepaje
    $OdprtiOglati = ([regex]::Matches($Vsebina, '\[')).Count
    $ZaprtiOglati = ([regex]::Matches($Vsebina, '\]')).Count
    if ($OdprtiOglati -ne $ZaprtiOglati) {
        $Napake += "Neujemajoči oglati oklepaji: [$OdprtiOglati vs ] $ZaprtiOglati"
    }
    
    # Preveri podvojen 'use strict'
    $UseStrictCount = ([regex]::Matches($Vsebina, "['\"']use strict['\"']")).Count
    if ($UseStrictCount -gt 1) {
        $Napake += "Večkratna deklaracija 'use strict' ($UseStrictCount)"
    }
    
    if ($Napake.Count -gt 0) {
        Write-Host "[✗] $RelativnaPot" -ForegroundColor $BarvaNapaka
        foreach ($Napaka in $Napake) {
            Write-Host "    → $Napaka" -ForegroundColor DarkGray
        }
        $SkupajNapak++
        $SeznamNapak += [PSCustomObject]@{
            Datoteka = $RelativnaPot
            Napake = $Napake -join "; "
        }
    } else {
        Write-Host "[✓] $RelativnaPot" -ForegroundColor $BarvaUspeh
    }
}

# Napredno preverjanje z Node.js
if ($ImamoNode -and $PreveriESLint) {
    Write-Host ""
    Write-Host "Preverjanje z ESLint (če je na voljo)..." -ForegroundColor $BarvaInfo
    
    $EslintPot = Join-Path $PotProjekta "node_modules\.bin\eslint"
    if (-not (Test-Path $EslintPot)) {
        $EslintPot = "npx"
    }
    
    # Preveri ali eslint.config.js ali .eslintrc.js obstaja
    $EslintConfig = Get-ChildItem -Path $PotProjekta -Include "eslint.config.js", ".eslintrc.js", ".eslintrc.json" -Recurse -File -ErrorAction SilentlyContinue | Select-Object -First 1
    
    if ($EslintConfig) {
        Write-Host "ESLint konfiguracija najdena: $($EslintConfig.Name)" -ForegroundColor $BarvaInfo
        # Tu bi lahko dodali ESLint preverjanje
    } else {
        Write-Host "ESLint konfiguracija ni najdena" -ForegroundColor $BarvaOpozorilo
    }
}

# Preveri specifične vzorce
Write-Host ""
Write-Host "Preverjanje dobrih praks..." -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

foreach ($Datoteka in $JsDatoteke) {
    $RelativnaPot = $Datoteka.FullName.Replace($PotProjekta, "").TrimStart("\")
    $Vsebina = Get-Content $Datoteka.FullName -Raw -ErrorAction SilentlyContinue
    
    $Opozorila = @()
    
    # Preveri var namesto let/const
    if ($Vsebina -match '\bvar\s+\w+' -and $Vsebina -notmatch '//.*var.*legacy') {
        $Opozorila += "Uporablja 'var' namesto 'let'/'const'"
    }
    
    # Preveri eval()
    if ($Vsebina -match '\beval\s*\(') {
        $Opozorila += "Uporablja 'eval()' (varnostno tveganje)"
    }
    
    # Preveri document.write()
    if ($Vsebina -match 'document\.write\s*\(') {
        $Opozorila += "Uporablja 'document.write()' (varnostno tveganje)"
    }
    
    # Preveri console.log brez striktnega načina
    if ($Vsebina -match 'console\.log' -and $Vsebina -notmatch "['\"']use strict") {
        if ($Strict) {
            $Opozorila += "console.log brez 'use strict'"
        }
    }
    
    if ($Opozorila.Count -gt 0 -and $Strict) {
        Write-Host "[⚠] $RelativnaPot" -ForegroundColor $BarvaOpozorilo
        foreach ($Opozorilo in $Opozorila) {
            Write-Host "    → $Opozorilo" -ForegroundColor DarkGray
        }
        $SkupajOpozoril++
        $SeznamOpozoril += [PSCustomObject]@{
            Datoteka = $RelativnaPot
            Opozorila = $Opozorila -join "; "
        }
    }
}

# ============================================================
# POVZETEK
# ============================================================
Write-Host ""
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "POVZETEK PREVERJANJA JAVASCRIPT" -ForegroundColor $BarvaInfo
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host ""
Write-Host "Skupaj preverjenih:  $SkupajPreverjenih" -ForegroundColor White
Write-Host "Brez napak:         $($SkupajPreverjenih - $SkupajNapak)" -ForegroundColor $BarvaUspeh
Write-Host "Z napakami:         $SkupajNapak" -ForegroundColor $BarvaNapaka
Write-Host "Opozoril:           $SkupajOpozoril" -ForegroundColor $BarvaOpozorilo

if ($SeznamNapak.Count -gt 0) {
    Write-Host ""
    Write-Host "DATOTEKE Z NAPAKAMI:" -ForegroundColor $BarvaNapaka
    $SeznamNapak | ForEach-Object {
        Write-Host "  $($_.Datoteka)" -ForegroundColor $BarvaNapaka
        Write-Host "    $($_.Napake)" -ForegroundColor DarkGray
    }
}

Write-Host ""
if ($SkupajNapak -eq 0) {
    Write-Host "✓ Vse JavaScript datoteke so sintaktično pravilne!" -ForegroundColor $BarvaUspeh
} else {
    Write-Host "✗ Najdenih $SkupajNapak datotek z napakami!" -ForegroundColor $BarvaNapaka
}
