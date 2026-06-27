<?php
namespace Stelaris\Pripomocki;

class Pomoznik {
    public static function formatirajDatum(\DateTime $datum): string {
        return $datum->format('d. m. Y H:i');
    }
    
    public static function preveriEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}