<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_zhongyi_funkcije.php";
require_once __DIR__ . "/modul_zhongyi_pravila.php";
require_once __DIR__ . "/modul_zhongyi_jsonbaza.php";
class ModulZhongYi {
    private array $config;
    public function __construct() { $db = new ModulZhongYiJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"ZhongYi","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"ZhongYi","vsebina"=>"<div class=\"modul-zhongyi\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
