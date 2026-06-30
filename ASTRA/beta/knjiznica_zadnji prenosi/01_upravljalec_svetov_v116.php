<?php
/**
 * ============================================================
 * POT: SISTEM/kernel/jedro/01_upravljalec_svetov.php
 * 📅 VERZIJA: v116 (28.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: KERNEL N3 (JEDRO)
 *
 * 📰 NAMEN:
 *     Upravljalec svetov – generira whiteliste glede na vlogo
 *     prijavljenega uporabnika (RBAC).
 *
 *     Definira TRI whiteliste:
 *
 *     A) SVETOVI   – kateri "sveti" (URL parametri ?svet=X) so dovoljeni
 *     B) MODULI    – kateri moduli so vidni/dostopni (profil razvoj)
 *     C) GRADNIKI  – kateri UI gradniki profila so na voljo
 *
 *     Vsak whitelist je razbit po nivojih vloge (VLOGA_* konstante
 *     iz pot.php). Višja vloga dobi vse kar ima nižja + svoje.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - upravljalec_svetov_izvedi(array $zahteva): array
 *     - svetovi_dovoljeni(int $vloga): array
 *     - moduli_dovoljeni(int $vloga): array
 *     - gradniki_dovoljeni(int $vloga): array
 *     - svetovi_je_dovoljen(string $svet, int $vloga): bool
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (VLOGA_* konstante, POT_PODATKI)
 *     - 04_seja.php  → seja_pridobi_uporabnika() (naloži se kasneje,
 *       zato upravljalec_svetov_izvedi() pridobi vlogo direktno iz
 *       $_SESSION, ne prek seja_pridobi_uporabnika())
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *     - Brez poslovne logike (samo whitelist definicije)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v116: popolna prenova – ločeni whitelisti za svetove,
 *             module in gradnike; RBAC stopnje (GOST→S5→ADMIN);
 *             kumulativno seštevanje (višja vloga = vse nižjih + svoje)
 *     - v115: samo svetovi, brez RBAC
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     kernel, jedro, whitelist, rbac, svetovi, moduli, gradniki
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// A) WHITELIST SVETOV  (URL parameter ?svet=X)
//
// Vsak nivo dobi svoje + vse od nižjih (kumulativno).
// ============================================================

const _SVETOVI_PO_VLOGI = [
    //  vloga_min  => [dodatni svetovi za ta nivo]
    VLOGA_GOST  => ['GLOBALNO', 'SISTEM'],               // prijava, registracija, pravno
    VLOGA_S0    => ['UPORABNIKI'],                        // lasten profil, dashboard
    VLOGA_S1    => ['MODULI'],                            // branje modulov
    VLOGA_S2    => ['ASTRA'],                             // napredna vsebina
    VLOGA_S3    => ['ADAPTER'],                           // AI adapter
    VLOGA_S4    => ['AI'],                                // direktni AI klici
    VLOGA_S5    => ['PODATKI'],                           // podatkovni dostop
    VLOGA_ADMIN => ['ADMIN', 'VODA', 'ZRAK', 'ETER', 'ZEMLJA', 'OGENJ'],
];

// ============================================================
// B) WHITELIST MODULOV  (kateri moduli so vidni na profilu)
//
// Ključ = identifikator modula (mapira na MODULI/<ime>/)
// ============================================================

const _MODULI_PO_VLOGI = [
    VLOGA_GOST  => [],                                    // gost – brez modulov
    VLOGA_S0    => [                                      // osnovna stopnja
        'horoskop',
        'numerologija',
        'vreme',
    ],
    VLOGA_S1    => [                                      // + razširjeno branje
        'rune',
        'simboli',
        'knjiznica',
    ],
    VLOGA_S2    => [                                      // + interakcija
        'kristali',
        'cakre',
        'meditacija',
    ],
    VLOGA_S3    => [                                      // + orodja
        'portali',
        'peskovnik',
        'synera',
    ],
    VLOGA_S4    => [                                      // + AI funkcije
        'ai_asistent',
        'duhovni_varuh',
        'glasovni',
    ],
    VLOGA_S5    => [                                      // + napredni razvoj
        'sigili',
        'frekvence',
        'geometrija',
        'zalozba',
        'trznica',
    ],
    VLOGA_ADMIN => [                                      // + admin orodja
        'statistike',
        'email_porocila',
        'izvoz_podatkov',
        'varnostno_kopiranje',
    ],
];

// ============================================================
// C) WHITELIST GRADNIKOV PROFILA
//    (kateri UI elementi so na voljo za razvoj lastnega profila)
//
// Ključ = identifikator gradnika (CSS razred / tip)
// ============================================================

const _GRADNIKI_PO_VLOGI = [
    VLOGA_GOST  => [],
    VLOGA_S0    => [                                      // osnova profila
        'avatar_osnovni',
        'ime_prikazno',
        'bio_kratka',
        'tema_osnovna',
    ],
    VLOGA_S1    => [                                      // + vizualna prilagoditev
        'avatar_srednji',
        'barva_ozadja',
        'pisava_izbira',
        'energijski_trak',
    ],
    VLOGA_S2    => [                                      // + vsebinski bloki
        'avatar_doprsni',
        'karte_prikaz',
        'zvitek_osebni',
        'cakre_prikaz',
    ],
    VLOGA_S3    => [                                      // + interaktivni elementi
        'avatar_odsev',
        'portal_vstop',
        'kristali_zbirka',
        'rune_dnevne',
        'animacije_osnova',
    ],
    VLOGA_S4    => [                                      // + AI in glasovni
        'ai_asistent_widget',
        'glasovni_panel',
        'misticni_avatar',
        'animacije_napredno',
        'zemlja_3d',
    ],
    VLOGA_S5    => [                                      // + premium izgled
        'avatar_velik',
        'preroska_krogla',
        'relikvije_razstava',
        'kozmos_3d',
        'tema_po_meri',
        'css_po_meri',
    ],
    VLOGA_ADMIN => [                                      // + debug elementi
        'debug_panel',
        'sistem_info',
        'metrike_widget',
    ],
];

// ============================================================
// JAVNI VMESNIK
// ============================================================

/**
 * Vrne kumulativni seznam svetov za dano vlogo.
 * Višja vloga vključuje vse svetove nižjih vlog.
 */
