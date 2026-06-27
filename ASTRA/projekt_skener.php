<?php
/**
 * ============================================================
 * POT: ASTRA/projekt_skener.php
 * 📅 VERZIJA: v118 (18.6.2026 23:00)
 * ============================================================
 *
 * 🏛️ NIVO: ASTRA (ADMIN)
 *
 * 📰 NAMEN:
 *     Skenira strukturo projekta (root) in shrani v
 *     AI/sistemskiAI/vizija/projekt_struktura.json.
 *     AI agenti berejo ta snapshot kot referenco pri gradnji
 *     zrcalnega peskovnika RAZVOJ/.
 *
 *     Podpira dva vira:
 *     A) Avtomatski scan — PHP pregleda filesystem od korena
 *     B) Ročni upload — lastnik prilepi/naloži drevesno strukturo
 *        (izhod ukaza `tree`, File Explorer izvoz, ipd.)
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - (HTML vmesnik — ni javnih PHP funkcij)
 *
 * 📡 ODVISNOSTI:
 *     - pot.php (POT_KOREN, POT_AI)
 *     - seja_zacni() iz jedra
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Nikoli dostopno brez S5+ vloge
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v118: nova datoteka; skener + ročni upload
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     astra, skener, projekt, struktura, ai, vizija
 * ============================================================
 */

declare(strict_types=1);

if (!defined('SISTEM_VARNOST')) {
    http_response_code(403);
    header('Location: /');
    return;
}

seja_zacni();
$vloga = $_SESSION['uporabnik_vloga'] ?? 0;
if ($vloga < 60) {
    header('Location: ?svet=GLOBALNO&error=' . urlencode('Nimate dostopa'));
    return;
}

// ============================================================
// IZKLJUČENE MAPE — ne skeniramo
// ============================================================
const SKENER_IZKLUCENE = [
    'RAZVOJ',       // peskovnik — ni del originala
    '.git',
    '.idea',
    'node_modules',
    'vendor',
];

// ============================================================
// FUNKCIJA: rekurzivni scan
// ============================================================
function skener_scan(string $mapa, string $koren, int $globina = 0): array {
    $rezultat = [];
    if ($globina > 8) {
        return $rezultat;
    }

    $elementi = @scandir($mapa);
    if (!$elementi) {
        return $rezultat;
    }

    foreach ($elementi as $ime) {
        if ($ime === '.' || $ime === '..') {
            continue;
        }

        $relativno = ltrim(str_replace($koren, '', $mapa . '/' . $ime), '/');
        $polnaPot  = $mapa . '/' . $ime;

        // Preskoči izključene
        if (in_array($ime, SKENER_IZKLUCENE, true)) {
            continue;
        }

        if (is_dir($polnaPot)) {
            $rezultat[] = [
                'tip'      => 'mapa',
                'pot'      => $relativno,
                'ime'      => $ime,
                'otroci'   => skener_scan($polnaPot, $koren, $globina + 1),
            ];
        } else {
            $rezultat[] = [
                'tip'      => 'datoteka',
                'pot'      => $relativno,
                'ime'      => $ime,
                'velikost' => filesize($polnaPot),
                'spremen'  => date('Y-m-d H:i:s', filemtime($polnaPot)),
            ];
        }
    }

    return $rezultat;
}

// ============================================================
// OBDELAVA AKCIJ
// ============================================================
$sporocilo = '';
$napaka    = '';
$ciljnaD   = POT_AI . '/sistemskiAI/vizija/projekt_struktura.json';

