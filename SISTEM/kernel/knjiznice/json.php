<?php
/**
 * ============================================================
 * POT: SISTEM/sistem_runtime/knjiznice/json.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     JSON upravljanje
 * 
 * 🔧 FUNKCIJE:
 *     - json_preberi(string $pot, bool $as_array = true): mixed
 *     - json_zapisi(string $pot, $data, int $flags = JSON_PRETTY_PRINT): bool
 *     - json_validiraj(string $json): bool
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 1
 * ============================================================
 */

function json_preberi(string $pot, bool $as_array = true): mixed
{
    if (!file_exists($pot)) {
        return $as_array ? [] : null;
    }
    
    $vsebina = file_get_contents($pot);
    if ($vsebina === false) {
        return $as_array ? [] : null;
    }
    
    return json_decode($vsebina, $as_array);
}

function json_zapisi(string $pot, $data, int $flags = JSON_PRETTY_PRINT): bool
{
    $mapa = dirname($pot);
    if (!is_dir($mapa)) {
        mkdir($mapa, 0755, true);
    }
    
    $json = json_encode($data, $flags | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }
    
    return file_put_contents($pot, $json) !== false;
}

function json_validiraj(string $json): bool
{
    json_decode($json);
    return json_last_error() === JSON_ERROR_NONE;
}

function json_pretvori($data): string
{
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}