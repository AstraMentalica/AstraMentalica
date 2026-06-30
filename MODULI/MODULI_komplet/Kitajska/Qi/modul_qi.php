<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_qi_funkcije.php";
require_once __DIR__ . "/modul_qi_pravila.php";
require_once __DIR__ . "/modul_qi_jsonbaza.php";
class ModulQi {
    private array $config;
    public function __construct() { $db = new ModulQiJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Qi","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Qi","vsebina"=>"<div class=\"modul-qi\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
