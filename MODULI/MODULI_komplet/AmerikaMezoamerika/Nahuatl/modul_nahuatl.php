<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_nahuatl_funkcije.php";
require_once __DIR__ . "/modul_nahuatl_pravila.php";
require_once __DIR__ . "/modul_nahuatl_jsonbaza.php";
class ModulNahuatl {
    private array $config;
    public function __construct() { $db = new ModulNahuatlJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Nahuatl","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Nahuatl","vsebina"=>"<div class=\"modul-nahuatl\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
