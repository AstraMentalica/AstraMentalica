<?php
/**
 * ============================================================
 *  POT: UPORABNIKI/prikaz/sistem/uporabnik_zgodovina.php
 *  
 *  v112
 * ============================================================
 * 
 * 📦 NAMEN: Zgodovina aktivnosti uporabnika
 * 
 * 🔧 FUNKCIJE:
 *     - Seznam aktivnosti (prijava, odjava, dnevnik, sanje, meditacije...)
 *     - Filter po tipu aktivnosti
 *     - Filter po datumu (danes, zadnjih 7/30 dni, ta mesec)
 *     - Paginacija
 *     - Podrobnosti aktivnosti (details)
 * 
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - $vsebina['uporabnik'], $vsebina['aktivnosti']
 *     - $vsebina['filter_tip'], $vsebina['filter_datum'], $vsebina['stran']
 * 
 * ⚠️ UPORABA:
 *     Ko uporabnik želi videti svojo zgodovino
 * 
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez direktnega branja PODATKI/
 *     - Brez require_once MODULI/
 *     - Brez require_once UPORABNIKI/
 * 
 * 
 */

declare(strict_types=1);

$uporabnik = $vsebina['uporabnik'] ?? null;
$aktivnosti = $vsebina['aktivnosti'] ?? [];
$filterTip = $vsebina['filter_tip'] ?? 'vse';
$filterDatum = $vsebina['filter_datum'] ?? 'vse';
$stran = $vsebina['stran'] ?? 1;
$naStran = $vsebina['na_stran'] ?? 20;

if (!$uporabnik) {
    header('Location: ?svet=UPORABNIKI&pot=prijava');
    exit;
}

$skupaj = count($aktivnosti);
$skupajStrani = ceil($skupaj / $naStran);
$offset = ($stran - 1) * $naStran;
$prikazaneAktivnosti = array_slice($aktivnosti, $offset, $naStran);

$tipi = [
    'prijava' => ['ikona' => '🔑', 'ime' => 'Prijava'],
    'odjava' => ['ikona' => '🚪', 'ime' => 'Odjava'],
    'dnevnik' => ['ikona' => '📝', 'ime' => 'Dnevnik'],
    'sanje' => ['ikona' => '🌙', 'ime' => 'Sanje'],
    'meditacija' => ['ikona' => '🧘', 'ime' => 'Meditacija'],
    'modul' => ['ikona' => '📦', 'ime' => 'Modul'],
    'nastavitev' => ['ikona' => '⚙️', 'ime' => 'Nastavitve'],
    'profil' => ['ikona' => '👤', 'ime' => 'Profil']
];
?>

