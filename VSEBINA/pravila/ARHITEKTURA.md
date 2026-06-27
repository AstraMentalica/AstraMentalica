# ARHITEKTURA SISTEMA ASTRAMENTALICA

## Temeljno pravilo

Celoten sistem uporablja enotno notranjo komunikacijo preko API formata.

Ne glede na izvor zahteve mora vsaka zahteva najprej skozi ADAPTER, kjer se pretvori v enotni sistemski format.

---

## TOK IZVAJANJA

```text
Zahteva
    ↓
index.php / api.php / ai.php / cli.php ...
    ↓
ADAPTER
    ↓
Enotni API format
    ↓
SISTEM/api.php
    ↓
Kernel/zaganjalnik.php
    ↓
01_upravljalec_svetov.php
    ↓
Poslovna logika
    ↓
Odziv
    ↓
ADAPTER
    ↓
Končni kanal
```

---

# ROOT MAPE

## ADAPTER

### Namen

Pretvorba različnih vhodov in izhodov v enotni sistemski format.

ADAPTER ne vsebuje poslovne logike.

ADAPTER ne pozna notranjosti:

- GLOBALNO
- UPORABNIKI
- MODULI
- VSEBINA
- PODATKI

### Naloge

- prepozna kanal
- normalizira zahtevo
- pošlje zahtevo v SISTEM
- prejme odziv
- pripravi odziv za končni kanal

---

## SISTEM

### Namen

Srce in motor celotnega projekta.

SISTEM vsebuje:

- kernel
- kernel/jedro
- storitve svetov
- administracijo

SISTEM je edini backend projekta.

---

## MODUL_BRIDGE

### Namen

Izvajalno in povezovalno okolje za module.

### V SISTEMU

- preveri whitelist
- preveri dostop
- registrira modul
- omogoči prikaz modula
- poveže modul s sistemom

### IZVEN SISTEMA

- demo konstante
- demo glava
- demo noga
- demo nastavitve

### Definicija

Modul_Bridge je izvajalno in povezovalno okolje modulov, ki omogoča delovanje modulov znotraj sistema ali samostojno izven sistema brez sprememb samega modula.
