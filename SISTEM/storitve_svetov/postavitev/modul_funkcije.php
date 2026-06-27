<?php
function globalno_modul(array $d): string {
    $rel=$d['rel']??'';
    $wl=upravljalec_whitelist();
    if(!isset($wl[$rel])) return '<p>Modul ni dostopen.</p>';
    $file=MODULI.'/'.$rel.'/'.$wl[$rel]['vstopna'];
    if(!file_exists($file)) return '<p>Modul ne obstaja.</p>';
    ob_start(); require $file; $v=ob_get_clean();
    return '<!DOCTYPE html><html><head><title>'.htmlspecialchars($wl[$rel]['ime']).'</title><link rel="stylesheet" href="/GLOBALNO/slog/osnova.css"></head><body><div class="notranjost">'.$v.'</div></body></html>';
}
