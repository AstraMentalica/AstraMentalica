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
 *     PRIORITETA: lokalni faster-whisper servis (brezplačno,
 *     enaka natančnost kot OpenAI API, brez pošiljanja
 *     podatkov zunaj) → Whisper API → Azure.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - stt_prepoznaj(string $avdioBase64, array $opcije): array
 *     - stt_podprta_jezika(): array
 *     - stt_lokalni_servis_zdrav(): bool
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - tts_storitev.php (za _tts_env, _tts_http_get)
 *     - Lokalni glasovni-servis (privzeto http://127.0.0.1:8088)
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, HTML
 *     - Brez trajnega shranjevanja avdio datotek
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v114: dodan 'lokalni' ponudnik (faster-whisper,
 *             brezplačno, lokalno) kot prva prioriteta
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     storitev, glasovno, stt, whisper, lokalno, faster-whisper
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

require_once __DIR__ . '/tts_storitev.php'; // _tts_env(), _tts_http_get(), TTS_LOKALNI_URL

// ============================================================
// GLAVNA FUNKCIJA
// ============================================================

/**
 * Pretvori avdio posnetek (base64) v besedilo.
 *
 * @param string $avdioBase64
 * @param array  $opcije
 *   ponudnik – 'lokalni'|'whisper'|'azure'|'auto' (privzeto: 'auto')
 *   jezik    – 'sl' (privzeto)
 *   format   – 'webm'|'mp3'|'wav' (privzeto: 'webm')
 *   prompt   – kontekst za boljšo prepoznavo domenskih besed
 *
 * @return array  status, besedilo, zaupanje, jezik, ponudnik, segmenti
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
        'prompt'   => 'AstraMentalica, astrologija, numerologija, meditacija, zavest, Codex, Aeternum, tarot, kabala',
    ], $opcije);

    $ponudnik = $opcije['ponudnik'];
    if ($ponudnik === 'auto') {
        $ponudnik = _stt_izberi_ponudnika();
    }

    $rezultat = match ($ponudnik) {
        'lokalni' => _stt_lokalni($avdioBase64, $opcije),
        'whisper' => _stt_whisper($avdioBase64, $opcije),
        'azure'   => _stt_azure($avdioBase64, $opcije),
        default   => ['status' => 'napaka', 'sporocilo' => "Neznan STT ponudnik: $ponudnik"],
    };

    // Samodejni fallback če lokalni servis ni dosegljiv
    if (($rezultat['status'] ?? '') === 'napaka' && $ponudnik === 'lokalni' && $opcije['ponudnik'] === 'auto') {
        $naslednji = _stt_izberi_ponudnika(['lokalni']);
        if ($naslednji !== 'lokalni') {
            return stt_prepoznaj($avdioBase64, array_merge($opcije, ['ponudnik' => $naslednji]));
        }
    }

    return $rezultat;
}

function stt_podprta_jezika(): array
{
    return ['sl', 'en', 'de', 'hr'];
}

function stt_lokalni_servis_zdrav(): bool
{
    $odziv = _tts_http_get(TTS_LOKALNI_URL . '/zdravje', 2);
    return $odziv['uspeh'] ?? false;
}

// ============================================================
// LOKALNI FASTER-WHISPER SERVIS (prioritetni, brezplačen)
// ============================================================

function _stt_lokalni(string $avdioBase64, array $opcije): array
{
    $tmpPot = sys_get_temp_dir() . '/astra_stt_' . uniqid() . '.' . $opcije['format'];
    $zapisano = file_put_contents($tmpPot, base64_decode($avdioBase64));

    if ($zapisano === false) {
        return ['status' => 'napaka', 'sporocilo' => 'Napaka pri pisanju začasne datoteke.'];
    }

    try {
        $polja = [
            'avdio'  => new CURLFile($tmpPot, 'audio/' . $opcije['format'], 'posnetek.' . $opcije['format']),
            'jezik'  => $opcije['jezik'],
            'prompt' => $opcije['prompt'] ?? '',
        ];

        $odziv = _stt_http_post_form(TTS_LOKALNI_URL . '/stt', $polja);

        if (!$odziv['uspeh']) {
            return [
                'status'    => 'napaka',
                'sporocilo' => 'Lokalni glasovni servis ni dosegljiv: ' . $odziv['napaka']
                    . ' (preveri da teče: uvicorn glasovni_servis:app --port 8088)',
            ];
        }

        $json = json_decode($odziv['vsebina'], true);
        if (!$json || empty($json['besedilo'])) {
            return ['status' => 'napaka', 'sporocilo' => 'Whisper ni vrnil besedila (morda tišina v posnetku).'];
        }

        return [
            'status'    => 'success',
            'besedilo'  => trim($json['besedilo']),
            'zaupanje'  => $json['zaupanje'] ?? 0.9,
            'jezik'     => $json['jezik'] ?? $opcije['jezik'],
            'ponudnik'  => 'lokalni',
            'segmenti'  => $json['segmenti'] ?? [],
        ];

    } finally {
        if (file_exists($tmpPot)) unlink($tmpPot);
    }
}

// ============================================================
// OPENAI WHISPER API (fallback)
// ============================================================

function _stt_whisper(string $avdioBase64, array $opcije): array
{
    $apiKljuc = _tts_env('OPENAI_API_KEY');
    if (!$apiKljuc) {
        return ['status' => 'napaka', 'sporocilo' => 'OpenAI API ključ ni nastavljen.'];
    }

    $tmpPot   = sys_get_temp_dir() . '/astra_stt_' . uniqid() . '.' . $opcije['format'];
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
            'response_format' => 'verbose_json',
            'prompt'          => $opcije['prompt'],
        ];

        curl_setopt_array($c, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $polja,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKljuc],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $odziv      = curl_exec($c);
        $httpKoda   = curl_getinfo($c, CURLINFO_HTTP_CODE);
        $napakaCurl = curl_error($c);
        curl_close($c);

        if ($napakaCurl) throw new RuntimeException('cURL: ' . $napakaCurl);
        if ($httpKoda !== 200) throw new RuntimeException("HTTP $httpKoda: $odziv");

        $json = json_decode($odziv, true);
        if (!$json || empty($json['text'])) {
            throw new RuntimeException('Prazni rezultati.');
        }

        $zaupanje = 0.9;
        if (!empty($json['segments'])) {
            $vsota = array_sum(array_column($json['segments'], 'avg_logprob'));
            $st    = count($json['segments']);
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
        return ['status' => 'napaka', 'sporocilo' => 'Azure STT: ' . ($json['RecognitionStatus'] ?? 'Neznana napaka')];
    }

    $najboljsi = $json['NBest'][0] ?? null;

    return [
        'status'   => 'success',
        'besedilo' => trim($json['DisplayText'] ?? ''),
        'zaupanje' => round((float)($najboljsi['Confidence'] ?? 0.8), 2),
        'jezik'    => $jezikKoda,
        'ponudnik' => 'azure',
    ];
}

// ============================================================
// IZBIRA PONUDNIKA
// ============================================================

function _stt_izberi_ponudnika(array $izkljuci = []): string
{
    if (!in_array('lokalni', $izkljuci) && stt_lokalni_servis_zdrav()) {
        return 'lokalni';
    }
    if (!in_array('whisper', $izkljuci) && !empty(_tts_env('OPENAI_API_KEY'))) {
        return 'whisper';
    }
    if (!in_array('azure', $izkljuci) && !empty(_tts_env('AZURE_TTS_KEY'))) {
        return 'azure';
    }
    return 'browser';
}

// ============================================================
// HTTP FORM-DATA POST (lokalni servis)
// ============================================================

function _stt_http_post_form(string $url, array $polja): array
{
    if (!function_exists('curl_init')) {
        return ['uspeh' => false, 'napaka' => 'cURL ni na voljo.'];
    }

    $c = curl_init($url);
    curl_setopt_array($c, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $polja,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60, // Whisper large-v3 na CPU je lahko počasen
    ]);

    $vsebina  = curl_exec($c);
    $httpKoda = curl_getinfo($c, CURLINFO_HTTP_CODE);
    $napaka   = curl_error($c);
    curl_close($c);

    if ($napaka) return ['uspeh' => false, 'napaka' => $napaka];

    if ($httpKoda < 200 || $httpKoda >= 300) {
        $sporocilo = "HTTP $httpKoda";
        $json = json_decode((string)$vsebina, true);
        if (isset($json['detail'])) {
            $sporocilo .= ': ' . (is_string($json['detail']) ? $json['detail'] : json_encode($json['detail']));
        }
        return ['uspeh' => false, 'napaka' => $sporocilo];
    }

    return ['uspeh' => true, 'vsebina' => $vsebina];
}
