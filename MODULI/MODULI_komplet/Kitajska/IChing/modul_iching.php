<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_iching_funkcije.php";
require_once __DIR__ . "/modul_iching_pravila.php";
require_once __DIR__ . "/modul_iching_jsonbaza.php";
class ModulIChing {
    private array $config;
    public function __construct() { $db = new ModulIChingJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"IChing","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"IChing","vsebina"=>"<div class=\"modul-iching\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
