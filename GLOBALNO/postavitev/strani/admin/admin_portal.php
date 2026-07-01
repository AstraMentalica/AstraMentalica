<?php
/**
 * ============================================================
 * POT: GLOBALNO/postavitev/strani/admin/admin_portal.php
 * 📅 VERZIJA: v116 (18.6.2026 21:00)
 * ============================================================
 *
 * 🏛️ NIVO: ASTRA (ADMIN)
 *
 * 📰 NAMEN:
 *     Admin portal – premium kode, magični portali, sistem.
 *
 * ✅ DOVOLJENO:
 *     - echo, HTML
 *
 * 🚫 PREPOVEDI:
 *     - Brez business logike
 *     - Brez die(), exit()
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v116: uskladitev s Header Standard v116,
 *             odstranjeni vsi die() in exit()
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     astra, admin, portal
 * ============================================================
 */
declare(strict_types=1);

// VARNOST – namesto die() uporabimo return
if (!defined('SISTEM_VARNOST')) {
    http_response_code(403);
    header('Location: /');
    return;
}

if (session_status() === PHP_SESSION_NONE) {
    session_name('ASTRA_SID');
    session_start();
}

// RBAC – samo S5 / admin
if (($_SESSION['vloga_int'] ?? 0) < VLOGA_S5) {
    header('Location: /?svet=UPORABNIKI&pot=prijava');
    return;
}

// Poti
$PODATKI_JSON = POT_PODATKI . '/json';
$PORTALI_JSON = $PODATKI_JSON . '/portali/portali.json';
$KODE_JSON    = $PODATKI_JSON . '/portali/kode.json';
$PREMIUM_JSON = $PODATKI_JSON . '/portali/premium_uporabniki.json';

$portali_mapa = $PODATKI_JSON . '/portali';
if (!is_dir($portali_mapa)) {
    mkdir($portali_mapa, 0755, true);
}

function adm_beri(string $pot): array {
    return file_exists($pot) ? (json_decode(file_get_contents($pot), true) ?? []) : [];
}

function adm_pisi(string $pot, array $data): void {
    file_put_contents($pot, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function adm_koda(): string {
    $z = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    return substr(str_shuffle($z), 0, 4) . '-' . substr(str_shuffle($z), 0, 4) . '-' . substr(str_shuffle($z), 0, 3);
}

$register = adm_beri(PODATKI_JSON . '/moduli_register.json');
$sporocilo = '';
$napaka = '';

// 1. Generiraj kode
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generiraj_kode'])) {
    $modul    = $_POST['koda_modul'] ?? '';
    $stevilo  = min((int)($_POST['koda_stevilo'] ?? 5), 100);
    $trajanje = (int)($_POST['koda_trajanje'] ?? 30);
    $kode = adm_beri($KODE_JSON);
    for ($i = 0; $i < $stevilo; $i++) {
        $kode[] = [
            'koda' => adm_koda(),
            'modul' => $modul,
            'trajanje' => $trajanje,
            'porabljena' => false,
            'porabljeno_ob' => null,
            'porabljeno_id' => null,
            'ustvarjena' => date('Y-m-d H:i:s'),
        ];
    }
    adm_pisi($KODE_JSON, $kode);
    $sporocilo = "Ustvarjenih $stevilo kod za '" . basename($modul) . "'.";
}

// 2. Aktiviraj kodo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aktiviraj_kodo'])) {
    $vnos    = strtoupper(trim($_POST['aktivacijska_koda'] ?? ''));
    $uid     = $_SESSION['uporabnik_id'] ?? 0;
    $kode    = adm_beri($KODE_JSON);
    $premium = adm_beri($PREMIUM_JSON);
    $najdena = false;
    foreach ($kode as &$k) {
        if ($k['koda'] === $vnos && !$k['porabljena']) {
            $k['porabljena'] = true;
            $k['porabljeno_ob'] = date('Y-m-d H:i:s');
            $k['porabljeno_id'] = $uid;
            $premium[] = [
                'uporabnik_id' => $uid,
                'modul' => $k['modul'],
                'poteče' => date('Y-m-d H:i:s', strtotime("+{$k['trajanje']} days")),
                'aktivirano' => date('Y-m-d H:i:s'),
            ];
            $najdena = true;
            $sporocilo = "Koda aktivirana! Dostop do '" . basename($k['modul']) . "' velja {$k['trajanje']} dni.";
            break;
        }
    }
    if (!$najdena) {
        $napaka = 'Neveljavna ali že porabljena koda.';
    }
    adm_pisi($KODE_JSON, $kode);
    if ($najdena) {
        adm_pisi($PREMIUM_JSON, $premium);
    }
}

