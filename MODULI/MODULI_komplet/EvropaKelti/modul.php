<?php
declare(strict_types=1);
$h=[__DIR__ . '/../../Modul_Bridge/modul_bridge.php']; $f=false;
foreach($h as $p){if(file_exists($p)){require_once $p; $f=true; break;}}
if(!$f){header("Content-Type: application/json"); echo json_encode(["e"=>"Bridge"]); exit;}
if(!function_exists("ru")){function ru($d,$s=""){return["st"=>"ok","c"=>200,"sp"=>$s,"v"=>$d];} function er($m,$k=400){return["st"=>"err","c"=>$k,"sp"=>$m,"v"=>[]];}}
function modul_evropakelti_a($a,$d=[]){if(!Modul_Bridge::vloga_preveri("S0")) return er("Zaprto",403); return match($a){"info"=>_evropakelti_i($d), "domov"=>_evropakelti_d($d), default=>er("?",400)};}
function _evropakelti_i($d){$u=Modul_Bridge::uporabnik_pridobi(); return ru(["n"=>"EvropaKelti","i"=>"evropakelti","v"=>"1.0","k"=>"evropakelti","o"=>"EvropaKelti","u"=>($u["ime"]??"Gost"), "opis"=>"Modul EvropaKelti — kategorija MODULI_komplet."], "i");}
function _evropakelti_d($d){return ru(["s"=>"Pozdravljen v modulu EvropaKelti!","t"=>time(),"i"=>"evropakelti"], "d");}
if(basename($_SERVER["SCRIPT_FILENAME"]??"")==="modul.php" && !defined("SISTEM_OBSTAJA")){$q=$_REQUEST["akcija"]??"domov"; $o=modul_evropakelti_a($q,$_REQUEST); header("Content-Type: application/json; charset=utf-8"); echo json_encode($o,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);}
