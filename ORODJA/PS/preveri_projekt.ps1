<#
.SYNOPSIS
    Preveri strukturo projekta AstraMentalica.
.DESCRIPTION
    Analizira strukturo projekta in prikaže hierarhijo mapa, število datotek
    in pomembne konfiguracijske datoteke.
.EXAMPLE
    .\preveri_projekt.ps1
#>

# ============================================================
# AstraMentalica - Preverjanje strukture projekta
# ============================================================
# Namen: Analiza strukture projekta za Copilot optimizacijo
# Avtor: AstraMentalica Mojster
# Verzija: 1.0.0
# ============================================================

[CmdletBinding()]
param(
    [Parameter(Mandatory=$false)]
    [string]$PotProjekta = $PSScriptRoot | Split-Path -Parent
)

# Barve za izpis
$BarvaNaslov = "Cyan"
$BarvaOpravilo = "Green"
$BarvaOpozorilo = "Yellow"
$BarvaNapaka = "Red"
$BarvaInfo = "White"

# Inicializacija števcev
$SkupajDatotek = 0
$SkupajMap = 0
$Rezultati = @{}

function Write-Naslov {
    param([string]$Besedilo)
    Write-Host ""
    Write-Host ("=" * 70) -ForegroundColor $BarvaNaslov
    Write-Host $Besedilo -ForegroundColor $BarvaNaslov
    Write-Host ("=" * 70) -ForegroundColor $BarvaNaslov
}

function Write-Podnaslov {
    param([string]$Besedilo)
    Write-Host ""
    Write-Host ("-" * 50) -ForegroundColor $BarvaInfo
    Write-Host $Besedilo -ForegroundColor $BarvaInfo
}

function Write-Sporocilo {
    param(
        [string]$Besedilo,
        [string]$Tip = "Info"
    )
    switch ($Tip) {
        "Opravilo" { Write-Host "[✓] $Besedilo" -ForegroundColor $BarvaOpravilo }
        "Opozorilo" { Write-Host "[⚠] $Besedilo" -ForegroundColor $BarvaOpozorilo }
        "Napaka" { Write-Host "[✗] $Besedilo" -ForegroundColor $BarvaNapaka }
        default { Write-Host "[i] $Besedilo" -ForegroundColor $BarvaInfo }
    }
}

# Začetek preverjanja
Clear-Host
Write-Naslov "ASTRA MENTALICA - PREVERJANJE PROJEKTA"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor $BarvaInfo
Write-Host "Čas: $(Get-Date -Format 'dd.MM.yyyy HH:mm:ss')" -ForegroundColor $BarvaInfo

# Preveri ali pot obstaja
if (-not (Test-Path $PotProjekta)) {
    Write-Sporocilo "Pot ne obstaja: $PotProjekta" -Tip "Napaka"
    exit 1
}

# ============================================================
# 1. ANALIZA GLAVNIH MAP
# ============================================================
Write-Podnaslov "1. Glavne mape projekta"

$GlavneMape = @(
    "ADAPTER", "AI", "ASTRA", "GLOBALNO", "MODULI",
    "PODATKI", "SISTEM", "UPORABNIKI", "VSEBINA"
)

foreach ($Mapa in $GlavneMape) {
    $PolnaPot = Join-Path $PotProjekta $Mapa
    if (Test-Path $PolnaPot) {
        $SteviloDatotek = (Get-ChildItem -Path $PolnaPot -Recurse -File -ErrorAction SilentlyContinue).Count
        $SteviloPodmap = (Get-ChildItem -Path $PolnaPot -Directory -ErrorAction SilentlyContinue).Count
        Write-Sporocilo "$Mapa`: $SteviloDatotek datotek, $SteviloPodmap podmap" -Tip "Opravilo"
        $SkupajDatotek += $SteviloDatotek
        $SkupajMap += $SteviloPodmap
    } else {
        Write-Sporocilo "$Mapa`: MANJKA" -Tip "Opozorilo"
    }
}

# ============================================================
# 2. KLJUČNE DATOTEKE
# ============================================================
Write-Podnaslov "2. Ključne datoteke"

$KljučneDatoteke = @(
    @{Ime="index.php";Opis="Vstopna točka"},
    @{Ime="pot.php";Opis="Absolutno sidro"},
    @{Ime="ai_proxy.php";Opis="AI proxy"},
    @{Ime="AstraMentalica.md";Opis="Glavni README"}
)

foreach ($Datoteka in $KljučneDatoteke) {
    $PolnaPot = Join-Path $PotProjekta $Datoteka.Ime
    if (Test-Path $PolnaPot) {
        $Velikost = (Get-Item $PolnaPot).Length
        Write-Sporocilo "$($Datoteka.Ime) ($($Datoteka.Opis)): $([math]::Round($Velikost/1KB, 2)) KB" -Tip "Opravilo"
    } else {
        Write-Sporocilo "$($Datoteka.Ime) ($($Datoteka.Opis)): MANJKA" -Tip "Opozorilo"
    }
}

