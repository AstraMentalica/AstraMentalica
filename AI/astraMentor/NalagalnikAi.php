<?php
// =============================================================================
// AstraMentalica - Nalagalnik AI Modulov
// =============================================================================
// Ta razred samodejno naloži vse AI module (GPT, Gemini, Deepseek) 
// in uporablja ključe iz .env
// =============================================================================

<?php
// =============================================================================
// NALAGALNIK AI MODULOV - AstraMentalica
// =============================================================================
// Ta razred skrbi za nalaganje samostojnih AI modulov, preverjanje
// ključev in pripravo vsebine za uporabo v sistemu.
// ============================================================================

class NalagalnikAI {
    // Shranjeni naloženi moduli
    private static $nalozeni_moduli = [];

    // ========================================================================
    // Naloži AI modul
    // ========================================================================
    // $ime_modula : ime modula brez .php
    public static function naloziModul($ime_modula) {
        // Preveri, če ima uporabnik veljaven AI ključ
        self::preveriAIKljuc($ime_modula);

        // Pot do datoteke modula
        $pot_modula = __DIR__ . '/' . $ime_modula . '.php';

        if (!file_exists($pot_modula)) {
            return [
                'napaka' => true,
                'sporocilo' => "Modul '$ime_modula' ne obstaja."
            ];
        }

        // Vključi modul in zajemi izhod
        ob_start();
        try {
            include $pot_modula;
            $vsebina = ob_get_clean();

            // Zabeleži, da je modul naložen
            self::$nalozeni_moduli[$ime_modula] = [
                'nalozen' => time(),
                'pot' => $pot_modula
            ];

            return [
                'uspesno' => true,
                'vsebina' => $vsebina
            ];
        } catch (Exception $e) {
            ob_end_clean();
            return [
                'napaka' => true,
                'sporocilo' => "Napaka pri nalaganju modula: " . $e->getMessage()
            ];
        }
    }

    // ========================================================================
    // Preveri AI ključ za modul
    // ========================================================================
    private static function preveriAIKljuc($ime_modula) {
        // Tukaj se lahko vključi logika za preverjanje ključev
        // npr. DEEPSEEK, GEMINI ali drugi AI ključi
        if (!defined('DEEPSEEK_API_KEY') || empty(DEEPSEEK_API_KEY)) {
            die("❌ AI ključ za modul '$ime_modula' ni definiran.");
        }
    }

    // ========================================================================
    // Vrni seznam naloženih modulov
    // ========================================================================


    // ========================================================================
    // 2. Vrni seznam naloženih modulov
    // ========================================================================
    public static function vrniNalozeneModule() {
        return self::$nalozeni_moduli;
    }

    // ========================================================================
    // 3. Samodejno naloži vse AI module (če obstajajo)
    // ========================================================================
    public static function samodejnoNaloziVse() {
        $moduli = ['povezava_ai', 'gemini', 'deepseek']; // imena datotek brez .php
        $rezultati = [];

        foreach ($moduli as $modul) {
            $rezultati[$modul] = self::naloziModul($modul);
        }

        return $rezultati;
    }
}

// ============================================================================
// Samodejno naloži vse AI module ob includu
// ============================================================================
NalagalnikAI::samodejnoNaloziVse();
?>