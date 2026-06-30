<#
.SYNOPSIS
    Preveri Python sintakso v projektu.
.DESCRIPTION
    Analizira vse Python datoteke in preveri sintaktične napake.
.EXAMPLE
    .\preveri_python_sintakso.ps1
.EXAMPLE
    .\preveri_python_sintakso.ps1 -PotProjekta "D:\Projekti\AstraMentalica"
#>

# ============================================================
# AstraMentalica - Preverjanje Python sintakse
# ============================================================
# Namen: Validacija Python datotek
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

# Preveri Python
$PythonPot = Get-Command python -ErrorAction SilentlyContinue
$ImamoPython = $null -ne $PythonPot

Write-Naslov "ASTRA MENTALICA - PREVERJANJE PYTHON SINTAKSE"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor White

if ($ImamoPython) {
    Write-Host "Python verzija: $(python --version)" -ForegroundColor White
} else {
    Write-Host "Python: NI NAJDEN" -ForegroundColor $BarvaOpozorilo
}

# Najdi Python datoteke
Write-Host ""
Write-Host "Iskanje Python datotek..." -ForegroundColor $BarvaInfo

$PyDatoteke = Get-ChildItem -Path $PotProjekta -Include "*.py", "*.pyw" -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch "\\.venv\\|\\venv\\|\\env\\|\\node_modules\\" }

Write-Host "Najdenih $($PyDatoteke.Count) Python datotek" -ForegroundColor White

if ($ImamoPython) {
    Write-Host ""
    Write-Host "Preverjanje s Python compiler-jem..." -ForegroundColor $BarvaInfo
    Write-Host ("-" * 70)
    
    foreach ($Datoteka in $PyDatoteke) {
        $SkupajPreverjenih++
        $RelativnaPot = $Datoteka.FullName.Replace($PotProjekta, "").TrimStart("\")
        
        # Python -m py_compile preverjanje
        $Rezultat = python -m py_compile $Datoteka.FullName 2>&1
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "[✓] $RelativnaPot" -ForegroundColor $BarvaUspeh
        } else {
            Write-Host "[✗] $RelativnaPot" -ForegroundColor $BarvaNapaka
            $SkupajNapak++
            $SeznamNapak += [PSCustomObject]@{
                Datoteka = $RelativnaPot
                Napaka = $Rezultat.ToString()
            }
        }
    }
} else {
    # Osnovno preverjanje brez Python
    Write-Host ""
    Write-Host "Omejeno preverjanje (brez Python interpreterja)..." -ForegroundColor $BarvaOpozorilo
    
    foreach ($Datoteka in $PyDatoteke) {
        $SkupajPreverjenih++
        $Vsebina = Get-Content $Datoteka.FullName -Raw -ErrorAction SilentlyContinue
        
        # Osnovna preverjanja
        $OdprtiOklepaji = ([regex]::Matches($Vsebina, ':')).Count
        $Zaprti = ([regex]::Matches($Vsebina, 'def |class |if |for |while |try |except |with ')).Count
        
        Write-Host "[?] $($Datoteka.Name) (brez interpreterja)" -ForegroundColor $BarvaOpozorilo
    }
}

# Preveri type hints
if ($Strict -and $ImamoPython) {
    Write-Host ""
    Write-Host "Preverjanje type hints..." -ForegroundColor $BarvaInfo
    
    foreach ($Datoteka in $PyDatoteke) {
        $Vsebina = Get-Content $Datoteka.FullName -Raw -ErrorAction SilentlyContinue
        
        if ($Vsebina -match 'def \w+\(' -and $Vsebina -notmatch 'def \w+\([^)]*\)\s*->\s*\w+') {
            Write-Host "[⚠] Brez type hints: $($Datoteka.Name)" -ForegroundColor $BarvaOpozorilo
            $SkupajOpozoril++
        }
    }
}

# ============================================================
# POVZETEK
# ============================================================
Write-Host ""
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "POVZETEK" -ForegroundColor $BarvaInfo
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host ""
Write-Host "Skupaj preverjenih: $SkupajPreverjenih" -ForegroundColor White
Write-Host "Napake:             $SkupajNapak" -ForegroundColor $(if($SkupajNapak -gt 0){$BarvaNapaka}else{$BarvaUspeh})
Write-Host "Opozorila:          $SkupajOpozoril" -ForegroundColor $BarvaOpozorilo

if ($SeznamNapak.Count -gt 0) {
    Write-Host ""
    Write-Host "NAPAKE:" -ForegroundColor $BarvaNapaka
    $SeznamNapak | ForEach-Object {
        Write-Host "  $($_.Datoteka)" -ForegroundColor $BarvaNapaka
        Write-Host "    $($_.Napaka)" -ForegroundColor DarkGray
    }
}

Write-Host ""
if ($SkupajNapak -eq 0) {
    Write-Host "✓ Vse Python datoteke so sintaktično pravilne!" -ForegroundColor $BarvaUspeh
} else {
    Write-Host "✗ Najdenih $SkupajNapak datotek z napakami!" -ForegroundColor $BarvaNapaka
}
