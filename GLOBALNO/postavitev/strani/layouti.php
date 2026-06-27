<?php
/**
 * ============================================================
 * GLOBALNO: layouti.php
 * POT: GLOBALNO/postavitev/strani/layouti.php
 * ============================================================
 *
 * Namen:
 *     Enoten resolver layoutov za strani v GLOBALNO.
 *     Stran lahko izbere svoj layout, sicer pade na osnovnega.
 *
 * Pravila:
 *     - Layout je samo vizualna sestava
 *     - Layout ne sme odločati o poslovni logiki
 *     - Moduli lahko kasneje dobijo svoj layout po imenu
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/gradniki/registri.php';

if (!function_exists('globalno_layouti_registry')) {
    function globalno_layouti_registry(): array
    {
        return [
            'osnova' => [
                'ime' => 'osnova',
                'opis' => 'Privzeti layout za standardne strani',
                'gradniki' => ['glava', 'navigacija', 'noga'],
            ],
            'enostaven' => [
                'ime' => 'enostaven',
                'opis' => 'Lahek layout za prijavo in minimalne strani',
                'gradniki' => ['glava', 'noga'],
            ],
            'modul' => [
                'ime' => 'modul',
                'opis' => 'Layout za posamezen modul',
                'gradniki' => ['glava', 'navigacija', 'kartica', 'noga'],
            ],
            'peskovnik' => [
                'ime' => 'peskovnik',
                'opis' => 'Layout za gradnike in interaktivne kompozicije',
                'gradniki' => ['glava', 'seznam', 'noga'],
            ],
        ];
    }
}

if (!function_exists('globalno_layout_najdi')) {
    function globalno_layout_najdi(?string $ime = null): array
    {
        $registry = globalno_layouti_registry();
        $kljuc = $ime ?: 'osnova';
        return $registry[$kljuc] ?? $registry['osnova'];
    }
}

if (!function_exists('globalno_layout_gradniki')) {
    function globalno_layout_gradniki(?string $ime = null): array
    {
        $layout = globalno_layout_najdi($ime);
        $rezultat = [];

        foreach ($layout['gradniki'] ?? [] as $gradnikIme) {
            $gradnik = globalno_gradnik_najdi($gradnikIme);
            if ($gradnik) {
                $rezultat[] = $gradnik;
            }
        }

        return $rezultat;
    }
}
