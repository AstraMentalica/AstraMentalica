<#
.SYNOPSIS
    Preveri poti v PHP kodi.
.DESCRIPTION
    Analizira vse poti v PHP datotekah in preveri njihovo veljavnost ter morebitne podvojitve.
.EXAMPLE
    .\preveri_poti.ps1
.EXAMPLE
    .\preveri_poti.ps1 -PotProjekta "D:\Projekti\AstraMentalica"
#>

# ============================================================
# AstraMentalica - Preverjanje poti v kodi
# ============================================================
# Namen: Validacija poti za Copilot optimizacijo
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

# Barve
$BarvaUspeh = "Green"
$BarvaNapaka = "Red"
$BarvaOpozorilo = "Yellow"
$BarvaInfo = "Cyan"

# Inicializacija
$SeznamPotnihNapak = @()
$SkupajPot = 0
$VeljavnePoti = 0
$NeveljavnePoti = 0
$RelativnePoti = 0

function Write-Naslov {
    param([string]$Besedilo)
    Write-Host ""
    Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
    Write-Host $Besedilo -ForegroundColor $BarvaInfo
    Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
}

Write-Naslov "ASTRA MENTALICA - PREVERJANJE POTI V KODI"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor White
Write-Host "Čas: $(Get-Date -Format 'dd.MM.yyyy HH:mm:ss')" -ForegroundColor White

# Najdi vse PHP datoteke
Write-Host ""
Write-Host "Iskanje PHP datotek..." -ForegroundColor $BarvaInfo

$PhpDatoteke = Get-ChildItem -Path $PotProjekta -Filter "*.php" -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch "\\.venv\\|\\vendor\\|\\node_modules\\" }

Write-Host "Najdenih $($PhpDatoteke.Count) PHP datotek" -ForegroundColor White

# Vzorci za iskanje poti
$VzorecAbsolutnaPot = '(?i)(?:require|include|require_once|include_once|file_exists|is_dir)\s*\(\s*[\'"]([A-Za-z]:\\|/)[^\'"]+[\'"]'
$VzorecRelativnaPot = '(?i)(?:require|include|require_once|include_once|__DIR__\s*\.\s*[\'"][^\'"]+[\'"]|dirname\(__FILE__\)'
$VzorecGetcwd = '(?i)getcwd\(\)|getcwd\s*\(\s*\)'

Write-Host ""
Write-Host "Analiziranje poti..." -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

