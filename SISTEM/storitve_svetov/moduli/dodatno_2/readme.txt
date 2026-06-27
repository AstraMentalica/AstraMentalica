MODUL MYSTICA - Navodila za uporabo

STRUKTURA:
/Modul_Mystica/
  ├── index.php          (Tvoja datoteka - jedro sistema)
  ├── moj_index.php      (Moj index z glavo in nogo)
  └── README.txt         (Ta navodila)

OPIS SISTEMA:
Modul Mystica je magicni portal, ki omogoca:
- Prijavo uporabnikov z token sistemom
- Aktivacijo magicnih dogodkov
- Spremljanje stanja sistema
- Nastavljanje cron nalog
- Komunikacijo z magicnim svetom

UPORABA:
1. Odpri moj_index.php v brskalniku
2. Uporabi prijavo z uporabniskim imenom in geslom
   (vsaj 3 znaki za ime, 6 za geslo)
3. Po uspesni prijavi dobis token za nadaljne operacije
4. Uporabi razlicne panele za magicne operacije

UKAZI:
- prijava: Prijava v sistem
- magicni_dogodek: Aktivira magicni dogodek
- pridobi_stanje: Pridobi stanje sistema  
- nastavi_cron: Nastavi periodično nalogo
- komunikacija: Komuniciraj z sistemom

TOKEN SISTEM:
- Token je potreben za vse operacije po prijavi
- Samodejno se prikaze po uspesni prijavi
- Veljaven je 2 uri

MAGICNA MOC:
- Sledi magicni moci sistema
- Samodejno se manjsa s casom
- Vpliva na uspesnost magicnih dogodkov

CRON NALOGE:
- Omogoca avtomatsko izvajanje ukazov
- Nastavi interval v sekundah
- Podpri ukazi: pridobi_stanje, itd.

OPOMBA:
Sistem je pripravljen za testiranje vsebin in funkcionalnosti.
Vse komunikacije potekajo preko JSON API-ja.