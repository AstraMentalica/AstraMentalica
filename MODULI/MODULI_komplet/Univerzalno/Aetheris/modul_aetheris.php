<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_aetheris_funkcije.php";
require_once __DIR__ . "/modul_aetheris_pravila.php";
require_once __DIR__ . "/modul_aetheris_jsonbaza.php";
class ModulAetheris {
    private array $config;
    public function __construct() { $db = new ModulAetherisJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Aetheris","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Aetheris","vsebina"=>"<div class=\"modul-aetheris\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
