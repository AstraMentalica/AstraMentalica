<?php
function globalno_uporabniki(array $d): string {
    $pot = $d['pot'] ?? 'prijava';
    if($pot==='prijava') return '<!DOCTYPE html><html><head><title>Prijava</title><link rel="stylesheet" href="/GLOBALNO/slog/osnova.css"></head><body>
    <div style="max-width:400px;margin:100px auto"><h1>Prijava</h1>
    <input id="email" placeholder="Email" class="vnos" style="width:100%;margin:10px 0"><input id="geslo" type="password" placeholder="Geslo" class="vnos" style="width:100%;margin:10px 0">
    <button class="gumb" style="width:100%" onclick="prijava()">Prijava</button><p style="margin-top:16px"><a href="/registracija">Registracija</a></p></div>
    <script>async function prijava(){const r=await fetch("/api",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({akcija:"prijava",podatki:{email:document.getElementById("email").value,geslo:document.getElementById("geslo").value}})});const d=await r.json();if(d.status==="success")location.href="/";else alert(d.napaka);}</script></body></html>';
    if($pot==='registracija') return '<!DOCTYPE html><html><head><title>Registracija</title><link rel="stylesheet" href="/GLOBALNO/slog/osnova.css"></head><body>
    <div style="max-width:400px;margin:100px auto"><h1>Registracija</h1>
    <input id="ime" placeholder="Ime" class="vnos" style="width:100%;margin:10px 0"><input id="email" placeholder="Email" class="vnos" style="width:100%;margin:10px 0"><input id="geslo" type="password" placeholder="Geslo" class="vnos" style="width:100%;margin:10px 0">
    <button class="gumb" style="width:100%" onclick="registracija()">Registracija</button></div>
    <script>async function registracija(){const r=await fetch("/api",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({akcija:"registracija",podatki:{ime:document.getElementById("ime").value,email:document.getElementById("email").value,geslo:document.getElementById("geslo").value}})});const d=await r.json();if(d.status==="success")location.href="/prijava";else alert(d.napaka);}</script></body></html>';
    if($pot==='profil'){
        if(empty($_SESSION["uporabnik_id"])) return '<script>location.href="/prijava";</script>';
        $ime=htmlspecialchars($_SESSION["uporabnik_ime"]??'Neznan');
        return '<!DOCTYPE html><html><head><title>Profil</title><link rel="stylesheet" href="/GLOBALNO/slog/osnova.css"></head><body><div><h1>Profil</h1><p>Ime: '.$ime.'</p><p>Vloga: '.$_SESSION["vloga"].'</p></div></body></html>';
    }
    return '<p>Stran ne obstaja.</p>';
}
