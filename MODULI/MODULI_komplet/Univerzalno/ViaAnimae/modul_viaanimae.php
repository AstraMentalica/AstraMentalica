<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_viaanimae_funkcije.php";
require_once __DIR__ . "/modul_viaanimae_pravila.php";
require_once __DIR__ . "/modul_viaanimae_jsonbaza.php";
class ModulViaAnimae {
    private array $config;
    public function __construct() { $db = new ModulViaAnimaeJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"ViaAnimae","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"ViaAnimae","vsebina"=>"<div class=\"modul-viaanimae\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
