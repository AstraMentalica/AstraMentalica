<#
.SYNOPSIS
    Preveri jezikovne in lokalne nastavitve.
.DESCRIPTION
    Analizira jezik kode, comments in docs.
.EXAMPLE
    .\preveri_locale.ps1
#>

# ============================================================
# AstraMentalica - Preverjanje lokalizacije
# ============================================================
# Namen: Analiza jezika v projektu
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

Write-Naslov "ASTRA MENTALICA - PREVERJANJE LOKALIZACIJE"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor White

# Jezik kode
Write-Host ""
Write-Host "Analiza jezika v kodi:" -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

$PhpDatoteke = Get-ChildItem -Path $PotProjekta -Filter "*.php" -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch "\\.venv\\|\\vendor\\|\\node_modules\\" }

$Slovenski = 0
$Angleski = 0
$Mešani = 0

foreach ($Datoteka in $PhpDatoteke) {
    $Vsebina = Get-Content $Datoteka.FullName -Raw -ErrorAction SilentlyContinue
    $RelativnaPot = $Datoteka.FullName.Replace($PotProjekta, "").TrimStart("\")
    
    # Štej črke šumnike
    $Sumniki = ($Vsebina -split '' | Where-Object { $_ -match '[čšžČŠŽ]' }).Count
    $SkupajCrk = $Vsebina.Length
    
    $Razmerje = if ($SkupajCrk -gt 0) { $Sumniki / $SkupajCrk } else { 0 }
    
    if ($Razmerje -gt 0.05) {
        $Slovenski++
    } elseif ($Vsebina -match '\bfunction\b|\bclass\b|\breturn\b|\bif\b|\bforeach\b') {
        $Angleski++
    } else {
        $Mešani++
    }
}

Write-Host "PHP datotek: $($PhpDatoteke.Count)" -ForegroundColor White
Write-Host "Pretežno slovenskih: $Slovenski" -ForegroundColor $BarvaUspeh
Write-Host "Pretežno angleških: $Angleski" -ForegroundColor $BarvaOpozorilo
Write-Host "Mešanih: $Mešani" -ForegroundColor DarkGray

# Komentar jezik
Write-Host ""
Write-Host "Jezik komentarjev:" -ForegroundColor $BarvaInfo

$SlovenskiKomentarji = 0
$AngleskiKomentarji = 0

foreach ($Datoteka in $PhpDatoteke) {
    $Vsebina = Get-Content $Datoteka.FullName -Raw -ErrorAction SilentlyContinue
    
    # PHP komentarji
    $PhpKomentarji = [regex]::Matches($Vsebina, '(?://.*|#.*|/\*[\s\S]*?\*/)')
    foreach ($Komentar in $PhpKomentarji) {
        $Besedilo = $Komentar.Value
        if ($Besedilo -match '[čšžČŠŽ]') {
            $SlovenskiKomentarji++
        } else {
            $AngleskiKomentarji++
        }
    }
}

Write-Host "Slovenskih komentarjev: $SlovenskiKomentarji" -ForegroundColor $BarvaUspeh
Write-Host "Angleških komentarjev: $AngleskiKomentarji" -ForegroundColor $BarvaOpozorilo

# Privzeti jezik
Write-Host ""
Write-Host "Privzeti jezik v kodi:" -ForegroundColor $BarvaInfo

$VzorecJezik = '🌐 JEZIK:\s*\n\s*\*\s*(\w+)'
$ Jeziki = @{}

foreach ($Datoteka in $PhpDatoteke | Select-Object -First 50) {
    $Vsebina = Get-Content $Datoteka.FullName -Raw -ErrorAction SilentlyContinue
    
    $JezikMatch = [regex]::Match($Vsebina, $VzorecJezik)
    if ($JezikMatch.Success) {
        $Jezik = $JezikMatch.Groups[1].Value
        if (-not $Jeziki.ContainsKey($Jezik)) {
            $Jeziki[$Jezik] = 0
        }
        $Jeziki[$Jezik]++
    }
}

foreach ($Jezik in $Jeziki.Keys) {
    Write-Host "  $Jezik`: $($Jeziki[$Jezik]) datotek" -ForegroundColor White
}

# Oznake
Write-Host ""
Write-Host "Oznake (🏷️):" -ForegroundColor $BarvaInfo

$Oznake = @{}
foreach ($Datoteka in $PhpDatoteke | Select-Object -First 20) {
    $Vsebina = Get-Content $Datoteka.FullName -Raw -ErrorAction SilentlyContinue
    $Matches = [regex]::Matches($Vsebina, '🏷️ OZNAKE:\s*\n\s*\*\s*(.+)')
    
    foreach ($Match in $Matches) {
        $TrenutneOznake = $Match.Groups[1].Value -split ',\s*'
        foreach ($Oznaka in $TrenutneOznake) {
            $Oznaka = $Oznaka.Trim()
            if (-not $Oznake.ContainsKey($Oznaka)) {
                $Oznake[$Oznaka] = 0
            }
            $Oznake[$Oznaka]++
        }
    }
}

$Skupaj = $Oznake.Values | Measure-Object -Sum | Select-Object -ExpandProperty Sum
Write-Host "Najdenih $Skupaj oznak" -ForegroundColor White

$Oznake.GetEnumerator() | Sort-Object Value -Descending | Select-Object -First 10 | ForEach-Object {
    Write-Host "  $($_.Key): $($_.Value)x" -ForegroundColor DarkGray
}

Write-Host ""
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "Projekt uporablja: Slovenščina" -ForegroundColor $BarvaUspeh
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