foreach ($Datoteka in $PhpDatoteke) {
    $Vsebina = Get-Content $Datoteka.FullName -Raw -ErrorAction SilentlyContinue
    $RelativnaPot = $Datoteka.FullName.Replace($PotProjekta, "").TrimStart("\")
    
    # Preveri __DIR__ uporabo
    $DirUporabe = ([regex]::Matches($Vsebina, '__DIR__')).Count
    if ($DirUporabe -gt 0 -and $Datoteka.Name -ne "pot.php") {
        Write-Host "[⚠] $RelativnaPot - $($DirUporabe)x __DIR__" -ForegroundColor $BarvaOpozarilo
    }
    
    # Preveri require/include
    $Matches = [regex]::Matches($Vsebina, $VzorecAbsolutnaPot)
    foreach ($Match in $Matches) {
        $SkupajPot++
        $Pot = $Match.Groups[1].Value
        
        # Preveri ali pot obstaja
        if (Test-Path $Pot) {
            $VeljavnePoti++
        } else {
            $NeveljavnePoti++
            Write-Host "[✗] $RelativnaPot" -ForegroundColor $BarvaNapaka
            Write-Host "    → Neveljavna pot: $Pot" -ForegroundColor DarkGray
            $SeznamPotnihNapak += [PSCustomObject]@{
                Datoteka = $RelativnaPot
                Pot = $Pot
                Tip = "Absolutna pot"
            }
        }
    }
    
    # Preveri POT_* konstante
    $PotMatches = [regex]::Matches($Vsebina, 'POT_[A-Z_]+')
    if ($PotMatches.Count -gt 0 -and $Strict) {
        Write-Host "    └─ Uporablja $($PotMatches.Count) POT_* konstant" -ForegroundColor DarkGray
    }
}

# Preveri specifične poti v pot.php
Write-Host ""
Write-Host "Preverjanje sistemskih poti..." -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

$PotPhp = Join-Path $PotProjekta "pot.php"
if (Test-Path $PotPhp) {
    $Vsebina = Get-Content $PotPhp -Raw
    
    # Ekstrahiraj konstante
    $Konstante = [regex]::Matches($Vsebina, "define\s*\(\s*['\"](POT_[A-Z_]+)['\"]")
    
    foreach ($Konstanta in $Konstante) {
        $Ime = $Konstanta.Groups[1].Value
        $Pot = [regex]::Match($Vsebina, "$Ime[^,]+,\s*([^)]+)").Groups[1].Value.Trim()
        
        if ($Pot -match 'POT_[A-Z]+') {
            Write-Host "[✓] $Ime" -ForegroundColor $BarvaUspeh
        }
    }
}

# Preveri Modul_Bridge poti
Write-Host ""
Write-Host "Preverjanje Modul_Bridge poti..." -ForegroundColor $BarvaInfo

$BridgePot = Join-Path $PotProjekta "MODULI\Modul_Bridge\modul_bridge.php"
if (Test-Path $BridgePot) {
    $Vsebina = Get-Content $BridgePot -Raw
    
    # Preveri iskane poti
    $IskanePotiMatches = [regex]::Matches($Vsebina, '\$iskanePoti\s*=\s*\[([^\]]+)\]')
    foreach ($Match in $IskanePotiMatches) {
        Write-Host "[i] Bridge iskane poti:" -ForegroundColor $BarvaInfo
        $VsebinaMatcha = $Match.Groups[1].Value
        $PosameznePoti = [regex]::Matches($VsebinaMatcha, '[\'"]([^\'"]+)[\'"]')
        foreach ($Pot in $PosameznePoti) {
            $PopolnaPot = $Pot.Groups[1].Value -replace '__DIR__', (Split-Path $BridgePot -Parent)
            if (Test-Path $PopolnaPot) {
                Write-Host "  [✓] $PopolnaPot" -ForegroundColor $BarvaUspeh
            } else {
                Write-Host "  [✗] $PopolnaPot" -ForegroundColor $BarvaNapaka
            }
        }
    }
}

# Preveri konfiguracijske poti
Write-Host ""
Write-Host "Preverjanje konfiguracijskih poti..." -ForegroundColor $BarvaInfo

$KonfiguracijskePoti = @(
    @{Pot="MODULI/Modul_Bridge";Opis="Modul Bridge"},
    @{Pot="MODULI/Modul_Bridge/embed";Opis="Bridge embed"},
    @{Pot="MODULI/Modul_Bridge/jedro";Opis="Bridge jedro"},
    @{Pot="SISTEM/kernel";Opis="Sistemsko jedro"},
    @{Pot="SISTEM/kernel/jedro";Opis="Kernel jedro"},
    @{Pot="SISTEM/storitve_svetov";Opis="Storitve svetov"},
    @{Pot="PODATKI/sef";Opis="Varovani podatki"},
    @{Pot="PODATKI/registri";Opis="Registri"},
    @{Pot="ADAPTER/izhod_kanali";Opis="Izhodni kanali"}
)

foreach ($Konf in $KonfiguracijskePoti) {
    $PolnaPot = Join-Path $PotProjekta $Konf.Pot
    
    if (Test-Path $PolnaPot) {
        Write-Host "[✓] $($Konf.Opis): $($Konf.Pot)" -ForegroundColor $BarvaUspeh
    } else {
        Write-Host "[✗] $($Konf.Opis): $($Konf.Pot) - MANJKA" -ForegroundColor $BarvaNapaka
        $NeveljavnePoti++
        $SeznamPotnihNapak += [PSCustomObject]@{
            Datoteka = $Konf.Pot
            Pot = $Konf.Pot
            Tip = "Manjkajoča mapa"
        }
    }
}

# ============================================================
# POVZETEK
# ============================================================
Write-Host ""
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "POVZETEK PREVERJANJA POTI" -ForegroundColor $BarvaInfo
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host ""
Write-Host "Skupaj najdenih poti:  $SkupajPot" -ForegroundColor White
Write-Host "Veljavnih:             $VeljavnePoti" -ForegroundColor $BarvaUspeh
Write-Host "Neveljavnih:           $NeveljavnePoti" -ForegroundColor $(if($NeveljavnePoti -gt 0){$BarvaNapaka}else{$BarvaUspeh})

if ($SeznamPotnihNapak.Count -gt 0) {
    Write-Host ""
    Write-Host "TEŽAVE S POTMI:" -ForegroundColor $BarvaNapaka
    $SeznamPotnihNapak | Group-Object Datoteka | ForEach-Object {
        Write-Host "  $($_.Name)" -ForegroundColor $BarvaNapaka
        $_.Group | ForEach-Object {
            Write-Host "    → $($_.Pot) [$($_.Tip)]" -ForegroundColor DarkGray
        }
    }
}

Write-Host ""
if ($NeveljavnePoti -eq 0) {
    Write-Host "✓ Vse poti so veljavne!" -ForegroundColor $BarvaUspeh
} else {
    Write-Host "✗ Najdenih $NeveljavnePoti težav s potmi!" -ForegroundColor $BarvaNapaka
}
