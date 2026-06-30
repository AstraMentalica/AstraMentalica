<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_yume_funkcije.php";
require_once __DIR__ . "/modul_yume_pravila.php";
require_once __DIR__ . "/modul_yume_jsonbaza.php";
class ModulYume {
    private array $config;
    public function __construct() { $db = new ModulYumeJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Yume","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Yume","vsebina"=>"<div class=\"modul-yume\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
