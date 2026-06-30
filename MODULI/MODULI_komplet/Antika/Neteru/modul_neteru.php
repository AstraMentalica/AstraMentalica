<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_neteru_funkcije.php";
require_once __DIR__ . "/modul_neteru_pravila.php";
require_once __DIR__ . "/modul_neteru_jsonbaza.php";
class ModulNeteru {
    private array $config;
    public function __construct() { $db = new ModulNeteruJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Neteru","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Neteru","vsebina"=>"<div class=\"modul-neteru\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
