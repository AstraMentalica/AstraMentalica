<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_nasa_funkcije.php";
require_once __DIR__ . "/modul_nasa_pravila.php";
require_once __DIR__ . "/modul_nasa_jsonbaza.php";
class ModulNasa {
    private array $config;
    public function __construct() { $db = new ModulNasaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Nasa","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Nasa","vsebina"=>"<div class=\"modul-nasa\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
