<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/register_gradnikov.php
 * 📅 VERZIJA: v100 (28.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Centralni register vseh UI gradnikov sistema.
 *     Vsak gradnik ima določeno:
 *       - minimalno RBAC stopnjo (vloga_min)
 *       - kategorijo (kje se nahaja / čemu služi)
 *       - tip (php_fragment | js_modul | css_razred | kombinacija)
 *       - pot do datoteke (relativno na POT_GLOBALNO)
 *       - opis za UI in admin
 *       - ali je na voljo za razvoj profila (profil: true/false)
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - gradniki_register(): array
 *     - gradniki_za_vlogo(int $vloga): array
 *     - gradnik_pridobi(string $kljuc): ?array
 *     - gradniki_po_kategoriji(int $vloga): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (VLOGA_* konstante)
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *     - Brez logike (samo definicije)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v100: prva implementacija – popoln register vseh
 *             obstoječih gradnikov z RBAC stopnjami
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kernel, jedro, register, gradniki, rbac, ui
 * ============================================================
 *
 * KATEGORIJE GRADNIKOV:
 * ─────────────────────────────────────────────────────────────
 *   osnova       → temeljni HTML fragmenti (gumb, kartica, ...)
 *   navigacija   → navigacijski elementi
 *   obrazci      → vnosni elementi (input, select, ...)
 *   prikaz       → prikaz podatkov (tabela, seznam, ...)
 *   layout       → postavitve in strukture strani
 *   profil       → gradniki za razvoj lastnega profila
 *   avatar       → slike in vizualni identiteta
 *   vizualno     → animacije, efekti, 3D
 *   ai           → AI in glasovni vmesnik
 *   admin        → admin in debug orodja
 * ─────────────────────────────────────────────────────────────
 *
 * VLOGA_MIN (iz pot.php):
 *   VLOGA_GOST  =   0   → neregistriran obiskovalec
 *   VLOGA_S0    =  10   → registriran, neaktiviran
 *   VLOGA_S1    =  20   → osnovna stopnja
 *   VLOGA_S2    =  30   → razširjena stopnja
 *   VLOGA_S3    =  40   → napredna stopnja
 *   VLOGA_S4    =  50   → ekspertna stopnja
 *   VLOGA_S5    =  60   → mojstrska stopnja
 *   VLOGA_ADMIN = 100   → administrator
 * ─────────────────────────────────────────────────────────────
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// REGISTER – definitiva vseh gradnikov
// ============================================================

function gradniki_register(): array
{
    return [

        // ════════════════════════════════════════════════════
        // KATEGORIJA: osnova
        // Temeljni HTML fragmenti – dostopni vsem, ker gradijo
        // javni del strani (prijava, domov, napake).
        // ════════════════════════════════════════════════════

        'gumb' => [
            'ime'       => 'Gumb',
            'opis'      => 'Splošni gumb – primaren, sekundaren, nevaren, majhen. Podpira ikono in href.',
            'kategorija'=> 'osnova',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/gradniki/gumb.php',
            'vloga_min' => VLOGA_GOST,
            'profil'    => false,
            'parametri' => ['besedilo', 'vrsta', 'pot', 'ikona', 'razred', 'atributi'],
        ],

        'kartica' => [
            'ime'       => 'Kartica',
            'opis'      => 'Vsebinska kartica z glavo, telesom in nogo. Podpira seznam ključ→vrednost ali HTML.',
            'kategorija'=> 'osnova',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/gradniki/kartica.php',
            'vloga_min' => VLOGA_GOST,
            'profil'    => false,
            'parametri' => ['naslov', 'vsebina', 'ikona', 'barva', 'noga', 'razred'],
        ],

        'modal' => [
            'ime'       => 'Modalno okno',
            'opis'      => 'Pojavno okno z animacijo odpiranja. Vsebuje glavo, telo in gumbe v nogi.',
            'kategorija'=> 'osnova',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/gradniki/modal.php',
            'vloga_min' => VLOGA_GOST,
            'profil'    => false,
            'parametri' => ['id', 'naslov', 'vsebina', 'gumbi', 'razred'],
        ],

        'stranski_pas' => [
            'ime'       => 'Stranski pas',
            'opis'      => 'Sidebar z menijem ali vsebinskimi bloki. Pozicija levo/desno.',
            'kategorija'=> 'layout',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/gradniki/stranski_pas.php',
            'vloga_min' => VLOGA_GOST,
            'profil'    => false,
            'parametri' => ['elementi', 'pozicija', 'razred'],
        ],

        // ════════════════════════════════════════════════════
        // KATEGORIJA: obrazci
        // Vnosni elementi. S0 dobi osnoven obrazec (prijava/reg),
        // višje stopnje dobijo naprednejše tipe polj.
        // ════════════════════════════════════════════════════

        'obrazec' => [
            'ime'       => 'Obrazec',
            'opis'      => 'Generični obrazec s polji (text, email, geslo, textarea, select, checkbox). '
                         . 'Gradnik prijave in registracije.',
            'kategorija'=> 'obrazci',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/gradniki/obrazec.php',
            'vloga_min' => VLOGA_GOST,
            'profil'    => false,
            'parametri' => ['akcija', 'metoda', 'polja', 'gumb', 'razred'],
            'opomba'    => 'CSRF token doda nalagalnik obrazca – ne v gradniku.',
        ],

        'polje_oblike' => [
            'ime'       => 'Polje oblike',
            'opis'      => 'Helper funkcije za individualna vnosna polja: polje_text(), polje_select() itd.',
            'kategorija'=> 'obrazci',
            'tip'       => 'php_funkcije',
            'pot'       => 'postavitev/gradniki/polje_oblike.php',
            'vloga_min' => VLOGA_S0,
            'profil'    => false,
            'parametri' => ['ime', 'vrednost', 'atributi'],
        ],

        // ════════════════════════════════════════════════════
        // KATEGORIJA: prikaz
        // Tabelarični in seznamski prikaz podatkov.
        // ════════════════════════════════════════════════════

        'tabela' => [
            'ime'       => 'Tabela',
            'opis'      => 'Podatkovona tabela z glavo in vrsticami. Horizontalni scroll na mobilnih.',
            'kategorija'=> 'prikaz',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/gradniki/tabela.php',
            'vloga_min' => VLOGA_S1,
            'profil'    => false,
            'parametri' => ['glava', 'vrstice', 'prazno', 'razred'],
        ],

        'kazalnik' => [
            'ime'       => 'Kazalnik korakov',
            'opis'      => 'Vizualni indikator napredka po korakih (wizard). Označuje opravljene in trenutni korak.',
            'kategorija'=> 'prikaz',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/osnova/kazalnik.php',
            'vloga_min' => VLOGA_S0,
            'profil'    => false,
            'parametri' => ['koraki', 'trenutni', 'opravljeni', 'prikaziStevilke'],
        ],

        'napredek' => [
            'ime'       => 'Vrstica napredka',
            'opis'      => 'Progresna vrstica s % ali vrednost/skupaj prikazom. Barvno prilagodljiva.',
            'kategorija'=> 'prikaz',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/osnova/napredek.php',
            'vloga_min' => VLOGA_S0,
            'profil'    => true,
            'parametri' => ['trenutni', 'skupaj', 'prikaz', 'barva', 'visina'],
        ],

        // ════════════════════════════════════════════════════
        // KATEGORIJA: navigacija
        // ════════════════════════════════════════════════════

        'navigacija' => [
            'ime'       => 'Navigacija',
            'opis'      => 'Glavna horizontalna navigacija z logotipom, menijemi in hamburger gumbom.',
            'kategorija'=> 'navigacija',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/osnova/navigacija.php',
            'vloga_min' => VLOGA_GOST,
            'profil'    => false,
            'parametri' => ['aktivnaPot', 'uporabnik'],
            'opomba'    => 'Prikaže/skrije elemente glede na sejo samodejno.',
        ],

        'navigacija_kozmicna' => [
            'ime'       => 'Kozmična navigacija',
            'opis'      => 'Razširjena navigacija z elementarnimi svetovi (Voda, Zrak, Eter, Zemlja, Ogenj). '
                         . 'Vidna samo za S2+ uporabnike.',
            'kategorija'=> 'navigacija',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/osnova/navigacija_kozmicna.php',
            'vloga_min' => VLOGA_S2,
            'profil'    => false,
            'parametri' => ['aktivnaSvet', 'svetovi'],
        ],

        'gumb_glas' => [
            'ime'       => 'Glasovni gumb',
            'opis'      => 'Gumb za aktivacijo glasovnega vmesnika. Poveže se z glasovni_panel JS modulom.',
            'kategorija'=> 'navigacija',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/osnova/gumb_glas.php',
            'vloga_min' => VLOGA_S4,
            'profil'    => true,
            'parametri' => ['razred'],
        ],

        // ════════════════════════════════════════════════════
        // KATEGORIJA: profil – gradniki za razvoj profila
        // Ti se prikazujejo v "gradniku profila" kjer vsak
        // uporabnik gradi svojo stran znotraj sistema.
        // ════════════════════════════════════════════════════

        'avatar_osnovni' => [
            'ime'       => 'Avatar – osnovna slika',
            'opis'      => 'Profilna slika iz mape avatarji/osnovni/. 10 možnosti. Osnova vsakega profila.',
            'kategorija'=> 'avatar',
            'tip'       => 'css_razred',
            'pot'       => 'vmesnik/elementi/varuhi/avatarji/osnovni/',
            'vloga_min' => VLOGA_S0,
            'profil'    => true,
            'moznosti'  => 10,
        ],

        'avatar_srednji' => [
            'ime'       => 'Avatar – srednji format',
            'opis'      => 'Večji avatar z več izrazi. 10 možnosti. Prikazan na profilu in v navigaciji.',
            'kategorija'=> 'avatar',
            'tip'       => 'css_razred',
            'pot'       => 'vmesnik/elementi/varuhi/avatarji/srednji/',
            'vloga_min' => VLOGA_S1,
            'profil'    => true,
            'moznosti'  => 10,
        ],

        'avatar_doprsni' => [
            'ime'       => 'Avatar – doprsni portret',
            'opis'      => 'Reprezentativni doprsni avatar. 10 možnosti. Prikazan na javnem profilu.',
            'kategorija'=> 'avatar',
            'tip'       => 'css_razred',
            'pot'       => 'vmesnik/elementi/varuhi/avatarji/doprsni/',
            'vloga_min' => VLOGA_S2,
            'profil'    => true,
            'moznosti'  => 10,
        ],

        'avatar_odsev' => [
            'ime'       => 'Avatar – odsev (posebni efekti)',
            'opis'      => 'Avatar z zrcalnim/energijskim odbleskOm. 8 možnosti. Za napredne profile.',
            'kategorija'=> 'avatar',
            'tip'       => 'css_razred',
            'pot'       => 'vmesnik/elementi/varuhi/avatarji/odsev/',
            'vloga_min' => VLOGA_S3,
            'profil'    => true,
            'moznosti'  => 8,
        ],

        'avatar_velik' => [
            'ime'       => 'Avatar – premium veliki format',
            'opis'      => 'Veliki premium avatar z dvema serijama. 15 možnosti. Za mojstrske profile.',
            'kategorija'=> 'avatar',
            'tip'       => 'css_razred',
            'pot'       => 'vmesnik/elementi/varuhi/avatarji/velik/',
            'vloga_min' => VLOGA_S5,
            'profil'    => true,
            'moznosti'  => 15,
        ],

        'misticni_avatar' => [
            'ime'       => 'Mistični avatar (generirani)',
            'opis'      => 'JS generator avatarja iz uporabniškega imena + energije. Unikatna kompozicija '
                         . 'iz emoji simbolov, ozadij in bitij. Vsak je edinstven.',
            'kategorija'=> 'avatar',
            'tip'       => 'js_modul',
            'pot'       => 'izvajanje/jedro/misticni_avatar.js',
            'vloga_min' => VLOGA_S4,
            'profil'    => true,
        ],

        'ime_prikazno' => [
            'ime'       => 'Prikazno ime',
            'opis'      => 'Vidno ime na profilu (ne nujno pravo ime). Besedilno polje z validacijo.',
            'kategorija'=> 'profil',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/gradniki/polje_oblike.php',
            'vloga_min' => VLOGA_S0,
            'profil'    => true,
        ],

        'bio_kratka' => [
            'ime'       => 'Kratka bio',
            'opis'      => 'Kratko besedilo o sebi (max 160 znakov). Prikazano pod imenom.',
            'kategorija'=> 'profil',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/gradniki/polje_oblike.php',
            'vloga_min' => VLOGA_S0,
            'profil'    => true,
        ],

        'tema_osnovna' => [
            'ime'       => 'Osnovna tema',
            'opis'      => 'Izbira med temno in svetlo temo. Shrani se v sejo in localStorage.',
            'kategorija'=> 'profil',
            'tip'       => 'kombinacija',
            'pot'       => 'postavitev/osnova/nastavitve_tema.php',
            'vloga_min' => VLOGA_S0,
            'profil'    => true,
            'teme'      => ['temna', 'svetla'],
        ],

        'barva_ozadja' => [
            'ime'       => 'Barva ozadja profila',
            'opis'      => 'Gradient ozadja profila iz predpripravljene palete. 12 kombinacij.',
            'kategorija'=> 'profil',
            'tip'       => 'css_razred',
            'pot'       => 'vmesnik/stili/teme/teme/',
            'vloga_min' => VLOGA_S1,
            'profil'    => true,
            'teme'      => ['avrora', 'oganj', 'astra', 'beta', 'minimal', 'mystic', 'standard'],
        ],

        'pisava_izbira' => [
            'ime'       => 'Pisava',
            'opis'      => 'Izbira tipografije za profil. Nastavlja CSS spremenljivko --pisava-osnova.',
            'kategorija'=> 'profil',
            'tip'       => 'css_razred',
            'pot'       => 'vmesnik/stili/css/spremenljivke.css',
            'vloga_min' => VLOGA_S1,
            'profil'    => true,
        ],

        'tema_po_meri' => [
            'ime'       => 'Tema po meri',
            'opis'      => 'Napredni urejevalnik CSS spremenljivk – barve, zaobljenosti, sence. '
                         . 'Vizualni editor brez pisanja kode.',
            'kategorija'=> 'profil',
            'tip'       => 'kombinacija',
            'pot'       => 'postavitev/strani/uporabniki/nastavitve_tema.php',
            'vloga_min' => VLOGA_S5,
            'profil'    => true,
        ],

        'css_po_meri' => [
            'ime'       => 'CSS po meri',
            'opis'      => 'Direktno pisanje CSS za profil. Sandbox – izoliran v profil-specifični selektor. '
                         . 'Samo za izkušene uporabnike.',
            'kategorija'=> 'profil',
            'tip'       => 'php_fragment',
            'pot'       => null,
            'vloga_min' => VLOGA_S5,
            'profil'    => true,
            'opomba'    => 'CSS se sanitizira pred shranjevanjem.',
        ],

        // ════════════════════════════════════════════════════
        // KATEGORIJA: vizualno
        // JS animacije in vizualni efekti.
        // ════════════════════════════════════════════════════

        'energijski_trak' => [
            'ime'       => 'Energijski trak',
            'opis'      => 'Animirana vrstica energije ki reagira na interakcijo z gradniki. '
                         . 'Polni se z aktivnostjo, prazni se v mirovanju.',
            'kategorija'=> 'vizualno',
            'tip'       => 'js_modul',
            'pot'       => 'izvajanje/vizualno/energijski_trak.js',
            'vloga_min' => VLOGA_S1,
            'profil'    => true,
        ],

        'delci_miske' => [
            'ime'       => 'Delci miške',
            'opis'      => 'Čarobni delci ki sledijo kazalcu. Animacija v ozadju profila.',
            'kategorija'=> 'vizualno',
            'tip'       => 'js_modul',
            'pot'       => 'izvajanje/vizualno/delci_miske.js',
            'vloga_min' => VLOGA_S2,
            'profil'    => true,
        ],

        'menjalnik_tem' => [
            'ime'       => 'Menjalnik tem',
            'opis'      => 'Gumb za preklapljanje med temami (misticna/svetla/temna). '
                         . 'Shranjuje izbiro v localStorage.',
            'kategorija'=> 'vizualno',
            'tip'       => 'js_modul',
            'pot'       => 'izvajanje/vizualno/menjalnik_tem.js',
            'vloga_min' => VLOGA_S1,
            'profil'    => true,
        ],

        'merilci' => [
            'ime'       => 'Merilci',
            'opis'      => 'Krožni in linearni merilci za prikaz vrednosti (energija, napredek, statistike).',
            'kategorija'=> 'vizualno',
            'tip'       => 'js_modul',
            'pot'       => 'izvajanje/vizualno/merilci.js',
            'vloga_min' => VLOGA_S2,
            'profil'    => true,
        ],

        'vizualizacija' => [
            'ime'       => 'Vizualizacija podatkov',
            'opis'      => 'Grafikoni in vizualizacije za osebne podatke (aktivnost, napredek, moduli).',
            'kategorija'=> 'vizualno',
            'tip'       => 'js_modul',
            'pot'       => 'izvajanje/vizualno/vizualizacija.js',
            'vloga_min' => VLOGA_S3,
            'profil'    => true,
        ],

        'povleci_spusti' => [
            'ime'       => 'Povleci in spusti',
            'opis'      => 'Drag & drop urejanje gradnikov profila. Omogoča prerazporeditev blokov.',
            'kategorija'=> 'vizualno',
            'tip'       => 'js_modul',
            'pot'       => 'izvajanje/vizualno/povleci_spusti.js',
            'vloga_min' => VLOGA_S3,
            'profil'    => true,
        ],

        'animacije_osnova' => [
            'ime'       => 'Animacije – osnova',
            'opis'      => 'CSS animacije za vstop/izstop elementov. Fade, slide, scale efekti.',
            'kategorija'=> 'vizualno',
            'tip'       => 'css_razred',
            'pot'       => 'vmesnik/stili/css/misticna_svetloba.css',
            'vloga_min' => VLOGA_S3,
            'profil'    => true,
        ],

        'animacije_napredno' => [
            'ime'       => 'Animacije – napredne',
            'opis'      => 'Kompleksne energijske animacije: avrora, pulsiranje, kozmični efekti.',
            'kategorija'=> 'vizualno',
            'tip'       => 'css_razred',
            'pot'       => 'vmesnik/stili/css/misticno.css',
            'vloga_min' => VLOGA_S4,
            'profil'    => true,
        ],

        'zemlja_3d' => [
            'ime'       => 'Zemlja 3D',
            'opis'      => 'Three.js kozmični background z orbitalnimi telesi. Interaktiven z miško.',
            'kategorija'=> 'vizualno',
            'tip'       => 'kombinacija',
            'pot'       => 'postavitev/3d/js/universe.js',
            'vloga_min' => VLOGA_S4,
            'profil'    => true,
            'odvisnosti'=> ['three.js'],
        ],

        'kozmos_3d' => [
            'ime'       => 'Kozmos 3D (premium)',
            'opis'      => 'Polni 3D kozmični prikaz z galaxijami, zvezdami in portalnimi efekti. '
                         . 'Zahteva WebGL. Samo za mojstrsko stopnjo.',
            'kategorija'=> 'vizualno',
            'tip'       => 'kombinacija',
            'pot'       => 'postavitev/3d/js/universe.js',
            'vloga_min' => VLOGA_S5,
            'profil'    => true,
            'odvisnosti'=> ['three.js', 'universe_data.php'],
        ],

        // ════════════════════════════════════════════════════
        // KATEGORIJA: vsebina profila
        // Mistični in duhovni elementi ki jih uporabnik
        // razstavi na svojem profilu.
        // ════════════════════════════════════════════════════

        'karte_prikaz' => [
            'ime'       => 'Karte – prikaz',
            'opis'      => '7 tarot/simbolnih kart iz zbirke. Prikazane na profilu kot razstavni predmeti. '
                         . 'Hrbet karte vidijo vsi, lice samo S2+.',
            'kategorija'=> 'profil',
            'tip'       => 'kombinacija',
            'pot'       => 'vmesnik/elementi/ui/karte/',
            'vloga_min' => VLOGA_S2,
            'profil'    => true,
            'moznosti'  => 7,
        ],

        'zvitek_osebni' => [
            'ime'       => 'Osebni zvitek',
            'opis'      => 'Dekorativni zvitek z osebnim besedilom (moto, citat, opis). '
                         . '10 različnih zvitkov za izbiro.',
            'kategorija'=> 'profil',
            'tip'       => 'kombinacija',
            'pot'       => 'vmesnik/elementi/ui/zvitki/',
            'vloga_min' => VLOGA_S2,
            'profil'    => true,
            'moznosti'  => 10,
        ],

        'cakre_prikaz' => [
            'ime'       => 'Čakre – prikaz',
            'opis'      => '7 čakr z barvnimi indikatorji. Vsaka čakra prikazuje nivo aktivacije. '
                         . 'Podatke vnese uporabnik ali AI asistent.',
            'kategorija'=> 'profil',
            'tip'       => 'kombinacija',
            'pot'       => 'vmesnik/elementi/ui/cakre/',
            'vloga_min' => VLOGA_S2,
            'profil'    => true,
            'moznosti'  => 7,
        ],

        'rune_dnevne' => [
            'ime'       => 'Dnevne rune',
            'opis'      => 'Widget z dnevno runo. Samodejno se posodobi vsak dan. '
                         . 'Podpira 31 run iz zbirke.',
            'kategorija'=> 'profil',
            'tip'       => 'kombinacija',
            'pot'       => 'vmesnik/elementi/ui/rune/',
            'vloga_min' => VLOGA_S3,
            'profil'    => true,
            'moznosti'  => 31,
        ],

        'kristali_zbirka' => [
            'ime'       => 'Zbirka kristalov',
            'opis'      => 'Galerija pridobljenih kristalov (dosežki sistema). Vsak kristal ima ime, '
                         . 'redkost (common→legendary) in opis. Pridobijo se z aktivnostjo.',
            'kategorija'=> 'profil',
            'tip'       => 'kombinacija',
            'pot'       => 'vmesnik/elementi/ui/kristali/',
            'vloga_min' => VLOGA_S3,
            'profil'    => true,
            'redkosti'  => ['common', 'rare', 'epic', 'legendary'],
        ],

        'portal_vstop' => [
            'ime'       => 'Portalni vstop',
            'opis'      => 'Animiran portal na profilu ki vodi v izbrani elementarni svet. '
                         . '4 vizualni portali za izbiro.',
            'kategorija'=> 'profil',
            'tip'       => 'kombinacija',
            'pot'       => 'vmesnik/elementi/ui/portali/',
            'vloga_min' => VLOGA_S3,
            'profil'    => true,
            'moznosti'  => 4,
        ],

        'preroska_krogla' => [
            'ime'       => 'Preročiška krogla',
            'opis'      => '4 variante preročiških krogel z animiranim odbleskOm. '
                         . 'Ob kliku pokaže naključno modrosti sporočilo.',
            'kategorija'=> 'profil',
            'tip'       => 'kombinacija',
            'pot'       => 'vmesnik/elementi/ui/preroska_krogla/',
            'vloga_min' => VLOGA_S5,
            'profil'    => true,
            'moznosti'  => 4,
        ],

        'relikvije_razstava' => [
            'ime'       => 'Razstava relikvij',
            'opis'      => 'Do 7 pridobljenih relikvij na posebnem razstavnem stojalu profila. '
                         . 'Relikvije se pridobijo z zaključkom naprednih nalog.',
            'kategorija'=> 'profil',
            'tip'       => 'kombinacija',
            'pot'       => 'vmesnik/elementi/ui/relikvije/',
            'vloga_min' => VLOGA_S5,
            'profil'    => true,
            'moznosti'  => 7,
        ],

        // ════════════════════════════════════════════════════
        // KATEGORIJA: ai
        // AI in glasovni gradniki za S4+ uporabnike.
        // ════════════════════════════════════════════════════

        'ai_asistent_widget' => [
            'ime'       => 'AI Asistent – widget',
            'opis'      => 'Mini widget na profilu za hiter klic AI asistenta. '
                         . 'Odpre klepetalnik z AstraMentalica AI.',
            'kategorija'=> 'ai',
            'tip'       => 'kombinacija',
            'pot'       => 'asistent/ai_asistent.php',
            'vloga_min' => VLOGA_S4,
            'profil'    => true,
        ],

        'glasovni_panel' => [
            'ime'       => 'Glasovni panel',
            'opis'      => 'Glasovni vmesnik za upravljanje sistema z govorom. '
                         . 'STT (speech-to-text) + TTS (text-to-speech) slovensko.',
            'kategorija'=> 'ai',
            'tip'       => 'kombinacija',
            'pot'       => 'zvoki/glasovni/glasovni_ui.js',
            'vloga_min' => VLOGA_S4,
            'profil'    => true,
            'odvisnosti'=> ['glasovni_servis'],
        ],

        'umetna_inteligenca' => [
            'ime'       => 'AI vizualizacija',
            'opis'      => 'Vizualni prikaz AI stanja in aktivnosti. Animirani možgani/nevronska mreža.',
            'kategorija'=> 'ai',
            'tip'       => 'js_modul',
            'pot'       => 'izvajanje/vizualno/umetna_inteligenca.js',
            'vloga_min' => VLOGA_S4,
            'profil'    => true,
        ],

        // ════════════════════════════════════════════════════
        // KATEGORIJA: admin
        // Samo VLOGA_ADMIN – debug in nadzor.
        // ════════════════════════════════════════════════════

        'debug_panel' => [
            'ime'       => 'Debug panel',
            'opis'      => 'Diagnostični panel: faze zaganjalnika, zaznamki, whitelist, seja, env.',
            'kategorija'=> 'admin',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/strani/admin/astra_nadzor.php',
            'vloga_min' => VLOGA_ADMIN,
            'profil'    => false,
        ],

        'sistem_info' => [
            'ime'       => 'Sistemske informacije',
            'opis'      => 'PHP verzija, naložene razširitve, pot konstante, env status, cache statistike.',
            'kategorija'=> 'admin',
            'tip'       => 'php_fragment',
            'pot'       => 'postavitev/strani/admin/nadzorni_center.php',
            'vloga_min' => VLOGA_ADMIN,
            'profil'    => false,
        ],

        'metrike_widget' => [
            'ime'       => 'Metrike widget',
            'opis'      => 'Live metrike sistema: aktivni uporabniki, zahtevki/min, napake, cache hit rate.',
            'kategorija'=> 'admin',
            'tip'       => 'kombinacija',
            'pot'       => 'postavitev/strani/admin/admin_portal.php',
            'vloga_min' => VLOGA_ADMIN,
            'profil'    => false,
        ],

    ]; // konec register
}

// ============================================================
// JAVNE FUNKCIJE
// ============================================================

/**
 * Vrne vse gradnike dostopne za dano vlogo (vloga_min <= vloga).
 *
 * @return array<string, array>
 */
function gradniki_za_vlogo(int $vloga): array
{
    return array_filter(
        gradniki_register(),
        fn(array $g) => $vloga >= $g['vloga_min']
    );
}

/**
 * Vrne samo gradnike namenjene razvoju profila, dostopne za vlogo.
 *
 * @return array<string, array>
 */
function gradniki_profila(int $vloga): array
{
    return array_filter(
        gradniki_za_vlogo($vloga),
        fn(array $g) => $g['profil'] === true
    );
}

/**
 * Vrne posamezni gradnik po ključu ali null.
 */
function gradnik_pridobi(string $kljuc): ?array
{
    return gradniki_register()[$kljuc] ?? null;
}

/**
 * Vrne gradnike za vlogo, grupirane po kategoriji.
 *
 * @return array<string, array<string, array>>
 */
function gradniki_po_kategoriji(int $vloga): array
{
    $rezultat = [];
    foreach (gradniki_za_vlogo($vloga) as $kljuc => $gradnik) {
        $kat = $gradnik['kategorija'];
        $rezultat[$kat][$kljuc] = $gradnik;
    }
    return $rezultat;
}

/**
 * Preveri ali ima vloga dostop do konkretnega gradnika.
 */
function gradnik_je_dovoljen(string $kljuc, int $vloga): bool
{
    $g = gradnik_pridobi($kljuc);
    return $g !== null && $vloga >= $g['vloga_min'];
}
