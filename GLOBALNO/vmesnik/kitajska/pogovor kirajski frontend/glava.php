<?php
/**
 * ============================================================
 * POT: GLOBALNO/vmesnik/kitajska/pogovor kirajski frontend/glava.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (render)
 *
 * 📰 NAMEN:
 *     Zgornja glava – iskanje, obvestila, breadcrumb.
 *
 * ✅ DOVOLJENO:
 *     - echo, HTML
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez SQL klicev
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v114: implementacija
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     globalno, render, glava
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

$naslovStrani  = $naslovStrani ?? 'AstraMentalica';
$steviloObvestil = $steviloObvestil ?? 0;
?>
<header class="glava" role="banner">

    <!-- Breadcrumb / naslov -->
    <div class="glava-levo">
        <h1 class="glava-naslov"><?= htmlspecialchars($naslovStrani) ?></h1>
    </div>

    <!-- Desna stran -->
    <div class="glava-desno">

        <!-- Iskanje -->
        <div class="glava-iskanje">
            <span class="glava-iskanje-ikona">🔍</span>
            <input type="search"
                   class="glava-iskanje-vnos"
                   placeholder="Iskanje..."
                   id="globalnoIskanje"
                   aria-label="Globalno iskanje">
        </div>

        <!-- Obvestila -->
        <button class="glava-gumb" id="gumbObvestila" aria-label="Obvestila" title="Obvestila">
            🔔
            <?php if ($steviloObvestil > 0): ?>
                <span class="glava-obvestilo-znacka"><?= min($steviloObvestil, 99) ?></span>
            <?php endif; ?>
        </button>

    </div>

</header>

<style>
.glava {
    grid-area: glava;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 var(--razmik-xl);
    background: var(--povrsina);
    border-bottom: 1px solid var(--rob);
    height: var(--glava-visina);
    position: sticky;
    top: 0;
    z-index: 100;
    gap: var(--razmik-l);
}

.glava-levo { display: flex; align-items: center; gap: var(--razmik-m); }

.glava-naslov {
    font-family: var(--pisava-osnovna);
    font-size: var(--velikost-l);
    font-weight: var(--teza-krepka);
    color: var(--besedilo-s);
    white-space: nowrap;
}

.glava-desno {
    display: flex;
    align-items: center;
    gap: var(--razmik-m);
    flex: 1;
    justify-content: flex-end;
}

/* Iskanje */
.glava-iskanje {
    display: flex;
    align-items: center;
    background: var(--kartica);
    border: 1px solid var(--rob);
    border-radius: var(--rob-pill);
    padding: 0.35rem 0.9rem;
    gap: 0.5rem;
    max-width: 340px;
    width: 100%;
    transition: border-color var(--prehod);
}

.glava-iskanje:focus-within {
    border-color: var(--zlata);
    background: rgba(232,200,74,0.04);
}

.glava-iskanje-ikona { font-size: 0.85rem; flex-shrink: 0; }

.glava-iskanje-vnos {
    background: none;
    border: none;
    color: var(--besedilo);
    font-size: var(--velikost-s);
    outline: none;
    width: 100%;
}

.glava-iskanje-vnos::placeholder { color: var(--besedilo-m); }

/* Gumbi glave */
.glava-gumb {
    position: relative;
    background: var(--kartica);
    border: 1px solid var(--rob);
    border-radius: var(--rob-pill);
    color: var(--besedilo-d);
    cursor: pointer;
    padding: 0.4rem 0.75rem;
    font-size: 1rem;
    transition: all var(--prehod);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.glava-gumb:hover {
    border-color: var(--zlata);
    color: var(--zlata);
    background: var(--zlata-dim);
}

.glava-obvestilo-znacka {
    position: absolute;
    top: -4px;
    right: -4px;
    background: var(--rdeca);
    color: #fff;
    border-radius: var(--rob-krog);
    font-size: 0.6rem;
    font-weight: var(--teza-mocna);
    min-width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 3px;
}
</style>