// A) Avtomatski scan
if (isset($_POST['akcija']) && $_POST['akcija'] === 'scan') {
    $struktura = [
        'vir'       => 'avtomatski_scan',
        'cas'       => date('Y-m-d H:i:s'),
        'koren'     => POT_KOREN,
        'struktura' => skener_scan(POT_KOREN, POT_KOREN),
    ];

    if (!is_dir(dirname($ciljnaD))) {
        mkdir(dirname($ciljnaD), 0755, true);
    }

    file_put_contents($ciljnaD, json_encode($struktura, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $sporocilo = 'Struktura uspešno skenirana in shranjena.';
}

// B) Ročni upload — tekst (tree izhod ali podobno)
if (isset($_POST['akcija']) && $_POST['akcija'] === 'upload_tekst') {
    $tekst = trim($_POST['struktura_tekst'] ?? '');

    if (empty($tekst)) {
        $napaka = 'Besedilo je prazno.';
    } else {
        $struktura = [
            'vir'       => 'rocni_upload',
            'cas'       => date('Y-m-d H:i:s'),
            'surovo'    => $tekst,
        ];

        if (!is_dir(dirname($ciljnaD))) {
            mkdir(dirname($ciljnaD), 0755, true);
        }

        file_put_contents($ciljnaD, json_encode($struktura, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $sporocilo = 'Ročna struktura uspešno shranjena.';
    }
}

// Preveri obstoj trenutnega snapshota
$obstaja     = file_exists($ciljnaD);
$zadnjiScan  = $obstaja ? json_decode(file_get_contents($ciljnaD), true) : null;
$zadnjiCas   = $zadnjiScan['cas'] ?? '—';
$zadnjiVir   = $zadnjiScan['vir'] ?? '—';

?>

<div class="kartica">
    <h1>🗺️ Projekt skener</h1>
    <p>Ustvari ali posodobi <strong>zemljevid projekta</strong> za AI agente.<br>
       Agenti preberejo <code>AI/sistemskiAI/vizija/projekt_struktura.json</code>
       in vedo točno kaj zrcaliti v <code>RAZVOJ/</code>.</p>
</div>

<?php if ($sporocilo): ?>
<div class="kartica" style="border-left: 4px solid var(--uspeh, #4caf50)">
    <p>✅ <?= htmlspecialchars($sporocilo) ?></p>
</div>
<?php endif; ?>

<?php if ($napaka): ?>
<div class="kartica" style="border-left: 4px solid var(--napaka, #f44336)">
    <p>❌ <?= htmlspecialchars($napaka) ?></p>
</div>
<?php endif; ?>

<!-- Status -->
<div class="kartica">
    <h2>Trenutni snapshot</h2>
    <table class="tabela">
        <tr><td><strong>Obstaja</strong></td><td><?= $obstaja ? '✅ Da' : '❌ Ne' ?></td></tr>
        <tr><td><strong>Zadnji scan</strong></td><td><?= htmlspecialchars($zadnjiCas) ?></td></tr>
        <tr><td><strong>Vir</strong></td><td><?= htmlspecialchars($zadnjiVir) ?></td></tr>
    </table>
</div>

<div class="mreza-2">

    <!-- A) Avtomatski scan -->
    <div class="kartica">
        <h2>🤖 A) Avtomatski scan</h2>
        <p>PHP pregleda celoten root in zapiše JSON strukturo z vsemi mapami in datotekami.</p>
        <p><small>Izključene mape: <code>RAZVOJ/</code>, <code>.git</code>, <code>node_modules</code>, <code>vendor</code></small></p>
        <form method="post">
            <input type="hidden" name="akcija" value="scan">
            <button type="submit" class="gumb gumb-primarni">🔍 Zaženi scan</button>
        </form>
    </div>

    <!-- B) Ročni upload -->
    <div class="kartica">
        <h2>📋 B) Ročni vnos strukture</h2>
        <p>Prilepi izhod ukaza <code>tree</code>, seznam map iz File Explorerja ali karkoli podobnega.</p>
        <p><small>Primer: <code>tree /f /a</code> na Windowsih ali <code>tree -a</code> na Linuxu/Macu</small></p>
        <form method="post">
            <input type="hidden" name="akcija" value="upload_tekst">
            <textarea
                name="struktura_tekst"
                rows="12"
                style="width:100%; font-family:monospace; font-size:0.85em; padding:var(--razmik-s)"
                placeholder="Sem prilepi drevesno strukturo projekta..."></textarea>
            <br><br>
            <button type="submit" class="gumb gumb-primarni">💾 Shrani strukturo</button>
        </form>
    </div>

</div>

<!-- Link nazaj -->
<div class="kartica" style="margin-top:var(--razmik-l)">
    <a href="?svet=ASTRA" class="gumb">← Nazaj na nadzorni center</a>
    &nbsp;
    <?php if ($obstaja): ?>
    <a href="?svet=ASTRA&orodje=projekt_skener&prikaz=json" class="gumb">👁️ Prikaži JSON</a>
    <?php endif; ?>
</div>
