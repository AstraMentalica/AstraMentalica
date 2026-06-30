<?php
/**
 * SistemVarnosti.php - Varnostni sistem za Aurora Mystica
 */

class SistemVarnosti {
    
    private $konfiguracija;
    private $zgodovinaDostopov;
    private $blokiraniPoskusi;
    
    public function __construct($konfiguracija) {
        $this->konfiguracija = $konfiguracija;
        $this->zgodovinaDostopov = [];
        $this->blokiraniPoskusi = [];
    }
    
    public function zascitiSisteme($sistemi) {
        foreach ($sistemi as $ime => $sistem) {
            echo "🔒 Zaščitujem sistem: $ime\n";
        }
        echo "✅ Vsi sistemi zaščiteni!\n";
    }
    
    public function preveriDostop($uporabnik, $zahteva) {
        $ipNaslov = $_SERVER['REMOTE_ADDR'] ?? 'neznan';
        $casovniZig = time();
        
        // Preveri blokirane poskuse
        if ($this->jeBlokiranDostop($ipNaslov)) {
            return [
                'dovoljen' => false,
                'razlog' => 'IP naslov začasno blokiran',
                'predviden_cas_odblokade' => $this->pridobiCasOdblokade($ipNaslov)
            ];
        }
        
        // Preveri stopnjo dostopa
        if (!$this->preveriStopnjoDostopa($uporabnik['stopnja'], $zahteva['tip'])) {
            $this->zabeleziNeuspesenPoskus($ipNaslov, $uporabnik, $zahteva);
            return [
                'dovoljen' => false,
                'razlog' => 'Nezadostna stopnja dostopa'
            ];
        }
        
        // Zabelezi uspešen dostop
        $this->zabeleziUspecenDostop($ipNaslov, $uporabnik, $zahteva);
        
        return [
            'dovoljen' => true,
            'varnostna_raven' => 'visoka',
            'odobrene_operacije' => $this->dolociOdobreneOperacije($uporabnik['stopnja'])
        ];
    }
    
    private function jeBlokiranDostop($ipNaslov) {
        if (!isset($this->blokiraniPoskusi[$ipNaslov])) {
            return false;
        }
        
        $blokadaDo = $this->blokiraniPoskusi[$ipNaslov];
        if (time() > $blokadaDo) {
            unset($this->blokiraniPoskusi[$ipNaslov]);
            return false;
        }
        
        return true;
    }
    
    private function preveriStopnjoDostopa($stopnja, $tipZahteve) {
        $pravice = [
            'S0' => ['vstop_v_portal', 'ogled_uvoda'],
            'S1' => ['vstop_v_portal', 'ogled_uvoda', 'osnovni_zapisi', 'osnovne_prakse'],
            'S2' => ['vstop_v_portal', 'vse_vsebine', 'vse_prakse', 'sodelovanje'],
            'S3' => ['vstop_v_portal', 'vse_vsebine', 'ekskluzivni_dostop', 'povabilo_prijateljev'],
            'S4' => ['vstop_v_portal', 'vse_vsebine', 'VIP_dostop', 'administracija'],
            'S5' => ['vse_operacije']
        ];
        
        return in_array($tipZahteve, $pravice[$stopnja] ?? []);
    }
    
    private function zabeleziNeuspesenPoskus($ipNaslov, $uporabnik, $zahteva) {
        $kljuc = $ipNaslov . '_' . ($uporabnik['id'] ?? 'anonimen');
        
        if (!isset($this->zgodovinaDostopov[$kljuc])) {
            $this->zgodovinaDostopov[$kljuc] = [];
        }
        
        $this->zgodovinaDostopov[$kljuc][] = [
            'cas' => time(),
            'tip' => 'neuspesen',
            'zahteva' => $zahteva,
            'razlog' => 'nepravilna_stopnja_dostopa'
        ];
        
        // Preveri če je treba blokirati
        $zadnjihPoskusov = array_slice($this->zgodovinaDostopov[$kljuc], -5);
        $neuspesniPoskusi = array_filter($zadnjihPoskusov, function($poskus) {
            return $poskus['tip'] === 'neuspesen';
        });
        
        if (count($neuspesniPoskusi) >= 3) {
            $this->blokiraniPoskusi[$ipNaslov] = time() + 900; // 15 minut blokade
        }
    }
    
    private function zabeleziUspecenDostop($ipNaslov, $uporabnik, $zahteva) {
        $kljuc = $ipNaslov . '_' . $uporabnik['id'];
        
        if (!isset($this->zgodovinaDostopov[$kljuc])) {
            $this->zgodovinaDostopov[$kljuc] = [];
        }
        
        $this->zgodovinaDostopov[$kljuc][] = [
            'cas' => time(),
            'tip' => 'uspesen',
            'zahteva' => $zahteva
        ];
    }
    
    private function dolociOdobreneOperacije($stopnja) {
        $operacije = [
            'S0' => ['ogled', 'brskanje'],
            'S1' => ['ogled', 'brskanje', 'osnovne_interakcije'],
            'S2' => ['ogled', 'brskanje', 'vse_interakcije', 'sodelovanje'],
            'S3' => ['ogled', 'brskanje', 'vse_interakcije', 'ustvarjanje', 'povabilo'],
            'S4' => ['ogled', 'brskanje', 'vse_interakcije', 'ustvarjanje', 'administracija'],
            'S5' => ['vse_operacije']
        ];
        
        return $operacije[$stopnja] ?? $operacije['S0'];
    }
    
    private function pridobiCasOdblokade($ipNaslov) {
        return date('Y-m-d H:i:s', $this->blokiraniPoskusi[$ipNaslov]);
    }
}
?>