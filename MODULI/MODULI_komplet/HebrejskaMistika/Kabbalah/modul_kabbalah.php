<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_kabbalah_funkcije.php";
require_once __DIR__ . "/modul_kabbalah_pravila.php";
require_once __DIR__ . "/modul_kabbalah_jsonbaza.php";
class ModulKabbalah {
    private array $config;
    public function __construct() { $db = new ModulKabbalahJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Kabbalah","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Kabbalah","vsebina"=>"<div class=\"modul-kabbalah\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
