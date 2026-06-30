<#
.SYNOPSIS
    Preveri podvojene datoteke v projektu.
.DESCRIPTION
    Poišče podvojene datoteke glede na vsebino (MD5 hash) in ime.
.EXAMPLE
    .\preveri_podvojene_datoteke.ps1
.EXAMPLE
    .\preveri_podvojene_datoteke.ps1 -PotProjekta "D:\Projekti\AstraMentalica" -MinVelikostKB 5
#>

# ============================================================
# AstraMentalica - Preverjanje podvojenih datotek
# ============================================================
# Namen: Iskanje podvojenih datotek za refactoring
# Avtor: AstraMentalica Mojster
# Verzija: 1.0.0
# ============================================================

[CmdletBinding()]
param(
    [Parameter(Mandatory=$false)]
    [string]$PotProjekta = $PSScriptRoot | Split-Path -Parent,
    
    [Parameter(Mandatory=$false)]
    [int]$MinVelikostKB = 1,
    
    [Parameter(Mandatory=$false)]
    [switch]$PoisciPoImenu,
    
    [Parameter(Mandatory=$false)]
    [switch]$PoisciPoVsebini
)

# Barve
$BarvaUspeh = "Green"
$BarvaNapaka = "Red"
$BarvaOpozorilo = "Yellow"
$BarvaInfo = "Cyan"

# Inicializacija
$SkupajDatotek = 0
$SeznamPodvojenih = @()
$HashTabela = @{}

function Write-Naslov {
    param([string]$Besedilo)
    Write-Host ""
    Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
    Write-Host $Besedilo -ForegroundColor $BarvaInfo
    Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
}

Write-Naslov "ASTRA MENTALICA - PREVERJANJE PODVOJENIH DATOTEK"
Write-Host "Pot projekta: $PotProjekta" -ForegroundColor White
Write-Host "Minimalna velikost: $MinVelikostKB KB" -ForegroundColor White
Write-Host "Čas: $(Get-Date -Format 'dd.MM.yyyy HH:mm:ss')" -ForegroundColor White

# Najdi vse datoteke
Write-Host ""
Write-Host "Iskanje datotek..." -ForegroundColor $BarvaInfo

$Datoteke = Get-ChildItem -Path $PotProjekta -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { 
        $_.FullName -notmatch "\\.git\\|\\.venv\\|\\vendor\\|\\node_modules\\|\\.vscode\\" -and
        $_.Length -ge ($MinVelikostKB * 1024)
    }

Write-Host "Najdenih $($Datoteke.Count) datotek (večjih od $MinVelikostKB KB)" -ForegroundColor White

# Išči podvojitve po imenu
if ($PoisciPoImenu -or (-not $PoisciPoVsebini)) {
    Write-Host ""
    Write-Host "Iskanje podvojenih datotek po imenu..." -ForegroundColor $BarvaInfo
    Write-Host ("-" * 70)
    
    $PoImenu = $Datoteke | Group-Object Name | Where-Object { $_.Count -gt 1 }
    
    foreach ($Skupina in $PoImenu) {
        $Velikost = ($Skupina.Group | Select-Object -First 1).Length
        Write-Host "[⚠] $($Skupina.Name) - $($Skupina.Count) primerkov" -ForegroundColor $BarvaOpozorilo
        
        $SeznamPodvojenih += [PSCustomObject]@{
            Ime = $Skupina.Name
            Stevilo = $Skupina.Count
            Pot = ($Skupina.Group | Select-Object -First 1).DirectoryName.Replace($PotProjekta, "")
            Tip = "Po imenu"
        }
        
        foreach ($Datoteka in $Skupina.Group) {
            Write-Host "    → $($Datoteka.FullName.Replace($PotProjekta, ""))" -ForegroundColor DarkGray
        }
    }
}

# Išči podvojitve po vsebini (MD5 hash)
if ($PoisciPoVsebini -or (-not $PoisciPoImenu)) {
    Write-Host ""
    Write-Host "Iskanje podvojenih datotek po vsebini (MD5)..." -ForegroundColor $BarvaInfo
    Write-Host ("-" * 70)
    
    $Stevec = 0
    foreach ($Datoteka in $Datoteke) {
        $Stevec++
        if ($Stevec % 100 -eq 0) {
            Write-Host "  [$Stevec/$($Datoteke.Count)] obdelanih..." -ForegroundColor DarkGray
        }
        
        try {
            $Md5 = Get-FileHash -Path $Datoteka.FullName -Algorithm MD5 -ErrorAction SilentlyContinue
            
            if ($HashTabela.ContainsKey($Md5.Hash)) {
                $Obstojeca = $HashTabela[$Md5.Hash]
                
                # Preveri ali sta res enaki (ne samo naključno)
                if ($Obstojeca.Length -eq $Datoteka.Length) {
                    Write-Host "[✗] PODVOJENA: $($Datoteka.Name)" -ForegroundColor $BarvaNapaka
                    Write-Host "    → $($Datoteka.FullName.Replace($PotProjekta, ""))" -ForegroundColor DarkGray
                    Write-Host "    = $($Obstojeca.FullName.Replace($PotProjekta, ""))" -ForegroundColor DarkGray
                    
                    $SeznamPodvojenih += [PSCustomObject]@{
                        Ime = $Datoteka.Name
                        Stevilo = 2
                        Pot = $Datoteka.FullName.Replace($PotProjekta, "")
                        Tip = "Po vsebini (MD5)"
                        Hash = $Md5.Hash
                        VelikostKB = [math]::Round($Datoteka.Length / 1KB, 2)
                    }
                }
            } else {
                $HashTabela[$Md5.Hash] = $Datoteka
            }
        } catch {
            # Preskoči datoteke do katerih nimamo dostopa
        }
    }
}

