<?php
declare(strict_types=1);
/**
 * odjava.php – Backend odjave
 * AstraMentalica v7.0
 */

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

registriraj_pot('odjava', function($p) {
    seja_uniti();
    return api_uspeh(['sporocilo' => 'Uspešno odjavljen.']);
});