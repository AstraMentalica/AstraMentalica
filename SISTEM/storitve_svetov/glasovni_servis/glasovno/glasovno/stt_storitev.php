<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/glasovno/stt_storitev.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM (storitve_svetov)
 *
 * 📰 NAMEN:
 *     STT storitev – pretvori posnetek glasu v besedilo.
 *     Primarno: OpenAI Whisper (najboljša SL natančnost ~95%+).
 *     Fallback: Azure Speech-to-Text (sl-SI).
 *     Browser Web Speech API je le zadnja možnost.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - stt_prepoznaj(string $avdioBase64, array $opcije): array
 *     - stt_podprta_jezika(): array
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - PODATKI/sistem/env/kljuci.json
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, HTML
 *     - Brez direktnega shranjevanja avdio datotek (samo začasno)
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v114: implementacija – Whisper + Azure STT
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     storitev, glasovno, stt, whisper, prepoznava
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

require_once __DIR__ . '/tts_storitev.php'; // Za _tts_env() in _tts_http_post()

// ============================================================
// GLAVNA FUNKCIJA
// ============================================================

/**
 * Pretvori avdio posnetek (base64) v besedilo.
 *
 * @param string $avdioBase64  Base64 zakodiran avdio (webm/mp3/wav)
 * @param array  $opcije
 *   ponudnik – 'whisper'|'azure'|'auto'
 *   jezik    – 'sl' (privzeto) – ISO 639-1
 *   format   – 'webm'|'mp3'|'wav' (privzeto: 'webm')
 *   prompt   – napotek za kontekst (npr. "AstraMentalica, astrologija")
 *
 * @return array
 *   status      – 'success'|'napaka'
 *   besedilo    – prepoznan tekst
 *   zaupanje    – 0.0–1.0 (zaupanje v prepoznavo)
 *   jezik       – zaznan jezik
 *   ponudnik    – kateri ponudnik je bil uporabljen
 */
function stt_prepoznaj(string $avdioBase64, array $opcije = []): array
{
    if (empty($avdioBase64)) {
        return ['status' => 'napaka', 'sporocilo' => 'Avdio podatki manjkajo.'];
    }

    $opcije = array_merge([
        'ponudnik' => 'auto',
        'jezik'    => 'sl',
        'format'   => 'webm',
        'prompt'   => 'AstraMentalica, astrologija, numerologija, meditacija, zavest',
    ], $opcije);

    $ponudnik = $opcije['ponudnik'];
    if ($ponudnik === 'auto') {
        $ponudnik = _stt_izberi_ponudnika();
    }

    return match ($ponudnik) {
        'whisper' => _stt_whisper($avdioBase64, $opcije),
        'azure'   => _stt_azure($avdioBase64, $opcije),
        default   => ['status' => 'napaka', 'sporocilo' => "Neznan STT ponudnik: $ponudnik"],
    };
}

function stt_podprta_jezika(): array
{
    return ['sl', 'en', 'de', 'hr'];
}

// ============================================================
// WHISPER (OpenAI) – najboljša točnost za SL
// ============================================================

