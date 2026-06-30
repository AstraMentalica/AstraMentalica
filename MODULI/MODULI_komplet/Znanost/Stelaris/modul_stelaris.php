<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_stelaris_funkcije.php";
require_once __DIR__ . "/modul_stelaris_pravila.php";
require_once __DIR__ . "/modul_stelaris_jsonbaza.php";
class ModulStelaris {
    private array $config;
    public function __construct() { $db = new ModulStelarisJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Stelaris","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Stelaris","vsebina"=>"<div class=\"modul-stelaris\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
