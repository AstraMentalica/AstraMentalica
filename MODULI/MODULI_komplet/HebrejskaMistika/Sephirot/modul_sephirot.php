<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_sephirot_funkcije.php";
require_once __DIR__ . "/modul_sephirot_pravila.php";
require_once __DIR__ . "/modul_sephirot_jsonbaza.php";
class ModulSephirot {
    private array $config;
    public function __construct() { $db = new ModulSephirotJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Sephirot","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Sephirot","vsebina"=>"<div class=\"modul-sephirot\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
