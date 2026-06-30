<?php
declare(strict_types=1);

// Crestodian agent: monitoring, local fuzzy planning via Ollama, and routing to OpenClaw.
// Gateway: ws://127.0.0.1:18789 (TCP check)

if (!defined('AI_VSTOP')) {
    $v = __DIR__ . '/../../varnost.php';
    if (file_exists($v)) require_once $v;
}

$GW_HOST = '127.0.0.1';
$GW_PORT = 18789;

$CRESTODIAN_SYSTEM_PROMPT = <<<'PROMPT'
Si Crestodian — varuh sistema in lokalni planer. Ko zaznaš težave pri namestitvi, konfiguraciji,
Gatewayu, izbiri modela ali agent routing-u, obvesti administratorja in predlagaj korake popravila.
Uporabi Ollama lokalno za fuzzy planning in OpenClaw za generiranje kode.
PRAVILA:
- Preizkusi dostopnost gateway-a (TCP)
- Preizkusi Ollama endpoint (HTTP)
- Če je vse OK, vrni JSON status z agenti in njihovimi stanji
- Če zaznaš napako, vrni diagnostični JSON z navodili
PROMPT;

function crestodian_check_gateway(string $host = '127.0.0.1', int $port = 18789, int $timeout = 2): bool {
    $errNo = 0; $errStr = '';
    $fp = @fsockopen($host, $port, $errNo, $errStr, $timeout);
    if ($fp === false) return false;
    fclose($fp);
    return true;
}

function crestodian_check_ollama(string $url = null, int $timeout = 3) {
    $cfg = function_exists('llama_config') ? llama_config() : null;
    $url = $url ?? ($cfg['url'] ?? 'http://localhost:11434');
    $modelsUrl = rtrim($url, '/') . '/api/models';
    $ctx = stream_context_create(['http'=>['timeout'=>$timeout]]);
    $resp = @file_get_contents($modelsUrl, false, $ctx);
    if ($resp === false) return false;
    $data = json_decode($resp, true);
    return $data ?: $resp;
}

function crestodian_status(): array {
    global $GW_HOST, $GW_PORT, $CRESTODIAN_SYSTEM_PROMPT;
    $gw = crestodian_check_gateway($GW_HOST, $GW_PORT);
    $oll = crestodian_check_ollama();

    $agents = [
        'main' => ['default'=>true, 'local_ready'=>true, 'state'=>'idle'],
        'crestodian' => ['agent'=>'Crestodian', 'session'=>'main', 'backend'=>'ollama']
    ];

    // Determine available models if Ollama reachable
    $models = [];
    $selected = null;
    if (is_array($oll)) {
        // Ollama /api/models may return structure with 'models' or list
        if (isset($oll['models'])) {
            $models = array_map(function($m){ return is_array($m) ? ($m['name'] ?? $m) : $m; }, $oll['models']);
        } elseif (isset($oll[0])) {
            $models = $oll;
        }
    }
    // If helper available, read selected/default models
    if (function_exists('llama_config')) {
        $cfg = llama_config();
        $selected = $cfg['model'] ?? null;
        if (isset($cfg['models_env']) && $cfg['models_env']) {
            $envList = array_map('trim', explode(',', $cfg['models_env']));
            $models = array_values(array_unique(array_merge($models, $envList)));
        }
    }

    $status = ['gateway'=> $gw ? 'reachable' : 'unreachable', 'ollama'=> is_array($oll) ? 'ok' : ($oll ? 'ok' : 'unreachable'), 'models'=>$models, 'selected_model'=>$selected, 'agents'=>$agents];
    return $status;
}

function crestodian_talk(string $message, string $lang = 'sl'): string {
    // Forward to OpenClaw (code/style) when appropriate, else to default agent.
    $openclaw = __DIR__ . '/openclaw_coder.php';
    if (file_exists($openclaw)) require_once $openclaw;
    if (function_exists('openclaw_klic')) {
        $prompt = ($lang === 'sl') ? "Uporabnik (slovenščina): $message" : $message;
        try {
            return openclaw_klic($prompt, 0.2);
        } catch (Throwable $e) {
            return json_encode(['error'=>'openclaw failed','msg'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
    return json_encode(['error'=>'openclaw missing','message'=>$message], JSON_UNESCAPED_UNICODE);
}

function crestodian_plan(string $goal): string {
    // Use local Ollama for fuzzy planning
    if (!function_exists('llama_chat')) {
        $lh = __DIR__ . '/../../llama_helper.php';
        if (file_exists($lh)) require_once $lh;
    }
    if (!function_exists('llama_chat')) return json_encode(['error'=>'llama helper missing']);

    $messages = [
        ['role'=>'system','content'=>'You are a local fuzzy planner. Produce a short plan (steps) in JSON array.'],
        ['role'=>'user','content'=>$goal]
    ];
    try {
        $out = llama_chat($messages, null, 0.2, 512);
        return $out;
    } catch (Throwable $e) {
        return json_encode(['error'=>'llama chat failed','msg'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
}

// Simple CLI/HTTP interface
if (php_sapi_name() === 'cli' || (defined('STDIN') && !isset($_SERVER['REMOTE_ADDR']))) {
    // CLI usage: php crestodian.php status|talk "message"|plan "goal"
    $cmd = $argv[1] ?? 'status';
    if ($cmd === 'status') {
        echo json_encode(crestodian_status(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        exit;
    }
    if ($cmd === 'talk') {
        $msg = $argv[2] ?? '';
        echo crestodian_talk($msg) . "\n";
        exit;
    }
    if ($cmd === 'plan') {
        $goal = $argv[2] ?? '';
        echo crestodian_plan($goal) . "\n";
        exit;
    }
    if ($cmd === 'set-model') {
        $m = $argv[2] ?? '';
        if (empty($m)) { echo "Usage: php crestodian.php set-model <model>\n"; exit(1); }
        if (!function_exists('llama_set_selected_model')) {
            $lh = __DIR__ . '/../../llama_helper.php'; if (file_exists($lh)) require_once $lh;
        }
        if (function_exists('llama_set_selected_model')) {
            echo (llama_set_selected_model($m) ? "ok\n" : "fail\n");
        } else echo "no helper\n";
        exit;
    }
} else {
    // Minimal HTTP API
    header('Content-Type: application/json; charset=utf-8');
    $op = $_GET['op'] ?? 'status';
    if ($op === 'status') echo json_encode(crestodian_status());
    elseif ($op === 'talk') echo json_encode(['response'=>crestodian_talk($_POST['msg'] ?? '')]);
    elseif ($op === 'plan') echo crestodian_plan($_POST['goal'] ?? '');
    elseif ($op === 'set_model') {
        $m = $_POST['model'] ?? '';
        if (empty($m)) echo json_encode(['error'=>'model missing']);
        else {
            if (!function_exists('llama_set_selected_model')) {
                $lh = __DIR__ . '/../../llama_helper.php'; if (file_exists($lh)) require_once $lh;
            }
            if (function_exists('llama_set_selected_model')) {
                $ok = llama_set_selected_model($m);
                echo json_encode(['ok'=>$ok, 'selected'=>$m]);
            } else echo json_encode(['error'=>'llama helper missing']);
        }
    } else echo json_encode(['error'=>'unknown op']);
}
