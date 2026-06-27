<?php
/**
 * ============================================================
 * POT: ADAPTER/odzivi/adapter_serializacija.php
 * ============================================================
 * 
 * 📦 NAMEN:
 *     Pretvorba podatkov (JSON, XML, ...)
 * 
 * @author ASTRAMENTALICA
 * @version 1.0.0
 * @since FAZA 4b
 * ============================================================
 */

function adapter_serializacija_json(array $podatki): string
{
    return json_encode($podatki, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function adapter_serializacija_xml(array $podatki, string $korenski_element = 'odziv'): string
{
    $xml = new SimpleXMLElement("<{$korenski_element}/>");
    array_walk_recursive($podatki, function($value, $key) use ($xml) {
        $xml->addChild($key, htmlspecialchars($value));
    });
    return $xml->asXML();
}

function adapter_serializacija_po_kanalu(array $odziv, string $kanal): string
{
    switch ($kanal) {
        case 'api':
        case 'telegram':
        case 'facebook':
            return adapter_serializacija_json($odziv);
        case 'web':
            return ''; // HTML se obravnava posebej
        default:
            return adapter_serializacija_json($odziv);
    }
}