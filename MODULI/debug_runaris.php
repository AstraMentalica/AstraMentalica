<?php
// Debug script for Runaris

$manifest_path = __DIR__ . '/Runaris/podatki/manifest.json';

echo "Checking: $manifest_path\n\n";

if (!file_exists($manifest_path)) {
    echo "❌ File does not exist\n";
    exit;
}

echo "✅ File exists\n\n";

$content = file_get_contents($manifest_path);
echo "File content:\n$content\n\n";

$manifest = json_decode($content, true);

if ($manifest === null) {
    echo "❌ JSON decode failed: " . json_last_error_msg() . "\n";
    exit;
}

echo "✅ JSON decoded successfully\n\n";

echo "Keys in manifest:\n";
print_r(array_keys($manifest));

echo "\n_id value: " . ($manifest['_id'] ?? 'NOT SET') . "\n";
echo "Has _id: " . (isset($manifest['_id']) ? 'YES' : 'NO') . "\n";