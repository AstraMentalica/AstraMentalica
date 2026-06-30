<#
.SYNOPSIS
    Preveri JSON datoteke v projektu.
.DESCRIPTION
    Analizira vse JSON datoteke in preveri njihovo veljavnost ter strukturo.
.EXAMPLE
    .\preveri_json.ps1
.EXAMPLE
    .\preveri_json.ps1 -PotProjekta "D:\Projekti\AstraMentalica" -ShemaValidacija
#>

# ============================================================
# AstraMentalica - Preverjanje JSON datotek
# ============================================================
# Namen: Validacija JSON konfiguracij
# Avtor: AstraMentalica Mojster
# Verzija: 1.0.0
# ============================================================

[CmdletBinding()]
param(
    [Parameter(Mandatory=$false)]
    [string]$PotProjekta = $PSScriptRoot | Split-Path -Parent,
    
    [Parameter(Mandatory=$false)]
    [switch]$ShemaValidacija,
    
    [Parameter(Mandatory=$false)]
    [switch]$Strict
)

# Barve
$BarvaUspeh = "Green"
$BarvaNapaka = "Red"
$BarvaOpozorilo = "Yellow"
$BarvaInfo = "Cyan"

# Inicializacija
$SkupajPreverjenih = 0
$SkupajNapak = 0
$SkupajOpozoril = 0
$SeznamNapak = @()

function Write-Naslov {
    param([string]$Besedilo)
    Write-Host ""
    Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
    Write-Host $Besedilo -ForegroundColor $BarvaInfo
    Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
}

Write-Naslov "ASTRA MENTALICA - PREVERJANJE JSON DATOTEK"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor White
Write-Host "Čas: $(Get-Date -Format 'dd.MM.yyyy HH:mm:ss')" -ForegroundColor White

# Najdi JSON datoteke
Write-Host ""
Write-Host "Iskanje JSON datotek..." -ForegroundColor $BarvaInfo

$JsonDatoteke = Get-ChildItem -Path $PotProjekta -Filter "*.json" -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch "\\node_modules\\|\\.venv\\|\\vendor\\" }

Write-Host "Najdenih $($JsonDatoteke.Count) JSON datotek" -ForegroundColor White

Write-Host ""
Write-Host "Preverjanje JSON veljavnosti..." -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

