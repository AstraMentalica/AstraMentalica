<?php
/**
 * ============================================================
 * POT: MODULI/Modul_Bridge/embed/mini_izhod.php
 * 📅 VERZIJA: v114 (18.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: BRIDGE / EMBED
 *
 * 📰 NAMEN:
 *     HTML render za Bridge (glava, noga, navigacija).
 *     Edina datoteka v Bridge-u, ki sme producirati HTML/echo.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - mini_izhod_glava(string $naslov): void
 *     - mini_izhod_noga(): void
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     bridge, embed, izhod, html
 * ============================================================
 */

declare(strict_types=1);

function mini_izhod_glava(string $naslov = 'Modul Bridge'): void {
    $uporabnik   = mini_pridobi_uporabnika();
    $vloga_ime   = mini_vloga_v_ime($uporabnik['vloga']);
    $verzija     = MINI_BRIDGE_VERZIJA;
    $naslov_esc  = htmlspecialchars($naslov, ENT_QUOTES, 'UTF-8');
    $vloga_esc   = htmlspecialchars($vloga_ime, ENT_QUOTES, 'UTF-8');
    ?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $naslov_esc ?> | Modul Bridge</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --zlata:    #e8c84a;
            --svetla:   #d4c5a9;
            --temna:    #0a0a1a;
            --ploskev:  rgba(255,255,255,0.05);
            --rob:      rgba(255,255,255,0.08);
            --zelena:   #4caf50;
            --rdeca:    #f44336;
            --modra:    #818cf8;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: var(--temna);
            color: var(--svetla);
            min-height: 100vh;
        }

        /* ── NAVIGACIJA ─────────────────────────────────────── */
        .bridge-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 2rem;
            background: rgba(0,0,0,0.4);
            border-bottom: 1px solid var(--rob);
            flex-wrap: wrap;
        }

        .bridge-logo {
            font-weight: 700;
            color: var(--zlata);
            letter-spacing: 0.05em;
            margin-right: auto;
        }

        .bridge-logo small {
            font-size: 0.7em;
            opacity: 0.6;
            font-weight: 400;
        }

        .nav-gumb {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 25px;
            background: var(--ploskev);
            color: var(--svetla);
            text-decoration: none;
            font-size: 0.875rem;
            border: 1px solid var(--rob);
            transition: background 0.2s, color 0.2s;
        }

        .nav-gumb:hover,
        .nav-gumb.aktiven {
            background: var(--zlata);
            color: var(--temna);
            border-color: var(--zlata);
        }

        .nav-vloga {
            font-size: 0.8rem;
            opacity: 0.6;
            padding: 0.4rem 0.8rem;
            background: var(--ploskev);
            border-radius: 20px;
            border: 1px solid var(--rob);
        }

        /* ── VSEBINA ────────────────────────────────────────── */
        .bridge-vsebina {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .naslov-strani {
            color: var(--zlata);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* ── KARTICE ────────────────────────────────────────── */
        .kartica {
            background: var(--ploskev);
            border: 1px solid var(--rob);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .kartica h2 {
            color: var(--zlata);
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        /* ── GUMBI ──────────────────────────────────────────── */
        .gumb {
            display: inline-block;
            background: var(--zlata);
            color: var(--temna);
            padding: 0.5rem 1.2rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .gumb:hover { opacity: 0.85; }

        .gumb-sekundarni {
            background: var(--ploskev);
            color: var(--svetla);
            border: 1px solid var(--rob);
        }

        .gumb-nevarno {
            background: var(--rdeca);
            color: #fff;
        }

        /* ── TABELE ─────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        th {
            text-align: left;
            color: var(--zlata);
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid var(--rob);
        }

        td {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid rgba(255,255,255,0.03);
        }

        tr:hover td { background: rgba(255,255,255,0.03); }

        /* ── OBRAZCI ────────────────────────────────────────── */
        .polje {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            margin-bottom: 1rem;
        }

        label { font-size: 0.85rem; color: var(--zlata); }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            background: rgba(255,255,255,0.07);
            border: 1px solid var(--rob);
            border-radius: 8px;
            color: var(--svetla);
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-family: inherit;
            width: 100%;
        }

        textarea { min-height: 80px; resize: vertical; }

        /* ── STATUSNE OZNAKE ────────────────────────────────── */
        .oznaka {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .oznaka-uspeh   { background: rgba(76,175,80,0.2); color: var(--zelena); }
        .oznaka-napaka  { background: rgba(244,67,54,0.2); color: var(--rdeca); }
        .oznaka-info    { background: rgba(129,140,248,0.2); color: var(--modra); }
        .oznaka-zlata   { background: rgba(232,200,74,0.2); color: var(--zlata); }

        /* ── SPOROČILO ──────────────────────────────────────── */
        .sporocilo {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .sporocilo-uspeh  { background: rgba(76,175,80,0.15); border-left: 4px solid var(--zelena); }
        .sporocilo-napaka { background: rgba(244,67,54,0.15); border-left: 4px solid var(--rdeca); }
    </style>
</head>
<body>

<nav class="bridge-nav">
    <div class="bridge-logo">
        🔧 Modul Bridge
        <small><?= $verzija ?></small>
    </div>

    <?php
    $akcija_trenutna = $_GET['akcija'] ?? 'pregled';
    $nav_postavke = [
        'pregled'  => '📦 Pregled',
        'testnik'  => '🧪 Testnik',
        'generiraj'=> '🏭 Generator',
        'pakiraj'  => '📦 Pakiranje',
    ];
    foreach ($nav_postavke as $akcija_nav => $oznaka_nav):
    ?>
        <a href="?akcija=<?= $akcija_nav ?>"
           class="nav-gumb <?= $akcija_nav === $akcija_trenutna ? 'aktiven' : '' ?>">
            <?= $oznaka_nav ?>
        </a>
    <?php endforeach; ?>

    <span class="nav-vloga">
        👤 <?= $vloga_esc ?> (<?= (int)($uporabnik['vloga'] ?? 0) ?>)
    </span>
</nav>

<div class="bridge-vsebina">
<h1 class="naslov-strani"><?= $naslov_esc ?></h1>
    <?php
}

function mini_izhod_noga(): void {
    ?>
</div><!-- .bridge-vsebina -->
</body>
</html>
    <?php
}
