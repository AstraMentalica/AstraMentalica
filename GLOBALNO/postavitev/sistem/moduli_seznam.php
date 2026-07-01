<?php
/**
 * ============================================================
 * POT: GLOBALNO/postavitev/sistem/moduli_seznam.php
 * 📅 VERZIJA: v1.0 (24.6.2026)
 * ============================================================
 *
 * 📰 NAMEN:
 *     Prikaže seznam vseh modulov z uporabo zemljevih
 *     (svetovnega zemljevida) za navigacijo.
 *
 * 📡 ODVISNOSTI:
 *     - GLOBALNO/vmesnik/elementi/zemljevi.php
 *     - GLOBALNO/vmesnik/css/zemljevi.css
 *     - GLOBALNO/vmesnik/js/zemljevi.js
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *
 * 📌 STATUS:
 *     Stabilno
 * ============================================================
 */

declare(strict_types=1);

// Pridobi vse module
$moduli = zemljevi_pridobi_module();

// Pridobi trenutno aktivni modul iz URL-ja
$aktivniModul = $_GET['modul'] ?? '';
$aktivnaPot = $_GET['pot'] ?? '';

// Za auth strani uporabi 'pot' parameter
if (in_array($aktivnaPot, ['prijava', 'registracija', 'profil', 'nastavitve'])) {
    $aktivniModul = $aktivnaPot;
}

// Naslov strani
$naslov = '🌍 Svetovni zemljevid – Moduli';
?>

<div class="moduli-seznam">
    <h1 class="moduli-naslov"><?= htmlspecialchars($naslov) ?></h1>
    <p class="moduli-podnaslov">
        Raziskujte <?= count($moduli) ?> modulov na kozmičnem zemljevidu
    </p>

    <?php if (empty($moduli)): ?>
        <div class="kartica">
            <h2 class="kartica-naslov">⚠️ Ni modulov</h2>
            <p>Ni najdenih modulov v mapi MODULI/.</p>
            <p class="besedilo-prigl">
                Preverite, ali ima vsak modul datoteko <code>modul.php</code> ali <code>manifest.json</code>.
            </p>
        </div>
    <?php else: ?>
        <!-- Zemljevi (svetovni zemljevid) -->
        <?php zemljevi_prikazi($moduli, $aktivniModul, '?svet=MODULI&modul='); ?>
    <?php endif; ?>
</div>

<style>
.moduli-seznam {
    max-width: 1400px;
    margin: 0 auto;
}

.moduli-naslov {
    font-size: var(--velikost-3xl);
    color: var(--besedilo-s);
    text-align: center;
    margin-bottom: var(--razmik-s);
    font-weight: var(--teza-mocna);
}

.moduli-podnaslov {
    font-size: var(--velikost-l);
    color: var(--besedilo-d);
    text-align: center;
    margin-bottom: var(--razmik-2xl);
}

/* Dodatni stili za seznam modulov */
.moduli-seznam .zemljevi-okvir {
    padding-top: 0;
}

.moduli-seznam .zemljevi-naslov {
    display: none; /* Skrij naslov, ker že imamo glavnega */
}

.moduli-seznam .zemljevi-podnaslov {
    display: none; /* Skrij podnaslov, ker že imamo glavnega */
}
</style>