# Posebej poišči "(2)" datoteke
Write-Host ""
Write-Host "Iskanje '(2)' datotek..." -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

$Podvojitve2 = Get-ChildItem -Path $PotProjekta -Recurse -File -ErrorAction SilentlyContinue |
    Where-Object { $_.Name -match '\s*\(2\)\.[a-z]+$' -and $_.FullName -notmatch '\\.git\\' }

foreach ($Datoteka in $Podvojitve2) {
    $Original = $Datoteka.Name -replace '\s*\(2\)', ''
    $OriginalnaPot = Join-Path $Datoteka.DirectoryName $Original
    
    Write-Host "[⚠] $($Datoteka.Name)" -ForegroundColor $BarvaOpozorilo
    Write-Host "    → $($Datoteka.FullName.Replace($PotProjekta, ""))" -ForegroundColor DarkGray
    
    if (Test-Path $OriginalnaPot) {
        Write-Host "    = Original: $($OriginalnaPot.Replace($PotProjekta, ""))" -ForegroundColor DarkGray
    } else {
        Write-Host "    ? Original NE obstaja!" -ForegroundColor $BarvaNapaka
    }
    
    $SeznamPodvojenih += [PSCustomObject]@{
        Ime = $Datoteka.Name
        Stevilo = 1
        Pot = $Datoteka.FullName.Replace($PotProjekta, "")
        Tip = "(2) podvojitev"
    }
}

# Specifične podvojitve v projektu
Write-Host ""
Write-Host "Specifične podvojitve projekta..." -ForegroundColor $BarvaInfo
Write-Host ("-" * 70)

# DeepSeek podvojitve
$DeepseekDatoteke = Get-ChildItem -Path "$PotProjekta\AI\zasebniAi\arhitekturniAi" -Filter "*.php" -File -ErrorAction SilentlyContinue |
    Where-Object { $_.Name -match 'deepseek_|nadzornik|koder' }

$DeepseekPoImenu = $DeepseekDatoteke | Group-Object BaseName | Where-Object { $_.Count -gt 1 }
if ($DeepseekPoImenu) {
    Write-Host ""
    Write-Host "DeepSeek arhitekt podvojitve:" -ForegroundColor $BarvaOpozorilo
    foreach ($Skupina in $DeepseekPoImenu) {
        Write-Host "  $($Skupina.Name): $($Skupina.Count) verzij" -ForegroundColor White
        $Skupina.Group | ForEach-Object {
            Write-Host "    → $($_.Name)" -ForegroundColor DarkGray
        }
    }
}

# Modul boilerplate podvojitve
Write-Host ""
Write-Host "Modul boilerplate analiza..." -ForegroundColor $BarvaInfo

$ModulneDatoteke = Get-ChildItem -Path "$PotProjekta\MODULI" -Filter "modul.php" -Recurse -File -ErrorAction SilentlyContinue

$BoilerplateHash = @{}
foreach ($Modul in $ModulneDatoteke) {
    try {
        $Md5 = Get-FileHash -Path $Modul.FullName -Algorithm MD5 -ErrorAction SilentlyContinue
        $Mapa = Split-Path $Modul.DirectoryName -Leaf
        
        if ($BoilerplateHash.ContainsKey($Md5.Hash)) {
            Write-Host "[⚠] Podoben boilerplate: $($BoilerplateHash[$Md5.Hash]) ≈ $Mapa" -ForegroundColor $BarvaOpozorilo
        } else {
            $BoilerplateHash[$Md5.Hash] = $Mapa
        }
    } catch {}
}

# ============================================================
# POVZETEK
# ============================================================
Write-Host ""
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host "POVZETEK PODVOJENIH DATOTEK" -ForegroundColor $BarvaInfo
Write-Host ("=" * 70) -ForegroundColor $BarvaInfo
Write-Host ""

if ($SeznamPodvojenih.Count -eq 0) {
    Write-Host "Ni podvojenih datotek!" -ForegroundColor $BarvaUspeh
} else {
    $SeznamPodvojenih | Group-Object Tip | ForEach-Object {
        Write-Host "$($_.Name): $($_.Count) podvojitev" -ForegroundColor White
    }
    
    Write-Host ""
    Write-Host "Priporočila za refactoring:" -ForegroundColor $BarvaInfo
    Write-Host "  1. Analiziraj (2) datoteke - izbriši ali združi" -ForegroundColor DarkGray
    Write-Host "  2. DeepSeek nadzorniki - izberi najboljšo verzijo" -ForegroundColor DarkGray
    Write-Host "  3. Modul boilerplate - premakni v skupno datoteko" -ForegroundColor DarkGray
}

Write-Host ""
Write-Host "Preverjanje končano!" -ForegroundColor $BarvaInfo
