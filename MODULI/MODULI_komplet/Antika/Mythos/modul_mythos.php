<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_mythos_funkcije.php";
require_once __DIR__ . "/modul_mythos_pravila.php";
require_once __DIR__ . "/modul_mythos_jsonbaza.php";
class ModulMythos {
    private array $config;
    public function __construct() { $db = new ModulMythosJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Mythos","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Mythos","vsebina"=>"<div class=\"modul-mythos\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
