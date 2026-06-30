<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_amazoniasacra_funkcije.php";
require_once __DIR__ . "/modul_amazoniasacra_pravila.php";
require_once __DIR__ . "/modul_amazoniasacra_jsonbaza.php";
class ModulAmazoniaSacra {
    private array $config;
    public function __construct() { $db = new ModulAmazoniaSacraJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"AmazoniaSacra","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"AmazoniaSacra","vsebina"=>"<div class=\"modul-amazoniasacra\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
