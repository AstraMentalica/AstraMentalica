<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_codex_funkcije.php";
require_once __DIR__ . "/modul_codex_pravila.php";
require_once __DIR__ . "/modul_codex_jsonbaza.php";
class ModulCodex {
    private array $config;
    public function __construct() { $db = new ModulCodexJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Codex","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Codex","vsebina"=>"<div class=\"modul-codex\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
