<?php
/**
 * ============================================================
 * POT: AI/varnost.php
 * 📅 VERZIJA: v2.1 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: AI INFRASTRUKTURA
 *
 * 📰 NAMEN:
 *     Varnostni sistem za AI delavce (Arhitekt, Koder, Integrator, Revizor).
 *     Določa kaj smejo agenti brati, pisati in do česar nimajo dostopa.
 *
 *     LOGIKA DOSTOPA:
 *     - WHITELIST pisanja: agenti pišejo SAMO v dovoljene mape
 *     - POSEBNA ZAŠČITA: pot.php je dovoljeno pisati, a se zabeleži v opozorilo
 *     - READ-ONLY: PODATKI/ in VSEBINA/ — smejo brati, ne pisati
 *     - BLOCKED po defaultu: vse kar ni na whitelistu je nedosegljivo
 *
 * 📡 ODVISNOSTI:
 *     - Nič (ta datoteka je prva, se ne zanašá na nič drugega)
 *
 * 🚫 PREPOVEDI:
 *     - Brez die(), exit()
 *     - Brez direktnih poti
 *
 * 📌 STATUS:
 *     Aktivno
 *
 * 👤 AVTOR:
 *     AI / Claude
 *
 * 🌐 JEZIK:
 *     sl
 * ============================================================
 */

declare(strict_types=1);

// ============================================================
// ROOT — absolutno sidro
// varnost.php je v AI/ → ROOT je ena mapa gor
// ============================================================

$ROOT = realpath(__DIR__ . "/..");

if ($ROOT === false) {
    throw new Exception("Kritična napaka: ROOT ni določljiv.");
}

// ============================================================
// DOVOLJENO PISANJE (whitelist)
// Agenti smejo ustvarjati in spreminjati SAMO te mape.
// ============================================================

$PISANJE_DOVOLJENO = [
    "ADAPTER/",
    "SISTEM/",
    "GLOBALNO/",
    "MODULI/",
    "UPORABNIKI/",
    "AI/sistemskiAI/naloge/",      // poročila, backupi, patchi agentov
];

// ============================================================
// SAMO BRANJE
// Agenti smejo brati, pisanje je blokirano.
// ============================================================

$SAMO_BRANJE = [
    "PODATKI/",
    "VSEBINA/",
    "AI/sistemskiAI/pravila/",
    "AI/sistemskiAI/agentni_manifesti/",
];

// ============================================================
// POSEBNA ZAŠČITA — datoteke ki zahtevajo opozorilo v logu
// Pisanje je tehnično dovoljeno (projekt se gradi),
// ampak vsaka sprememba se zabeleži kot OPOZORILO.
// ============================================================

$POSEBNA_ZASCITA = [
    "pot.php",              // absolutno sidro — USTAVA §1.0
    "index.php",            // edina javna vstopna točka — USTAVA §1
    "SISTEM/api.php",       // edini vstop v sistem — USTAVA §1.1
];

// ============================================================
// POMOŽNE FUNKCIJE
// ============================================================

/**
 * Odstrani poskuse path traversala.
 */
function normaliziraj(string $pot): string {
    return str_replace(["..\\", "../", "./"], "", $pot);
}

/**
 * Preveri ali relativna pot začne z določenim direktorijem.
 */
function jeVMapi(string $relPot, string $mapa): bool {
    return strpos($relPot, $mapa) === 0;
}

/**
 * Razreši pot in preveri da je znotraj ROOT-a.
 * Vrne [absolutna_pot, relativna_pot].
 *
 * @throws Exception če je pot izven ROOT-a ali neveljavna
 */
function varnoRazresi(string $pot, string $ROOT): array {
    $pot = normaliziraj($pot);

    $polna = realpath($ROOT . "/" . $pot);

    if ($polna === false) {
        // Datoteka še ne obstaja — preveri vsaj mapo
        $mapa = dirname($ROOT . "/" . $pot);
        $realnaMapa = realpath($mapa);

        if ($realnaMapa === false) {
            throw new Exception("Neveljavna pot (mapa ne obstaja): $pot");
        }

        if (strpos($realnaMapa, $ROOT) !== 0) {
            throw new Exception("VARNOSTNA KRŠITEV: pot izven ROOT-a: $pot");
        }

        $polna = $realnaMapa . "/" . basename($pot);
    }

    if (strpos($polna, $ROOT) !== 0) {
        throw new Exception("VARNOSTNA KRŠITEV: pot izven ROOT-a: $pot");
    }

    $relativna = ltrim(str_replace($ROOT, "", $polna), "/");

    return [$polna, $relativna];
}

/**
 * Zabeleži opozorilo za posebej zaščitene datoteke.
 */
function zabeleziOpozorilo(string $relativna, string $ROOT): void {
    $dnevnikDir = $ROOT . "/AI/sistemskiAI/naloge/dnevnik/";
    if (!is_dir($dnevnikDir)) {
        mkdir($dnevnikDir, 0755, true);
    }

    $vrstica = "[" . date("Y-m-d H:i:s") . "] [OPOZORILO] Sprememba posebej zaščitene datoteke: $relativna\n";
    file_put_contents($dnevnikDir . "opozorila.log", $vrstica, FILE_APPEND);
}

/**
 * Uveljavi dovoljenje za PISANJE.
 * Vrne absolutno pot če je pisanje dovoljeno.
 *
 * @throws Exception če pisanje ni dovoljeno
 */
