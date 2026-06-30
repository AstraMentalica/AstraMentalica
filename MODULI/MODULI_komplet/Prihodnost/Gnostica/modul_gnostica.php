<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_gnostica_funkcije.php";
require_once __DIR__ . "/modul_gnostica_pravila.php";
require_once __DIR__ . "/modul_gnostica_jsonbaza.php";
class ModulGnostica {
    private array $config;
    public function __construct() { $db = new ModulGnosticaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Gnostica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Gnostica","vsebina"=>"<div class=\"modul-gnostica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
