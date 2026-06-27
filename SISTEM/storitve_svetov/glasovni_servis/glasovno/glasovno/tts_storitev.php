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
 *     TTS storitev – pretvori besedilo v govor z naravnim
 *     slovenskim glasom. Multi-provider: ElevenLabs → Azure → fallback.
 *     Odgovorna samo za pripravo zvoka, ne za HTTP izhod.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - tts_sintetiziraj(string $besedilo, array $opcije): array
 *     - tts_pridobi_glasove(string $ponudnik): array
 *     - tts_predpomni_kljuc(string $besedilo, array $opcije): string
 *
 * 📡 ODVISNOSTI:
 *     - pot.php
 *     - SISTEM/kernel/jedro/06_cache.php
 *     - SISTEM/kernel/knjiznice/kriptografija.php
 *     - PODATKI/sistem/env/ (.env ključi)
 *
 * 🚫 PREPOVEDI:
 *     - Brez echo, HTML
 *     - Brez direktnega HTTP izhoda
 *     - Brez $_GET/$_POST direktno
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 📅 ZGODOVINA:
 *     - v114: implementacija – ElevenLabs, Azure Neural, browser fallback
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     storitev, glasovno, tts, elevenlabs, azure, slovenscina
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

// ============================================================
// KONFIGURACIJA GLASOV
// ============================================================

define('TTS_GLASOVI', [
    'elevenlabs' => [
        // ElevenLabs glasovi optimizirani za slovenščino
        // Multilingual v2 model – razume SL naravno
        'privzeti' => [
            'id'      => 'pNInz6obpgDQGcFmaJgB', // "Adam" – globok moški
            'model'   => 'eleven_multilingual_v2',
            'jezik'   => 'sl',
            'naziv'   => 'Adam (moški)',
        ],
        'glasovi' => [
            'adam'    => ['id' => 'pNInz6obpgDQGcFmaJgB', 'model' => 'eleven_multilingual_v2', 'naziv' => 'Adam – moški, globok'],
            'rachel'  => ['id' => '21m00Tcm4TlvDq8ikWAM', 'model' => 'eleven_multilingual_v2', 'naziv' => 'Rachel – ženski, umirjen'],
            'domi'    => ['id' => 'AZnzlk1XvdvUeBnXmlld', 'model' => 'eleven_multilingual_v2', 'naziv' => 'Domi – ženski, energičen'],
            'bella'   => ['id' => 'EXAVITQu4vr4xnSDxMaL', 'model' => 'eleven_multilingual_v2', 'naziv' => 'Bella – ženski, mehak'],
            'josh'    => ['id' => 'TxGEqnHWrfWFTfGW9XjX', 'model' => 'eleven_multilingual_v2', 'naziv' => 'Josh – moški, mlad'],
            'custom'  => ['id' => null, 'model' => 'eleven_multilingual_v2', 'naziv' => 'Klonirani glas'],
        ],
        'nastavitve_privzete' => [
            'stability'        => 0.5,  // 0–1 (višje = bolj stabilno)
            'similarity_boost' => 0.75, // 0–1 (višje = bolj podobno originalu)
            'style'            => 0.0,  // 0–1 (ekspresivnost)
            'use_speaker_boost'=> true,
        ],
    ],
    'azure' => [
        // Azure Neural – edina storitev z dediciranima SL glasovoma
        'privzeti' => [
            'ime'     => 'sl-SI-PetraNeural', // ženski
            'jezik'   => 'sl-SI',
            'naziv'   => 'Petra (ženski, Neural)',
        ],
        'glasovi' => [
            'petra' => ['ime' => 'sl-SI-PetraNeural', 'spol' => 'Female', 'naziv' => 'Petra – ženski, Neural'],
            'rok'   => ['ime' => 'sl-SI-RokNeural',   'spol' => 'Male',   'naziv' => 'Rok – moški, Neural'],
        ],
        'format' => 'audio-24khz-96kbitrate-mono-mp3',
        'ssml_template' => '<speak version="1.0" xmlns="http://www.w3.org/2001/10/synthesis" xml:lang="{jezik}"><voice name="{glas}"><prosody rate="{hitrost}" pitch="{visina}">{besedilo}</prosody></voice></speak>',
    ],
    'openai' => [
        'privzeti' => [
            'glas'  => 'onyx',
            'model' => 'tts-1-hd',
            'naziv' => 'Onyx',
        ],
        'glasovi' => [
            'alloy'   => 'Alloy – nevtralen',
            'echo'    => 'Echo – moški',
            'fable'   => 'Fable – britanski',
            'onyx'    => 'Onyx – globok moški',
            'nova'    => 'Nova – ženski, živahen',
            'shimmer' => 'Shimmer – ženski, mehak',
        ],
    ],
]);

