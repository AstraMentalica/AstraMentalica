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

## Javne pristajalne strani

- `javno/pristajalna_astramentalica.html` — statična javna pristajalna stran za AstraMentalico.
  Uporablja samo prikaz, slovenska imena gradnikov/razredov in povezave na obstoječi sistemski vhod `index.php?svet=UPORABNIKI&pot=...`.
  Ne vsebuje poslovne logike, sej, SQL ali direktnih klicev v SISTEM.

- `javno/raziskovanje_modulov.html` — statični javni katalog modulov za neprijavljene obiskovalce.
  Prikazuje samo dovoljene opisne vire modulov (`manifest.json`, `manifest.md`, `modul.md`, `{ImeModula}.md`, `README.md`, `opis.txt`) in ne izvaja modulov ali PHP kode.

- `javno/kozmologija.html` — vizualni katalog kozmologije (zavesti, avatarji, varuhi, duhovi, magične živali, svetovi).
- `javno/gradniki_katalog.html` — vizualni katalog gradnikov za ponovno uporabo.
- `javno/passport.html` — osebna knjiga uporabnika (PASSPORT), prikazni atlas za zapise in rast.
- `javno/atlas.html` — enoten vizualni atlas, ki poveže PASSPORT, kozmologijo, gradnike in moduli.
