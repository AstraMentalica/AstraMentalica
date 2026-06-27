<?php
/**
 * ============================================================
 * POT: SISTEM/sistem_runtime/knjiznice/datoteke.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Upravljanje datotek
 * 
 * 🔧 FUNKCIJE:
 *     - datoteka_preberi(string $pot): ?string
 *     - datoteka_zapisi(string $pot, string $vsebina): bool
 *     - datoteka_obstaja(string $pot): bool
 *     - datoteka_zbrisi(string $pot): bool
 *     - mapa_ustvari(string $pot, int $permisije = 0755): bool
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 2b
 * ============================================================
 */

function datoteka_preberi(string $pot): ?string
{
    if (!file_exists($pot)) {
        return null;
    }
    
    $vsebina = file_get_contents($pot);
    return $vsebina === false ? null : $vsebina;
}

function datoteka_zapisi(string $pot, string $vsebina, int $flags = 0): bool
{
    $mapa = dirname($pot);
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }
    
    return file_put_contents($pot, $vsebina, $flags) !== false;
}

function datoteka_obstaja(string $pot): bool
{
    return file_exists($pot);
}

function datoteka_zbrisi(string $pot): bool
{
    if (!file_exists($pot)) {
        return true;
    }
    
    return unlink($pot);
}

function mapa_ustvari(string $pot, int $permisije = 0755): bool
{
    if (is_dir($pot)) {
        return true;
    }
    
    return mkdir($pot, $permisije, true);
}

function mapa_izbrisi(string $pot, bool $rekurzivno = false): bool
{
    if (!is_dir($pot)) {
        return false;
    }
    
    if ($rekurzivno) {
        $files = array_diff(scandir($pot), ['.', '..']);
        foreach ($files as $file) {
            $path = $pot . '/' . $file;
            if (is_dir($path)) {
                mapa_izbrisi($path, true);
            } else {
                unlink($path);
            }
        }
    }
    
    return rmdir($pot);
}