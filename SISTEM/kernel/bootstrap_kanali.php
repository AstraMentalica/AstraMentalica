<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/kernel/bootstrap_kontrakti.php
 * v111 (27.5.2026)
 * ---------------------------------------------------------
 * OPIS: Zagotovi, da so vsi kontrakti na voljo (tudi če ASTRA manjka)
 * ---------------------------------------------------------
 */

declare(strict_types=1);

// ============================================================
// 1. DEFINIRAJ KONSTANTE, ČE NE OBSTAJAJO
// ============================================================
if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__, 2));
}
if (!defined('POT_ASTRA')) {
    define('POT_ASTRA', ROOT . '/ASTRA/');
}

// ============================================================
// 2. POSKUSI NAJTI KONTRAKTE V ASTRA/
// ============================================================
$kontraktMape = [
    POT_ASTRA . '/razvoj/kontrakti/',
    ROOT . '/ASTRA/razvoj/kontrakti/',
    ROOT . '/SISTEM/kernel/../ASTRA/razvoj/kontrakti/'
];

$kontraktiNalozeni = false;
foreach ($kontraktMape as $mapa) {
    if (is_dir($mapa)) {
        $datoteke = glob($mapa . '*.php');
        foreach ($datoteke as $datoteka) {
            require_once $datoteka;
        }
        $kontraktiNalozeni = true;
        break;
    }
}

// ============================================================
// 3. ČE ASTRA KONTRAKTI NISO NA VOLJO, DEFINIRAJ ZAČASNE
// ============================================================
if (!$kontraktiNalozeni) {
    
    // ---------- SHRAMBA KONTRAKTI ----------
    if (!interface_exists('ShrambaBranje')) {
        interface ShrambaBranje {
            public function beri(string $zbirka, array $pogoji = []): array;
            public function beri_enega(string $zbirka, string $id): ?array;
            public function obstaja(string $zbirka, string $id): bool;
            public function prestej(string $zbirka, array $pogoji = []): int;
        }
    }
    
    if (!interface_exists('ShrambaPisanje')) {
        interface ShrambaPisanje {
            public function zapisi(string $zbirka, array $podatki): ?string;
            public function posodobi(string $zbirka, string $id, array $podatki): bool;
            public function zbrisi(string $zbirka, string $id): bool;
        }
    }
    
    if (!interface_exists('ShrambaTransakcija')) {
        interface ShrambaTransakcija {
            public function transakcija_zacni(): void;
            public function transakcija_potrdi(): void;
            public function transakcija_preklici(): void;
        }
    }
    
    if (!interface_exists('ShrambaZaklep')) {
        interface ShrambaZaklep {
            public function zaklep_pridobi(string $ime, int $časovna_omejitev = 30): bool;
            public function zaklep_spusti(string $ime): void;
            public function zaklep_je_zaklenjen(string $ime): bool;
        }
    }
    
    // ---------- ODZIV IN ZAHTEVA KONTRAKTI ----------
    if (!interface_exists('OdzivPogodba')) {
        interface OdzivPogodba {
            public static function ustvari(array $podatki): array;
            public static function preveri(array $odziv): bool;
            public static function napaka(string $sporocilo, int $koda = 500): array;
            public static function uspeh(array $vsebina, string $sporocilo = ''): array;
        }
    }
    
    if (!interface_exists('ZahtevaPogodba')) {
        interface ZahtevaPogodba {
            public static function ustvari(array $podatki): array;
            public static function preveri(array $zahteva): bool;
            public static function iz(array $superglobal): array;
        }
    }
    
    // ---------- KANAL KONTRAKTI ----------
    if (!interface_exists('KanalInterface')) {
        interface KanalInterface {
            public function obdelaj(array $zahteva): array;
            public function ime(): string;
            public function poslji(array $odziv): void;
            public function podpira(string $tip): bool;
        }
    }
    
    // ---------- ADAPTER KONTRAKTI ----------
    if (!interface_exists('AdapterMost')) {
        interface AdapterMost {
            public function sprejmi(array $zahteva): array;
            public function poslji(array $odziv): void;
            public function kanal(string $ime): KanalInterface;
            public function kanali(): array;
        }
    }
    
    // ---------- MODUL KONTRAKTI ----------
    if (!interface_exists('ModulInterface')) {
        interface ModulInterface {
            public function izvedi(array $zahteva): array;
            public function razglas(): array;
            public function ime(): string;
            public function različica(): string;
            public function kategorija(): string;
            public function jeAktiviran(): bool;
        }
    }
    
    if (!interface_exists('ModulPeskovnik')) {
        interface ModulPeskovnik {
            public function nalozi(string $ime): ModulInterface;
            public function aktiviraj(string $ime): bool;
            public function deaktiviraj(string $ime): bool;
            public function vsiModuli(): array;
            public function obstaja(string $ime): bool;
        }
    }
    
    // ---------- CACHE KONTRAKT ----------
    if (!interface_exists('Predpomnilnik')) {
        interface Predpomnilnik {
            public function shrani(string $ključ, $vrednost, int $čas_življenja = 3600): bool;
            public function preberi(string $ključ);
            public function zbrisi(string $ključ): bool;
            public function počisti(): void;
            public function obstaja(string $ključ): bool;
        }
    }
    
    // ---------- QUEUE KONTRAKT ----------
    if (!interface_exists('CakalnaVrsta')) {
        interface CakalnaVrsta {
            public function dodaj(array $paket, string $vrsta = 'obicajna_prednost'): bool;
            public function vzemi(string $vrsta = 'obicajna_prednost'): ?array;
            public function stevilo(string $vrsta = 'obicajna_prednost'): int;
            public function potrdi(string $vrsta, string $id): bool;
            public function vseVrste(): array;
        }
    }
    
    // ---------- DNEVNIK KONTRAKT ----------
    if (!interface_exists('DnevnikInterface')) {
        interface DnevnikInterface {
            public function zapisi(string $sporocilo, string $nivo = 'INFO', array $kontekst = []): void;
            public function info(string $sporocilo, array $kontekst = []): void;
            public function opozorilo(string $sporocilo, array $kontekst = []): void;
            public function napaka(string $sporocilo, array $kontekst = []): void;
            public function debug(string $sporocilo, array $kontekst = []): void;
        }
    }
    
    // ---------- VALIDACIJA KONTRAKT ----------
    if (!interface_exists('ValidatorInterface')) {
        interface ValidatorInterface {
            public function validiraj(array $podatki, array $pravila): array;
            public function napake(): array;
            public function jeVeljaven(): bool;
        }
    }
    
    // ---------- ZAGANJALNIK KONTRAKT ----------
    if (!interface_exists('ZaganjalnikKontrakt')) {
        interface ZaganjalnikKontrakt {
            public function zagon(): void;
            public function faze(): array;
            public function pridobiZaznamke(): array;
        }
    }
}

// ============================================================
// 4. DODATNI POMOŽNIKI ZA KONTRRKTE (če niso definirani)
// ============================================================
if (!function_exists('preveri_kontrakt')) {
    /**
     * Preveri ali kontrakt obstaja
     */
    function preveri_kontrakt(string $imeKontrakta): bool
    {
        return interface_exists($imeKontrakta) || class_exists($imeKontrakta);
    }
}

if (!function_exists('nalozi_kontrakt')) {
    /**
     * Naloži kontrakt, če obstaja
     */
    function nalozi_kontrakt(string $potKontrakta): bool
    {
        if (file_exists($potKontrakta)) {
            require_once $potKontrakta;
            return true;
        }
        return false;
    }
}