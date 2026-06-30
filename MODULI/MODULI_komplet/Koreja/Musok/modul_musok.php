<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_musok_funkcije.php";
require_once __DIR__ . "/modul_musok_pravila.php";
require_once __DIR__ . "/modul_musok_jsonbaza.php";
class ModulMusok {
    private array $config;
    public function __construct() { $db = new ModulMusokJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Musok","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Musok","vsebina"=>"<div class=\"modul-musok\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
