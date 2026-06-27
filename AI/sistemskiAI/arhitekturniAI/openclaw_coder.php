<?php
declare(strict_types=1);

// OpenClaw / Anthropic Coder adapter — uporablja deepseek_adapter_klic.
// Namen: omogočiti klice "Anthropic coder" stile preko DeepSeek ali lokalnega Llama.

if (!defined('AI_VSTOP')) {
    $v = __DIR__ . '/../../varnost.php';
    if (file_exists($v)) require_once $v;
}

$adapter = __DIR__ . '/deepseek_adapter.php';
if (!file_exists($adapter)) throw new RuntimeException('deepseek_adapter.php ni najden.');
require_once $adapter;

const OPENCLAW_SYSTEM_PROMPT = <<<'PROMPT'
Si OpenClaw / Anthropic Coder — specializiran za generiranje in popravljanje kode.
- Vrni samo JSON z rezultatom ali čisto kodo, kadar je to primerno.
- Upoštevaj varnostne smernice projekta (brez eksplcitnih eval/dangerous exec).
- Govori slovensko, če uporabnik zahteva to.
PROMPT;

function openclaw_klic(string $userPrompt, float $temp = 0.1): string {
    return deepseek_adapter_klic(OPENCLAW_SYSTEM_PROMPT, $userPrompt, $temp);
}

// Kratek primer:
// $out = openclaw_klic("Napiši PHP funkcijo, ki izračuna fibonaccijeve številke do n.");
// echo $out;
