
<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/globalno/media_storitev.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     Centralno upravljanje media poti.
 *     Edina datoteka ki sestavlja poti do slik, ikon, ozadij.
 *     Nobena druga datoteka ne sme sestavljati media poti ročno.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - media_pot(string $tip, string $kategorija, string $datoteka): string
 *     - media_url(string $tip, string $kategorija, string $datoteka): string
 *     - media_obstaja(string $tip, string $kategorija, string $datoteka): bool
 *     - media_seznam(string $tip, string $kategorija): array
 *     - media_avatar_url(string $kategorija, int $stevilka): string
 *     - media_varuh_url(string $kategorija, int $stevilka): string
 *     - media_uporabnik_url(string $uporabnikId): string
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (vse POT_* media konstante – sekcija 8)
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *     - Brez die(), exit()
 *     - Brez ročnega sestavljanja poti izven te datoteke
 *     - Brez hardcoded končnic (.webp, .png)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v115: nova datoteka – centralno upravljanje media poti
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     storitev, globalno, media, poti
 * ============================================================
 */
declare(strict_types=1);
 
if (!defined('SISTEM_VARNOST')) {
    http_response_code(403);
    header('Location: /');
    return;
}
 
// ============================================================
// INTERNI POMOČNIK – mapa tipa na konstanto
// ============================================================
function _media_osnova(string $tip): string
{
    return match($tip) {
        // Liki
        'avatar'        => POT_AVATARJI,
        'varuh'         => POT_VARUHI,
        'portret'       => POT_PORTRETI,
 
        // Ezoterija
        'runa'          => POT_RUNE,
        'sigil'         => POT_SIGILI,
        'znak'          => POT_ZNAKI,
        'karta'         => POT_KARTE,
        'cakra'         => POT_CAKRE,
        'geometrija'    => POT_GEOMETRIJA,
        'kompas'        => POT_KOMPAS,
        'pecat'         => POT_PECATI,
        'zig'           => POT_ZIGI,
        'relikt'        => POT_RELIKTI,
        'portal'        => POT_PORTALI,
        'kozmos'        => POT_KOZMOS,
        'duh'           => POT_DUHOVI,
        'magija'        => POT_MAGIJA,
 
        // Narava
        'zival'         => POT_ZIVALI,
        'element'       => POT_ELEMENTI,
 
        // Nagrade
        'amulet'        => POT_AMULETI,
        'kristal'       => POT_KRISTALI,
        'kljuc'         => POT_KLJUCI,
        'bonus'         => POT_BONUS,
        'dodatek'       => POT_DODATKI,
 
        // Bralnik
        'zvitek'        => POT_BRALNIK_MEDIA . '/zvitki',
        'pero'          => POT_BRALNIK_MEDIA . '/peresa',
        'knjiga'        => POT_BRALNIK_MEDIA . '/knjige',
 
        // Svet
        'zemljevid'     => POT_ZEMLJEVID,
        'logotip'       => POT_LOGOTIPI,
 
        // UI
        'ui_gumb'       => POT_UI_GUMBI,
        'ui_okvir'      => POT_UI_OKVIRJI,
        'ui_badge'      => POT_UI_BADGE,
        'ui_kartica'    => POT_UI_KARTICE,
        'ui_tooltip'    => POT_UI_TOOLTIPI,
        'ui_inventar'   => POT_UI_INVENTAR,
        'ui_dosezek'    => POT_UI_DOSEZKI,
        'ui_simbol'     => POT_UI_SIMBOLI,
 
        // Ikone
        'ikona_soc'     => POT_IKONE_SOC,
        'ikona_sis'     => POT_IKONE_SIS,
        'ikona'         => POT_IKONE,
 
        // Ozadje
        'ozadje_glava'  => POT_OZADJE_GLAVA,
        'ozadje_stran'  => POT_OZADJE_STRANI,
        'ozadje_tema'   => POT_OZADJE_TEME,
        'ozadje'        => POT_OZADJE,
 
        // Uporabnik
        'uporabnik'     => POT_UPORABNIKI_MEDIA,
 
        default         => POT_MEDIA,
    };
}
 
