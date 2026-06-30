<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_buddhica_funkcije.php";
require_once __DIR__ . "/modul_buddhica_pravila.php";
require_once __DIR__ . "/modul_buddhica_jsonbaza.php";
class ModulBuddhica {
    private array $config;
    public function __construct() { $db = new ModulBuddhicaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Buddhica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Buddhica","vsebina"=>"<div class=\"modul-buddhica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
