```markdown
# Pogosta vprašanja (FAQ)

---

## 1. Splošno

### 1.1 Kaj je AstraMentalica?

AstraMentalica je večsvetovni runtime sistem za duhovni razvoj in raziskovanje. Omogoča modularno razširjanje funkcionalnosti preko izoliranih modulov.

### 1.2 Kako začnem uporabljati sistem?

1. Namestite sistem preko `install.php`
2. Zaženite strežnik (`php -S localhost:8000`)
3. Prijavite se v ASTRA admin (`/astra/prijava`)
4. Raziskujte module in funkcionalnosti

### 1.3 Kateri so sistemski zahtevki?

- PHP 8.1 ali novejši
- JSON, MBString, Session razširitve
- MySQL (opcijsko) ali SQLite (privzeto)
- 50MB prostora na disku (osnovna namestitev)

---

## 2. Namestitev

### 2.1 Kako namestim sistem?

Kopirajte `install.php` na strežnik in ga zaženite:

```bash
php install.php
Skripta bo ustvarila vse mape in osnovne datoteke.

2.2 Kako zaženem strežnik?
bash
php -S localhost:8000
2.3 Kako dostopam do ASTRA admin?
Odprite http://localhost:8000/?svet=ASTRA&pot=prijava

Privzeto uporabniško ime: admin
Privzeto geslo: admin2024

3. Moduli
3.1 Kako namestim modul?
Preko ASTRA nadzorne plošče kliknite "Namesti" pri želenem modulu, ali preko CLI:

bash
php cli.php modul:install ImeModula
3.2 Kako ustvarim nov modul?
Ustvarite mapo v MODULI/[KATEGORIJA]/[ImeModula]/ z:

konfiguracija/manifest.json – definicija modula

modul.php – vstopna točka

3.3 Kako aktiviram modul?
V ASTRA nadzorni plošči kliknite "Aktiviraj" pri želenem modulu.

3.4 Kateri so podprti kanali za module?
splet – HTML prikaz

api – JSON API

telegram – Telegram bot

facebook – Facebook Messenger

ai – AI poizvedbe

cli – ukazna vrstica

4. Varnost
4.1 Kako spremenim admin geslo?
Spremenite v .env datoteki v PODATKI/sef/.env:

text
ASTRA_ADMIN_GESLO=novo_geslo
4.2 Ali so moji podatki varni?
Da. Uporabljamo:

HTTPS (priporočeno)

Hashiranje gesel (bcrypt)

CSRF zaščito

JWT za API

Rate limiting

4.3 Kako omogočim dvofaktorsko avtentikacijo?
V uporabniških nastavitvah vključite opcijo "Dvofaktorska avtentikacija".

5. Uporaba
5.1 Kako uporabljam PASSPORT?
PASSPORT je osebni prostor za:

Dnevnik (/dnevnik)

Sanje (/sanje)

Meditacije (/meditacije)

Vsi podatki so shranjeni samo za vas in niso dostopni nikomur drugemu.

5.2 Kako izvozim svoje podatke?
V profilu izberite "Izvoz podatkov". Podatki bodo pripravljeni v JSON formatu.

5.3 Kako izbrišem svoj račun?
V profilu izberite "Izbriši račun". Izbris je TRAJEN in ga ni mogoče razveljaviti.

6. Čakalna vrsta (Queue)
6.1 Kaj je čakalna vrsta?
Čakalna vrsta omogoča asinhrono obdelavo opravil (email, notifikacije, obdelava podatkov).

6.2 Kako zaženem workerja?
bash
php cli.php worker
6.3 Katere vrste paketov obstajajo?
sprotno – takojšnja obdelava

visoka_prednost – visoka prioriteta

obicajna_prednost – normalna prioriteta (privzeto)

nizka_prednost – nizka prioriteta

elektronska_posta – emaili

casovnik – časovno načrtovane naloge

7. Cron
7.1 Kako zaženem cron jobove?
bash
php cli.php cron
7.2 Kateri cron jobovi so privzeti?
cleanup_cache – čiščenje cache (vsakih 6 ur)

cleanup_logs – čiščenje dnevnikov (vsako nedeljo)

sync_modules – sinhronizacija modulov (vsakih 30 minut)

health_check – preverjanje zdravja (vsakih 5 minut)

8. Reševanje težav
8.1 Stran se ne prikaže (white screen)
Nastavite RAZVOJNI_NACIN = true v pot.php za prikaz napak.

8.2 Napaka "Ne morem pisati v PODATKI/"
Preverite dovoljenja mape:

bash
chmod 755 PODATKI
chmod 777 PODATKI/sistem/cache
8.3 Kako pogledam dnevnike?
Dnevniki so v PODATKI/sistem/dnevnik/ ali preko ASTRA → Dnevniki.

8.4 Kako ponastavim sistem?
bash
php cli.php recovery
8.5 Modul se ne prikaže v seznamu
Preverite ali ima modul pravilen manifest.json in ali ste ga sinhronizirali:

bash
php cli.php moduli:sinhroniziraj
text

---
