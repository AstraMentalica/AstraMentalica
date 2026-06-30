<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_jyotisha_funkcije.php";
require_once __DIR__ . "/modul_jyotisha_pravila.php";
require_once __DIR__ . "/modul_jyotisha_jsonbaza.php";
class ModulJyotisha {
    private array $config;
    public function __construct() { $db = new ModulJyotishaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Jyotisha","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Jyotisha","vsebina"=>"<div class=\"modul-jyotisha\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
