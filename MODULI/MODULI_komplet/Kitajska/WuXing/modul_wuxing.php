<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_wuxing_funkcije.php";
require_once __DIR__ . "/modul_wuxing_pravila.php";
require_once __DIR__ . "/modul_wuxing_jsonbaza.php";
class ModulWuXing {
    private array $config;
    public function __construct() { $db = new ModulWuXingJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"WuXing","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"WuXing","vsebina"=>"<div class=\"modul-wuxing\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
