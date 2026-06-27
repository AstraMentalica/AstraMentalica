# GLOBALNO/postavitev/strani

Ta mapa je pripravljena kot **postavitev strani** za GLOBALNO.
Deluje samo kot sestavljalnik pogledov. Poslovna logika ostane v SISTEM/storitve_svetov/.

## Namen

- razdeli prikaz na **gradnike**
- omogoča **različne layoute** za različne tipe strani
- omogoča, da si **vsak modul sestavi svoj pogled** iz vnaprej definiranih kosov

## Osnovno pravilo

- **gradnik** = en sam vizualni ali funkcijski kos UI
- **layout** = sestava več gradnikov v celoto
- **stran** = konkreten prikaz, ki izbere layout in podatke
- **svet** = sistemski domeni pripadajoča logika, ki jo sestavlja SISTEM

## Trenutni začetni registri

- `layouti.php` — izbor layoutov in pripadajočih gradnikov
- `gradniki/registri.php` — katalog razpoložljivih gradnikov

## Kako naj se uporablja kasneje

1. Stran izbere layout po tipu uporabe.
2. Layout prebere seznam gradnikov.
3. SISTEM določi, kateri svet in kateri podatki gredo v prikaz.
4. Moduli dobijo svoj layout ali svoj seznam gradnikov.
5. Če gradnik obstaja, ga stran samo vključi.

## Pravilo poimenovanja

Uporabljaj slovenska imena, kratka in jasna:

- `glava`
- `noga`
- `navigacija`
- `kartica`
- `obrazec`
- `gumb`
- `seznam`

## Opomba

To je šele ogrodje. Pravi gradniki in layout datoteke bodo dodajani postopno, po potrebi posameznih strani in modulov.
