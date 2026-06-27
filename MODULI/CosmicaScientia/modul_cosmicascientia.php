<?php
if (!defined('BRIDGE_VARNOST') && !defined('SISTEM_VARNOST')) {
    die('Direktni dostop je prepovedan');
}
require_once __DIR__ . '/modul_cosmicascientia_funkcije.php';
require_once __DIR__ . '/modul_cosmicascientia_pravila.php';
require_once __DIR__ . '/modul_cosmicascientia_jsonbaza.php';

class ModulCosmicaScientia {
    private $db;
    private $config;

    public function __construct() {
        $this->db = new ModulCosmicaScientiaJsonBaza();
        $this->config = $this->db->pridobiVse();
    }

    public function pridobiOsnovnePodatke() {
        return [
            'ime' => $this->config['ime'] ?? 'CosmicaScientia',
            'opis' => $this->config['opis'] ?? '',
            'razlicica' => $this->config['razlicica'] ?? '1.0.0',
            'avtor' => $this->config['avtor'] ?? '',
            'minimalna_vloga' => $this->config['minimalna_vloga'] ?? 'S0'
        ];
    }

    public function pridobiVsebino($param = []) {
        $html = \"<div class='modul-cosmicascientia'>\" . htmlspecialchars($this->config['opis'] ?? '') . \"</div>\";
        return ['naslov' => $this->config['opis'] ?? 'CosmicaScientia', 'vsebina' => $html];
    }

    public function obdelajZahtevek($akcija, $podatki = []) {
        return ['uspeh' => false, 'napaka' => \"Neznana akcija $akcija\"];
    }
}
?>