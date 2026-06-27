#!/usr/bin/env php
<?php
/**
 * agent.php - Samoučeči AI agent
 * Bere sistemske loge, analizira napake in predlaga popravke
 */

chdir(dirname(__DIR__,1));
require_once 'config.php';

define('AGENT_LOG', PODATKI_PATH . '/agent.log');

function log_agent($msg) {
    file_put_contents(AGENT_LOG, '[' . date('Y-m-d H:i:s') . "] $msg\n", FILE_APPEND);
}

log_agent("🚀 Agent started");

// 1. Preberi zadnje sistemske loge
$logFiles = [
    PODATKI_PATH . '/error.log',
    PODATKI_PATH . '/ai.log',
    PODATKI_PATH . '/system.log'
];

$napake = [];
foreach ($logFiles as $logFile) {
    if (file_exists($logFile)) {
        $lines = file($logFile);
        $zadnje = array_slice($lines, -50);
        foreach ($zadnje as $line) {
            if (preg_match('/error|fatal|exception|warning|napaka/i', $line)) {
                $napake[] = trim($line);
            }
        }
    }
}

if (empty($napake)) {
    log_agent("✅ No errors found in logs");
    exit(0);
}

$napake = array_unique($napake);
log_agent("📊 Found " . count($napake) . " unique errors");

// 2. AI analiza (če obstaja)
if (function_exists('ai_generiraj')) {
    $errorText = implode("\n", array_slice($napake, 0, 15));
    $prompt = "Analiziraj sledeče napake iz log datotek in predlagaj popravke za PHP aplikacijo. 
    Vrni v formatu MARKDOWN s predlogi kode. Bodisi konkreten.\n\nNapake:\n$errorText";
    
    log_agent("🤔 Requesting AI analysis...");
    $analiza = ai_generiraj($prompt, ['max_tokens' => 2000]);
    
    if ($analiza) {
        $pravilaPath = ROOT_PATH . '/razvoj/pravila';
        if (!is_dir($pravilaPath)) mkdir($pravilaPath, 0755, true);
        
        $filename = $pravilaPath . '/' . date('Y-m-d_His') . '_ai_predlog.md';
        $content = "---\n";
        $content .= "generated_by: agent.php\n";
        $content .= "date: " . date('Y-m-d H:i:s') . "\n";
        $content .= "errors_count: " . count($napake) . "\n";
        $content .= "---\n\n";
        $content .= "# 🤖 AI Predlog za izboljšave\n\n";
        $content .= "## Analizirane napake\n\n```\n" . implode("\n", array_slice($napake, 0, 20)) . "\n```\n\n";
        $content .= "## Predlogi popravkov\n\n";
        $content .= $analiza . "\n";
        
        file_put_contents($filename, $content);
        log_agent("💾 Saved analysis to " . basename($filename));
    } else {
        log_agent("⚠️ AI analysis failed");
    }
} else {
    log_agent("⚠️ AI functions not available");
}

// 3. Počisti stare pravila (več kot 7 dni)
$pravilaPath = ROOT_PATH . '/razvoj/pravila';
if (is_dir($pravilaPath)) {
    $files = glob($pravilaPath . '/*.md');
    $now = time();
    $deleted = 0;
    foreach ($files as $file) {
        if ($now - filemtime($file) > 7 * 86400) {
            unlink($file);
            $deleted++;
        }
    }
    if ($deleted) log_agent("🧹 Deleted $deleted old rule(s)");
}

log_agent("✅ Agent finished");
exit(0);