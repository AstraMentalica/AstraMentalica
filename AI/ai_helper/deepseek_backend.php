
<?php
// deepseek_backend.php
// AstraMentalica: backend API klic OpenRouter (DeepSeek free)

// API ključ (tukaj vstavi svoj ključ)
$api_key = "TUKAJ_VSTAVI_OPENROUTER_API_KEY";

// Preveri POST zahtevek
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_message = $_POST['message'] ?? '';

    if (!$user_message) {
        echo json_encode(['error' => 'Ni bilo vnosa.']);
        exit;
    }

    $payload = [
        "model" => "deepseek-r1:free",
        "messages" => [
            ["role" => "system", "content" => "Si AstraMentor, odgovarjaj v slovenskem jeziku."],
            ["role" => "user", "content" => $user_message]
        ]
    ];

    $ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer $api_key"
        ],
        CURLOPT_TIMEOUT => 20
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        $data = json_decode($response, true);
        $answer = $data['choices'][0]['message']['content'] ?? "AI ni vrnil odgovora.";
        echo json_encode(['answer' => $answer]);
    } else {
        echo json_encode([
            'error' => 'Napaka pri API klicu.',
            'http_code' => $http_code,
            'response' => $response
        ]);
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AstraMentalica AI Test (Backend)</title>
<style>
body { font-family: Arial, sans-serif; background: #1a1a2e; color: #eee; display: flex; flex-direction: column; align-items: center; padding: 50px; }
input, button { padding: 10px; font-size: 16px; margin: 5px 0; width: 300px; }
#response { margin-top: 20px; background: #162447; padding: 20px; border-radius: 8px; width: 320px; min-height: 50px; }
</style>
</head>
<body>

<h2>AstraMentalica AI (Backend API)</h2>
<input type="text" id="userInput" placeholder="Vpiši vprašanje...">
<button id="askBtn">Pošlji</button>

<div id="response">Odgovor se bo prikazal tukaj...</div>

<script>
document.getElementById("askBtn").addEventListener("click", async () => {
    const userMessage = document.getElementById("userInput").value.trim();
    if(!userMessage) return;

    document.getElementById("response").innerText = "⏳ Počakaj, AI odgovarja...";

    try {
        const res = await fetch("", { // pošlji POST na isti PHP fajl
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "message=" + encodeURIComponent(userMessage)
        });

        const data = await res.json();
        if(data.answer) {
            document.getElementById("response").innerText = data.answer;
        } else {
            document.getElementById("response").innerText = data.error || "❌ Napaka.";
        }
    } catch(e) {
        console.error(e);
        document.getElementById("response").innerText = "❌ Napaka pri povezavi do API-ja.";
    }
});
</script>

</body>
</html>
