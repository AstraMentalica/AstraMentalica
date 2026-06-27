<?php
/**
 * ============================================================
 * POT: SISTEM/storitve_svetov/glasovno/tts_storitev.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: SISTEM (storitve_svetov)
 *
 * 📰 NAMEN:
 *     TTS storitev – pretvori besedilo v govor.
 *     PRIORITETA: lokalni XTTS-v2 servis (brezplačno, voice
 *     cloning, lasten naglas) → ElevenLabs → Azure → OpenAI.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - tts_sintetiziraj(string $besedilo, array $opcije): array
 *     - tts_pridobi_glasove(string $ponudnik): array
 *     - tts_predpomni_kljuc(string $besedilo, array $opcije): string
 *     - tts_lokalni_servis_zdrav(): bool
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - SISTEM/kernel/jedro/06_cache.php
 *     - PODATKI/sistem/env/kljuci.json
 *     - Lokalni glasovni-servis (privzeto http://127.0.0.1:8088)
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, HTML
 *     - Brez direktnega HTTP izhoda
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v114: dodan 'lokalni' ponudnik (XTTS-v2 voice cloning,
 *             brezplačno, samostojen mikroservis) kot prva
 *             prioriteta v auto-izbiri ponudnika
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     storitev, glasovno, tts, xtts, lokalno, elevenlabs, azure
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// KONFIGURACIJA
// ============================================================

define('TTS_LOKALNI_URL', _tts_env('GLASOVNI_SERVIS_URL') ?: 'http://127.0.0.1:8088');

define('TTS_GLASOVI', [
    // ----------------------------------------------------------
    // LOKALNI – XTTS-v2 voice cloning (brezplačno, prioriteta)
    // ----------------------------------------------------------
    'lokalni' => [
        'privzeti' => [
            'glas'  => 'privzeti',  // ime .wav v glasovni-servis/glasovi/
            'jezik' => 'sl',
            'naziv' => 'Privzeti kloniran glas',
        ],
        // Glasovi se naložijo dinamično iz /glasovi endpointa servisa,
        // ta seznam je samo fallback če servis ni dosegljiv
        'glasovi_fallback' => [
            'privzeti' => ['naziv' => 'Privzeti glas'],
        ],
    ],

    // ----------------------------------------------------------
    // ELEVENLABS (plačljivo, fallback)
    // ----------------------------------------------------------
    'elevenlabs' => [
        'privzeti' => [
            'id'      => 'pNInz6obpgDQGcFmaJgB',
            'model'   => 'eleven_multilingual_v2',
            'jezik'   => 'sl',
            'naziv'   => 'Adam (moški)',
        ],
        'glasovi' => [
            'adam'    => ['id' => 'pNInz6obpgDQGcFmaJgB', 'model' => 'eleven_multilingual_v2', 'naziv' => 'Adam – moški, globok'],
            'rachel'  => ['id' => '21m00Tcm4TlvDq8ikWAM', 'model' => 'eleven_multilingual_v2', 'naziv' => 'Rachel – ženski, umirjen'],
            'bella'   => ['id' => 'EXAVITQu4vr4xnSDxMaL', 'model' => 'eleven_multilingual_v2', 'naziv' => 'Bella – ženski, mehak'],
            'josh'    => ['id' => 'TxGEqnHWrfWFTfGW9XjX', 'model' => 'eleven_multilingual_v2', 'naziv' => 'Josh – moški, mlad'],
        ],
        'nastavitve_privzete' => [
            'stability'         => 0.5,
            'similarity_boost'  => 0.75,
            'style'             => 0.0,
            'use_speaker_boost' => true,
        ],
    ],

    // ----------------------------------------------------------
    // AZURE (plačljivo, fallback)
    // ----------------------------------------------------------
    'azure' => [
        'privzeti' => [
            'ime'   => 'sl-SI-PetraNeural',
            'jezik' => 'sl-SI',
            'naziv' => 'Petra (ženski, Neural)',
        ],
        'glasovi' => [
            'petra' => ['ime' => 'sl-SI-PetraNeural', 'spol' => 'Female', 'naziv' => 'Petra – ženski, Neural'],
            'rok'   => ['ime' => 'sl-SI-RokNeural',   'spol' => 'Male',   'naziv' => 'Rok – moški, Neural'],
        ],
        'format' => 'audio-24khz-96kbitrate-mono-mp3',
        'ssml_template' => '<speak version="1.0" xmlns="http://www.w3.org/2001/10/synthesis" xml:lang="{jezik}"><voice name="{glas}"><prosody rate="{hitrost}" pitch="{visina}">{besedilo}</prosody></voice></speak>',
    ],

    // ----------------------------------------------------------
    // OPENAI (plačljivo, zadnji fallback)
    // ----------------------------------------------------------
    'openai' => [
        'privzeti' => ['glas' => 'onyx', 'model' => 'tts-1-hd', 'naziv' => 'Onyx'],
        'glasovi'  => [
            'alloy' => 'Alloy', 'echo' => 'Echo', 'fable' => 'Fable',
            'onyx'  => 'Onyx',  'nova' => 'Nova',  'shimmer' => 'Shimmer',
        ],
    ],
]);

// ============================================================
// GLAVNA FUNKCIJA
// ============================================================

/**
 * Sintetizira besedilo v zvok.
 *
 * @param string $besedilo
 * @param array  $opcije
 *   ponudnik  – 'lokalni'|'elevenlabs'|'azure'|'openai'|'auto' (privzeto: 'auto')
 *   glas      – ime/ID glasu (ponudnik specifičen)
 *   jezik     – 'sl' (privzeto)
 *   finetune  – bool, samo za 'lokalni' – uporabi fine-tuned SL model
 *   hitrost   – 0.5–2.0
 *   format    – 'mp3'|'wav' (privzeto: ponudnikov privzeti)
 *   predpomni – bool
 *
 * @return array  status, avdio (base64), format, ponudnik, predpomni
 */
