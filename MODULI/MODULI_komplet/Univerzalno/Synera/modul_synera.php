<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_synera_funkcije.php";
require_once __DIR__ . "/modul_synera_pravila.php";
require_once __DIR__ . "/modul_synera_jsonbaza.php";
class ModulSynera {
    private array $config;
    public function __construct() { $db = new ModulSyneraJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Synera","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Synera","vsebina"=>"<div class=\"modul-synera\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
