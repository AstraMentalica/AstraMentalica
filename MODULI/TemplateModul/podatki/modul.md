# Template Modul

*To je predloga za nov modul. Kopiraj to mapo in jo preimenuj.*

---

## O tem modulu

Ta modul je namenjen kot osnova za ustvarjanje novih modulov.

Vsebuje:
- Osnovno strukturo map
- manifest.json
- Vstopno točko modul.php
- Primer vsebine v Markdown

## Kako uporabiti

1. Kopiraj mapo `TemplateModul`
2. Preimenuj jo v ime svojega modula (npr. `MODULI/Stelaris/`)
3. Uredi `podatki/manifest.json` – vsaj `modul.id`, `modul.ime`, `modul.opis`
4. Uredi `modul.php` – preimenuj vstopno funkcijo v `modul_{ime}_akcija()`
5. Uredi to datoteko (`podatki/modul.md`)

## Struktura (ploski MODULI/)

```
MODULI/
└── ImeModula/
    ├── modul.php          ← vstopna točka (edina ki jo kliče SISTEM)
    └── podatki/
        ├── manifest.json  ← identiteta in dovoljenja
        └── modul.md       ← ta datoteka
```

## Naslednji koraki

Ko je modul pripravljen, ga registriraj v `PODATKI/registri/moduli_register.json`.
