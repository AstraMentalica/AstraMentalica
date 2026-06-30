<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_aeternum_funkcije.php";
require_once __DIR__ . "/modul_aeternum_pravila.php";
require_once __DIR__ . "/modul_aeternum_jsonbaza.php";
class ModulAeternum {
    private array $config;
    public function __construct() { $db = new ModulAeternumJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Aeternum","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Aeternum","vsebina"=>"<div class=\"modul-aeternum\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
