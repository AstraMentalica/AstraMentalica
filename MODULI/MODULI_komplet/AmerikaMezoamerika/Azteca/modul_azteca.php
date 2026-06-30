<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_azteca_funkcije.php";
require_once __DIR__ . "/modul_azteca_pravila.php";
require_once __DIR__ . "/modul_azteca_jsonbaza.php";
class ModulAzteca {
    private array $config;
    public function __construct() { $db = new ModulAztecaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Azteca","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Azteca","vsebina"=>"<div class=\"modul-azteca\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
