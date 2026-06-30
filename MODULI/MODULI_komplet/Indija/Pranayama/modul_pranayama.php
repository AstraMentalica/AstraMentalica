<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_pranayama_funkcije.php";
require_once __DIR__ . "/modul_pranayama_pravila.php";
require_once __DIR__ . "/modul_pranayama_jsonbaza.php";
class ModulPranayama {
    private array $config;
    public function __construct() { $db = new ModulPranayamaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Pranayama","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Pranayama","vsebina"=>"<div class=\"modul-pranayama\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