foreach ($Datoteka in $JsonDatoteke) {
    $SkupajPreverjenih++
    $RelativnaPot = $Datoteka.FullName.Replace($PotProjekta, "").TrimStart("\")
    
    $Vsebina = Get-Content $Datoteka.FullName -Raw -ErrorAction SilentlyContinue
    
    # Preveri ali je datoteka prazna
    if ([string]::IsNullOrWhiteSpace($Vsebina)) {
        Write-Host "[⚠] PRAZNA: $RelativnaPot" -ForegroundColor $BarvaOpozorilo
        $SkupajOpozoril++
        continue
    }
    
    # Preveri zadnjo vejico
    if ($Vsebina -match ',(\s*[\]\}])') {
        Write-Host "[⚠] Zadnja vejica: $RelativnaPot" -ForegroundColor $BarvaOpozorilo
        $SkupajOpozoril++
    }
    
    try {
        $JsonObjekt = $Vsebina | ConvertFrom-Json -ErrorAction Stop
        
        Write-Host "[✓] $RelativnaPot" -ForegroundColor $BarvaUspeh
        
        # Dodatna preverjanja
        if ($Strict) {
            # Preveri ali je tabela ali objekt
            if ($JsonObjekt -is [System.Collections.ArrayList]) {
                Write-Host "    → Polje (array) z $($JsonObjekt.Count) elementi" -ForegroundColor DarkGray
            } elseif ($JsonObjekt -is [PSCustomObject]) {
                $Kljuci = ($JsonObjekt | Get-Member -MemberType NoteProperty).Name
                Write-Host "    → Objekt s $($Kljuci.Count) lastnostmi" -ForegroundColor DarkGray
            }
        }
        
    } catch {
        Write-Host "[✗] NEVELJAVEN JSON: $RelativnaPot" -ForegroundColor $BarvaNapaka
        Write-Host "    → $($_.Exception.Message)" -ForegroundColor DarkGray
        $SkupajNapak++
        $SeznamNapak += [PSCustomObject]@{
            Datoteka = $RelativnaPot
            Napaka = $_.Exception.Message
        }
    }
}

# Preveri specifične JSON datoteke projekta
Write-Host ""
Write-Host "Preverjanje sistemskih JSON datotek..." -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

$SistemskeDatoteke = @(
    @{Pot="PODATKI\registri\moduli_register.json";Opis="Register modulov"},
    @{Pot="PODATKI\data_bus.json";Opis="Data bus"},
    @{Pot="PODATKI\frekvence.json";Opis="Frekvence"},
    @{Pot="PODATKI\kanonični_varuhi.json";Opis="Kanonični varuhi"}
)

foreach ($Sistemska in $SistemskeDatoteke) {
    $PolnaPot = Join-Path $PotProjekta $Sistemska.Pot
    
    if (Test-Path $PolnaPot) {
        $Vsebina = Get-Content $PolnaPot -Raw -ErrorAction SilentlyContinue
        
        try {
            $Json = $Vsebina | ConvertFrom-Json
            Write-Host "[✓] $($Sistemska.Opis) ($($Sistemska.Pot))" -ForegroundColor $BarvaUspeh
        } catch {
            Write-Host "[✗] $($Sistemska.Opis) - $($_.Exception.Message)" -ForegroundColor $BarvaNapaka
        }
    } else {
        Write-Host "[?] $($Sistemska.Opis) - NE NAJDEN" -ForegroundColor $BarvaOpozorilo
    }
}

# Preveri module JSON datoteke
Write-Host ""
Write-Host "Preverjanje modul JSON datotek..." -ForegroundColor $BarvaInfo

$ModulJsonDatoteke = Get-ChildItem -Path "$PotProjekta\MODULI" -Filter "modul.json" -Recurse -File -ErrorAction SilentlyContinue

foreach ($ModulJson in $ModulJsonDatoteke) {
    $Vsebina = Get-Content $ModulJson.FullName -Raw -ErrorAction SilentlyContinue
    
    try {
        $Json = $Vsebina | ConvertFrom-Json
        
        $ImeModula = Split-Path $ModulJson.DirectoryName -Leaf
        $ObveznaPolja = @('aktiviran', 'nivo', 'tip')
        $Manjkajoča = $ObveznaPolja | Where-Object { -not $Json.PSObject.Properties.Name.Contains($_) }
        
        if ($Manjkajoča.Count -eq 0) {
            Write-Host "[✓] $ImeModula" -ForegroundColor $BarvaUspeh
        } else {
            Write-Host "[⚠] $ImeModula - manjkajoča polja: $($Manjkajoča -join ', ')" -ForegroundColor $BarvaOpozorilo
        }
    } catch {
        Write-Host "[✗] $($ModulJson.Name) - $($_.Exception.Message)" -ForegroundColor $BarvaNapaka
    }
}

# ============================================================
# POVZETEK
# ============================================================
Write-Host ""
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "POVZETEK JSON PREVERJANJA" -ForegroundColor $BarvaInfo
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host ""
Write-Host "Skupaj preverjenih: $SkupajPreverjenih" -ForegroundColor White
Write-Host "Neveljavnih:        $SkupajNapak" -ForegroundColor $(if($SkupajNapak -gt 0){$BarvaNapaka}else{$BarvaUspeh})
Write-Host "Opozoril:           $SkupajOpozoril" -ForegroundColor $BarvaOpozorilo

if ($SeznamNapak.Count -gt 0) {
    Write-Host ""
    Write-Host "NEVELJAVNE DATOTEKE:" -ForegroundColor $BarvaNapaka
    $SeznamNapak | ForEach-Object {
        Write-Host "  $($_.Datoteka)" -ForegroundColor $BarvaNapaka
        Write-Host "    $($_.Napaka)" -ForegroundColor DarkGray
    }
}

Write-Host ""
if ($SkupajNapak -eq 0) {
    Write-Host "✓ Vse JSON datoteke so veljavne!" -ForegroundColor $BarvaUspeh
} else {
    Write-Host "✗ Najdenih $SkupajNapak neveljavnih JSON datotek!" -ForegroundColor $BarvaNapaka
}