function tts_sintetiziraj(string $besedilo, array $opcije = []): array
{
    if (empty(trim($besedilo))) {
        return ['status' => 'napaka', 'sporocilo' => 'Besedilo je prazno.'];
    }

    $besedilo = mb_substr($besedilo, 0, 5000);

    $opcije = array_merge([
        'ponudnik'  => 'auto',
        'glas'      => null,
        'jezik'     => 'sl',
        'finetune'  => false,
        'hitrost'   => 1.0,
        'visina'    => 1.0,
        'format'    => 'mp3',
        'predpomni' => true,
    ], $opcije);

    if ($opcije['predpomni'] && function_exists('cache_pridobi')) {
        $kljuc    = tts_predpomni_kljuc($besedilo, $opcije);
        $iz_cache = cache_pridobi($kljuc);
        if ($iz_cache) {
            return array_merge($iz_cache, ['predpomni' => true]);
        }
    }

    $ponudnik = $opcije['ponudnik'];
    if ($ponudnik === 'auto') {
        $ponudnik = _tts_izberi_ponudnika();
    }

    $rezultat = match ($ponudnik) {
        'lokalni'    => _tts_lokalni($besedilo, $opcije),
        'elevenlabs' => _tts_elevenlabs($besedilo, $opcije),
        'azure'      => _tts_azure($besedilo, $opcije),
        'openai'     => _tts_openai($besedilo, $opcije),
        default      => ['status' => 'napaka', 'sporocilo' => "Neznan ponudnik: $ponudnik"],
    };

    // Če lokalni ni dosegljiv – samodejni fallback na naslednjega
    if (($rezultat['status'] ?? '') === 'napaka' && $ponudnik === 'lokalni' && $opcije['ponudnik'] === 'auto') {
        $naslednji = _tts_izberi_ponudnika(['lokalni']);
        if ($naslednji !== 'lokalni') {
            $rezultat = tts_sintetiziraj($besedilo, array_merge($opcije, ['ponudnik' => $naslednji]));
            return $rezultat;
        }
    }

    if ($opcije['predpomni'] && ($rezultat['status'] ?? '') === 'success' && function_exists('cache_shrani')) {
        cache_shrani($kljuc ?? tts_predpomni_kljuc($besedilo, $opcije), $rezultat, 86400);
    }

    return $rezultat;
}

function tts_predpomni_kljuc(string $besedilo, array $opcije): string
{
    return 'tts_' . md5($besedilo . json_encode($opcije));
}

