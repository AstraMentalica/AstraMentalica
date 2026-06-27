<?php
declare(strict_types=1);

if (!function_exists('globalno_gradnik_kartica')) {
    function globalno_gradnik_kartica(string $naslov, string $vsebina, array $moznosti = []): string
    {
        $razred = 'kartica' . (!empty($moznosti['razred']) ? ' ' . preg_replace('/[^a-z0-9_-]/i', '', (string)$moznosti['razred']) : '');
        $akcent = !empty($moznosti['akcent']) ? ' style="border-color:' . htmlspecialchars((string)$moznosti['akcent']) . ';"' : '';

        $gumbi = '';
        if (!empty($moznosti['gumbi']) && is_array($moznosti['gumbi'])) {
            $gumbi = '<div class="kartica-gumbi">';
            foreach ($moznosti['gumbi'] as $gumb) {
                $gumbi .= is_string($gumb) ? $gumb : '';
            }
            $gumbi .= '</div>';
        }

        return '<section class="' . htmlspecialchars($razred) . '"' . $akcent . '>'
            . '<h3>' . htmlspecialchars($naslov) . '</h3>'
            . '<div class="kartica-vsebina">' . $vsebina . '</div>'
            . $gumbi
            . '</section>';
    }
}
