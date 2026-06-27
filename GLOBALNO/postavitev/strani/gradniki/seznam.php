<?php
declare(strict_types=1);

if (!function_exists('globalno_gradnik_seznam')) {
    function globalno_gradnik_seznam(array $postavke, array $moznosti = []): string
    {
        $razred = 'seznam' . (!empty($moznosti['razred']) ? ' ' . preg_replace('/[^a-z0-9_-]/i', '', (string)$moznosti['razred']) : '');
        $html = '<div class="' . htmlspecialchars($razred) . '">';

        foreach ($postavke as $postavka) {
            if (is_string($postavka)) {
                $html .= '<div class="seznam-postavka">' . htmlspecialchars($postavka) . '</div>';
            } elseif (is_array($postavka)) {
                $besedilo = htmlspecialchars((string)($postavka['besedilo'] ?? $postavka['ime'] ?? ''));
                $povezava = (string)($postavka['povezava'] ?? '');

                if ($povezava !== '') {
                    $html .= '<a class="seznam-postavka" href="' . htmlspecialchars($povezava) . '">' . $besedilo . '</a>';
                } else {
                    $html .= '<div class="seznam-postavka">' . $besedilo . '</div>';
                }
            }
        }

        $html .= '</div>';
        return $html;
    }
}
