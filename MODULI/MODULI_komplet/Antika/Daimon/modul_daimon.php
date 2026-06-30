<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_daimon_funkcije.php";
require_once __DIR__ . "/modul_daimon_pravila.php";
require_once __DIR__ . "/modul_daimon_jsonbaza.php";
class ModulDaimon {
    private array $config;
    public function __construct() { $db = new ModulDaimonJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Daimon","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Daimon","vsebina"=>"<div class=\"modul-daimon\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
