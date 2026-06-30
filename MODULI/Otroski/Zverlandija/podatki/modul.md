# Zverlandija — Otroški svet magičnih živali

Otroški interaktivni svet, kjer žive magične živali, ki pišejo zgodbe in učijo skozi igro in dogodivščine.

## Tehnične informacije
- **ID:** zverlandija
- **Verzija:** 1.0.0
- **Tip:** interaktivni_svet
- **Nivo:** 1
- **Kategorija:** Otroski
- **Minimalna vloga:** S0
- **Plan:** osnova
- **Jeziki:** sl

## Svetovi
1. **Gozd živali** — Ris Zorza, Zajec Bimbam, Sova Luna, Medved Muki
2. **Morje čarov** — Kit Koral, Delfin Fjoli, Rak Raka, Ribica Bela
3. **Nebesna krila** — Orel Orjak, Feniks Fija, Vetrovnik Viti
4. **Gorska skrivnost** — Koza Koka, Orel Osol, Ris Ruj
5. **Mesto meščanov** — Mačka Mia, Pes Pajk, Golob Gaga

## Funkcionalnosti
- Izbira sveta in živali
- Generiranje zgodb v slovenščini
- Učne naloge za otroke (uganke, naravoslovje, matematika, ustvarjalnost)
- Glasovni odgovori (text-to-speech)
- Prilagojen otroški UI (barve, velike ikone, animacije)

## Javne metode
- info
- domov
- svet
- zival
- zgodba
- naloga
- glas

## HTTP poti
- /zverlandija/info
- /zverlandija/domov
- /zverlandija/svet/{svet_id}
- /zverlandija/zival/{zival_id}
- /zverlandija/zgodba
- /zverlandija/naloga
- /zverlandija/glas

## Dogodki
- **Oddaja:** zverlandija.zgodba.ustvarjena, zverlandija.naloga.razpisana, zverlandija.zival.izbrana, zverlandija.svet.odprt
- **Bere iz:** astramentalica.uporabnik.prijavljen, astramentalica.otroski.nacin.aktiviran

## Avtor
- **Avtor:** Claude (za Damir Šafarič)
- **Licenca:** Zaprta koda
- **Ustvarjeno:** 2026-06-28