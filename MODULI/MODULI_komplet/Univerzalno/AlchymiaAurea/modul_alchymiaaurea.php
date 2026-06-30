<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_alchymiaaurea_funkcije.php";
require_once __DIR__ . "/modul_alchymiaaurea_pravila.php";
require_once __DIR__ . "/modul_alchymiaaurea_jsonbaza.php";
class ModulAlchymiaAurea {
    private array $config;
    public function __construct() { $db = new ModulAlchymiaAureaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"AlchymiaAurea","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"AlchymiaAurea","vsebina"=>"<div class=\"modul-alchymiaaurea\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
