<?php
declare(strict_types=1);
defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');
$vloga = $_SESSION['vloga'] ?? 'gost';
$ime   = $_SESSION['uporabnik_ime'] ?? '';
$layout = $_SESSION['layout'] ?? 'temna';
?>
<style>
.nav-koz{background:rgba(10,0,30,.9);padding:10px 20px;display:flex;gap:12px;align-items:center;border-bottom:1px solid #4a3a8a;flex-wrap:wrap;backdrop-filter:blur(5px);}
.nav-koz a{color:#9a8aff;text-decoration:none;padding:6px 10px;border-radius:4px;font-size:.9rem;}
.nav-koz a:hover,.nav-koz a.akt{color:#c8a0ff;background:rgba(74,58,138,.3);}
.nav-koz .admin-link{color:#ff69b4 !important;}
.nav-koz .logout{color:#ff4444 !important;}
.lay-izbira{margin-left:auto;display:flex;gap:4px;}
.lay-izbira a{padding:4px 8px;border-radius:4px;font-size:.75rem;background:#2a1a4a;color:#9a8aff;text-decoration:none;}
.lay-izbira a.akt{background:#7a5aff;color:#fff;}
</style>
<nav class="nav-koz">
    <a href="/" class="akt">✦ Domov</a>
    <a href="/?svet=MODULI&pot=osnovni/Codex">📖 Codex</a>
    <?php if (!empty($_SESSION['uporabnik_id'])): ?>
        <a href="/?svet=UPORABNIKI&pot=profil">👤 <?= htmlspecialchars($ime) ?></a>
        <?php if ($vloga === 'admin' || $vloga === 'S5'): ?>
            <a href="/?svet=ASTRA" class="admin-link">⚡ Astra</a>
        <?php endif; ?>
        <a href="/?svet=UPORABNIKI&pot=odjava" class="logout">🚪</a>
    <?php else: ?>
        <a href="/?svet=UPORABNIKI&pot=prijava">🔐 Prijava</a>
    <?php endif; ?>
    <div class="lay-izbira">
        <a href="?layout=temna" class="<?= $layout=='temna'?'akt':'' ?>">🌑</a>
        <a href="?layout=svetla" class="<?= $layout=='svetla'?'akt':'' ?>">☀️</a>
        <a href="?layout=kozmicna" class="<?= $layout=='kozmicna'?'akt':'' ?>">🌌</a>
    </div>
</nav>