<?php
declare(strict_types=1);

if (!function_exists('globalno_gradnik_obrazec')) {
    function globalno_gradnik_obrazec(string $akcija, array $polja = [], array $moznosti = []): string
    {
        $method = strtoupper((string)($moznosti['method'] ?? 'POST'));
        $razred = 'obrazec' . (!empty($moznosti['razred']) ? ' ' . preg_replace('/[^a-z0-9_-]/i', '', (string)$moznosti['razred']) : '');
        $html = '<form method="' . htmlspecialchars($method) . '" action="' . htmlspecialchars($akcija) . '" class="' . htmlspecialchars($razred) . '">';

        foreach ($polja as $polje) {
            if (!is_array($polje)) {
                continue;
            }

            $tip = (string)($polje['tip'] ?? 'text');
            $ime = (string)($polje['ime'] ?? '');
            $oznaka = (string)($polje['oznaka'] ?? $ime);
            $vrednost = htmlspecialchars((string)($polje['vrednost'] ?? ''));
            $place = htmlspecialchars((string)($polje['place'] ?? ''));

            $html .= '<div class="obrazec-skupina">'
                . '<label for="' . htmlspecialchars($ime) . '">' . htmlspecialchars($oznaka) . '</label>'
                . '<input type="' . htmlspecialchars($tip) . '" id="' . htmlspecialchars($ime) . '" name="' . htmlspecialchars($ime) . '" value="' . $vrednost . '" placeholder="' . $place . '">'
                . '</div>';
        }

        $html .= '</form>';
        return $html;
    }
}
