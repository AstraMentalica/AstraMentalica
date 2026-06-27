#!/usr/bin/env php
<?php
/**
 * UNIVERZALNA AI SKRIPTA
 * Rečeš ji kar hočeš, ona naredi!
 * 
 * UPORABA:
 * php ai_asistent.php "Napiši mi funkcijo za pozdrav"
 * php ai_asistent.php "Popravi to datoteko: path/to/file.php"
 * php ai_asistent.php "Analiziraj loge in najdi napake"
 * php ai_asistent.php "Ustvari mi CRUD za tabelo uporabniki"
 * php ai_asistent.php "Prevedi to v slovenščino: Hello world"
 * 
 * INTERAKTIVNI NAČIN:
 * php asistent.php --interactive
 */


// In potem zbriši vse klicanje chdir in require_once nastavitve.php
To je vse – ne rabiš nastavitve.php! 🚀
// Inicializacija
chdir(dirname(__DIR__));
//require_once 'nastavitve.php';
// Nastavi ključ direktno (brez iskanja nastavitve.php) — nadomesti s svojim
$openrouter_key = getenv('OPENROUTER_API_KEY') ?: 'sk-your-openrouter-key';
$cerebras_key = getenv('CEREBRAS_API_KEY') ?: 'csk-your-cerebras-key';

