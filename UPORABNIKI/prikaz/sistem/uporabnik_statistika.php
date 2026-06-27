<?php
/**
 * ============================================================
 *  POT: UPORABNIKI/prikaz/sistem/uporabnik_statistika.php
 *  
 *  v112
 * ============================================================
 * 
 * 📦 NAMEN: Uporabniška statistika – pregled osebnih metrik
 * 
 * 🔧 FUNKCIJE:
 *     - Prikaz povzetka (dnevniki, sanje, meditacije, minute)
 *     - Mesečna aktivnost (graf)
 *     - Aktivnosti po tipu (tabela z odstotki)
 *     - Dosežki (odklenjeni/zaklenjeni)
 *     - Priporočila
 * 
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - $vsebina['uporabnik'], $vsebina['statistika']
 *     - $vsebina['mesečna_statistika'], $vsebina['aktivnosti_po_tipu']
 * 
 * ⚠️ UPORABA:
 *     Ko uporabnik želi videti svojo statistiko
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
$statistika = $vsebina['statistika'] ?? [];
$mesečnaStatistika = $vsebina['mesečna_statistika'] ?? [];
$aktivnostiPoTipu = $vsebina['aktivnosti_po_tipu'] ?? [];

if (!$uporabnik) {
    header('Location: ?svet=UPORABNIKI&pot=prijava');
    exit;
}

$maxVrednost = !empty($mesečnaStatistika) ? max(array_column($mesečnaStatistika, 'stevilo')) : 1;
?>

<div class="statistika-stran">
<div class="statistika-vsebina">
    <div class="statistika-glava">
        <h1 class="statistika-naslov">📊 Moja statistika</h1>
        <p class="statistika-podnaslov">Pregled vaših aktivnosti in napredka</p>
    </div>
    
    <div class="statistika-povzetek">
        <div class="povzetek-kartica">
            <div class="povzetek-stevilka"><?= $statistika['skupaj_dnevnikov'] ?? 0 ?></div>
            <div class="povzetek-oznaka">Dnevniki</div>
        </div>
        <div class="povzetek-kartica">
            <div class="povzetek-stevilka"><?= $statistika['skupaj_sanj'] ?? 0 ?></div>
            <div class="povzetek-oznaka">Sanje</div>
        </div>
        <div class="povzetek-kartica">
            <div class="povzetek-stevilka"><?= $statistika['skupaj_meditacij'] ?? 0 ?></div>
            <div class="povzetek-oznaka">Meditacije</div>
        </div>
        <div class="povzetek-kartica">
            <div class="povzetek-stevilka"><?= $statistika['skupaj_minut_meditacije'] ?? 0 ?></div>
            <div class="povzetek-oznaka">Minut meditacije</div>
        </div>
        <div class="povzetek-kartica">
            <div class="povzetek-stevilka"><?= $statistika['aktivnih_dni'] ?? 0 ?></div>
            <div class="povzetek-oznaka">Aktivnih dni</div>
        </div>
        <div class="povzetek-kartica">
            <div class="povzetek-stevilka"><?= $statistika['trenutni_niz'] ?? 0 ?></div>
            <div class="povzetek-oznaka">Trenutni niz (dni)</div>
        </div>
    </div>
    
    <div class="statistika-graf">
        <h2>📈 Mesečna aktivnost</h2>
        <div class="graf-stolpci">
            <?php foreach ($mesečnaStatistika as $mesec): 
                $visina = $mesec['stevilo'] > 0 ? ($mesec['stevilo'] / $maxVrednost) * 100 : 0;
            ?>
                <div class="graf-stolpec">
                    <div class="stolpec" style="height: <?= $visina ?>px;"></div>
                    <div class="stolpec-oznaka"><?= htmlspecialchars($mesec['mesec']) ?></div>
                    <div class="stolpec-vrednost"><?= $mesec['stevilo'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="statistika-tipi">
        <h2>📋 Aktivnosti po tipu</h2>
        <div class="tipi-seznam">
            <?php foreach ($aktivnostiPoTipu as $tip => $stevilo): 
                $ikone = [
                    'dnevnik' => '📝', 'sanje' => '🌙', 'meditacija' => '🧘',
                    'modul' => '📦', 'prijava' => '🔑', 'nastavitve' => '⚙️'
                ];
                $ikona = $ikone[$tip] ?? '📌';
                $odstotek = $statistika['skupaj_aktivnosti'] > 0 
                    ? round($stevilo / $statistika['skupaj_aktivnosti'] * 100, 1) : 0;
            ?>
                <div class="tip-vrstica">
                    <div class="tip-ikona"><?= $ikona ?></div>
                    <div class="tip-ime"><?= ucfirst($tip) ?></div>
                    <div class="tip-stevilo"><?= $stevilo ?></div>
                    <div class="tip-napredek">
                        <div class="tip-napredek-bar" style="width: <?= $odstotek ?>%"></div>
                    </div>
                    <div class="tip-odstotek"><?= $odstotek ?>%</div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="statistika-dosezki">
        <h2>🏆 Dosežki</h2>
        <div class="dosezki-seznam">
            <?php if (($statistika['skupaj_dnevnikov'] ?? 0) >= 10): ?>
                <div class="dosezek dosezek-odklenjen">
                    <div class="dosezek-ikona">📓</div>
                    <div class="dosezek-info">
                        <div class="dosezek-naslov">Pisatelj</div>
                        <div class="dosezek-opis">10+ dnevniških zapisov</div>
                    </div>
                </div>
            <?php else: ?>
                <div class="dosezek dosezek-zaklenjen">
                    <div class="dosezek-ikona">📓</div>
                    <div class="dosezek-info">
                        <div class="dosezek-naslov">Pisatelj</div>
                        <div class="dosezek-opis">Napišite še <?= 10 - ($statistika['skupaj_dnevnikov'] ?? 0) ?> dnevnikov</div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (($statistika['skupaj_minut_meditacije'] ?? 0) >= 100): ?>
                <div class="dosezek dosezek-odklenjen">
                    <div class="dosezek-ikona">🧘</div>
                    <div class="dosezek-info">
                        <div class="dosezek-naslov">Mojster meditacije</div>
                        <div class="dosezek-opis">100+ minut meditacije</div>
                    </div>
                </div>
            <?php else: ?>
                <div class="dosezek dosezek-zaklenjen">
                    <div class="dosezek-ikona">🧘</div>
                    <div class="dosezek-info">
                        <div class="dosezek-naslov">Mojster meditacije</div>
                        <div class="dosezek-opis">Meditirajte še <?= 100 - ($statistika['skupaj_minut_meditacije'] ?? 0) ?> minut</div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (($statistika['trenutni_niz'] ?? 0) >= 7): ?>
                <div class="dosezek dosezek-odklenjen">
                    <div class="dosezek-ikona">🔥</div>
                    <div class="dosezek-info">
                        <div class="dosezek-naslov">Vztrajnik</div>
                        <div class="dosezek-opis">7+ dni aktivnosti zapored</div>
                    </div>
                </div>
            <?php else: ?>
                <div class="dosezek dosezek-zaklenjen">
                    <div class="dosezek-ikona">🔥</div>
                    <div class="dosezek-info">
                        <div class="dosezek-naslov">Vztrajnik</div>
                        <div class="dosezek-opis">Aktivirajte se še <?= 7 - ($statistika['trenutni_niz'] ?? 0) ?> dni zapored</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($statistika['priporocila'])): ?>
        <div class="statistika-priporocila">
            <h2>💡 Priporočila</h2>
            <ul>
                <?php foreach ($statistika['priporocila'] as $priporocilo): ?>
                    <li><?= htmlspecialchars($priporocilo) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
</div>

<style>
.statistika-stran { max-width: 900px; margin: 0 auto; padding: 2rem; }
.statistika-glava { text-align: center; margin-bottom: 2rem; }
.statistika-naslov { color: #e8c84a; font-size: 1.8rem; }
.statistika-podnaslov { color: #aaa; }
.statistika-povzetek { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.povzetek-kartica { background: rgba(255, 255, 255, 0.03); border-radius: 15px; padding: 1rem; text-align: center; }
.povzetek-stevilka { font-size: 1.5rem; font-weight: bold; color: #e8c84a; }
.povzetek-oznaka { font-size: 0.75rem; color: #888; }
.statistika-graf, .statistika-tipi, .statistika-dosezki, .statistika-priporocila { background: rgba(255, 255, 255, 0.03); border-radius: 15px; padding: 1.5rem; margin-bottom: 2rem; }
.statistika-graf h2, .statistika-tipi h2, .statistika-dosezki h2, .statistika-priporocila h2 { color: #e8c84a; margin-bottom: 1rem; font-size: 1.2rem; }
.graf-stolpci { display: flex; justify-content: space-around; align-items: flex-end; gap: 0.5rem; height: 150px; }
.graf-stolpec { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; }
.stolpec { width: 100%; max-width: 40px; background: linear-gradient(180deg, #e8c84a, #a88a2a); border-radius: 5px 5px 0 0; transition: height 0.5s; min-height: 2px; }
.stolpec-oznaka { font-size: 0.7rem; color: #888; }
.stolpec-vrednost { font-size: 0.7rem; color: #e8c84a; }
.tipi-seznam { display: flex; flex-direction: column; gap: 0.75rem; }
.tip-vrstica { display: flex; align-items: center; gap: 1rem; padding: 0.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
.tip-ikona { font-size: 1.2rem; width: 30px; }
.tip-ime { width: 100px; color: #d4c5a9; }
.tip-stevilo { width: 50px; color: #e8c84a; font-weight: bold; }
.tip-napredek { flex: 1; background: rgba(255, 255, 255, 0.1); border-radius: 10px; height: 8px; overflow: hidden; }
.tip-napredek-bar { background: #e8c84a; height: 100%; border-radius: 10px; transition: width 0.5s; }
.tip-odstotek { width: 50px; color: #888; font-size: 0.8rem; }
.dosezki-seznam { display: flex; flex-direction: column; gap: 1rem; }
.dosezek { display: flex; gap: 1rem; align-items: center; padding: 1rem; border-radius: 12px; transition: all 0.3s; }
.dosezek-odklenjen { background: rgba(76, 175, 80, 0.1); border-left: 3px solid #4caf50; }
.dosezek-zaklenjen { background: rgba(255, 255, 255, 0.03); border-left: 3px solid #666; opacity: 0.6; }
.dosezek-ikona { font-size: 2rem; }
.dosezek-info { flex: 1; }
.dosezek-naslov { font-weight: bold; color: #e8c84a; margin-bottom: 0.25rem; }
.dosezek-opis { font-size: 0.8rem; color: #aaa; }
.statistika-priporocila ul { margin-left: 1.5rem; color: #aaa; }
.statistika-priporocila li { margin-bottom: 0.5rem; }
@media (max-width: 768px) {
    .statistika-povzetek { grid-template-columns: repeat(2, 1fr); }
    .tip-vrstica { flex-wrap: wrap; }
    .tip-napredek { width: 100%; order: 1; margin-top: 0.5rem; }
}
</style>