/**
 * Vrne razpoložljive glasove za ponudnika.
 * Za 'lokalni' poskuša pridobiti dinamično iz servisa.
 */
function tts_pridobi_glasove(string $ponudnik = 'lokalni'): array
{
    if ($ponudnik === 'lokalni') {
        $dinamicni = _tts_lokalni_glasovi();
        if ($dinamicni !== null) {
            return $dinamicni;
        }
        return TTS_GLASOVI['lokalni']['glasovi_fallback'] ?? [];
    }

    return TTS_GLASOVI[$ponudnik]['glasovi'] ?? [];
}

/**
 * Preveri ali je lokalni glasovni servis dosegljiv.
 */
function tts_lokalni_servis_zdrav(): bool
{
    $odziv = _tts_http_get(TTS_LOKALNI_URL . '/zdravje', 2);
    return $odziv['uspeh'] ?? false;
}

// ============================================================
// LOKALNI XTTS-v2 SERVIS (prioritetni, brezplačen)
// ============================================================

function _tts_lokalni(string $besedilo, array $opcije): array
{
    $nastavitve = TTS_GLASOVI['lokalni'];
    $glas       = $opcije['glas'] ?? $nastavitve['privzeti']['glas'];
    $jezik      = $opcije['jezik'] ?? $nastavitve['privzeti']['jezik'];

    $polja = [
        'besedilo'         => $besedilo,
        'glas'             => $glas,
        'jezik'            => $jezik,
        'uporabi_finetune' => $opcije['finetune'] ? 'true' : 'false',
    ];

    $odziv = _tts_http_post_form(TTS_LOKALNI_URL . '/tts', $polja);

    if (!$odziv['uspeh']) {
        return [
            'status'    => 'napaka',
            'sporocilo' => 'Lokalni glasovni servis ni dosegljiv: ' . $odziv['napaka']
                . ' (preveri da teče: uvicorn glasovni_servis:app --port 8088)',
        ];
    }

    return [
        'status'    => 'success',
        'avdio'     => base64_encode($odziv['vsebina']),
        'format'    => 'wav', // XTTS vrača WAV
        'ponudnik'  => 'lokalni',
        'glas'      => $glas,
        'predpomni' => false,
    ];
}

function _tts_lokalni_glasovi(): ?array
{
    $odziv = _tts_http_get(TTS_LOKALNI_URL . '/glasovi', 2);
    if (!$odziv['uspeh']) {
        return null;
    }

    $json = json_decode($odziv['vsebina'], true);
    if (!isset($json['glasovi'])) {
        return null;
    }

    $rezultat = [];
    foreach ($json['glasovi'] as $g) {
        $rezultat[$g['id']] = [
            'naziv' => $g['naziv'] ?? $g['id'],
            'opis'  => $g['opis']  ?? '',
            'jezik' => $g['jezik'] ?? 'sl',
        ];
    }
    return $rezultat;
}

// ============================================================
// IZBIRA PONUDNIKA (auto)
// ============================================================

/**
 * @param array $izkljuci  Ponudniki ki naj se preskočijo (za fallback verigo)
 */
function _tts_izberi_ponudnika(array $izkljuci = []): string
{
    // 1. Lokalni servis – brezplačno, prioriteta
    if (!in_array('lokalni', $izkljuci) && tts_lokalni_servis_zdrav()) {
        return 'lokalni';
    }
    // 2. ElevenLabs
    if (!in_array('elevenlabs', $izkljuci) && !empty(_tts_env('ELEVENLABS_API_KEY'))) {
        return 'elevenlabs';
    }
    // 3. Azure
    if (!in_array('azure', $izkljuci) && !empty(_tts_env('AZURE_TTS_KEY'))) {
        return 'azure';
    }
    // 4. OpenAI
    if (!in_array('openai', $izkljuci) && !empty(_tts_env('OPENAI_API_KEY'))) {
        return 'openai';
    }
    // 5. Browser fallback (JS bo sam)
    return 'browser';
}

// ============================================================
// ELEVENLABS
// ============================================================