// 3. Ustvari portal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ustvari_portal'])) {
    $portali = adm_beri($PORTALI_JSON);
    $portali[] = [
        'id'         => uniqid('portal_'),
        'ime'        => htmlspecialchars($_POST['portal_ime'] ?? ''),
        'ure'        => $_POST['trigger_ure'] ?? '',
        'dnevi'      => $_POST['trigger_dnevi'] ?? '',
        'polna_luna' => isset($_POST['trigger_luna']),
        'efekt'      => $_POST['portal_efekt'] ?? 'stars',
        'nagrada'    => htmlspecialchars($_POST['portal_nagrada'] ?? ''),
        'modul'      => $_POST['nagrada_modul'] ?? '',
        'aktiven'    => true,
        'ustvarjen'  => date('Y-m-d H:i:s'),
    ];
    adm_pisi($PORTALI_JSON, $portali);
    $sporocilo = "Portal '{$_POST['portal_ime']}' ustvarjen!";
}

// 4. Toggle portal
if (isset($_GET['toggle_portal'])) {
    $pid = $_GET['toggle_portal'];
    $portali = adm_beri($PORTALI_JSON);
    foreach ($portali as &$p) {
        if ($p['id'] === $pid) {
            $p['aktiven'] = !$p['aktiven'];
        }
    }
    adm_pisi($PORTALI_JSON, $portali);
    header('Location: /ASTRA/admin_portal.php?tab=portali');
    return;
}

// 5. Briši kodo
if (isset($_GET['brisi_kodo'])) {
    $kode = adm_beri($KODE_JSON);
    $kode = array_values(array_filter($kode, fn($k) => $k['koda'] !== $_GET['brisi_kodo']));
    adm_pisi($KODE_JSON, $kode);
    header('Location: /ASTRA/admin_portal.php?tab=kode');
    return;
}

$portali = adm_beri($PORTALI_JSON);
$kode    = array_slice(array_reverse(adm_beri($KODE_JSON)), 0, 30);
$premium = array_reverse(array_values(array_filter(
    adm_beri($PREMIUM_JSON),
    fn($p) => strtotime($p['poteče']) > time()
)));
$aktivni_tab = $_GET['tab'] ?? 'kode';
?>
<!DOCTYPE html>
<html lang="sl" data-tema="temna">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal – AstraMentalica</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/GLOBALNO/vmesnik/css/osnova.css">
    <style>
        .adm-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(440px, 1fr));
            gap: 20px;
        }
        .adm-tabela {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
        }
        .adm-tabela th {
            font-size: 0.62rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--besedilo-d);
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid var(--rob);
        }
        .adm-tabela td {
            padding: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            vertical-align: middle;
        }
        .adm-tabela tr:hover td {
            background: var(--kartica-hover);
        }
        .koda-badge {
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            background: rgba(201, 169, 110, 0.1);
            color: var(--zlata);
            padding: 3px 10px;
            border-radius: 4px;
            letter-spacing: 1px;
        }
        .tab-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }
        .tab-gumb {
            padding: 8px 20px;
            border-radius: 30px;
            border: 1px solid var(--rob);
            background: transparent;
            color: var(--besedilo-d);
            font-size: 0.78rem;
            cursor: pointer;
            transition: var(--prehod);
            font-family: 'Jost', sans-serif;
        }
        .tab-gumb:hover,
        .tab-gumb.aktiven {
            background: rgba(201, 169, 110, 0.12);
            border-color: rgba(201, 169, 110, 0.3);
            color: var(--zlata);
        }
        .tab-vsebina {
            display: none;
            animation: pojavi 0.3s ease;
        }
        .tab-vsebina.aktivna {
            display: block;
        }
        @keyframes pojavi {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: none; }
        }
        .status-ok {
            color: var(--zelena);
        }
        .status-ne {
            color: var(--rdeca);
        }
        .efekt-badge {
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 4px;
            background: var(--kartica);
            border: 1px solid var(--rob);
        }
    </style>
