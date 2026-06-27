<?php
function globalno_navigacija(): string {
    $prijavljen = !empty($_SESSION['uporabnik_id']);
    $wl = upravljalec_whitelist();
    $h = '<nav class="navigacija"><div class="nav-logotip"><span>✦</span> AstraMentalica</div><div class="nav-seznam'>
        <a href="/" class="nav-gumb"><span class="nav-ikona">✦</span> Domov</a>';
    if($prijavljen){
        $h .= '<a href="/profil" class="nav-gumb"><span class="nav-ikona">👤</span> Profil</a>
        <a href="/?svet=SISTEM&akcija=odjava" class="nav-gumb"><span class="nav-ikona">↪</span> Odjava</a>';
    }else{
        $h .= '<a href="/prijava" class="nav-gumb"><span class="nav-ikona">🔐</span> Prijava</a>
        <a href="/registracija" class="nav-gumb"><span class="nav-ikona">✨</span> Registracija</a>';
    }
    if(!empty($wl)){
        $h .= '<div class="nav-locilo"></div>';
        foreach($wl as $r=>$m) $h .= '<a href="/moduli/'.urlencode($r).'" class="nav-gumb"><span class="nav-ikona">'.htmlspecialchars($m['ikona']).'</span>'.htmlspecialchars($m['ime']).'</a>';
    }
    $h .= '</div></nav>';
    return $h;
}
