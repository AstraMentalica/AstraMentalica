<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/postavitev/osnova/stran_glava.php
 * v118 (30.6.2026)
 * ---------------------------------------------------------
 * OPIS: Naslovna pasica strani (page header / breadcrumb),
 *       uporabljena izključno iz GLOBALNO/render/render.php.
 *
 *       POZOR: To NI HTML <head>! Doctype, meta, <head> in
 *       odpiranje <body> že izvede globalno_html_glava() v
 *       render.php, preden se ta datoteka vključi. Namerno
 *       ima drugo ime kot GLOBALNO/postavitev/osnova/glava.php
 *       (tisto datoteko uporabljajo naprednLayouti/*.php in
 *       sama izriše cel samostojen HTML dokument).
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - render.php (lokalna spremenljivka $naslov)
 *
 * PREPOVEDI:
 * - Brez <!DOCTYPE>, <html>, <head>, <body>
 * - Brez business logike
 *
 * STATUS: Stabilno
 *
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

declare(strict_types=1);

$_glavaNaslov = $naslov ?? '';
?>

<?php if ($_glavaNaslov !== ''): ?>
<header class="stran-glava">
    <div class="stran-glava-vsebina">
        <h1 class="stran-naslov"><?= htmlspecialchars($_glavaNaslov) ?></h1>
    </div>
</header>

<style>
.stran-glava {
    padding: 1.5rem 2rem 0.5rem;
    max-width: 1200px;
    margin: 0 auto;
}

.stran-naslov {
    font-size: 1.6rem;
    color: #e8c84a;
    margin: 0;
}
</style>
<?php endif; ?>
