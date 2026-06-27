#!/bin/bash
# namestitev_sistema.sh
# Skripta za namestitev celotnega sistema

echo "=== NAMESTITEV AVTOMATIZIRANEGA BLOGA ==="

# Preveri odvisnosti
echo "1. Preverjam odvisnosti..."
if ! command -v php &> /dev/null; then
    echo "Napaka: PHP ni nameščen"
    exit 1
fi

if ! command -v mysql &> /dev/null; then
    echo "Napaka: MySQL ni nameščen"
    exit 1
fi

# Ustvari konfiguracijsko datoteko
echo "2. Ustvarjam konfiguracijo..."
cat > blog_konfiguracija.php << 'EOL'
<?php
// blog_konfiguracija.php
// Konfiguracija za avtomatiziran blog

return [
    'api_kljuc' => 'VAŠ_OPENROUTER_API_KLJUČ', // Zamenjaj s pravim ključem
    'zacetna_tema' => 'Tehnologija in umetna inteligenca',
    'stevilo_clankov' => 2,
    'baza_gostitelj' => 'localhost',
    'baza_uporabnik' => 'blog_uporabnik',
    'baza_geslo' => 'varno_geslo_123',
    'baza_ime' => 'avtomatiziran_blog',
    
    // Nastavitve optimizacije
    'max_tokens' => 4000,
    'zamik_med_zahtevki' => 2,
    
    // Cron nastavitve
    'cron_frekvenca' => '0 8 * * *' // Dnevno ob 8:00
];
?>
EOL

echo "3. Ustvarjam bazo podatkov..."
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS avtomatiziran_blog;"
mysql -u root -p -e "CREATE USER IF NOT EXISTS 'blog_uporabnik'@'localhost' IDENTIFIED BY 'varno_geslo_123';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON avtomatiziran_blog.* TO 'blog_uporabnik'@'localhost';"
mysql -u root -p -e "FLUSH PRIVILEGES;"

echo "4. Uvozam shemo baze..."
mysql -u blog_uporabnik -p'varno_geslo_123' avtomatiziran_blog < shema_baze.sql

echo "5. Nastavljam cron job..."
(crontab -l 2>/dev/null; echo "0 8 * * * /usr/bin/php /pot/do/tvoje/domene/cron_izvajalec.php >/dev/null 2>&1") | crontab -

echo "=== NAMESTITEV ZAKLJUČENA ==="
echo "Ne pozabi:"
echo "1. Zamenjaj VAŠ_OPENROUTER_API_KLJUČ v blog_konfiguracija.php"
echo "2. Prilagodi pot do datotek v cron jobu"
echo "3. Testiraj sistem s php testni_skript.php"