# ============================================================
# 3. PHP DATOTEKE PO MAPAH
# ============================================================
Write-Podnaslov "3. PHP datoteke po mapah"

$PhpPoMapah = @{}
Get-ChildItem -Path $PotProjekta -Filter "*.php" -Recurse -File -ErrorAction SilentlyContinue |
    ForEach-Object {
        $Mapa = $_.Directory.Name
        if (-not $PhpPoMapah.ContainsKey($Mapa)) {
            $PhpPoMapah[$Mapa] = 0
        }
        $PhpPoMapah[$Mapa]++
    }

$PhpPoMapah.GetEnumerator() | Sort-Object Value -Descending | Select-Object -First 10 | ForEach-Object {
    Write-Host "  $($_.Key): $($_.Value) PHP datotek" -ForegroundColor $BarvaInfo
}

# ============================================================
# 4. JSON DATOTEKE
# ============================================================
Write-Podnaslov "4. JSON konfiguracijske datoteke"

$JsonDatoteke = Get-ChildItem -Path $PotProjekta -Filter "*.json" -Recurse -File -ErrorAction SilentlyContinue

foreach ($Json in $JsonDatoteke) {
    $RelativnaPot = $Json.FullName.Replace($PotProjekta, "").TrimStart("\")
    Write-Sporocilo "$RelativnaPot" -Tip "Opravilo"
}

# ============================================================
# 5. MODULI
# ============================================================
Write-Podnaslov "5. Seznam modulov"

$PotModulov = Join-Path $PotProjekta "MODULI\Univerzalno"
if (Test-Path $PotModulov) {
    $Moduli = Get-ChildItem -Path $PotModulov -Directory -ErrorAction SilentlyContinue
    foreach ($Modul in $Moduli) {
        $ModulDatoteke = (Get-ChildItem -Path $Modul.FullName -Filter "*.php" -File -ErrorAction SilentlyContinue).Count
        Write-Host "  $($Modul.Name): $ModulDatoteke datotek" -ForegroundColor $BarvaInfo
    }
}

# ============================================================
# 6. PREGLED KODE
# ============================================================
Write-Podnaslov "6. Statistika kode"

$PhpDatoteke = Get-ChildItem -Path $PotProjekta -Filter "*.php" -Recurse -File -ErrorAction SilentlyContinue
$SkupajVrstic = 0
$SkupajVelikost = 0

foreach ($Datoteka in $PhpDatoteke) {
    $Vrstice = (Get-Content $Datoteka.FullName -ErrorAction SilentlyContinue | Measure-Object -Line).Lines
    $SkupajVrstic += $Vrstice
    $SkupajVelikost += $Datoteka.Length
}

Write-Sporocilo "PHP datotek: $($PhpDatoteke.Count)" -Tip "Info"
Write-Sporocilo "Skupaj vrstic kode: $SkupajVrstic" -Tip "Info"
Write-Sporocilo "Skupaj velikost: $([math]::Round($SkupajVelikost/1MB, 2)) MB" -Tip "Info"

# ============================================================
# 7. VARNOSTNI CHECK
# ============================================================
Write-Podnaslov "7. Varnostni pregled"

# Preveri .gitignore
$Gitignore = Join-Path $PotProjekta ".gitignore"
if (Test-Path $Gitignore) {
    Write-Sporocilo ".gitignore: obstaja" -Tip "Opravilo"
} else {
    Write-Sporocilo ".gitignore: MANJKA" -Tip "Opozorilo"
}

# Preveri .htaccess
$Htaccess = Join-Path $PotProjekta ".htaccess"
if (Test-Path $Htaccess) {
    Write-Sporocilo ".htaccess: obstaja" -Tip "Opravilo"
} else {
    Write-Sporocilo ".htaccess: MANJKA (priporočeno za Apache)" -Tip "Opozorilo"
}

# Preveri varnostne konstante
$VarnostneDatoteke = Get-ChildItem -Path $PotProjekta -Filter "varnost*.php" -Recurse -File -ErrorAction SilentlyContinue
Write-Sporocilo "Varnostne datoteke: $($VarnostneDatoteke.Count)" -Tip "Info"

# ============================================================
# POVZETEK
# ============================================================
Write-Naslov "POVZETEK"
Write-Host "Skupaj datotek: $SkupajDatotek" -ForegroundColor $BarvaOpravilo
Write-Host "Skupaj podmap: $SkupajMap" -ForegroundColor $BarvaOpravilo
Write-Host "PHP datotek: $($PhpDatoteke.Count)" -ForegroundColor $BarvaOpravilo
Write-Host "JSON datotek: $($JsonDatoteke.Count)" -ForegroundColor $BarvaOpravilo
Write-Host "Skupaj vrstic kode: $SkupajVrstic" -ForegroundColor $BarvaOpravilo
Write-Host "Skupaj velikost kode: $([math]::Round($SkupajVelikost/1MB, 2)) MB" -ForegroundColor $BarvaOpravilo

Write-Host ""
Write-Host "Preverjanje končano!" -ForegroundColor $BarvaNaslov
