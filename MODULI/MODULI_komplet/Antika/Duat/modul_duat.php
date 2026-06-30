<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_duat_funkcije.php";
require_once __DIR__ . "/modul_duat_pravila.php";
require_once __DIR__ . "/modul_duat_jsonbaza.php";
class ModulDuat {
    private array $config;
    public function __construct() { $db = new ModulDuatJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Duat","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Duat","vsebina"=>"<div class=\"modul-duat\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