<div class="zgodovina-stran">
<div class="zgodovina-vsebina">
    <div class="zgodovina-glava">
        <h1 class="zgodovina-naslov">📜 Zgodovina aktivnosti</h1>
        <p class="zgodovina-podnaslov">Pregled vaših aktivnosti v sistemu</p>
    </div>
    
    <div class="zgodovina-filtri">
        <div class="filter">
            <select id="filterTip" class="filter-select">
                <option value="vse" <?= $filterTip === 'vse' ? 'selected' : '' ?>>Vsi tipi</option>
                <?php foreach ($tipi as $kljuc => $tip): ?>
                    <option value="<?= $kljuc ?>" <?= $filterTip === $kljuc ? 'selected' : '' ?>>
                        <?= $tip['ikona'] ?> <?= $tip['ime'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter">
            <select id="filterDatum" class="filter-select">
                <option value="vse" <?= $filterDatum === 'vse' ? 'selected' : '' ?>>Vse obdobje</option>
                <option value="danes" <?= $filterDatum === 'danes' ? 'selected' : '' ?>>Danes</option>
                <option value="zadnjih_7" <?= $filterDatum === 'zadnjih_7' ? 'selected' : '' ?>>Zadnjih 7 dni</option>
                <option value="zadnjih_30" <?= $filterDatum === 'zadnjih_30' ? 'selected' : '' ?>>Zadnjih 30 dni</option>
                <option value="ta_mesec" <?= $filterDatum === 'ta_mesec' ? 'selected' : '' ?>>Ta mesec</option>
            </select>
        </div>
    </div>
    
    <div class="zgodovina-seznam">
        <?php if (empty($prikazaneAktivnosti)): ?>
            <div class="zgodovina-prazno">
                <div class="prazno-ikona">📭</div>
                <p>Ni aktivnosti za prikaz.</p>
            </div>
        <?php else: ?>
            <?php foreach ($prikazaneAktivnosti as $aktivnost): 
                $tipInfo = $tipi[$aktivnost['tip']] ?? ['ikona' => '📌', 'ime' => 'Aktivnost'];
            ?>
                <div class="zgodovina-aktivnost" data-tip="<?= $aktivnost['tip'] ?>">
                    <div class="aktivnost-ikona"><?= $tipInfo['ikona'] ?></div>
                    <div class="aktivnost-podatki">
                        <div class="aktivnost-tip"><?= $tipInfo['ime'] ?></div>
                        <div class="aktivnost-opis"><?= htmlspecialchars($aktivnost['opis'] ?? '') ?></div>
                        <div class="aktivnost-datum"><?= date('d.m.Y H:i:s', $aktivnost['cas'] ?? time()) ?></div>
                    </div>
                    <?php if (isset($aktivnost['podrobnosti']) && !empty($aktivnost['podrobnosti'])): ?>
                        <details class="aktivnost-podrobnosti">
                            <summary>Podrobnosti</summary>
                            <pre><?= htmlspecialchars(json_encode($aktivnost['podrobnosti'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                        </details>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <?php if ($skupajStrani > 1): ?>
                <div class="paginacija">
                    <?php for ($i = 1; $i <= $skupajStrani; $i++): ?>
                        <a href="?svet=UPORABNIKI&pot=zgodovina&stran=<?= $i ?>" 
                           class="paginacija-gumb <?= $i == $stran ? 'aktivno' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
</div>

<script nonce="<?= $vsebina['csp_nonce'] ?? '' ?>">
document.getElementById('filterTip')?.addEventListener('change', (e) => {
    window.location.href = `?svet=UPORABNIKI&pot=zgodovina&filter_tip=${e.target.value}&filter_datum=${document.getElementById('filterDatum').value}`;
});
document.getElementById('filterDatum')?.addEventListener('change', (e) => {
    window.location.href = `?svet=UPORABNIKI&pot=zgodovina&filter_tip=${document.getElementById('filterTip').value}&filter_datum=${e.target.value}`;
});
</script>

<style>
.zgodovina-stran { max-width: 800px; margin: 0 auto; padding: 2rem; }
.zgodovina-glava { text-align: center; margin-bottom: 2rem; }
.zgodovina-naslov { color: #e8c84a; font-size: 1.8rem; }
.zgodovina-podnaslov { color: #aaa; }
.zgodovina-filtri { display: flex; gap: 1rem; margin-bottom: 1.5rem; justify-content: flex-end; }
.filter-select { padding: 0.5rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid #2a2a4a; border-radius: 8px; color: #d4c5a9; }
.zgodovina-aktivnost { display: flex; gap: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 12px; margin-bottom: 0.5rem; transition: background 0.3s; }
.zgodovina-aktivnost:hover { background: rgba(255, 255, 255, 0.05); }
.aktivnost-ikona { font-size: 1.5rem; }
.aktivnost-podatki { flex: 1; }
.aktivnost-tip { font-weight: bold; color: #e8c84a; margin-bottom: 0.25rem; }
.aktivnost-opis { color: #d4c5a9; font-size: 0.9rem; margin-bottom: 0.25rem; }
.aktivnost-datum { color: #888; font-size: 0.75rem; }
.aktivnost-podrobnosti { margin-top: 0.5rem; }
.aktivnost-podrobnosti summary { cursor: pointer; color: #888; font-size: 0.8rem; }
.aktivnost-podrobnosti pre { margin-top: 0.5rem; padding: 0.5rem; background: #0a0a1a; border-radius: 5px; font-size: 0.7rem; overflow-x: auto; }
.zgodovina-prazno { text-align: center; padding: 3rem; color: #888; }
.prazno-ikona { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }
.paginacija { display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem; }
.paginacija-gumb { display: inline-block; padding: 0.3rem 0.8rem; background: rgba(255, 255, 255, 0.05); border-radius: 5px; color: #d4c5a9; text-decoration: none; }
.paginacija-gumb.aktivno { background: #e8c84a; color: #0a0a1a; }
@media (max-width: 768px) { .zgodovina-filtri { flex-direction: column; } }
</style>