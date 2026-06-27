<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/glasovno/stt_storitev.php
 * 📅 VERZIJA: v115 (14.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: STORITEV (BUSINESS LAYER)
 *
 * 📰 NAMEN:
 *     STT storitev – pretvori posnetek glasu v besedilo.
 *     Kliče zunanje API-je (Whisper, Azure) ali lokalni servis.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - stt_prepoznaj(string $avdioBase64, array $opcije): array
 *
 * 📡 ODVISNOSTI:
 *     - SISTEM/kernel/jedro/06_cache.php
 *     - Zunanji API (OpenAI Whisper, Azure)
 *
 * 🚫 PREPOVEDI:
 *     - Brez __DIR__
 *     - Brez echo, print_r, var_dump
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v115: uskladitev s Header Standard v115
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     storitev, glasovno, stt, whisper
 * ============================================================
 */
declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

function stt_prepoznaj(string $avdioBase64, array $opcije = []): array
{
    if (empty($avdioBase64)) {
        return [
            'status' => 'napaka',
            'sporocilo' => 'Avdio podatki manjkajo.'
        ];
    }
    
    $opcije = array_merge([
        'ponudnik' => 'auto',
        'jezik' => 'sl',
        'format' => 'webm'
    ], $opcije);
    
    // Izbira ponudnika
    $ponudnik = $opcije['ponudnik'];
    if ($ponudnik === 'auto') {
        $ponudnik = _stt_izberi_ponudnika();
    }
    
    $rezultat = match ($ponudnik) {
        'whisper' => _stt_whisper($avdioBase64, $opcije),
        'azure' => _stt_azure($avdioBase64, $opcije),
        default => [
            'status' => 'napaka',
            'sporocilo' => "Neznan STT ponudnik: $ponudnik"
        ]
    };
    
    return $rezultat;
}

function _stt_izberi_ponudnika(): string
{
    if (!empty(getenv('OPENAI_API_KEY'))) {
        return 'whisper';
    }
    if (!empty(getenv('AZURE_TTS_KEY'))) {
        return 'azure';
    }
    return 'browser';
}

function _stt_whisper(string $avdioBase64, array $opcije): array
{
    $apiKljuc = getenv('OPENAI_API_KEY');
    if (!$apiKljuc) {
        return [
            'status' => 'napaka',
            'sporocilo' => 'OpenAI API ključ ni nastavljen.'
        ];
    }
    
    // Shrani začasno datoteko
    $tmpPot = sys_get_temp_dir() . '/astra_stt_' . uniqid() . '.' . $opcije['format'];
    file_put_contents($tmpPot, base64_decode($avdioBase64));
    
    $ch = curl_init('https://api.openai.com/v1/audio/transcriptions');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'file' => new CURLFile($tmpPot, 'audio/' . $opcije['format'], 'posnetek.' . $opcije['format']),
            'model' => 'whisper-1',
            'language' => $opcije['jezik']
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $apiKljuc],
        CURLOPT_TIMEOUT => 30
    ]);
    
    $odgovor = curl_exec($ch);
    $httpKoda = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    unlink($tmpPot);
    
    if ($httpKoda !== 200) {
        return [
            'status' => 'napaka',
            'sporocilo' => 'Whisper STT napaka: HTTP ' . $httpKoda
        ];
    }
    
    $data = json_decode($odgovor, true);
    
    return [
        'status' => 'uspeh',
        'besedilo' => $data['text'] ?? '',
        'zaupanje' => 0.9,
        'jezik' => $opcije['jezik'],
        'ponudnik' => 'whisper'
    ];
}

function _stt_azure(string $avdioBase64, array $opcije): array
{
    // Azure STT implementacija (po potrebi)
    return [
        'status' => 'napaka',
        'sporocilo' => 'Azure STT še ni implementiran.'
    ];
}