<?php
/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/render/elementi/gumb.php
 * v111 (27.5.2026 15:00)
 * ---------------------------------------------------------
 * OPIS: Gumb element – pasivni PHP fragment
 * ---------------------------------------------------------
 *
 * ODVISNOSTI:
 * - nobenih
 *
 * UPORABA:
 * - GLOBALNO/render/strani/*.php
 *
 * PARAMETRI:
 * - $besedilo (string) – besedilo na gumbu
 * - $vrsta (string) – primaren, sekundaren, nevaren, majhen
 * - $pot (string) – kam vodi (ce ni podana, je gumb)
 * - $ikona (string) – emoji ikona pred besedilom
 *
 * PREPOVEDI:
 * - Brez business logike
 *
 * STATUS: Stabilno
 *
 * ZGODOVINA:
 * - v111: FAZA 20+ – implementacija
 *
 * ---------------------------------------------------------
 * AVTOR: AstraMentalica Mojster
 * ---------------------------------------------------------
 */

// Parametri
$besedilo = $besedilo ?? 'Gumb';
$vrsta = $vrsta ?? 'primaren';
$pot = $pot ?? null;
$ikona = $ikona ?? '';
$razred = $razred ?? '';
$atributi = $atributi ?? [];

$razredi = ['gumb', 'gumb-' . $vrsta];
if ($razred) {
    $razredi[] = $razred;
}

$atributiNiz = '';
foreach ($atributi as $kljuc => $vrednost) {
    $atributiNiz .= ' ' . htmlspecialchars($kljuc) . '="' . htmlspecialchars($vrednost) . '"';
}

$vsebinaGumba = '';
if ($ikona) {
    $vsebinaGumba .= '<span class="gumb-ikona">' . htmlspecialchars($ikona) . '</span>';
}
$vsebinaGumba .= '<span class="gumb-besedilo">' . htmlspecialchars($besedilo) . '</span>';

if ($pot !== null):
?>
<a href="<?= htmlspecialchars($pot) ?>" class="<?= implode(' ', $razredi) ?>"<?= $atributiNiz ?>>
    <?= $vsebinaGumba ?>
</a>
<?php else: ?>
<button class="<?= implode(' ', $razredi) ?>"<?= $atributiNiz ?>>
    <?= $vsebinaGumba ?>
</button>
<?php endif; ?>