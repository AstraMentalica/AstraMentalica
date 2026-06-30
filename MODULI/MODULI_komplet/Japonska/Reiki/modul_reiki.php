<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_reiki_funkcije.php";
require_once __DIR__ . "/modul_reiki_pravila.php";
require_once __DIR__ . "/modul_reiki_jsonbaza.php";
class ModulReiki {
    private array $config;
    public function __construct() { $db = new ModulReikiJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Reiki","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Reiki","vsebina"=>"<div class=\"modul-reiki\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