function uveljavljPisanje(string $pot, string $ROOT): string {
    global $PISANJE_DOVOLJENO, $SAMO_BRANJE, $POSEBNA_ZASCITA;

    [$polna, $relativna] = varnoRazresi($pot, $ROOT);

    // Posebej zaščitene datoteke — dovoljeno ampak z opozorilom
    foreach ($POSEBNA_ZASCITA as $zascitena) {
        if ($relativna === $zascitena || basename($relativna) === basename($zascitena)) {
            zabeleziOpozorilo($relativna, $ROOT);
            return $polna; // Nadaljuj z pisanjem
        }
    }

    // Samo branje — pisanje blokirano
    foreach ($SAMO_BRANJE as $r) {
        if (jeVMapi($relativna, $r)) {
            throw new Exception("SAMO BRANJE — pisanje ni dovoljeno: $relativna");
        }
    }

    // Whitelist pisanja
    foreach ($PISANJE_DOVOLJENO as $w) {
        if (jeVMapi($relativna, $w)) {
            return $polna;
        }
    }

    // Vse ostalo je blokirano
    throw new Exception("DOSTOP ZAVRNJEN — pot ni na whitelistu: $relativna");
}

/**
 * Uveljavi dovoljenje za BRANJE.
 * Branje je dovoljeno za vse whitelisted in read-only mape.
 *
 * @throws Exception če branje ni dovoljeno
 */
function uveljavljBranje(string $pot, string $ROOT): string {
    global $PISANJE_DOVOLJENO, $SAMO_BRANJE, $POSEBNA_ZASCITA;

    [$polna, $relativna] = varnoRazresi($pot, $ROOT);

    // Posebej zaščitene datoteke — branje vedno dovoljeno
    foreach ($POSEBNA_ZASCITA as $zascitena) {
        if ($relativna === $zascitena) {
            return $polna;
        }
    }

    // Read-only — branje dovoljeno
    foreach ($SAMO_BRANJE as $r) {
        if (jeVMapi($relativna, $r)) {
            return $polna;
        }
    }

    // Whitelist pisanja — branje je seveda tudi dovoljeno
    foreach ($PISANJE_DOVOLJENO as $w) {
        if (jeVMapi($relativna, $w)) {
            return $polna;
        }
    }

    // Vse ostalo je blokirano (druge mape na strežniku)
    throw new Exception("DOSTOP ZAVRNJEN — pot ni dosegljiva agentu: $relativna");
}

// ============================================================
// JAVNE FUNKCIJE ZA AGENTE
// ============================================================

/**
 * Prebere datoteko.
 *
 * @throws Exception če branje ni dovoljeno ali datoteka ne obstaja
 */
function preberiDatoteko(string $pot, string $ROOT): string {
    $polna = uveljavljBranje($pot, $ROOT);

    if (!file_exists($polna)) {
        throw new Exception("Datoteka ne obstaja: $pot");
    }

    return file_get_contents($polna);
}

/**
 * Zapiše datoteko.
 * Samodejno ustvari mapo če ne obstaja.
 *
 * @throws Exception če pisanje ni dovoljeno
 */
function zapisiDatoteko(string $pot, string $vsebina, string $ROOT): int|false {
    $polna = uveljavljPisanje($pot, $ROOT);

    $mapa = dirname($polna);
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }

    return file_put_contents($polna, $vsebina);
}

/**
 * Ustvari backup datoteke pred spremembo.
 * Backup gre vedno v AI/sistemskiAI/naloge/backup/.
 *
 * @throws Exception če datoteka ne obstaja ali branje ni dovoljeno
 */
function ustvariBackup(string $pot, string $ROOT): string {
    $polna = uveljavljBranje($pot, $ROOT);

    if (!file_exists($polna)) {
        throw new Exception("Backup ni možen — datoteka ne obstaja: $pot");
    }

    $backupMapa = $ROOT . "/AI/sistemskiAI/naloge/backup/";
    if (!is_dir($backupMapa)) {
        mkdir($backupMapa, 0755, true);
    }

    $backupDatoteka = $backupMapa . str_replace("/", "_", $pot) . ".bak_" . date("Y-m-d_H-i-s");
    copy($polna, $backupDatoteka);

    return $backupDatoteka;
}

/**
 * Ustvari unified diff med originalom in modificirano verzijo.
 * Uporablja se za beleženje sprememb v patch datotekah.
 */
function ustvariDiff(string $original, string $modificiran, string $oznaka): string {
    $vrsticeOrig = explode("\n", $original);
    $vrsticeMod  = explode("\n", $modificiran);

    $diff = [
        "--- {$oznaka}_original",
        "+++ {$oznaka}_modificiran",
    ];

    $i = 0;
    $j = 0;

    while ($i < count($vrsticeOrig) || $j < count($vrsticeMod)) {
        if (
            $i < count($vrsticeOrig) &&
            $j < count($vrsticeMod) &&
            $vrsticeOrig[$i] === $vrsticeMod[$j]
        ) {
            $diff[] = " " . $vrsticeOrig[$i];
            $i++;
            $j++;
        } else {
            $steviloOrig = count($vrsticeOrig) - $i;
            $steviloMod  = count($vrsticeMod)  - $j;
            $diff[] = "@@ -{$i},{$steviloOrig} +{$j},{$steviloMod} @@";

            while ($i < count($vrsticeOrig) && (
                $j >= count($vrsticeMod) ||
                $vrsticeOrig[$i] !== $vrsticeMod[$j]
            )) {
                $diff[] = "-" . $vrsticeOrig[$i];
                $i++;
            }

            while ($j < count($vrsticeMod) && (
                $i >= count($vrsticeOrig) ||
                $vrsticeOrig[$i] !== $vrsticeMod[$j]
            )) {
                $diff[] = "+" . $vrsticeMod[$j];
                $j++;
            }
        }
    }

    return implode("\n", $diff);
}