// ============================================================
// GLAVNA FUNKCIJA
// ============================================================

/**
 * Sintetizira besedilo v zvok.
 *
 * @param string $besedilo   Besedilo za pretvorbo
 * @param array  $opcije
 *   ponudnik  – 'elevenlabs'|'azure'|'openai'|'auto' (privzeto: 'auto')
 *   glas      – ID/ime glasu (ponudnik specifičen)
 *   hitrost   – 0.5–2.0 (privzeto: 1.0)
 *   visina    – 0.5–2.0 (privzeto: 1.0, samo Azure)
 *   format    – 'mp3'|'wav' (privzeto: 'mp3')
 *   predpomni – bool (privzeto: true)
 *
 * @return array
 *   status    – 'success'|'napaka'
 *   avdio     – base64 zakodiran zvok
 *   format    – 'mp3'
 *   ponudnik  – kateri ponudnik je bil uporabljen
 *   predpomni – bool ali iz predpomnilnika
 *   sporocilo – opis napake
 */
function tts_sintetiziraj(string $besedilo, array $opcije = []): array
{
    if (empty(trim($besedilo))) {
        return ['status' => 'napaka', 'sporocilo' => 'Besedilo je prazno.'];
    }

    // Omeji dolžino (varnostno)
    $besedilo = mb_substr($besedilo, 0, 5000);

    $opcije = array_merge([
        'ponudnik'  => 'auto',
        'glas'      => null,
        'hitrost'   => 1.0,
        'visina'    => 1.0,
        'format'    => 'mp3',
        'predpomni' => true,
    ], $opcije);

    // Preveri predpomnilnik
    if ($opcije['predpomni'] && function_exists('cache_pridobi')) {
        $kljuc    = tts_predpomni_kljuc($besedilo, $opcije);
        $iz_cache = cache_pridobi($kljuc);
        if ($iz_cache) {
            return array_merge($iz_cache, ['predpomni' => true]);
        }
    }

    // Določi ponudnika
    $ponudnik = $opcije['ponudnik'];
    if ($ponudnik === 'auto') {
        $ponudnik = _tts_izberi_ponudnika();
    }

    // Sintetiziraj
    $rezultat = match ($ponudnik) {
        'elevenlabs' => _tts_elevenlabs($besedilo, $opcije),
        'azure'      => _tts_azure($besedilo, $opcije),
        'openai'     => _tts_openai($besedilo, $opcije),
        default      => ['status' => 'napaka', 'sporocilo' => "Neznan ponudnik: $ponudnik"],
    };

    // Shrani v predpomnilnik (24h)
    if ($opcije['predpomni'] && ($rezultat['status'] ?? '') === 'success' && function_exists('cache_shrani')) {
        cache_shrani($kljuc ?? tts_predpomni_kljuc($besedilo, $opcije), $rezultat, 86400);
    }

    return $rezultat;
}

function tts_predpomni_kljuc(string $besedilo, array $opcije): string
{
    return 'tts_' . md5($besedilo . json_encode($opcije));
}

function tts_pridobi_glasove(string $ponudnik = 'elevenlabs'): array
{
    return TTS_GLASOVI[$ponudnik]['glasovi'] ?? [];
}

// ============================================================
// IZBIRA PONUDNIKA (auto)
// ============================================================

