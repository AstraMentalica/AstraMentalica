<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_perun_funkcije.php";
require_once __DIR__ . "/modul_perun_pravila.php";
require_once __DIR__ . "/modul_perun_jsonbaza.php";
class ModulPerun {
    private array $config;
    public function __construct() { $db = new ModulPerunJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Perun","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Perun","vsebina"=>"<div class=\"modul-perun\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