</head>
<body>
    <div class="postavitev">
        <main class="glavna">
            <div class="notranjost">

                <div class="am-header">
                    <h1>⚡ Admin Portal</h1>
                    <p>Premium kode · Magični portali · Sistem</p>
                </div>

                <?php if ($sporocilo): ?>
                    <div class="sporocilo sporocilo-uspeh">✓ <?= htmlspecialchars($sporocilo) ?></div>
                <?php endif; ?>
                <?php if ($napaka): ?>
                    <div class="sporocilo sporocilo-napaka">✕ <?= htmlspecialchars($napaka) ?></div>
                <?php endif; ?>

                <div class="tab-row">
                    <button class="tab-gumb <?= $aktivni_tab === 'kode' ? 'aktiven' : '' ?>" onclick="tab('kode', this)">🎫 Kode</button>
                    <button class="tab-gumb <?= $aktivni_tab === 'portali' ? 'aktiven' : '' ?>" onclick="tab('portali', this)">🌀 Portali</button>
                    <button class="tab-gumb <?= $aktivni_tab === 'premium' ? 'aktiven' : '' ?>" onclick="tab('premium', this)">👑 Premium</button>
                    <button class="tab-gumb <?= $aktivni_tab === 'aktivacija' ? 'aktiven' : '' ?>" onclick="tab('aktivacija', this)">🔑 Aktivacija</button>
                    <button class="tab-gumb <?= $aktivni_tab === 'sistem' ? 'aktiven' : '' ?>" onclick="tab('sistem', this)">📊 Sistem</button>
                </div>

                <!-- KODE -->
                <div class="tab-vsebina <?= $aktivni_tab === 'kode' ? 'aktivna' : '' ?>" id="tab-kode">
                    <div class="adm-grid">
                        <div class="kartica">
                            <h3 style="margin-bottom:20px;">🎫 Generiraj kode</h3>
                            <form method="POST">
                                <div class="obrazec-skupina">
                                    <label class="obrazec-oznaka">Modul</label>
                                    <select name="koda_modul" class="vnos" required>
                                        <option value="">Izberi modul...</option>
                                        <?php foreach ($register as $rel => $info): ?>
                                            <option value="<?= htmlspecialchars($rel) ?>"><?= htmlspecialchars(basename($rel)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px">
                                    <div class="obrazec-skupina">
                                        <label class="obrazec-oznaka">Število kod</label>
                                        <input type="number" name="koda_stevilo" class="vnos" value="5" min="1" max="100">
                                    </div>
                                    <div class="obrazec-skupina">
                                        <label class="obrazec-oznaka">Trajanje (dni)</label>
                                        <input type="number" name="koda_trajanje" class="vnos" value="30" min="1">
                                    </div>
                                </div>
                                <button type="submit" name="generiraj_kode" class="gumb gumb-primarni">✨ Generiraj</button>
                            </form>
                        </div>
                        <div class="kartica">
                            <h3 style="margin-bottom:16px;">Zadnje kode</h3>
                            <?php if (empty($kode)): ?>
                                <p style="color:var(--besedilo-d); font-size:0.85rem">Ni še nobene kode.</p>
                            <?php else: ?>
                                <div style="overflow-x:auto">
                                    <table class="adm-tabela">
                                        <thead>
                                            <tr><th>Koda</th><th>Modul</th><th>Dni</th><th>Status</th><th></th></tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($kode as $k): ?>
                                                <tr>
                                                    <td><span class="koda-badge"><?= htmlspecialchars($k['koda']) ?></span></td>
                                                    <td style="font-size:0.75rem;color:var(--besedilo-d)"><?= htmlspecialchars(basename($k['modul'])) ?></td>
                                                    <td style="font-size:0.78rem"><?= $k['trajanje'] ?></td>
                                                    <td><?= $k['porabljena'] ? '<span class="status-ne">Porabljena</span>' : '<span class="status-ok">Aktivna</span>' ?></td>
                                                    <td>
                                                        <?php if (!$k['porabljena']): ?>
                                                            <a href="?brisi_kodo=<?= urlencode($k['koda']) ?>" onclick="return confirm('Briši?')" style="font-size:0.7rem;color:var(--rdeca)">briši</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- PORTALI -->
                <div class="tab-vsebina <?= $aktivni_tab === 'portali' ? 'aktivna' : '' ?>" id="tab-portali">
                    <div class="adm-grid">
                        <div class="kartica">
                            <h3 style="margin-bottom:20px;">🌀 Nov magični portal</h3>
                            <form method="POST">
                                <div class="obrazec-skupina">
                                    <label class="obrazec-oznaka">Ime portala</label>
                                    <input type="text" name="portal_ime" class="vnos" placeholder="npr. Lunin portal" required>
                                </div>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px">
                                    <div class="obrazec-skupina">
                                        <label class="obrazec-oznaka">Ure (8,12,21)</label>
                                        <input type="text" name="trigger_ure" class="vnos" placeholder="8,12,21">
                                    </div>
                                    <div class="obrazec-skupina">
                                        <label class="obrazec-oznaka">Dnevi (1,14,28)</label>
                                        <input type="text" name="trigger_dnevi" class="vnos" placeholder="1,14,28">
                                    </div>
                                </div>
                                <div class="obrazec-skupina" style="display:flex;align-items:center;gap:10px">
                                    <input type="checkbox" name="trigger_luna" id="luna" style="accent-color:var(--zlata);width:16px;height:16px">
                                    <label for="luna" class="obrazec-oznaka" style="margin:0">Aktiviraj ob polni luni</label>
                                </div>
                                <div class="obrazec-skupina">
                                    <label class="obrazec-oznaka">Efekt</label>
                                    <select name="portal_efekt" class="vnos">
                                        <option value="stars">✨ Zvezde</option>
                                        <option value="portal">🌀 Portal</option>
                                        <option value="mist">🌫️ Meglica</option>
                                        <option value="fire">🔥 Ogenj</option>
                                        <option value="water">💧 Voda</option>
                                        <option value="moon">🌙 Luna</option>
                                        <option value="sun">☀️ Sonce</option>
                                    </select>
                                </div>
                                <div class="obrazec-skupina">
                                    <label class="obrazec-oznaka">Nagrada (besedilo)</label>
                                    <textarea name="portal_nagrada" class="vnos" rows="2" placeholder="Ko portal odpre..."></textarea>
                                </div>
                                <div class="obrazec-skupina">
                                    <label class="obrazec-oznaka">Odpre modul (opcijsko)</label>
                                    <select name="nagrada_modul" class="vnos">
                                        <option value="">Brez</option>
                                        <?php foreach ($register as $rel => $info): ?>
                                            <option value="<?= htmlspecialchars($rel) ?>"><?= htmlspecialchars(basename($rel)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" name="ustvari_portal" class="gumb gumb-primarni">🌀 Ustvari</button>
                            </form>
                        </div>
                        <div class="kartica">
                            <h3 style="margin-bottom:16px;">Portali</h3>
                            <?php if (empty($portali)): ?>
                                <p style="color:var(--besedilo-d); font-size:0.85rem">Ni še nobenega portala.</p>
                            <?php else: ?>
                                <table class="adm-tabela">
                                    <thead>
                                        <tr><th>Ime</th><th>Ure</th><th>Efekt</th><th>Status</th><th></th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($portali as $p): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($p['ime']) ?></td>
                                                <td style="font-size:0.75rem;color:var(--besedilo-d)"><?= $p['ure'] ?: '—' ?></td>
                                                <td><span class="efekt-badge"><?= htmlspecialchars($p['efekt']) ?></span></td>
                                                <td><?= $p['aktiven'] ? '<span class="status-ok">Aktiven</span>' : '<span class="status-ne">Izklop</span>' ?></td>
                                                <td>
                                                    <a href="?toggle_portal=<?= $p['id'] ?>&tab=portali" style="font-size:0.7rem;color:var(--zlata)">
                                                        <?= $p['aktiven'] ? 'izklopi' : 'vklopi' ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- PREMIUM -->
                <div class="tab-vsebina <?= $aktivni_tab === 'premium' ? 'aktivna' : '' ?>" id="tab-premium">
                    <div class="kartica">
                        <h3 style="margin-bottom:16px;">👑 Aktivni premium dostopi</h3>
                        <?php if (empty($premium)): ?>
                            <p style="color:var(--besedilo-d); font-size:0.85rem">Ni aktivnih premium dostopov.</p>
                        <?php else: ?>
                            <div style="overflow-x:auto">
                                <table class="adm-tabela">
                                    <thead>
                                        <tr><th>ID</th><th>Modul</th><th>Aktivirano</th><th>Poteče</th><th>Preostalo</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($premium as $pu):
                                            $dni = ceil((strtotime($pu['poteče']) - time()) / 86400);
                                        ?>
                                            <tr>
                                                <td style="font-family:monospace;font-size:0.75rem"><?= $pu['uporabnik_id'] ?></td>
                                                <td><?= htmlspecialchars(basename($pu['modul'])) ?></td>
                                                <td style="font-size:0.75rem;color:var(--besedilo-d)"><?= substr($pu['aktivirano'], 0, 16) ?></td>
                                                <td style="font-size:0.75rem"><?= substr($pu['poteče'], 0, 16) ?></td>
                                                <td class="<?= $dni < 7 ? 'status-ne' : 'status-ok' ?>"><?= $dni ?> dni</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- AKTIVACIJA -->
                <div class="tab-vsebina <?= $aktivni_tab === 'aktivacija' ? 'aktivna' : '' ?>" id="tab-aktivacija">
                    <div class="kartica" style="max-width:480px">
                        <h3 style="margin-bottom:20px;">🔑 Aktiviraj kodo</h3>
                        <form method="POST">
                            <div class="obrazec-skupina">
                                <label class="obrazec-oznaka">Aktivacijska koda</label>
                                <input type="text" name="aktivacijska_koda" class="vnos"
                                       placeholder="XXXX-XXXX-XXX" style="letter-spacing:2px;text-transform:uppercase"
                                       required autocomplete="off" id="koda-input">
                            </div>
                            <button type="submit" name="aktiviraj_kodo" class="gumb gumb-primarni" style="width:100%">✨ Aktiviraj</button>
                        </form>
                    </div>
                </div>

                <!-- SISTEM -->
                <div class="tab-vsebina <?= $aktivni_tab === 'sistem' ? 'aktivna' : '' ?>" id="tab-sistem">
                    <div class="adm-grid">
                        <div class="kartica">
                            <h3 style="margin-bottom:16px;">📊 Sistem</h3>
                            <table class="adm-tabela">
                                <tr><td style="color:var(--besedilo-d)">PHP</td><td><?= PHP_VERSION ?></td></tr>
                                <tr><td style="color:var(--besedilo-d)">Verzija</td><td><?= defined('SISTEM_VERZIJA') ? SISTEM_VERZIJA : '3.3' ?></td></tr>
                                <tr><td style="color:var(--besedilo-d)">Čas</td><td><?= date('Y-m-d H:i:s') ?></td></tr>
                                <tr><td style="color:var(--besedilo-d)">Vloga</td><td><?= htmlspecialchars($_SESSION['vloga'] ?? '') ?></td></tr>
                                <tr><td style="color:var(--besedilo-d)">Moduli</td><td><?= count($register) ?> registriranih</td></tr>
                                <tr><td style="color:var(--besedilo-d)">Kode skupaj</td><td><?= count(adm_beri($KODE_JSON)) ?></td></tr>
                                <tr><td style="color:var(--besedilo-d)">Portali</td><td><?= count($portali) ?></td></tr>
                            </table>
                        </div>
                        <div class="kartica">
                            <h3 style="margin-bottom:16px;">🔧 Akcije</h3>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <button class="gumb gumb-sekundarni" onclick="sysKlic('ping')">📡 Ping</button>
                                <button class="gumb gumb-sekundarni" onclick="sysKlic('cache_ocisti')">🗑️ Počisti cache</button>
                                <button class="gumb gumb-sekundarni" onclick="sysKlic('sistem_info')">ℹ️ Info</button>
                                <a href="/" class="gumb gumb-sekundarni" style="text-align:center">← Domov</a>
                            </div>
                            <pre id="sys-odziv" style="margin-top:16px;font-size:0.72rem;color:var(--modra);background:var(--kartica);padding:10px;border-radius:8px;display:none;overflow-x:auto"></pre>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        (function() {
            const t = localStorage.getItem('am_tema') || 'temna';
            document.documentElement.setAttribute('data-tema', t);
        })();

        function tab(ime, gumb) {
            document.querySelectorAll('.tab-vsebina').forEach(t => t.classList.remove('aktivna'));
            document.querySelectorAll('.tab-gumb').forEach(g => g.classList.remove('aktiven'));
            document.getElementById('tab-' + ime).classList.add('aktivna');
            gumb.classList.add('aktiven');
        }

        async function sysKlic(akcija) {
            const el = document.getElementById('sys-odziv');
            el.style.display = 'block';
            el.textContent = '...';
            try {
                const r = await fetch('/?svet=SISTEM', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ akcija: akcija, podatki: {} })
                });
                el.textContent = JSON.stringify(await r.json(), null, 2);
            } catch (e) {
                el.textContent = 'Napaka: ' + e.message;
            }
        }

        document.getElementById('koda-input')?.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>