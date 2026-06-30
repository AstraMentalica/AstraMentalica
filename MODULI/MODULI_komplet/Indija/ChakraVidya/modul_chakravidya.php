<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_chakravidya_funkcije.php";
require_once __DIR__ . "/modul_chakravidya_pravila.php";
require_once __DIR__ . "/modul_chakravidya_jsonbaza.php";
class ModulChakraVidya {
    private array $config;
    public function __construct() { $db = new ModulChakraVidyaJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"ChakraVidya","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"ChakraVidya","vsebina"=>"<div class=\"modul-chakravidya\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