function _stt_whisper(string $avdioBase64, array $opcije): array
{
    $apiKljuc = _tts_env('OPENAI_API_KEY');
    if (!$apiKljuc) {
        return ['status' => 'napaka', 'sporocilo' => 'OpenAI API ključ ni nastavljen.'];
    }

    // Shrani začasno datoteko
    $tmpPot  = sys_get_temp_dir() . '/astra_stt_' . uniqid() . '.' . $opcije['format'];
    $zapisano = file_put_contents($tmpPot, base64_decode($avdioBase64));

    if ($zapisano === false) {
        return ['status' => 'napaka', 'sporocilo' => 'Napaka pri pisanju začasne datoteke.'];
    }

    try {
        $c = curl_init('https://api.openai.com/v1/audio/transcriptions');

        $polja = [
            'file'            => new CURLFile($tmpPot, 'audio/' . $opcije['format'], 'posnetek.' . $opcije['format']),
            'model'           => 'whisper-1',
            'language'        => $opcije['jezik'],
            'response_format' => 'verbose_json', // Vrnemo zaupanje in segment podatke
            'prompt'          => $opcije['prompt'],
        ];

        curl_setopt_array($c, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $polja,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKljuc],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $odziv    = curl_exec($c);
        $httpKoda = curl_getinfo($c, CURLINFO_HTTP_CODE);
        $napakaCurl = curl_error($c);
        curl_close($c);

        if ($napakaCurl) throw new RuntimeException('cURL: ' . $napakaCurl);
        if ($httpKoda !== 200) throw new RuntimeException("HTTP $httpKoda: $odziv");

        $json = json_decode($odziv, true);
        if (!$json || empty($json['text'])) {
            throw new RuntimeException('Prazni rezultati.');
        }

        // Izračunaj povprečno zaupanje iz segmentov
        $zaupanje = 0.9;
        if (!empty($json['segments'])) {
            $vsota = array_sum(array_column($json['segments'], 'avg_logprob'));
            $st    = count($json['segments']);
            // Pretvori log-verjetnost v 0–1 (empirično)
            $zaupanje = min(1.0, max(0.0, exp($vsota / $st) + 0.5));
        }

        return [
            'status'    => 'success',
            'besedilo'  => trim($json['text']),
            'zaupanje'  => round($zaupanje, 2),
            'jezik'     => $json['language'] ?? $opcije['jezik'],
            'ponudnik'  => 'whisper',
            'segmenti'  => $json['segments'] ?? [],
        ];

    } catch (Throwable $e) {
        return ['status' => 'napaka', 'sporocilo' => 'Whisper STT: ' . $e->getMessage()];
    } finally {
        if (file_exists($tmpPot)) unlink($tmpPot);
    }
}

// ============================================================
// AZURE STT (sl-SI)
// ============================================================

function _stt_azure(string $avdioBase64, array $opcije): array
{
    $apiKljuc = _tts_env('AZURE_TTS_KEY');
    $regija   = _tts_env('AZURE_TTS_REGION') ?: 'westeurope';

    if (!$apiKljuc) {
        return ['status' => 'napaka', 'sporocilo' => 'Azure STT API ključ ni nastavljen.'];
    }

    $jezikKoda = match ($opcije['jezik']) {
        'sl'    => 'sl-SI',
        'en'    => 'en-US',
        'de'    => 'de-DE',
        'hr'    => 'hr-HR',
        default => 'sl-SI',
    };

    $url = "https://{$regija}.stt.speech.microsoft.com/speech/recognition/conversation/cognitiveservices/v1?language={$jezikKoda}&format=detailed";

    $avdioData = base64_decode($avdioBase64);

    $c = curl_init($url);
    curl_setopt_array($c, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $avdioData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Ocp-Apim-Subscription-Key: ' . $apiKljuc,
            'Content-Type: audio/webm; codecs=opus',
            'Accept: application/json',
        ],
        CURLOPT_TIMEOUT => 20,
    ]);

    $odziv    = curl_exec($c);
    $httpKoda = curl_getinfo($c, CURLINFO_HTTP_CODE);
    curl_close($c);

    if ($httpKoda !== 200) {
        return ['status' => 'napaka', 'sporocilo' => "Azure STT HTTP $httpKoda"];
    }

    $json = json_decode($odziv, true);

    if (($json['RecognitionStatus'] ?? '') !== 'Success') {
        return [
            'status'   => 'napaka',
            'sporocilo' => 'Azure STT: ' . ($json['RecognitionStatus'] ?? 'Neznana napaka'),
        ];
    }

    $najboljsi = $json['NBest'][0] ?? null;

    return [
        'status'    => 'success',
        'besedilo'  => trim($json['DisplayText'] ?? ''),
        'zaupanje'  => round((float)($najboljsi['Confidence'] ?? 0.8), 2),
        'jezik'     => $jezikKoda,
        'ponudnik'  => 'azure',
    ];
}

function _stt_izberi_ponudnika(): string
{
    if (!empty(_tts_env('OPENAI_API_KEY')))  return 'whisper';
    if (!empty(_tts_env('AZURE_TTS_KEY')))   return 'azure';
    return 'browser';
}
