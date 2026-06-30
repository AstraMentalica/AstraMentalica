<#
.SYNOPSIS
    Preveri podvojene funkcije v PHP kodi.
.DESCRIPTION
    Poišče podvojene funkcije in metode v PHP datotekah.
.EXAMPLE
    .\preveri_podvojene_funkcije.ps1
#>

# ============================================================
# AstraMentalica - Preverjanje podvojenih funkcij
# ============================================================
# Namen: Iskanje podvojene logike v PHP
# Avtor: AstraMentalica Mojster
# Verzija: 1.0.0
# ============================================================

[CmdletBinding()]
param(
    [Parameter(Mandatory=$false)]
    [string]$PotProjekta = $PSScriptRoot | Split-Path -Parent
)

$BarvaInfo = "Cyan"
$BarvaNapaka = "Red"
$BarvaOpozorilo = "Yellow"
$BarvaUspeh = "Green"

$SeznamFunkcij = @{}
$FunkcijePodvojene = @()

Write-Naslov "ASTRA MENTALICA - PREVERJANJE PODVOJENIH FUNKCIJ"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor White

$PhpDatoteke = Get-ChildItem -Path $PotProjekta -Filter "*.php" -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch "\\.venv\\|\\vendor\\|\\node_modules\\" }

Write-Host ""
Write-Host "Analiziranje $($PhpDatoteke.Count) PHP datotek..." -ForegroundColor $BarvaInfo

foreach ($Datoteka in $PhpDatoteke) {
    $Vsebina = Get-Content $Datoteka.FullName -Raw -ErrorAction SilentlyContinue
    $RelativnaPot = $Datoteka.FullName.Replace($PotProjekta, "").TrimStart("\")
    
    # Poišči function deklaracije
    $Funkcije = [regex]::Matches($Vsebina, 'function\s+(\w+)\s*\(')
    
    foreach ($Funkcija in $Funkcije) {
        $Ime = $Funkcija.Groups[1].Value
        
        if (-not $SeznamFunkcij.ContainsKey($Ime)) {
            $SeznamFunkcij[$Ime] = @()
        }
        
        $SeznamFunkcij[$Ime] += [PSCustomObject]@{
            Ime = $Ime
            Datoteka = $RelativnaPot
            Vrstica = $Datoteka.FullName + ":" + [regex]::Matches($Vsebina.Substring(0, $Funkcija.Index), "`n").Count
        }
    }
}

# Poišči podvojene
Write-Host ""
Write-Host "Podvojene funkcije:" -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

foreach ($Ime in $SeznamFunkcij.Keys) {
    $Funkcije = $SeznamFunkcij[$Ime]
    if ($Funkcije.Count -gt 1) {
        Write-Host "[⚠] function $Ime() - $($Funkcije.Count) definicij" -ForegroundColor $BarvaOpozorilo
        foreach ($F in $Funkcije) {
            Write-Host "    → $($F.Datoteka)" -ForegroundColor DarkGray
        }
    }
}

# Specifično preveri odziv funkcije
Write-Host ""
Write-Host "Standardne odziv funkcije:" -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

$OdzivUspeh = [regex]::Matches($Vsebina, 'function\s+odziv_uspeh')
$OdzivNapaka = [regex]::Matches($Vsebina, 'function\s+odziv_napaka')

foreach ($Datoteka in $PhpDatoteke) {
    $Vsebina = Get-Content $Datoteka.FullName -Raw
    $RelativnaPot = $Datoteka.FullName.Replace($PotProjekta, "").TrimStart("\")
    
    if ($Vsebina -match 'function odziv_uspeh' -or $Vsebina -match 'function odziv_napaka') {
        Write-Host "[i] $RelativnaPot vsebuje odziv funkcije" -ForegroundColor $BarvaInfo
    }
}

Write-Host ""
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "POVZETEK" -ForegroundColor $BarvaInfo
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo

$Podvojene = ($SeznamFunkcij.Values | Where-Object { $_.Count -gt 1 }).Count
Write-Host "Funkcij insgesamt: $($SeznamFunkcij.Count)"
Write-Host "Podvojenih: $Podvojene" -ForegroundColor $(if($Podvojene -gt 0){$BarvaOpozorilo}else{$BarvaUspeh})
