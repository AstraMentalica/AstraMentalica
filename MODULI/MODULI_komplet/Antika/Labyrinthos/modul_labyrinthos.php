<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_labyrinthos_funkcije.php";
require_once __DIR__ . "/modul_labyrinthos_pravila.php";
require_once __DIR__ . "/modul_labyrinthos_jsonbaza.php";
class ModulLabyrinthos {
    private array $config;
    public function __construct() { $db = new ModulLabyrinthosJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"Labyrinthos","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"Labyrinthos","vsebina"=>"<div class=\"modul-labyrinthos\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
