<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_polynesiamystica_funkcije.php";
require_once __DIR__ . "/modul_polynesiamystica_pravila.php";
require_once __DIR__ . "/modul_polynesiamystica_jsonbaza.php";
class ModulPolynesiaMystica {
    private array $config;
    public function __construct() { $db = new ModulPolynesiaMysticaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"PolynesiaMystica","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"PolynesiaMystica","vsebina"=>"<div class=\"modul-polynesiamystica\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