function svetovi_dovoljeni(int $vloga): array
{
    return _whitelist_kumulativno(_SVETOVI_PO_VLOGI, $vloga);
}

/**
 * Vrne kumulativni seznam modulov za dano vlogo.
 */
function moduli_dovoljeni(int $vloga): array
{
    return _whitelist_kumulativno(_MODULI_PO_VLOGI, $vloga);
}

/**
 * Vrne kumulativni seznam gradnikov za dano vlogo.
 */
function gradniki_dovoljeni(int $vloga): array
{
    return _whitelist_kumulativno(_GRADNIKI_PO_VLOGI, $vloga);
}

/**
 * Preveri ali je posamezni svet dovoljen za dano vlogo.
 */
function svetovi_je_dovoljen(string $svet, int $vloga): bool
{
    return in_array($svet, svetovi_dovoljeni($vloga), true);
}

/**
 * Glavna faza – kliče jo zaganjalnik.
 * Pridobi vlogo iz seje (seja se naloži v fazi 04, zato tukaj
 * beremo $_SESSION direktno – seja je morda že aktivna od prejšnjega
 * zahtevka) in zapiše whiteliste v $zahteva['sistem'].
 */
function upravljalec_svetov_izvedi(array $zahteva): array
{
    // Vloga: iz seje (če obstaja) ali GOST
    $vloga = 0;
    if (session_status() === PHP_SESSION_ACTIVE) {
        $vloga = (int)($_SESSION['uporabnik_vloga'] ?? VLOGA_GOST);
    }

    $svetovi  = svetovi_dovoljeni($vloga);
    $moduli   = moduli_dovoljeni($vloga);
    $gradniki = gradniki_dovoljeni($vloga);

    // Shrani v zahtevo
    $zahteva['sistem']['whitelist_svetovi']  = $svetovi;
    $zahteva['sistem']['whitelist_moduli']   = $moduli;
    $zahteva['sistem']['whitelist_gradniki'] = $gradniki;
    $zahteva['sistem']['whitelist_vloga']    = $vloga;
    $zahteva['sistem']['whitelist_generiran'] = time();

    // Opcijsko: zapiši v cache za hiter dostop
    _svetovi_shrani_cache($vloga, $svetovi, $moduli, $gradniki);

    return $zahteva;
}

// ============================================================
// INTERNE FUNKCIJE
// ============================================================

/**
 * Kumulativno sešteje whitelist za vse vloge <= $vloga.
 *
 * @param array<int, array<string>> $definicija
 */
function _whitelist_kumulativno(array $definicija, int $vloga): array
{
    $rezultat = [];
    foreach ($definicija as $minVloga => $elementi) {
        if ($vloga >= $minVloga) {
            $rezultat = array_merge($rezultat, $elementi);
        }
    }
    return array_unique($rezultat);
}

/**
 * Zapiše whitelist v JSON cache datoteko.
 * Cache se invalidira avtomatično (vsak request za to vlogo).
 */
function _svetovi_shrani_cache(int $vloga, array $svetovi, array $moduli, array $gradniki): void
{
    if (!defined('PODATKI_CACHE')) {
        return;
    }

    $mapa = PODATKI_CACHE . '/whitelist';
    if (!is_dir($mapa)) {
        @mkdir($mapa, 0750, true);
    }

    $datoteka = $mapa . '/vloga_' . $vloga . '.json';
    $podatki  = [
        'vloga'     => $vloga,
        'svetovi'   => $svetovi,
        'moduli'    => $moduli,
        'gradniki'  => $gradniki,
        'cas'       => time(),
    ];

    @file_put_contents($datoteka, json_encode($podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
