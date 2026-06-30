<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_kemetica_funkcije.php";
require_once __DIR__ . "/modul_kemetica_pravila.php";
require_once __DIR__ . "/modul_kemetica_jsonbaza.php";
class ModulKemetica {
    private array $config;
    public function __construct() { $db = new ModulKemeticaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Kemetica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Kemetica","vsebina"=>"<div class=\"modul-kemetica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
