<?php
/**
 * DATOTEKA: polje_oblike.php
 * NAMEN:   Helper za generiranje vnosnih polj (input, textarea, select)
 * NIVO:    GLOBALNO (frontend)
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

/**
 * Generira vnosno polje tipa text.
 *
 * @param string $ime ime in id polja
 * @param string $vrednost privzeta vrednost
 * @param array $atributi dodatni atributi
 * @return string
 */
function polje_text(string $ime, string $vrednost = '', array $atributi = []): string
{
    $attrs = '';
    foreach ($atributi as $k => $v) {
        $attrs .= ' ' . htmlspecialchars((string)$k) . '="' . htmlspecialchars((string)$v) . '"';
    }

    return '<input type="text" name="' . htmlspecialchars($ime) . '" id="' . htmlspecialchars($ime) . '" value="' . htmlspecialchars($vrednost) . '" class="am-polje"' . $attrs . ' />';
}
