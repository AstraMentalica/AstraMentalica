# Llama 3.2 (Ollama) integracija

Navodila za hitro integracijo z lokalnim Ollama strežnikom.

- Privzete nastavitve v AI/.env.example:
  - `OLLAMA_URL` - URL strežnika (privzeto `http://localhost:11434`)
  - `OLLAMA_CHAT_PATH` - pot do chat endpointa (privzeto `/api/chat`)
  - `OLLAMA_API_KEY` - neobvezno, če vaš proxy/endpoint zahteva token
  - `OLLAMA_MODEL` - privzeti model (npr. `kakar-2.2-gamma`)

- Datoteka s helper funkcijami: `AI/llama_helper.php`.

Primer klica (PHP):

```php
require_once __DIR__ . '/llama_helper.php';
$messages = [
  ['role'=>'system','content'=>'You are a helpful assistant.'],
  ['role'=>'user','content'=>'Pozdravi me v slovenščini.']
];
// Če imate nameščen model npr. 'kakar-2.2-gamma', pokličite z njegovim imenom:
// echo llama_chat($messages, 'kakar-2.2-gamma', 0.2);
echo llama_chat($messages, null, 0.2);
```

Če uporabljate Ollama lokalno, poskrbite, da je strežnik zagnan in da imate naložen model Llama 3.2 oziroma vaš model.
Če vam endpoint ne odgovarja, prilagodite `OLLAMA_CHAT_PATH` ali `OLLAMA_URL`.
