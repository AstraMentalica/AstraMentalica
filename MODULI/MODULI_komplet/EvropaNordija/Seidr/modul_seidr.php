<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_seidr_funkcije.php";
require_once __DIR__ . "/modul_seidr_pravila.php";
require_once __DIR__ . "/modul_seidr_jsonbaza.php";
class ModulSeidr {
    private array $config;
    public function __construct() { $db = new ModulSeidrJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Seidr","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Seidr","vsebina"=>"<div class=\"modul-seidr\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
