<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_transmutaria_funkcije.php";
require_once __DIR__ . "/modul_transmutaria_pravila.php";
require_once __DIR__ . "/modul_transmutaria_jsonbaza.php";
class ModulTransmutaria {
    private array $config;
    public function __construct() { $db = new ModulTransmutariaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Transmutaria","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Transmutaria","vsebina"=>"<div class=\"modul-transmutaria\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
