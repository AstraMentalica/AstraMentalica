<?php
/**
 * ---------------------------------------------------------
 * POT: SISTEM/kernel/seja.php
 * v100 (10.06.2026)
 * ---------------------------------------------------------
 * OPIS: Upravljanje sej – prijava, odjava, preverjanje
 * ---------------------------------------------------------
 * FUNKCIJE:
 *     seja_zacni()
 *     seja_prijavi(id, ime, email, vloga)
 *     seja_odjavi()
 *     seja_je_prijavljen() : bool
 *     seja_pridobi_uporabnika() : array|null
 *     seja_pridobi(kljuc) : mixed
 *     seja_nastavi(kljuc, vrednost)
 *     seja_unset(kljuc)
 * ---------------------------------------------------------
 */
declare(strict_types=1);

function seja_zacni(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        $cas = (int)(getenv('CAS_POTEKA_SEJE') ?: 3600);
        session_set_cookie_params([
            'lifetime' => $cas,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }
}

function seja_prijavi(string $id, string $ime, string $email, string $vloga): void
{
    seja_zacni();
    session_regenerate_id(true);
    $_SESSION['uporabnik'] = [
        'id'    => $id,
        'ime'   => $ime,
        'email' => $email,
        'vloga' => $vloga,
        'cas'   => time()
    ];
}

function seja_odjavi(): void
{
    seja_zacni();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']
        );
    }
    session_destroy();
}

function seja_je_prijavljen(): bool
{
    seja_zacni();
    return !empty($_SESSION['uporabnik']['id']);
}

function seja_pridobi_uporabnika(): ?array
{
    seja_zacni();
    return $_SESSION['uporabnik'] ?? null;
}

function seja_pridobi(string $kljuc): mixed
{
    seja_zacni();
    return $_SESSION[$kljuc] ?? null;
}

function seja_nastavi(string $kljuc, mixed $vrednost): void
{
    seja_zacni();
    $_SESSION[$kljuc] = $vrednost;
}

function seja_unset(string $kljuc): void
{
    seja_zacni();
    unset($_SESSION[$kljuc]);
}