// Barve za terminal
define('COLOR_RESET', "\033[0m");
define('COLOR_RED', "\033[31m");
define('COLOR_GREEN', "\033[32m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_BLUE', "\033[34m");
define('COLOR_MAGENTA', "\033[35m");
define('COLOR_CYAN', "\033[36m");
define('COLOR_WHITE', "\033[37m");

// Konfiguracija
$config = [
    'api_key' => getenv('DEEPSEEK_API_KEY') ?: 'sk-your-deepseek-api-key',
    'api_url' => 'https://api.deepseek.com/v1/chat/completions',
    'model' => 'deepseek-chat',
    'max_tokens' => 4000,
    'temperature' => 0.7
];

// ============================================================
// GLAVNA FUNKCIJA
// ============================================================
function print_help() {
    echo COLOR_CYAN . "
╔══════════════════════════════════════════════════════════════╗
║     🤖 UNIVERZALNI AI ASISTENT - Reči mi, kaj naj naredim    ║
╚══════════════════════════════════════════════════════════════╝
" . COLOR_RESET;

    echo COLOR_YELLOW . "
UPORABA:
  php ai_asistent.php [ukaz] [parametri]

PRIMERI:
  📝 PISANJE KODE:
    php ai_asistent.php \"Napiši PHP funkcijo za validacijo emaila\"
    php ai_asistent.php \"Ustvari mi REST API endpoint za prijavo\"
    php ai_asistent.php \"Napiši SQL za tabelo uporabniki\"

  🔧 POPRAVLJANJE:
    php ai_asistent.php \"Popravi to datoteko: index.php\"
    php ai_asistent.php \"Optimiziraj to kodo: \" --file=koda.php
    php ai_asistent.php \"Poišči napake v logu: error.log\"

  📄 ANALIZA:
    php ai_asistent.php \"Analiziraj ta JSON: data.json\"
    php ai_asistent.php \"Povzemi ta članek: clanek.txt\"
    php ai_asistent.php \"Kaj je narobe s to konfiguracijo? config.php\"

  🌐 PREVODI:
    php ai_asistent.php \"Prevedi v slovenščino: Hello world\"
    php ai_asistent.php \"Prevedi v angleščino: Zdravo svet\"

  🗄️ CRUD GENERATOR:
    php ai_asistent.php \"Ustvari CRUD za tabelo products\"
    php ai_asistent.php \"Generiraj admin panel za users\"

  🐛 DEBUG:
    php ai_asistent.php \"Debugiraj to napako: ...\"
    php ai_asistent.php \"Pojasni mi ta error: ...\"

  🎮 INTERAKTIVNI NAČIN:
    php ai_asistent.php --interactive
    php ai_asistent.php -i

  💾 BRANJE IZ DATOTEKE:
    php ai_asistent.php @zahteva.txt

" . COLOR_RESET;
}

function print_color($color, $text) {
    echo $color . $text . COLOR_RESET;
}

function call_ai($prompt, $system_prompt = null) {
    global $config;
    
    $messages = [];
    
    if ($system_prompt) {
        $messages[] = ['role' => 'system', 'content' => $system_prompt];
    }
    
    $messages[] = ['role' => 'user', 'content' => $prompt];
    
    $data = [
        'model' => $config['model'],
        'messages' => $messages,
        'max_tokens' => $config['max_tokens'],
        'temperature' => $config['temperature']
    ];
    
    $ch = curl_init($config['api_url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $config['api_key']
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? null;
    }
    
    return "❌ API napaka: $httpCode\n" . substr($response, 0, 500);
}

function read_file_content($filepath) {
    if (!file_exists($filepath)) {
        return null;
    }
    $content = file_get_contents($filepath);
    if (strlen($content) > 10000) {
        $content = substr($content, 0, 10000) . "\n... (datoteka prevelika, prikazanih prvih 10000 znakov)";
    }
    return $content;
}

function write_file_content($filepath, $content) {
    $backup = $filepath . '.backup_' . date('Ymd_His');
    if (file_exists($filepath)) {
        copy($filepath, $backup);
        echo COLOR_YELLOW . "📦 Backup shranjen kot: $backup\n" . COLOR_RESET;
    }
    file_put_contents($filepath, $content);
    return true;
}

function detect_command_type($input) {
    $input_lower = strtolower($input);
    
    if (preg_match('/popravi|fix|correct|popravek/i', $input_lower)) {
        return 'fix_code';
    }
    if (preg_match('/napiši|ustvari|create|write|generate|make|naredi/i', $input_lower)) {
        return 'generate';
    }
    if (preg_match('/analiziraj|analyze|preveri|check|kaj je narobe/i', $input_lower)) {
        return 'analyze';
    }
    if (preg_match('/prevedi|translate|v slovenščino|v angleščino/i', $input_lower)) {
        return 'translate';
    }
    if (preg_match('/crud|admin panel|generiraj za tabelo/i', $input_lower)) {
        return 'crud';
    }
    if (preg_match('/debug|pojasni|explain|zakaj|kako/i', $input_lower)) {
        return 'explain';
    }
    if (preg_match('/@/', $input)) {
        return 'read_file';
    }
    
    return 'general';
}

function process_request($input) {
    $type = detect_command_type($input);
    
    print_color(COLOR_CYAN, "\n🔍 Detekcija: ");
    echo $type . "\n";
    print_color(COLOR_BLUE, "📝 Obdelujem: ");
    echo $input . "\n\n";
    
    // Če je @ datoteka
    if (preg_match('/@(.+)/', $input, $matches)) {
        $filepath = trim($matches[1]);
        $content = read_file_content($filepath);
        if ($content) {
            $input = str_replace('@' . $filepath, "\n\n--- VSEBINA DATOTEKE $filepath ---\n$content\n--- KONEC VSEBINE ---\n", $input);
            print_color(COLOR_GREEN, "📄 Vsebina datoteke $filepath vključena.\n\n");
        } else {
            print_color(COLOR_RED, "❌ Datoteka $filepath ne obstaja!\n");
            return;
        }
    }
    
    // Pripravi system prompt glede na tip
    $system_prompt = null;
    switch ($type) {
        case 'generate':
            $system_prompt = "Ti si ekspertni programer. Generiraj čisto, optimizirano kodo z komentarji. 
            Uporabi PHP 8+, dodaj type hinting, razmisli o varnosti. 
            Če gre za SQL, uporabi prepared statements. 
            Če gre za HTML/CSS, naj bo responsive.";
            break;
        case 'fix_code':
            $system_prompt = "Ti si ekspertni programer. Analiziraj kodo, najdi napake in jih popravi. 
            Pokaži originalno kodo, nato popravljeno verzijo. 
            Razloži kaj je bilo narobe in zakaj. Uporabi barve za prikaz.";
            break;
        case 'analyze':
            $system_prompt = "Ti si analitik. Analiziraj podano vsebino temeljito. 
            Poišči vzorce, probleme, priložnosti. 
            Podaj strukturiran odgovor s točkami in priporočili.";
            break;
        case 'translate':
            $system_prompt = "Ti si profesionalni prevajalec. Prevajaj natančno, ohrani pomen in ton. 
            Če ni določeno drugače, prevajaj v slovenščino.";
            break;
        case 'crud':
            $system_prompt = "Ti si PHP developer. Generiraj popoln CRUD (Create, Read, Update, Delete) za podano tabelo.
            Ustvari: model, controller, view (HTML), JavaScript za AJAX, in SQL migracijo.
            Uporabi PDO, prepared statements, CSRF zaščito, in validacijo.";
            break;
        case 'explain':
            $system_prompt = "Ti si učitelj programiranja. Razloži koncepte preprosto, z primeri. 
            Naj bo razlaga razumljiva za začetnike, a vsebinsko točna.";
            break;
        default:
            $system_prompt = "Ti si koristen AI asistent. Odgovori jasno, natančno in uporabno. 
            Če gre za kodo, jo oblikuj lepo. Če gre za vprašanje, odgovori izčrpno.";
    }
    
    print_color(COLOR_YELLOW, "⏳ Kličem DeepSeek AI ...\n\n");
    
    $response = call_ai($input, $system_prompt);
    
    if ($response) {
        print_color(COLOR_GREEN, "✅ ODGOVOR:\n");
        echo str_repeat("═", 60) . "\n";
        echo $response . "\n";
        echo str_repeat("═", 60) . "\n\n";
        
        // Shrani v zgodovino
        save_to_history($input, $response);
        
        // Vprašaj za akcijo
        ask_for_action($response);
    } else {
        print_color(COLOR_RED, "❌ Napaka pri klicu AI. Preveri API ključ.\n");
    }
}

function ask_for_action($response) {
    print_color(COLOR_CYAN, "\n📋 Kaj želiš narediti?\n");
    echo COLOR_YELLOW . "  [1] 💾 Shrani odgovor v datoteko\n";
    echo "  [2] 📋 Kopiraj v clipboard (če imaš pbcopy/xclip)\n";
    echo "  [3] 🔧 Poskusi popraviti kodo iz odgovora\n";
    echo "  [4] ➡️  Nadaljuj pogovor (vprašaj nekaj o tem odgovoru)\n";
    echo "  [5] ❌ Končaj\n" . COLOR_RESET;
    
    echo COLOR_WHITE . "\nIzbira: " . COLOR_RESET;
    $choice = trim(fgets(STDIN));
    
    switch ($choice) {
        case '1':
            $filename = 'ai_output_' . date('Ymd_His') . '.txt';
            file_put_contents($filename, $response);
            print_color(COLOR_GREEN, "✅ Shranjeno v: $filename\n");
            break;
        case '2':
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows
                file_put_contents('clipboard_temp.txt', $response);
                echo COLOR_GREEN . "✅ Odgovor shranjen v clipboard_temp.txt (Windows: type clipboard_temp.txt | clip)\n" . COLOR_RESET;
            } else {
                // Linux/Mac
                $escaped = escapeshellarg($response);
                system("echo $escaped | pbcopy 2>/dev/null || echo $escaped | xclip -selection clipboard 2>/dev/null");
                echo COLOR_GREEN . "✅ Kopirano v clipboard!\n" . COLOR_RESET;
            }
            break;
        case '3':
            print_color(COLOR_CYAN, "\n🔧 Poskušam popraviti kodo iz odgovora...\n");
            // Izlušči kodo iz odgovora
            if (preg_match_all('/```(\w*)\n(.*?)```/s', $response, $matches)) {
                foreach ($matches[2] as $code) {
                    $temp_file = 'temp_fix_' . time() . '.php';
                    file_put_contents($temp_file, $code);
                    print_color(COLOR_GREEN, "✅ Koda shranjena v $temp_file\n");
                }
            } else {
                print_color(COLOR_RED, "❌ V odgovoru ni najdene kode.\n");
            }
            break;
        case '4':
            print_color(COLOR_CYAN, "\n💬 Nadaljuj pogovor. Vprašaj nekaj o prejšnjem odgovoru:\n");
            echo COLOR_WHITE . "> " . COLOR_RESET;
            $followup = trim(fgets(STDIN));
            if ($followup) {
                process_request($followup . "\n\n(Prejšnji odgovor: " . substr($response, 0, 500) . ")");
            }
            break;
        default:
            print_color(COLOR_YELLOW, "👋 Adijo!\n");
    }
}

function save_to_history($input, $response) {
    $history_file = 'ai_history.json';
    $history = [];
    if (file_exists($history_file)) {
        $history = json_decode(file_get_contents($history_file), true);
    }
    
    $history[] = [
        'timestamp' => date('Y-m-d H:i:s'),
        'input' => $input,
        'response' => substr($response, 0, 2000) // samo prvih 2000 znakov
    ];
    
    // Ohrani zadnjih 100 interakcij
    if (count($history) > 100) {
        $history = array_slice($history, -100);
    }
    
    file_put_contents($history_file, json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function interactive_mode() {
    print_color(COLOR_CYAN, "
╔══════════════════════════════════════════════════════════════╗
║     🤖 INTERAKTIVNI AI ASISTENT - Kar koli me lahko vprašaš  ║
║                                                              ║
║     Tipke:                                                   ║
║       /help      - Pomoč                                    ║
║       /history   - Zadnja zgodovina                         ║
║       /clear     - Počisti zaslon                           ║
║       /exit      - Izhod                                    ║
║       @file.txt  - Preberi datoteko                         ║
╚══════════════════════════════════════════════════════════════╝
" . COLOR_RESET);
    
    while (true) {
        echo COLOR_GREEN . "\n🤖 YOU > " . COLOR_RESET;
        $input = trim(fgets(STDIN));
        
        if (empty($input)) {
            continue;
        }
        
        if ($input === '/exit' || $input === '/quit') {
            print_color(COLOR_YELLOW, "👋 Adijo!\n");
            break;
        }
        
        if ($input === '/help') {
            print_help();
            continue;
        }
        
        if ($input === '/clear') {
            system('clear');
            continue;
        }
        
        if ($input === '/history') {
            if (file_exists('ai_history.json')) {
                $history = json_decode(file_get_contents('ai_history.json'), true);
                echo COLOR_CYAN . "\n📜 ZADNJIH 10 POGOVOROV:\n" . COLOR_RESET;
                foreach (array_slice($history, -10) as $h) {
                    echo COLOR_YELLOW . "[" . $h['timestamp'] . "] " . COLOR_RESET;
                    echo substr($h['input'], 0, 60) . "...\n";
                }
            } else {
                echo COLOR_YELLOW . "Ni zgodovine.\n" . COLOR_RESET;
            }
            continue;
        }
        
        process_request($input);
    }
}

// ============================================================
// GLAVNI PROGRAM
// ============================================================

// Preveri argumente
$args = array_slice($argv, 1);

if (empty($args)) {
    print_help();
    exit(0);
}

$first_arg = $args[0];

if ($first_arg === '--interactive' || $first_arg === '-i') {
    interactive_mode();
} elseif ($first_arg === '--help' || $first_arg === '-h') {
    print_help();
} else {
    // Združi vse argumente v en string
    $prompt = implode(' ', $args);
    process_request($prompt);
}