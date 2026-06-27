<?php
declare(strict_types=1);

if (!function_exists('globalno_gradnik_navigacija')) {
    function globalno_gradnik_navigacija(array $povezave, array $moznosti = []): string
    {
        $razred = 'navigacija' . (!empty($moznosti['razred']) ? ' ' . preg_replace('/[^a-z0-9_-]/i', '', (string)$moznosti['razred']) : '');
        $html = '<nav class="' . htmlspecialchars($razred) . '">';

        foreach ($povezave as $povezava) {
            if (!is_array($povezava)) {
                continue;
            }

            $ime = htmlspecialchars((string)($povezava['ime'] ?? ''));
            $href = htmlspecialchars((string)($povezava['href'] ?? '#'));
            $aktivna = !empty($povezava['aktivna']) ? ' aktivna' : '';

            $html .= '<a class="navigacija-povezava' . $aktivna . '" href="' . $href . '">' . $ime . '</a>';
        }

        $html .= '</nav>';
        return $html;
    }
}
