<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_umbraecodex_funkcije.php";
require_once __DIR__ . "/modul_umbraecodex_pravila.php";
require_once __DIR__ . "/modul_umbraecodex_jsonbaza.php";
class ModulUmbraeCodex {
    private array $config;
    public function __construct() { $db = new ModulUmbraeCodexJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"UmbraeCodex","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"UmbraeCodex","vsebina"=>"<div class=\"modul-umbraecodex\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
