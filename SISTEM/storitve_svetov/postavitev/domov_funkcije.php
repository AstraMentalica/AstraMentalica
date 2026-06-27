<?php
function globalno_domov(array $d): string {
    $ime = htmlspecialchars($d['ime'] ?? 'Popotnik');
    $wl = $d['whitelist'] ?? [];
    $mod = '';
    foreach($wl as $r=>$m) $mod .= '<a href="/moduli/'.urlencode($r).'" class="modul-kartica"><div class="modul-ikona">'.htmlspecialchars($m['ikona']).'</div><div class="modul-ime">'.htmlspecialchars($m['ime']).'</div><div class="modul-opis">'.htmlspecialchars($m['opis']).'</div></a>';
    return '<!DOCTYPE html><html lang="sl"><head><meta charset="UTF-8"><title>AstraMentalica</title><link rel="stylesheet" href="/GLOBALNO/slog/osnova.css"></head><body><div class="postavitev">'.globalno_navigacija().'<main class="glavna"><div class="notranjost"><h1>Dobrodošel, '.$ime.' ✦</h1><div class="moduli-mreza">'.$mod.'</div></div></main></div></body></html>';
}