function _tts_elevenlabs(string $besedilo, array $opcije): array
{
    $apiKljuc = _tts_env('ELEVENLABS_API_KEY');
    if (!$apiKljuc) {
        return ['status' => 'napaka', 'sporocilo' => 'ElevenLabs API ključ ni nastavljen.'];
    }

    $nastavitve = TTS_GLASOVI['elevenlabs'];
    $glasId     = $opcije['glas'] ?? $nastavitve['privzeti']['id'];
    $model      = $opcije['model'] ?? $nastavitve['privzeti']['model'];
    $glasNast   = $nastavitve['nastavitve_privzete'];

    $telo = json_encode([
        'text'           => $besedilo,
        'model_id'       => $model,
        'language_code'  => 'sl',
        'voice_settings' => [
            'stability'         => (float)($opcije['stabilnost'] ?? $glasNast['stability']),
            'similarity_boost'  => (float)($opcije['podobnost']  ?? $glasNast['similarity_boost']),
            'style'             => (float)($opcije['slog']       ?? $glasNast['style']),
            'use_speaker_boost' => $glasNast['use_speaker_boost'],
        ],
    ]);

    $url = "https://api.elevenlabs.io/v1/text-to-speech/{$glasId}";

    $odziv = _tts_http_post($url, $telo, [
        'xi-api-key: ' . $apiKljuc,
        'Content-Type: application/json',
        'Accept: audio/mpeg',
    ]);

    if (!$odziv['uspeh']) {
        return ['status' => 'napaka', 'sporocilo' => 'ElevenLabs: ' . $odziv['napaka']];
    }

    return [
        'status'    => 'success',
        'avdio'     => base64_encode($odziv['vsebina']),
        'format'    => 'mp3',
        'ponudnik'  => 'elevenlabs',
        'glas_id'   => $glasId,
        'predpomni' => false,
    ];
}

// ============================================================
// AZURE NEURAL TTS
// ============================================================

function _tts_azure(string $besedilo, array $opcije): array
{
    $apiKljuc = _tts_env('AZURE_TTS_KEY');
    $regija   = _tts_env('AZURE_TTS_REGION') ?: 'westeurope';

    if (!$apiKljuc) {
        return ['status' => 'napaka', 'sporocilo' => 'Azure TTS API ključ ni nastavljen.'];
    }

    $nastavitve = TTS_GLASOVI['azure'];
    $glasIme    = $opcije['glas'] ?? $nastavitve['privzeti']['ime'];
    $hitrost    = _tts_hitrost_v_procente($opcije['hitrost'] ?? 1.0);
    $visina     = _tts_visina_v_poltonoch($opcije['visina'] ?? 1.0);

    $ssml = str_replace(
        ['{jezik}', '{glas}', '{hitrost}', '{visina}', '{besedilo}'],
        ['sl-SI', $glasIme, $hitrost, $visina, htmlspecialchars($besedilo, ENT_XML1)],
        $nastavitve['ssml_template']
    );

    $url = "https://{$regija}.tts.speech.microsoft.com/cognitiveservices/v1";

    $odziv = _tts_http_post($url, $ssml, [
        'Ocp-Apim-Subscription-Key: ' . $apiKljuc,
        'Content-Type: application/ssml+xml',
        'X-Microsoft-OutputFormat: ' . $nastavitve['format'],
        'User-Agent: AstraMentalica',
    ]);

    if (!$odziv['uspeh']) {
        return ['status' => 'napaka', 'sporocilo' => 'Azure TTS: ' . $odziv['napaka']];
    }

    return [
        'status'    => 'success',
        'avdio'     => base64_encode($odziv['vsebina']),
        'format'    => 'mp3',
        'ponudnik'  => 'azure',
        'glas'      => $glasIme,
        'predpomni' => false,
    ];
}

// ============================================================
// OPENAI TTS
// ============================================================

function _tts_openai(string $besedilo, array $opcije): array
{
    $apiKljuc = _tts_env('OPENAI_API_KEY');
    if (!$apiKljuc) {
        return ['status' => 'napaka', 'sporocilo' => 'OpenAI API ključ ni nastavljen.'];
    }

    $nastavitve = TTS_GLASOVI['openai'];
    $telo = json_encode([
        'model' => $opcije['model'] ?? $nastavitve['privzeti']['model'],
        'input' => $besedilo,
        'voice' => $opcije['glas'] ?? $nastavitve['privzeti']['glas'],
        'speed' => (float)($opcije['hitrost'] ?? 1.0),
    ]);

    $odziv = _tts_http_post('https://api.openai.com/v1/audio/speech', $telo, [
        'Authorization: Bearer ' . $apiKljuc,
        'Content-Type: application/json',
    ]);

    if (!$odziv['uspeh']) {
        return ['status' => 'napaka', 'sporocilo' => 'OpenAI TTS: ' . $odziv['napaka']];
    }

    return [
        'status'    => 'success',
        'avdio'     => base64_encode($odziv['vsebina']),
        'format'    => 'mp3',
        'ponudnik'  => 'openai',
        'predpomni' => false,
    ];
}

