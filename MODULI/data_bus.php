<?php
/**
 * MODULI/data_bus.php
 * Centralni preprost podatkovni vmesnik (publish/subscribe) za izmenjavo med moduli.
 * Shrani v PODATKI/data_bus.json; namenjen modularni komunikaciji preko podatkov.
 *
 * Funkcije:
 *  - data_bus_publish(string $topic, array $payload): array
 *  - data_bus_fetch(string $topic = null, int $limit = 50, ?string $since = null): array
 *  - data_bus_list_topics(): array
 *  - data_bus_clear_topic(string $topic): bool
 *
 * Varnost/omejitve: ta implementacija je enostavna datotečna; za produkcijo uporabiti DB/queue.
 */

declare(strict_types=1);

define('DATA_BUS_PATH', __DIR__ . '/../PODATKI/data_bus.json');

function _data_bus_load(): array {
    $path = DATA_BUS_PATH;
    if (!file_exists($path)) return ['topics' => []];
    $json = @file_get_contents($path);
    if (!is_string($json)) return ['topics' => []];
    $data = json_decode($json, true);
    return is_array($data) ? $data : ['topics' => []];
}

function _data_bus_save(array $data): bool {
    $path = DATA_BUS_PATH;
    $dir = dirname($path);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

/**
 * Publish payload to topic.
 * Returns published entry.
 */
function data_bus_publish(string $topic, array $payload): array {
    $data = _data_bus_load();
    $topics = $data['topics'] ?? [];
    if (!isset($topics[$topic])) $topics[$topic] = [];
    $entry = [
        'id' => bin2hex(random_bytes(8)),
        'cas' => date('c'),
        'topic' => $topic,
        'payload' => $payload
    ];
    $topics[$topic][] = $entry;
    $data['topics'] = $topics;
    _data_bus_save($data);
    return $entry;
}

/**
 * Fetch entries. If $topic null, returns all topics flattened (limited).
 * $since can be ISO8601 datetime to get newer entries.
 */
function data_bus_fetch(?string $topic = null, int $limit = 50, ?string $since = null): array {
    $data = _data_bus_load();
    $out = [];
    $sinceTs = $since ? strtotime($since) : null;
    if ($topic === null) {
        foreach ($data['topics'] ?? [] as $t => $entries) {
            foreach (array_reverse($entries) as $e) {
                if ($sinceTs && strtotime($e['cas']) <= $sinceTs) continue;
                $out[] = $e;
                if (count($out) >= $limit) break 2;
            }
        }
        usort($out, fn($a,$b)=>strtotime($b['cas']) <=> strtotime($a['cas']));
        return $out;
    }
    $entries = $data['topics'][$topic] ?? [];
    $entries = array_reverse($entries);
    $filtered = [];
    foreach ($entries as $e) {
        if ($sinceTs && strtotime($e['cas']) <= $sinceTs) continue;
        $filtered[] = $e;
        if (count($filtered) >= $limit) break;
    }
    return $filtered;
}

/**
 * List known topics.
 */
function data_bus_list_topics(): array {
    $data = _data_bus_load();
    return array_keys($data['topics'] ?? []);
}

/**
 * Clear a topic (delete entries).
 */
function data_bus_clear_topic(string $topic): bool {
    $data = _data_bus_load();
    if (isset($data['topics'][$topic])) unset($data['topics'][$topic]);
    return _data_bus_save($data);
}

/**
 * Module helper: publish and optionally add metadata
 */
function modul_data_publish(string $topic, array $payload, array $meta = []): array {
    $entry = array_merge($payload, ['_meta' => $meta]);
    return data_bus_publish($topic, $entry);
}