function _tts_izberi_ponudnika(): string
{
    // Prioriteta: ElevenLabs → Azure → OpenAI
    if (!empty(_tts_env('ELEVENLABS_API_KEY'))) return 'elevenlabs';
    if (!empty(_tts_env('AZURE_TTS_KEY')))      return 'azure';
    if (!empty(_tts_env('OPENAI_API_KEY')))      return 'openai';

    return 'browser'; // Fallback – JS bo sam
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
            'stability'         => (float)($opcije['stabilnost']  ?? $glasNast['stability']),
            'similarity_boost'  => (float)($opcije['podobnost']   ?? $glasNast['similarity_boost']),
            'style'             => (float)($opcije['slog']        ?? $glasNast['style']),
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
// AZURE NEURAL TTS (Petra / Rok)
// ============================================================

function _tts_azure(string $besedilo, array $opcije): array
{
    $apiKljuc = _tts_env('AZURE_TTS_KEY');
    $regija   = _tts_env('AZURE_TTS_REGION') ?: 'westeurope';

    if (!$apiKljuc) {
        return ['status' => 'napaka', 'sporocilo' => 'Azure TTS API ključ ni nastavljen.'];
    }

    $nastavitve   = TTS_GLASOVI['azure'];
    $glasIme      = $opcije['glas'] ?? $nastavitve['privzeti']['ime'];
    $hitrost      = _tts_hitrost_v_procente($opcije['hitrost'] ?? 1.0);
    $visina       = _tts_visina_v_poltonoch($opcije['visina'] ?? 1.0);

    // SSML za boljši nadzor nad glasom
    $ssml = str_replace(
        ['{jezik}', '{glas}',    '{hitrost}', '{visina}',  '{besedilo}'],
        ['sl-SI',   $glasIme,    $hitrost,    $visina,     htmlspecialchars($besedilo, ENT_XML1)],
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
// OPENAI TTS (fallback)
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
// POMOČNIKI
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

    if ($napaka) {
        return ['uspeh' => false, 'napaka' => $napaka];
    }

    if ($httpKoda < 200 || $httpKoda >= 300) {
        $sporocilo = "HTTP $httpKoda";
        // Poskusi razčleniti JSON napako
        $json = json_decode($vsebina, true);
        if (isset($json['detail']['message'])) $sporocilo .= ': ' . $json['detail']['message'];
        elseif (isset($json['error']['message'])) $sporocilo .= ': ' . $json['error']['message'];
        return ['uspeh' => false, 'napaka' => $sporocilo];
    }

    return ['uspeh' => true, 'vsebina' => $vsebina];
}

function _tts_hitrost_v_procente(float $hitrost): string
{
    // Azure pričakuje "+10%" ali "-5%"
    $odstotek = (int)(($hitrost - 1.0) * 100);
    return $odstotek >= 0 ? "+{$odstotek}%" : "{$odstotek}%";
}

function _tts_visina_v_poltonoch(float $visina): string
{
    // Azure pričakuje "+2st" ali "-3st"
    $poltoni = (int)(($visina - 1.0) * 12);
    return $poltoni >= 0 ? "+{$poltoni}st" : "{$poltoni}st";
}

function _tts_env(string $kljuc): string
{
    // 1. Iz $_ENV / $_SERVER
    if (!empty($_ENV[$kljuc]))    return $_ENV[$kljuc];
    if (!empty($_SERVER[$kljuc])) return $_SERVER[$kljuc];
    // 2. Iz getenv()
    $v = getenv($kljuc);
    if ($v !== false && $v !== '') return $v;
    // 3. Iz PODATKI/sistem/env/.env JSON
    static $envDatoteka = null;
    if ($envDatoteka === null) {
        $pot = defined('PODATKI_ENV') ? PODATKI_ENV . '/kljuci.json' : null;
        $envDatoteka = ($pot && file_exists($pot))
            ? (json_decode(file_get_contents($pot), true) ?? [])
            : [];
    }
    return (string)($envDatoteka[$kljuc] ?? '');
}
