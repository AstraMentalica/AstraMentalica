<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_ogham_funkcije.php";
require_once __DIR__ . "/modul_ogham_pravila.php";
require_once __DIR__ . "/modul_ogham_jsonbaza.php";
class ModulOgham {
    private array $config;
    public function __construct() { $db = new ModulOghamJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Ogham","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Ogham","vsebina"=>"<div class=\"modul-ogham\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
