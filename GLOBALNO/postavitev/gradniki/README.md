# ELEMENTI - navodila in primeri

Ta mapa vsebuje ponazorila in helper datoteke za običajne UI elemente, ki jih uporablja `GLOBALNO` front-end.

Pravila:
- Vse datoteke in komentarji so v slovenščini.
- Funkcije ne izvajajo poslovne logike, vračajo samo HTML fragmente.
- Uporabljajte CSS razrede iz `GLOBALNO/vmesnik/stili` (če obstajajo) ali dodajte nove teme.

Datoteke v mapi:
- `gumb.php` — funkcija za generiranje gumbov
- `modal.php` — preprost modal (markup)
- `kartica.php` — komponenta kartice / itema
- `polje_oblike.php` — helper za vnosna polja

Primer uporabe:
```php
require_once POT_GLOBALNO . '/vmesnik/elementi/gumb.php';
echo gumb_generiraj('Shrani', 'primarni');
```

Če želite, da pripravim dodatne elemente (npr. dropdown, tooltip, navbar), povejte katere.
