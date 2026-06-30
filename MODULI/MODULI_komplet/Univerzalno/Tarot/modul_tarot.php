<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_tarot_funkcije.php";
require_once __DIR__ . "/modul_tarot_pravila.php";
require_once __DIR__ . "/modul_tarot_jsonbaza.php";
class ModulTarot {
    private array $config;
    public function __construct() { $db = new ModulTarotJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Tarot","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Tarot","vsebina"=>"<div class=\"modul-tarot\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
