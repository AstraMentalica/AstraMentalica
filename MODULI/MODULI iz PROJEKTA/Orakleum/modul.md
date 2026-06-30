# Orakleum

Modul za naročanje intuitivnih branj in splošni kontakt Orakleum strani.

## Namen

Orakleum ponuja paleto plačljivih intuitivnih branj (tarot, oraklji, energijska
diagnostika, astro-numerologija, Zoom svetovanje ...). Uporabnik izbere vrsto
branja, izpolni obrazec (ime, email, sporočilo, potrditev donacije) in odda
naročilo. Modul naročilo validira, ga shrani in pošlje email obvestilo.

Ločeno od naročil podpira tudi splošen kontaktni obrazec (vprašanja, ki niso
vezana na konkretno naročilo branja).

## Akcije

### `GET /orakleum/branja`
Vrne seznam vseh vrst branj s ceno (priporočeno donacijo), opisom in ikono.
Uporablja se za prikaz cenika v vmesniku.

**Primer odziva:**
```json
{
  "status": "uspeh",
  "vsebina": {
    "branja": [
      { "oznaka": "hitro", "naslov": "Hitro vprašanje", "donacija": 9, ... }
    ]
  }
}
```

### `POST /orakleum/narocilo`
Odda naročilo branja.

**Vhod:**
| Polje | Obvezno | Opis |
|---|---|---|
| `ime` | da | Ime naročnika |
| `email` | da | Veljaven email naslov |
| `branje` | da | Oznaka vrste branja (glej `/orakleum/branja`) |
| `sporocilo` | ne | Dodatno sporočilo / vprašanje |
| `potrdilo_donacija` | ne | bool — potrditev donacije |

**Izhod:** zapiše v `PODATKI/moduli/orakleum/narocila.json`, pošlje email na
`info@astramentalica.com`.

### `POST /orakleum/kontakt`
Splošno kontaktno sporočilo (ni vezano na konkretno branje).

**Vhod:** `ime`, `email`, `sporocilo` (vsa obvezna).

**Izhod:** zapiše v `PODATKI/moduli/orakleum/kontakti.json`, pošlje email.

### `GET /orakleum/info`
Osnovne informacije o modulu (ime, verzija, opis, število branj).

## Vrste branj (cenik)

| Oznaka | Naslov | Donacija |
|---|---|---|
| `hitro` | Hitro vprašanje | 9 € |
| `ljubezen` | Ljubezensko branje | 18 € |
| `kariera` | Kariera / Finance | 18 € |
| `senca` | Senca & notranje blokade | 18 € |
| `boginje` | Zmaji, boginje, vile | 18 € |
| `karma` | Dušna pot & karma | 27 € |
| `pretekla` | Pretekla življenja | 27 € |
| `energetsko` | Energetska diagnostika | 27 € |
| `pdf` | Mesečni PDF vodnik | 33 € |
| `dušni_par` | Dušni odnosi | 33 € |
| `astro` | Astro-numerološki vpogled | 42 € |
| `svetovanje` | Zoom svetovanje | 42 € |
| `celostno` | Celostno intuitivno branje | 54 € |

## Opombe glede arhitekture

- Email pošiljanje je "best effort" prek `mail()` — neuspeh pošiljanja ne
  prekine shranjevanja naročila (naročilo je vedno zapisano, tudi če email
  ne gre skozi).
- Modul ne uporablja cron/vrste — preprosto, sinhrono pošiljanje.
- Statična vsebina (FAQ, splošni opis Orakleuma, kontaktna stran kot stran)
  živi v `VSEBINA/kontakt/` in `VSEBINA/faq/`, NE v tem modulu — modul
  pokriva samo poslovno logiko (validacija, shramba, email).
