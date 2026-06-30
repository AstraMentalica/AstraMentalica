<?php
if (!defined("BRIDGE_VARNOST") && !defined("SISTEM_VARNOST")) die("Direktni dostop je prepovedan");
require_once __DIR__ . "/modul_oraculumvisionis_funkcije.php";
require_once __DIR__ . "/modul_oraculumvisionis_pravila.php";
require_once __DIR__ . "/modul_oraculumvisionis_jsonbaza.php";
class ModulOraculumVisionis {
    private array $config;
    public function __construct() { $db = new ModulOraculumVisionisJsonBaza(); $this->config = $db->pridobiVse(); }
    public function pridobiOsnovnePodatke(): array { return ["ime"=>$this->config["ime"]??"OraculumVisionis","opis"=>$this->config["opis"]??""]; }
    public function pridobiVsebino(array $param = []): array { return ["naslov"=>"OraculumVisionis","vsebina"=>"<div class=\"modul-oraculumvisionis\">" . htmlspecialchars($this->config["opis"]??"") . "</div>"]; }
}
