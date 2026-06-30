<?php
/**
 * ============================================================================
 * GLASOVNI ASISTENT za AstraMentalica
 * ============================================================================
 * Dodatek za glasovno upravljanje, AI pogovor in diktiranje
 * 
 * Uporaba: Vstavi v katerokoli stran:
 *   <?php include 'asistent.php'; ?>
 * 
 * Glasovni ukazi:
 *   "odpri Aeternum" - odpre modul
 *   "shrani" - shrani trenutno datoteko (v admin.php)
 *   "zaženi" - zaženi datoteko v brskalniku
 *   "išči [pojem]" - išče po projektu
 *   "preberi" - prebere trenutno datoteko na glas
 *   "pogovori se" - odpre AI pogovor
 *   "diktiraj" - začne diktirati v urejevalnik
 * ============================================================================
 */

// Prepreči direktni dostop
//if (!defined('KOREN') && !defined('SIDRO_AKTIVNO')) {
//    define('KOREN', __DIR__);
//    define('KOREN_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
//} <?php include 'glas.php'; ?>

// API KLJUČ (tvoj!)
$OPENROUTER_KEY = 'sk-7dff3711df044abc84efb2c3fa7e0310';

// Dodaj CSS in JS samo, če še ni dodano
$glasovni_dodan = isset($GLOBALS['glasovni_asistent_dodan']) ? true : false;
if (!$glasovni_dodan) {
    $GLOBALS['glasovni_asistent_dodan'] = true;
    ?>
    <style>
        .voice-assistant {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        
        .voice-btn {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a1a2e, #0a0a1a);
            border: 2px solid #c8a84b;
            color: #c8a84b;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        
        .voice-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(200,168,75,0.5);
        }
        
        .voice-btn.listening {
            background: linear-gradient(135deg, #c8a84b, #d4870a);
            color: #0a0a1a;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(200,168,75,0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(200,168,75,0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(200,168,75,0); }
        }
        
        .voice-panel {
            position: fixed;
            bottom: 90px;
            left: 20px;
            width: 340px;
            background: rgba(10,10,26,0.95);
            backdrop-filter: blur(12px);
            border: 1px solid #c8a84b;
            border-radius: 20px;
            padding: 15px;
            display: none;
            flex-direction: column;
            gap: 12px;
            z-index: 10000;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        
        .voice-panel.open {
            display: flex;
        }
        
        .voice-status {
            font-size: 12px;
            color: #c8a84b;
            text-align: center;
            padding: 5px;
            border-bottom: 1px solid rgba(200,168,75,0.3);
        }
        
        .voice-text {
            background: rgba(0,0,0,0.5);
            border: 1px solid #1a2535;
            border-radius: 12px;
            padding: 10px;
            color: #c8d4e8;
            font-size: 13px;
            max-height: 150px;
            overflow-y: auto;
            font-family: monospace;
        }
        
        .voice-commands {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            font-size: 10px;
            color: #4a6080;
        }
        
        .voice-commands span {
            background: rgba(200,168,75,0.1);
            padding: 3px 8px;
            border-radius: 12px;
        }
        
        .voice-ai {
            border-top: 1px solid rgba(200,168,75,0.3);
            padding-top: 10px;
            margin-top: 5px;
        }
        
        .voice-ai textarea {
            width: 100%;
            background: #0a1020;
            border: 1px solid #1a2535;
            border-radius: 8px;
            color: #c8d4e8;
            padding: 8px;
            font-size: 12px;
            resize: vertical;
            font-family: monospace;
        }
        
        .voice-ai button {
            background: transparent;
            border: 1px solid #c8a84b;
            color: #c8a84b;
            padding: 5px 12px;
            border-radius: 20px;
            cursor: pointer;
            margin-top: 8px;
            font-size: 11px;
        }
        
        .voice-ai button:hover {
            background: #c8a84b;
            color: #0a0a1a;
        }
        
        .voice-response {
            margin-top: 8px;
            padding: 8px;
            background: #0a1020;
            border-radius: 8px;
            font-size: 11px;
            color: #8ba0c0;
            max-height: 100px;
            overflow-y: auto;
        }
        
        .voice-toast {
            position: fixed;
            bottom: 100px;
            right: 20px;
            background: #0a1020;
            border: 1px solid #c8a84b;
            color: #c8a84b;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            z-index: 10001;
            transition: opacity 0.3s;
            opacity: 0;
        }
    </style>
    
    <div class="voice-assistant">
        <button class="voice-btn" id="voiceBtn" title="Glasovni asistent (CTRL+M)">🎤</button>
        <div class="voice-panel" id="voicePanel">
            <div class="voice-status" id="voiceStatus">🎤 Klikni mikrofon in govori</div>
            <div class="voice-text" id="voiceText">...</div>
            <div class="voice-commands">
                <span>🗣️ "odpri Aeternum"</span>
                <span>💾 "shrani"</span>
                <span>▶️ "zaženi"</span>
                <span>🔍 "išči [pojem]"</span>
                <span>📖 "preberi"</span>
                <span>💬 "pogovori se"</span>
                <span>✏️ "diktiraj"</span>
                <span>❓ "pomoč"</span>
            </div>
            <div class="voice-ai" id="voiceAi" style="display:none">
                <textarea id="aiPrompt" placeholder="Vprašaj AI ali diktiraj... (AI ima tvoj ključ)"></textarea>
                <button onclick="askAI()">🤖 Vprašaj AI</button>
                <div class="voice-response" id="aiResponse"></div>
            </div>
        </div>
    </div>
    <div id="voiceToast" class="voice-toast"></div>
    
    <script>
    // ============================================================
    // GLASOVNI ASISTENT - Z VSTAVLJENIM API KLJUČEM
    // ============================================================
    
    // 🔑 TVOJ API KLJUČ JE ŽE VSTAVLJEN!
    const OPENROUTER_API_KEY = '<?= $OPENROUTER_KEY ?>';
    
    const VoiceAssistant = {
        isListening: false,
        recognition: null,
        currentCommand: '',
        aiMode: false,
        
        init: function() {
            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                document.getElementById('voiceStatus').innerHTML = '❌ Glasovni vnos ni podprt';
                document.getElementById('voiceBtn').style.opacity = '0.5';
                return;
            }
            
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();
            this.recognition.continuous = true;
            this.recognition.interimResults = true;
            this.recognition.lang = 'sl-SI';
            
            this.recognition.onstart = () => this.onStart();
            this.recognition.onend = () => this.onEnd();
            this.recognition.onresult = (e) => this.onResult(e);
            this.recognition.onerror = (e) => this.onError(e);
            
            document.getElementById('voiceBtn').onclick = () => this.toggle();
            
            document.getElementById('voiceBtn').addEventListener('contextmenu', (e) => {
                e.preventDefault();
                this.togglePanel();
            });
            
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.key === 'm') {
                    e.preventDefault();
                    this.toggle();
                }
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    this.togglePanel();
                }
            });
            
            // Pokaži, da je AI pripravljen
            console.log('🎤 Glasovni asistent pripravljen! AI ključ:', OPENROUTER_API_KEY ? '✅ nastavljen' : '❌ manjka');
        },
        
        toggle: function() {
            if (this.isListening) {
                this.recognition.stop();
            } else {
                this.recognition.start();
            }
        },
        
        togglePanel: function() {
            const panel = document.getElementById('voicePanel');
            panel.classList.toggle('open');
        },
        
        onStart: function() {
            this.isListening = true;
            const btn = document.getElementById('voiceBtn');
            btn.classList.add('listening');
            document.getElementById('voiceStatus').innerHTML = '🔴 Poslušam... govori';
            document.getElementById('voiceText').innerHTML = '';
        },
        
        onEnd: function() {
            this.isListening = false;
            const btn = document.getElementById('voiceBtn');
            btn.classList.remove('listening');
            document.getElementById('voiceStatus').innerHTML = '🎤 Mikrofon pripravljen';
        },
        
        onError: function(e) {
            console.error('Speech error:', e);
            this.isListening = false;
            document.getElementById('voiceStatus').innerHTML = '❌ Napaka: ' + e.error;
            document.getElementById('voiceBtn').classList.remove('listening');
        },
        
        onResult: function(event) {
            let interim = '';
            let final = '';
            
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    final += transcript + ' ';
                } else {
                    interim += transcript;
                }
            }
            
            const fullText = final || interim;
            document.getElementById('voiceText').innerHTML = fullText;
            
            if (final) {
                this.processCommand(final.toLowerCase());
            }
        },
        
        processCommand: function(command) {
            console.log('Ukaz:', command);
            this.showToast('🎤 ' + command.substring(0, 50));
            
            // Moduli
            const moduli = ['Aeternum', 'Stelaris', 'Numyra', 'Synera', 'Orakleum', 'Mystaia', 'Aetheris', 'Celestara', 'Codex', 'AuroraMystica', 'VipPassport'];
            for (const mod of moduli) {
                if (command.includes('odpri ' + mod.toLowerCase()) || command.includes('odpri ' + mod.toLowerCase() + ' modul')) {
                    this.odpriModul(mod);
                    return;
                }
            }
            
            // Shrani
            if (command.includes('shrani') && typeof shraniTrenutno === 'function') {
                shraniTrenutno();
                this.speak('Shranjeno');
                return;
            }
            
            // Zaženi
            if (command.includes('zaženi') && window.trenutnaDatoteka) {
                window.open(window.trenutnaDatoteka, '_blank');
                this.speak('Zaganjam');
                return;
            }
            
            // Iskanje
            if (command.includes('išči')) {
                let iskalniPojem = command.replace(/išči|poišči|najdi/gi, '').trim();
                if (iskalniPojem) {
                    if (typeof globIsci === 'function') {
                        globIsci(iskalniPojem);
                    } else {
                        window.location.href = '?search=' + encodeURIComponent(iskalniPojem);
                    }
                    this.speak('Iščem ' + iskalniPojem);
                }
                return;
            }
            
            // Preberi
            if (command.includes('preberi')) {
                this.preberiTrenutno();
                return;
            }
            
            // Diktiraj
            if (command.includes('diktiraj')) {
                this.startDictation();
                return;
            }
            
            // AI pogovor
            if (command.includes('pogovori se') || command.includes('pogovor') || command.includes('vprašaj')) {
                this.openAIMode();
                return;
            }
            
            // Pomoč
            if (command.includes('pomoč') || command.includes('kaj lahko') || command.includes('ukazi')) {
                this.showHelp();
                return;
            }
            
            // Zapri panel
            if (command.includes('zapri asistent') || command.includes('skrij')) {
                document.getElementById('voicePanel').classList.remove('open');
                return;
            }
            
            // AI način
            if (this.aiMode) {
                document.getElementById('aiPrompt').value = command;
                this.askAI();
            } else {
                this.speak('Nisem razumel. Reci "pomoč" za seznam ukazov.');
            }
        },
        
        odpriModul: function(ime) {
            const pot = 'MODULI/osnovni/' + ime + '/index.php';
            fetch(pot, {method: 'HEAD'}).then(res => {
                if (res.ok) {
                    window.location.href = pot;
                    this.speak('Opiram ' + ime);
                } else {
                    if (fetch(ime.toLowerCase() + '.php', {method: 'HEAD'}).then(r => r.ok)) {
                        window.location.href = ime.toLowerCase() + '.php';
                        this.speak('Opiram ' + ime);
                    } else {
                        this.speak('Modul ' + ime + ' ne obstaja');
                    }
                }
            }).catch(() => {
                this.speak('Modul ' + ime + ' ne obstaja');
            });
        },
        
        preberiTrenutno: function() {
            let content = '';
            const editor = document.getElementById('editKoda');
            if (editor && editor.value) {
                content = editor.value;
            } else {
                const mainContent = document.querySelector('.vsebina, .modul-container, .vsebina-tekst, #vsebina');
                if (mainContent) {
                    content = mainContent.innerText;
                } else {
                    content = document.body.innerText;
                }
            }
            
            if (content && content.length > 0) {
                this.speak(content.substring(0, 2000));
            } else {
                this.speak('Ni vsebine za branje');
            }
        },
        
        startDictation: function() {
            const editor = document.getElementById('editKoda');
            if (!editor) {
                this.speak('Urejevalnik ni odprt. Odpri datoteko v admin.php');
                return;
            }
            
            this.speak('Diktiranje začeto. Govori, besedilo se bo zapisovalo.');
            
            if (this.recognition) {
                this.recognition.stop();
                setTimeout(() => {
                    const dictationRec = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
                    dictationRec.continuous = true;
                    dictationRec.interimResults = true;
                    dictationRec.lang = 'sl-SI';
                    
                    dictationRec.onresult = (e) => {
                        let text = '';
                        for (let i = e.resultIndex; i < e.results.length; i++) {
                            text += e.results[i][0].transcript;
                        }
                        editor.value += text + ' ';
                        editor.dispatchEvent(new Event('input'));
                    };
                    
                    dictationRec.onend = () => {
                        this.speak('Diktiranje končano');
                    };
                    
                    dictationRec.start();
                }, 100);
            }
        },
        
        openAIMode: function() {
            this.aiMode = true;
            const aiDiv = document.getElementById('voiceAi');
            aiDiv.style.display = 'block';
            document.getElementById('voiceStatus').innerHTML = '💬 AI način – vprašaj kar želiš';
            document.getElementById('aiPrompt').focus();
            this.speak('AI način aktiviran. Kaj te zanima?');
        },
        
        askAI: async function() {
            const prompt = document.getElementById('aiPrompt').value;
            if (!prompt) return;
            
            const responseDiv = document.getElementById('aiResponse');
            responseDiv.innerHTML = '⏳ Razmišljam...';
            
            try {
                // Uporabi tvoj API ključ
                const res = await fetch('https://openrouter.ai/api/v1/chat/completions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + OPENROUTER_API_KEY
                    },
                    body: JSON.stringify({
                        model: 'google/gemini-2.0-flash-exp:free',
                        messages: [
                            { role: 'system', content: 'Ti si AstraMentalica AI asistent. Odgovarjaj v slovenščini, kratko in jedrnato. Imej mističen, a prijazen ton.' },
                            { role: 'user', content: prompt }
                        ]
                    })
                });
                
                const data = await res.json();
                const answer = data.choices?.[0]?.message?.content || 'Oprosti, ne morem odgovoriti.';
                
                responseDiv.innerHTML = answer;
                this.speak(answer.substring(0, 500));
            } catch(e) {
                responseDiv.innerHTML = 'Napaka: ' + e.message;
                this.speak('Prišlo je do napake pri povezavi z AI.');
            }
        },
        
        speak: function(text) {
            if (!window.speechSynthesis) return;
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'sl-SI';
            utterance.rate = 0.9;
            window.speechSynthesis.speak(utterance);
        },
        
        showToast: function(msg) {
            const toast = document.getElementById('voiceToast');
            toast.textContent = msg;
            toast.style.opacity = '1';
            setTimeout(() => { toast.style.opacity = '0'; }, 2000);
        },
        
        showHelp: function() {
            const help = `🎤 GLASOVNI UKAZI:
• "odpri Aeternum" - odpre modul
• "shrani" - shrani datoteko
• "zaženi" - zaženi datoteko
• "išči [pojem]" - išče po projektu
• "preberi" - prebere besedilo na glas
• "diktiraj" - diktira v urejevalnik
• "pogovori se" - AI pogovor
• "pomoč" - ta seznam
• "zapri asistent" - skrije panel

💡 Nasveti:
• CTRL+M - vklopi/izklopi mikrofon
• CTRL+P - prikaži/skrij panel
• AI ima tvoj OpenRouter ključ!`;
            
            alert(help);
            this.speak('Ukazi so prikazani v oknu');
        }
    };
    
    document.addEventListener('DOMContentLoaded', () => VoiceAssistant.init());
    </script>
    <?php
}
?>