// ============================================================
// HTTP POMOČNIKI
// ============================================================

function _tts_http_post(string $url, string $telo, array $glave): array
{
    if (!function_exists('curl_init')) {
        return ['uspeh' => false, 'napaka' => 'cURL ni na voljo.'];
    }

    $c = curl_init($url);
    curl_setopt_array($c, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $telo,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $glave,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $vsebina  = curl_exec($c);
    $httpKoda = curl_getinfo($c, CURLINFO_HTTP_CODE);
    $napaka   = curl_error($c);
    curl_close($c);

    if ($napaka) return ['uspeh' => false, 'napaka' => $napaka];

    if ($httpKoda < 200 || $httpKoda >= 300) {
        $sporocilo = "HTTP $httpKoda";
        $json = json_decode($vsebina, true);
        if (isset($json['detail']['message'])) $sporocilo .= ': ' . $json['detail']['message'];
        elseif (isset($json['error']['message'])) $sporocilo .= ': ' . $json['error']['message'];
        elseif (isset($json['detail']) && is_string($json['detail'])) $sporocilo .= ': ' . $json['detail'];
        return ['uspeh' => false, 'napaka' => $sporocilo];
    }

    return ['uspeh' => true, 'vsebina' => $vsebina];
}

/**
 * POST kot form-data (za lokalni FastAPI servis – Form() parametri).
 */
function _tts_http_post_form(string $url, array $polja): array
{
    if (!function_exists('curl_init')) {
        return ['uspeh' => false, 'napaka' => 'cURL ni na voljo.'];
    }

    $c = curl_init($url);
    curl_setopt_array($c, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $polja, // array → multipart/form-data samodejno
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30, // XTTS na CPU je lahko počasen
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

function _tts_http_get(string $url, int $timeout = 5): array
{
    if (!function_exists('curl_init')) {
        return ['uspeh' => false, 'napaka' => 'cURL ni na voljo.'];
    }

    $c = curl_init($url);
    curl_setopt_array($c, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_CONNECTTIMEOUT => $timeout,
    ]);

    $vsebina  = curl_exec($c);
    $httpKoda = curl_getinfo($c, CURLINFO_HTTP_CODE);
    $napaka   = curl_error($c);
    curl_close($c);

    if ($napaka || $httpKoda !== 200) {
        return ['uspeh' => false, 'napaka' => $napaka ?: "HTTP $httpKoda"];
    }

    return ['uspeh' => true, 'vsebina' => $vsebina];
}

function _tts_hitrost_v_procente(float $hitrost): string
{
    $odstotek = (int)(($hitrost - 1.0) * 100);
    return $odstotek >= 0 ? "+{$odstotek}%" : "{$odstotek}%";
}

function _tts_visina_v_poltonoch(float $visina): string
{
    $poltoni = (int)(($visina - 1.0) * 12);
    return $poltoni >= 0 ? "+{$poltoni}st" : "{$poltoni}st";
}

function _tts_env(string $kljuc): string
{
    if (!empty($_ENV[$kljuc]))    return $_ENV[$kljuc];
    if (!empty($_SERVER[$kljuc])) return $_SERVER[$kljuc];
    $v = getenv($kljuc);
    if ($v !== false && $v !== '') return $v;

    static $envDatoteka = null;
    if ($envDatoteka === null) {
        $pot = defined('PODATKI_ENV') ? PODATKI_ENV . '/kljuci.json' : null;
        $envDatoteka = ($pot && file_exists($pot))
            ? (json_decode(file_get_contents($pot), true) ?? [])
            : [];
    }
    return (string)($envDatoteka[$kljuc] ?? '');
}
