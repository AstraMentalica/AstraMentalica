<?php
// main/moduli/astramentor/astramentor_history.php
// Modul za shranjevanje in iskanje pogovorov

define('HISTORY_DIR', dirname(__FILE__) . '/history/');

// Ustvari mapo za zgodovino, če ne obstaja
if (!file_exists(HISTORY_DIR)) mkdir(HISTORY_DIR, 0755, true);

/**
 * Shrani pogovor
 * @param string|int $user_id
 * @param string $user_message
 * @param string $ai_response
 */
function save_conversation($user_id, $user_message, $ai_response) {
    $file = HISTORY_DIR . 'history_' . preg_replace('/[^a-zA-Z0-9_\-]/', '', $user_id) . '.json';
    $history = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    $history[] = [
        'timestamp' => time(),
        'user_message' => $user_message,
        'ai_response' => $ai_response
    ];

    // omejimo na zadnjih 100 pogovorov
    if (count($history) > 100) $history = array_slice($history, -100);

    file_put_contents($file, json_encode($history, JSON_PRETTY_PRINT));
}

/**
 * Pridobi pogovore uporabnika
 * @param string|int $user_id
 * @param int $limit
 * @return array
 */
function get_conversations($user_id, $limit = 50) {
    $file = HISTORY_DIR . 'history_' . preg_replace('/[^a-zA-Z0-9_\-]/', '', $user_id) . '.json';
    if (!file_exists($file)) return [];
    
    $history = json_decode(file_get_contents($file), true);
    return array_slice(array_reverse($history), 0, $limit);
}

/**
 * Poišči pogovore, ki vsebujejo iskani niz
 * @param string|int $user_id
 * @param string $search
 * @return array
 */
function search_conversations($user_id, $search) {
    $conversations = get_conversations($user_id, 1000); // največ 1000 za iskanje
    $results = [];

    foreach ($conversations as $entry) {
        if (stripos($entry['user_message'], $search) !== false ||
            stripos($entry['ai_response'], $search) !== false) {
            $results[] = $entry;
        }
    }

    return $results;
}
?>


