<?php
declare(strict_types=1);

if (!function_exists('globalno_gradnik_gumb')) {
    function globalno_gradnik_gumb(string $napis, string $stil = 'primarni', array $dodatki = []): string
    {
        $razredi = 'gumb gumb-' . preg_replace('/[^a-z0-9_-]/i', '', $stil);
        $dodatniAtributi = '';

        foreach ($dodatki as $ime => $vrednost) {
            $dodatniAtributi .= ' ' . htmlspecialchars((string)$ime) . '="' . htmlspecialchars((string)$vrednost) . '"';
        }

        return '<button class="' . htmlspecialchars($razredi) . '"' . $dodatniAtributi . '>'
            . htmlspecialchars($napis)
            . '</button>';
    }
}
