<?php
declare(strict_types=1);
$h=[__DIR__ . '/../../Modul_Bridge/modul_bridge.php']; $f=false;
foreach($h as $p){if(file_exists($p)){require_once $p; $f=true; break;}}
if(!$f){header("Content-Type: application/json"); echo json_encode(["e"=>"Bridge"]); exit;}
if(!function_exists("ru")){function ru($d,$s=""){return["st"=>"ok","c"=>200,"sp"=>$s,"v"=>$d];} function er($m,$k=400){return["st"=>"err","c"=>$k,"sp"=>$m,"v"=>[]];}}
function modul_samanizem_a($a,$d=[]){if(!Modul_Bridge::vloga_preveri("S0")) return er("Zaprto",403); return match($a){"info"=>_samanizem_i($d), "domov"=>_samanizem_d($d), default=>er("?",400)};}
function _samanizem_i($d){$u=Modul_Bridge::uporabnik_pridobi(); return ru(["n"=>"Samanizem","i"=>"samanizem","v"=>"1.0","k"=>"samanizem","o"=>"Samanizem","u"=>($u["ime"]??"Gost"), "opis"=>"Modul Samanizem — kategorija MODULI_komplet."], "i");}
function _samanizem_d($d){return ru(["s"=>"Pozdravljen v modulu Samanizem!","t"=>time(),"i"=>"samanizem"], "d");}
if(basename($_SERVER["SCRIPT_FILENAME"]??"")==="modul.php" && !defined("SISTEM_OBSTAJA")){$q=$_REQUEST["akcija"]??"domov"; $o=modul_samanizem_a($q,$_REQUEST); header("Content-Type: application/json; charset=utf-8"); echo json_encode($o,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);}
