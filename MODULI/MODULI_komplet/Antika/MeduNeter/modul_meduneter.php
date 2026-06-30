<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_meduneter_funkcije.php";
require_once __DIR__ . "/modul_meduneter_pravila.php";
require_once __DIR__ . "/modul_meduneter_jsonbaza.php";
class ModulMeduNeter {
    private array $config;
    public function __construct() { $db = new ModulMeduNeterJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"MeduNeter","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"MeduNeter","vsebina"=>"<div class=\"modul-meduneter\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
