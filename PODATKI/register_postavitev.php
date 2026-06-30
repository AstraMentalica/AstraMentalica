<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/register_postavitev.php
 * 📅 VERZIJA: v100 (28.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Register vseh postavitev profila – definira kdaj se
 *     odklenejo (samodejno z napredovanjem ali z nakupom).
 *
 *     Vsaka postavitev ima:
 *       - vloga_min      → samodejno odprta ob tej stopnji
 *       - cena_tip/cena  → če NULL, je samo z napredovanjem
 *       - kupljivo       → ali jo je mogoče kupiti prej
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - postavitev_register(): array
 *     - postavitve_za_uid(string $uid): array  (odprte + zaprte)
 *     - postavitev_je_odprta(string $uid, string $kljuc): bool
 *     - postavitev_odklenjena_ob_napredovanju(int $vloga): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (VLOGA_*)
 *     - ekonomija/inventar_storitev.php
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__, echo, die()
 *
 * 📌 STATUS: Stabilno
 * 📅 ZGODOVINA:
 *     - v100: prva implementacija
 * 👤 AVTOR: AstraMentalica Mojster
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// REGISTER POSTAVITEV
// ============================================================

function postavitev_register(): array
{
    return [

        // ════════════════════════════════════════════════════
        // S0 – SAMODEJNO ob registraciji
        // ════════════════════════════════════════════════════

        'osnova_1col' => [
            'ime'        => 'Osnova – ena kolona',
            'opis'       => 'Profil v eni koloni. Avatar, ime, bio, napredek.',
            'vloga_min'  => VLOGA_S0,
            'kupljivo'   => false,
            'cena_tip'   => null,
            'cena'       => null,
            'predogled'  => 'postavitev/osnova/osnova.php',
        ],

        // ════════════════════════════════════════════════════
        // S1 – samodejno ali kupljivo z 200 točkami
        // ════════════════════════════════════════════════════

        'osnova_2col' => [
            'ime'        => 'Dve koloni',
            'opis'       => 'Profil z dvema kolonama. Sidebar + vsebina.',
            'vloga_min'  => VLOGA_S1,
            'kupljivo'   => true,
            'cena_tip'   => 'tocke',
            'cena'       => 200,
            'predogled'  => null,
        ],

        'tema_svetla' => [
            'ime'        => 'Svetla tema',
            'opis'       => 'Svetla barvna shema.',
            'vloga_min'  => VLOGA_S1,
            'kupljivo'   => false,
            'cena_tip'   => null,
            'cena'       => null,
            'predogled'  => 'vmesnik/stili/teme/svetla.css',
        ],

        'energijski_trak' => [
            'ime'        => 'Energijski trak',
            'opis'       => 'Animirana vrstica energije pod navigacijo.',
            'vloga_min'  => VLOGA_S1,
            'kupljivo'   => true,
            'cena_tip'   => 'tocke',
            'cena'       => 150,
            'predogled'  => null,
        ],

        // ════════════════════════════════════════════════════
        // S2 – samodejno ali z runo/kristalom
        // ════════════════════════════════════════════════════

        'kozmicna_nav' => [
            'ime'        => 'Kozmična navigacija',
            'opis'       => 'Razširjena navigacija z elementarnimi svetovi.',
            'vloga_min'  => VLOGA_S2,
            'kupljivo'   => true,
            'cena_tip'   => 'runa',
            'cena'       => 2,
            'predogled'  => null,
        ],

        'cakre_blok' => [
            'ime'        => 'Blok čakr',
            'opis'       => '7 čakr z barvnimi indikatorji na profilu.',
            'vloga_min'  => VLOGA_S2,
            'kupljivo'   => true,
            'cena_tip'   => 'tocke',
            'cena'       => 400,
            'predogled'  => null,
        ],

        'zvitek_blok' => [
            'ime'        => 'Osebni zvitek',
            'opis'       => 'Dekorativni zvitek z osebnim besedilom.',
            'vloga_min'  => VLOGA_S2,
            'kupljivo'   => true,
            'cena_tip'   => 'zvitek',   // plačaš z zvitkom
            'cena'       => 1,
            'predogled'  => null,
        ],

        'karte_razstava' => [
            'ime'        => 'Razstava kart',
            'opis'       => '3 karte razstavljene na profilu.',
            'vloga_min'  => VLOGA_S2,
            'kupljivo'   => true,
            'cena_tip'   => 'tocke',
            'cena'       => 600,
            'predogled'  => null,
        ],

        'delci_miske' => [
            'ime'        => 'Delci miške',
            'opis'       => 'Čarobni delci ki sledijo kazalcu.',
            'vloga_min'  => VLOGA_S2,
            'kupljivo'   => true,
            'cena_tip'   => 'tocke',
            'cena'       => 150,
            'predogled'  => null,
        ],

        // ════════════════════════════════════════════════════
        // S3 – portali, 3D osnova, kristali
        // ════════════════════════════════════════════════════

        'portal_vstop' => [
            'ime'        => 'Portalni vstop',
            'opis'       => 'Animiran portal ki vodi v elementarni svet.',
            'vloga_min'  => VLOGA_S3,
            'kupljivo'   => true,
            'cena_tip'   => 'runa',
            'cena'       => 3,
            'predogled'  => null,
        ],

        'rune_dnevne' => [
            'ime'        => 'Dnevna runa',
            'opis'       => 'Widget z dnevno runo. Posodobi se vsak dan.',
            'vloga_min'  => VLOGA_S3,
            'kupljivo'   => true,
            'cena_tip'   => 'runa',
            'cena'       => 1,
            'predogled'  => null,
        ],

        'kristali_zbirka' => [
            'ime'        => 'Zbirka kristalov',
            'opis'       => 'Galerija pridobljenih kristalov na profilu.',
            'vloga_min'  => VLOGA_S3,
            'kupljivo'   => true,
            'cena_tip'   => 'kristal',
            'cena'       => 1,
            'cena_redkost' => 'rare',
            'predogled'  => null,
        ],

        'zemlja_3d_osnova' => [
            'ime'        => 'Zemlja 3D – osnova',
            'opis'       => 'Three.js ozadje z zvezdami in orbitami.',
            'vloga_min'  => VLOGA_S3,
            'kupljivo'   => true,
            'cena_tip'   => 'tocke',
            'cena'       => 1200,
            'predogled'  => null,
        ],

        // ════════════════════════════════════════════════════
        // S4 – AI, glasovni, napredne animacije
        // ════════════════════════════════════════════════════

        'ai_widget' => [
            'ime'        => 'AI Asistent widget',
            'opis'       => 'Mini AI klepetalni widget na profilu.',
            'vloga_min'  => VLOGA_S4,
            'kupljivo'   => true,
            'cena_tip'   => 'relikvija',
            'cena'       => 1,
            'cena_redkost' => 'common',
            'predogled'  => null,
        ],

        'glasovni_panel' => [
            'ime'        => 'Glasovni panel',
            'opis'       => 'STT/TTS glasovni vmesnik slovensko.',
            'vloga_min'  => VLOGA_S4,
            'kupljivo'   => true,
            'cena_tip'   => 'relikvija',
            'cena'       => 1,
            'cena_redkost' => 'common',
            'predogled'  => null,
        ],

        'misticni_avatar' => [
            'ime'        => 'Mistični avatar',
            'opis'       => 'Generirani unikatni avatar iz energije profila.',
            'vloga_min'  => VLOGA_S4,
            'kupljivo'   => true,
            'cena_tip'   => 'kristal',
            'cena'       => 1,
            'cena_redkost' => 'epic',
            'predogled'  => null,
        ],

        'animacije_napredno' => [
            'ime'        => 'Napredne animacije',
            'opis'       => 'Avrora, pulsiranje, kozmični efekti.',
            'vloga_min'  => VLOGA_S4,
            'kupljivo'   => true,
            'cena_tip'   => 'tocke',
            'cena'       => 2000,
            'predogled'  => null,
        ],

        // ════════════════════════════════════════════════════
        // S5 – mojstrska stopnja, premium, CSS po meri
        // ════════════════════════════════════════════════════

        'kozmos_3d' => [
            'ime'        => 'Kozmos 3D',
            'opis'       => 'Polni 3D kozmični prikaz z galaxijami.',
            'vloga_min'  => VLOGA_S5,
            'kupljivo'   => true,            // S4 ga lahko kupi z epic relikvijo
            'cena_tip'   => 'relikvija',
            'cena'       => 1,
            'cena_redkost' => 'epic',
            'predogled'  => null,
        ],

        'preroska_krogla' => [
            'ime'        => 'Preročiška krogla',
            'opis'       => '4 krogel z animacijo. Ob kliku modrosti.',
            'vloga_min'  => VLOGA_S5,
            'kupljivo'   => true,
            'cena_tip'   => 'relikvija',
            'cena'       => 1,
            'cena_redkost' => 'rare',
            'predogled'  => null,
        ],

        'relikvije_razstava' => [
            'ime'        => 'Razstava relikvij',
            'opis'       => 'Stojalo z do 7 relikvijami na profilu.',
            'vloga_min'  => VLOGA_S5,
            'kupljivo'   => false,           // samo z napredovanjem
            'cena_tip'   => null,
            'cena'       => null,
            'predogled'  => null,
        ],

        'css_po_meri' => [
            'ime'        => 'CSS po meri',
            'opis'       => 'Direktno pisanje CSS – sandbox izoliran na profil.',
            'vloga_min'  => VLOGA_S5,
            'kupljivo'   => false,
            'cena_tip'   => null,
            'cena'       => null,
            'predogled'  => null,
        ],

        'tema_po_meri' => [
            'ime'        => 'Vizualni urejevalnik teme',
            'opis'       => 'Editor CSS spremenljivk brez pisanja kode.',
            'vloga_min'  => VLOGA_S5,
            'kupljivo'   => true,
            'cena_tip'   => 'kristal',
            'cena'       => 1,
            'cena_redkost' => 'legendary',
            'predogled'  => null,
        ],

    ];
}

// ============================================================
// JAVNE FUNKCIJE
// ============================================================

/**
 * Vrne vse postavitve za danega uporabnika z oznako odprta/zaprta.
 * Odprta = vloga >= vloga_min ALI je v inventarju kot kupljeno.
 */
function postavitve_za_uid(string $uid): array
{
    $inv       = inventar_pridobi($uid);
    $vloga     = _postavitev_vloga_uid($uid);
    $kupljene  = $inv['predmeti']['postavitev'] ?? [];
    $register  = postavitev_register();

    $rezultat = [];
    foreach ($register as $kljuc => $p) {
        $odprta = $vloga >= $p['vloga_min'] || isset($kupljene[$kljuc]);
        $rezultat[$kljuc] = [
            ...$p,
            'kljuc'    => $kljuc,
            'odprta'   => $odprta,
            'kupljena' => isset($kupljene[$kljuc]),
        ];
    }

    return $rezultat;
}

/**
 * Preveri ali ima uporabnik dostop do določene postavitve.
 */
function postavitev_je_odprta(string $uid, string $kljuc): bool
{
    $vse = postavitve_za_uid($uid);
    return $vse[$kljuc]['odprta'] ?? false;
}

/**
 * Vrne seznam postavitev ki se samodejno odklenejo ob napredovanju na vloga.
 */
function postavitev_odklenjena_ob_napredovanju(int $vloga): array
{
    return array_keys(array_filter(
        postavitev_register(),
        fn($p) => $p['vloga_min'] === $vloga
    ));
}

// ============================================================
// INTERNE
// ============================================================

function _postavitev_vloga_uid(string $uid): int
{
    $profil = shramba_beri_enega(PODATKI_UPORABNIKI . '/' . $uid, 'profil');
    return (int)($profil['vloga'] ?? VLOGA_GOST);
}