// ============================================================
// JAVNE FUNKCIJE
// ============================================================
 
/**
 * Absolutna pot do media datoteke na disku.
 * Če kategorija ni potrebna, pošlji ''.
 */
function media_pot(string $tip, string $kategorija, string $datoteka): string
{
    $osnova = _media_osnova($tip);
    return $kategorija !== ''
        ? $osnova . '/' . $kategorija . '/' . $datoteka
        : $osnova . '/' . $datoteka;
}
 
/**
 * Relativna URL pot za frontend (src v HTML).
 */
function media_url(string $tip, string $kategorija, string $datoteka): string
{
    $osnova    = _media_osnova($tip);
    $relativna = str_replace(['\\', ROOT], ['/', ''], $osnova);
    return $kategorija !== ''
        ? $relativna . '/' . $kategorija . '/' . $datoteka
        : $relativna . '/' . $datoteka;
}
 
/**
 * Preveri ali datoteka obstaja (ne glede na končnico).
 */
function media_obstaja(string $tip, string $kategorija, string $datoteka): bool
{
    return file_exists(media_pot($tip, $kategorija, $datoteka));
}
 
/**
 * Seznam vseh datotek v mapi – vse dovoljene končnice.
 */
function media_seznam(string $tip, string $kategorija = ''): array
{
    $mapa = _media_osnova($tip) . ($kategorija !== '' ? '/' . $kategorija : '');
    if (!is_dir($mapa)) {
        return [];
    }
    $datoteke = [];
    foreach (glob($mapa . '/*.{webp,png,svg,jpg,jpeg,gif}', GLOB_BRACE) as $pot) {
        $datoteke[] = [
            'ime' => basename($pot),
            'url' => media_url($tip, $kategorija, basename($pot)),
            'pot' => $pot,
        ];
    }
    return $datoteke;
}
 
/**
 * URL avatarja – s fallbackom na osnovni.
 * Primer: media_avatar_url('vip', 3) → '/VSEBINA/media/avatarji/vip/avatar_03.webp'
 */
function media_avatar_url(string $kategorija, int $stevilka): string
{
    foreach (['webp', 'png', 'jpg'] as $koncnica) {
        $ime = 'avatar_' . str_pad((string)$stevilka, 2, '0', STR_PAD_LEFT) . '.' . $koncnica;
        if (media_obstaja('avatar', $kategorija, $ime)) {
            return media_url('avatar', $kategorija, $ime);
        }
    }
    // Fallback
    foreach (['webp', 'png', 'jpg'] as $koncnica) {
        $privzeti = 'avatar_01.' . $koncnica;
        if (media_obstaja('avatar', 'osnovni', $privzeti)) {
            return media_url('avatar', 'osnovni', $privzeti);
        }
    }
    return '/VSEBINA/media/avatarji/osnovni/avatar_01.webp';
}
 
/**
 * URL varuha – s fallbackom na otroski.
 */
function media_varuh_url(string $kategorija, int $stevilka): string
{
    foreach (['webp', 'png', 'jpg'] as $koncnica) {
        $ime = 'varuh_' . str_pad((string)$stevilka, 2, '0', STR_PAD_LEFT) . '.' . $koncnica;
        if (media_obstaja('varuh', $kategorija, $ime)) {
            return media_url('varuh', $kategorija, $ime);
        }
    }
    return '/VSEBINA/media/varuhi/otroski/varuh_01.webp';
}
 
/**
 * URL profilne slike uporabnika.
 * Podpira webp, png, jpg – preveri kar obstaja.
 * Fallback na privzeti avatar.
 */
function media_uporabnik_url(string $uporabnikId): string
{
    foreach (['profilna.webp', 'profilna.png', 'profilna.jpg'] as $ime) {
        if (file_exists(POT_UPORABNIKI_MEDIA . '/' . $uporabnikId . '/' . $ime)) {
            return '/VSEBINA/media/uporabniki/' . $uporabnikId . '/' . $ime;
        }
    }
    return '/VSEBINA/media/avatarji/osnovni/avatar_01.webp';
}
 