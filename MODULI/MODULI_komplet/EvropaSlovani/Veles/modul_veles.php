<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_veles_funkcije.php";
require_once __DIR__ . "/modul_veles_pravila.php";
require_once __DIR__ . "/modul_veles_jsonbaza.php";
class ModulVeles {
    private array $config;
    public function __construct() { $db = new ModulVelesJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Veles","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Veles","vsebina"=>"<div class=\"modul-veles\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
