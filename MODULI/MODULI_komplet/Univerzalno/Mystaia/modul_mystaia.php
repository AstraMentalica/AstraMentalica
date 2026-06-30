<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_mystaia_funkcije.php";
require_once __DIR__ . "/modul_mystaia_pravila.php";
require_once __DIR__ . "/modul_mystaia_jsonbaza.php";
class ModulMystaia {
    private array $config;
    public function __construct() { $db = new ModulMystaiaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Mystaia","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Mystaia","vsebina"=>"<div class=\"modul-mystaia\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
