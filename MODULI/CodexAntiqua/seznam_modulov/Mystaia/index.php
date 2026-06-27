<?php
/**
 * Mystaia - Glavna stran trgovine
 * Eterična trgovina z energijskimi produkti
 */

if (!defined('ASTRA_PORTAL')) {
    define('ASTRA_PORTAL', true);
}

// Naloži konfiguracijo modula
require_once __DIR__ . '/config.php';

// Absolutna pot do Mystaia modula, da vedno deluje CSS/JS
if (!defined('MYSTAIA_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    define('MYSTAIA_URL', $protocol . '://' . $host . $basePath);
}

// Jezik
$jezik = $_SESSION['jezik'] ?? 'sl_SI';
$jezikovni_nizi = mystaia_nalozi_jezik($jezik);

// Izdelki
$izdelki = mystaia_pridobi_izdelke();
$izbrani_izdelki = array_slice($izdelki, 0, 6);
?>
<!DOCTYPE html>
<html lang="<?= $jezik ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $jezikovni_nizi['naslov_strani'] ?> - Mystaia</title>
    <link rel="stylesheet" href="<?= MYSTAIA_URL ?>/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Quicksand:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div id="mystaia-trgovina">
    <header class="module-header">
        <h1><?= $jezikovni_nizi['dobrodosli'] ?></h1>
        <nav class="module-nav">
            <a href="<?= MYSTAIA_URL ?>/index.php"><?= $jezikovni_nizi['domov'] ?></a>
            <a href="<?= MYSTAIA_URL ?>/izdelki.php"><?= $jezikovni_nizi['izdelki'] ?></a>
            <a href="<?= MYSTAIA_URL ?>/kategorije.php"><?= $jezikovni_nizi['kategorije'] ?></a>
            <a href="<?= MYSTAIA_URL ?>/kosarica.php"><?= $jezikovni_nizi['kosarica'] ?></a>
            <a href="<?= MYSTAIA_URL ?>/moja_narocila.php"><?= $jezikovni_nizi['moja_narocila'] ?></a>
        </nav>
    </header>

    <main class="module-content">
        <section class="hero">
            <h2>Odkrijte svet eteričnih izdelkov</h2>
            <p>Ponujamo izbrane kristale, sveče, nakit in druge duhovne pripomočke za vašo pot.</p>
        </section>

        <section class="kategorije">
            <h2>Raziščite naše kategorije</h2>
            <div class="kategorije-grid">
                <a href="<?= MYSTAIA_URL ?>/kategorije.php?kat=kristali" class="kategorija-kartica">
                    <div class="kategorija-ikona"><i class="fas fa-gem"></i></div>
                    <h3><?= $jezikovni_nizi['kategorija_kristali'] ?></h3>
                </a>
                <a href="<?= MYSTAIA_URL ?>/kategorije.php?kat=svece" class="kategorija-kartica">
                    <div class="kategorija-ikona"><i class="fas fa-fire"></i></div>
                    <h3><?= $jezikovni_nizi['kategorija_svece'] ?></h3>
                </a>
                <a href="<?= MYSTAIA_URL ?>/kategorije.php?kat=nakit" class="kategorija-kartica">
                    <div class="kategorija-ikona"><i class="fas fa-ring"></i></div>
                    <h3><?= $jezikovni_nizi['kategorija_nakit'] ?></h3>
                </a>
                <a href="<?= MYSTAIA_URL ?>/kategorije.php?kat=knjige" class="kategorija-kartica">
                    <div class="kategorija-ikona"><i class="fas fa-book"></i></div>
                    <h3><?= $jezikovni_nizi['kategorija_knjige'] ?></h3>
                </a>
                <a href="<?= MYSTAIA_URL ?>/kategorije.php?kat=aromaterapija" class="kategorija-kartica">
                    <div class="kategorija-ikona"><i class="fas fa-spa"></i></div>
                    <h3><?= $jezikovni_nizi['kategorija_aromaterapija'] ?></h3>
                </a>
            </div>
        </section>

        <section class="izdelki">
            <h2>Izbrani izdelki</h2>
            <div class="izdelki-grid">
                <?php foreach ($izbrani_izdelki as $izdelek): ?>
                    <div class="izdelek-kartica">
                        <div class="izdelek-slika">
                            <img src="<?= $izdelek['slika'] ?>" alt="<?= $izdelek['naziv'] ?>">
                        </div>
                        <div class="izdelek-podatki">
                            <h3><?= $izdelek['naziv'] ?></h3>
                            <p class="izdelek-opis"><?= $izdelek['kratek_opis'] ?></p>
                            <div class="izdelek-cena"><?= number_format($izdelek['cena'], 2, ',', '.') ?> €</div>
                            <div class="izdelek-akcije">
                                <a href="<?= MYSTAIA_URL ?>/izdelek.php?id=<?= $izdelek['id'] ?>" class="btn btn-secondary">Podrobnosti</a>
                                <button class="btn btn-primary dodaj-kosarico" data-id="<?= $izdelek['id'] ?>">Dodaj v košarico</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="izdelki-povezava">
                <a href="<?= MYSTAIA_URL ?>/izdelki.php" class="btn btn-primary">Oglej si vse izdelke</a>
            </div>
        </section>
    </main>

    <footer class="module-footer">
        <p>&copy; 2025 Mystaia. Vse pravice pridržane.</p>
    </footer>
</div>

<script src="<?= MYSTAIA_URL ?>/script.js"></script>
</body>
</html>
