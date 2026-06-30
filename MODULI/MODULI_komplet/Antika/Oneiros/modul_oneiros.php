<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_oneiros_funkcije.php";
require_once __DIR__ . "/modul_oneiros_pravila.php";
require_once __DIR__ . "/modul_oneiros_jsonbaza.php";
class ModulOneiros {
    private array $config;
    public function __construct() { $db = new ModulOneirosJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Oneiros","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Oneiros","vsebina"=>"<div class=\"modul-oneiros\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
