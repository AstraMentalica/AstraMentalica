/**
 * DATOTEKA: voice_commands.js
 * NAMEN:    Celotno glasovno upravljanje — Web Speech API
 * NIVO:     0 (frontend)
 * ODVISNO:  noben
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 */

const VoiceControl = (function() {
    'use strict';
    
    // ========== SPREMENLJIVKE ==========
    let recognition = null;
    let isListening = false;
    let synth = window.speechSynthesis;
    let settings = {
        lang: 'sl-SI',
        rate: 1.0,
        pitch: 1.0,
        autoListen: false,
        wakeWord: null  // 'pozor' ali 'hej Aeternum' aktivacija
    };
    
    let currentUtterance = null;
    let commandHistory = [];
    
    // ========== INICIALIZACIJA ==========
    function init() {
        // Preveri podporo
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            console.warn('Web Speech API ni podprt v tem brskalniku.');
            showUnsupportedMessage();
            return;
        }
        
        // Ustvari recognizer
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.continuous = true;      // neprekinjeno poslušanje
        recognition.interimResults = true;  // vmesni rezultati
        recognition.lang = settings.lang;
        
        // Eventi
        recognition.onresult = onSpeechResult;
        recognition.onerror = onSpeechError;
        recognition.onend = onSpeechEnd;
        
        // Naloži uporabniške nastavitve
        loadSettings();
        
        // Dodaj UI elemente
        createVoiceUI();
        
        // Dodaj tipkovnično bližnjico (Ctrl + preslednica)
        document.addEventListener('keydown', onKeyPress);
        
        console.log('Glasovni upravljalnik inicializiran');
    }
    
    // ========== UI ELEMENTI ==========
    function createVoiceUI() {
        // Glavni gumb za glas
        const voiceBtn = document.createElement('div');
        voiceBtn.id = 'voice-control-btn';
        voiceBtn.innerHTML = '🎤';
        voiceBtn.title = 'Glasovno upravljanje (Ctrl+Space)';
        voiceBtn.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #b8960c;
            color: #0a0806;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            cursor: pointer;
            z-index: 10000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transition: all 0.3s;
        `;
        voiceBtn.onclick = toggleListening;
        document.body.appendChild(voiceBtn);
        
        // Indikator poslušanja
        const indicator = document.createElement('div');
        indicator.id = 'voice-indicator';
        indicator.style.cssText = `
            position: fixed;
            bottom: 90px;
            right: 20px;
            background: rgba(0,0,0,0.8);
            color: #e8c84a;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            z-index: 10000;
            display: none;
            font-family: monospace;
            border: 1px solid #b8960c;
        `;
        document.body.appendChild(indicator);
        
        // Glasovni ukazi help panel
        const helpPanel = createHelpPanel();
        document.body.appendChild(helpPanel);
    }
    
    function createHelpPanel() {
        const panel = document.createElement('div');
        panel.id = 'voice-help';
        panel.innerHTML = `
            <div style="font-weight:bold; margin-bottom:8px;">🎤 GLASOVNI UKAZI</div>
            <div>📖 "preberi" / "beri" — prebere trenutno vsebino</div>
            <div>✏️ "zapiši [besedilo]" — doda zapisek</div>
            <div>🔍 "poišči [beseda]" — išče po strani</div>
            <div>⬅️ "nazaj" / "domov" — navigacija</div>
            <div>📜 "moj dnevnik" — prikaže glasovne zapiske</div>
            <div>⚙️ "nastavitve" — odpre glasovne nastavitve</div>
            <div>⏹️ "nehaj" — ustavi branje</div>
            <div>🎤 "aktiviraj" / "deaktiviraj" — vklop/izklop</div>
            <hr style="margin:8px 0; border-color:#b8960c;">
            <div style="font-size:10px;">💡 Reci "pomoč" za ta meni</div>
        `;
        panel.style.cssText = `
            position: fixed;
            bottom: 90px;
            right: 90px;
            background: rgba(10,8,6,0.95);
            backdrop-filter: blur(10px);
            border: 1px solid #b8960c;
            border-radius: 12px;
            padding: 12px;
            font-size: 11px;
            color: #d4c5a9;
            z-index: 9999;
            display: none;
            max-width: 280px;
            font-family: monospace;
            pointer-events: none;
        `;
        return panel;
    }
    
    function showHelp() {
        const panel = document.getElementById('voice-help');
        if (panel) {
            panel.style.display = 'block';
            setTimeout(() => {
                panel.style.display = 'none';
            }, 8000);
        }
    }
    
    function showIndicator(text, isError = false) {
        const indicator = document.getElementById('voice-indicator');
        if (indicator) {
            indicator.textContent = text;
            indicator.style.display = 'block';
            indicator.style.color = isError ? '#ff8888' : '#e8c84a';
            setTimeout(() => {
                indicator.style.display = 'none';
            }, 2000);
        }
    }
    
    // ========== POSLUŠANJE ==========
    function toggleListening() {
        if (isListening) {
            stopListening();
        } else {
            startListening();
        }
    }
    
    function startListening() {
        if (!recognition) return;
        
        try {
            recognition.start();
            isListening = true;
            updateButtonState(true);
            showIndicator('🎤 Poslušam... (povej ukaz)');
            
            // Vibrate if supported
            if (navigator.vibrate) navigator.vibrate(100);
        } catch (e) {
            console.error('Napaka pri zagonu poslušanja:', e);
            showIndicator('❌ Napaka pri zagonu', true);
        }
    }
    
    function stopListening() {
        if (recognition && isListening) {
            recognition.stop();
            isListening = false;
            updateButtonState(false);
            showIndicator('⏹️ Poslušanje ustavljeno');
        }
    }
    
    function updateButtonState(listening) {
        const btn = document.getElementById('voice-control-btn');
        if (btn) {
            btn.style.background = listening ? '#4caf50' : '#b8960c';
            btn.style.boxShadow = listening ? '0 0 15px #4caf50' : '0 4px 12px rgba(0,0,0,0.3)';
        }
    }
    
    // ========== OBDELAVA GOVORA ==========
    function onSpeechResult(event) {
        let interimTranscript = '';
        let finalTranscript = '';
        
        for (let i = event.resultIndex; i < event.results.length; i++) {
            const transcript = event.results[i][0].transcript;
            if (event.results[i].isFinal) {
                finalTranscript += transcript;
            } else {
                interimTranscript += transcript;
            }
        }
        
        if (finalTranscript) {
            processCommand(finalTranscript.toLowerCase());
        }
        
        // Prikaži vmesne rezultate
        if (interimTranscript) {
            showIndicator(`🎤: ${interimTranscript}`, false);
        }
    }
    
    function onSpeechError(event) {
        console.error('Glasovna napaka:', event.error);
        if (event.error === 'not-allowed') {
            showIndicator('❌ Dovoljenje za mikrofon zavrnjeno', true);
        } else {
            showIndicator(`❌ Napaka: ${event.error}`, true);
        }
        stopListening();
    }
    
    function onSpeechEnd() {
        isListening = false;
        updateButtonState(false);
        
        // Če je auto-listen vklopljen, ponovno zaženi
        if (settings.autoListen) {
            setTimeout(() => startListening(), 500);
        }
    }
    
    // ========== PROCESIRANJE UKAZOV ==========
    function processCommand(text) {
        console.log('Ukaz:', text);
        
        // Shrani v zgodovino
        commandHistory.unshift({ text: text, time: new Date() });
        if (commandHistory.length > 50) commandHistory.pop();
        
        // ===== NAVIGACIJA =====
        if (text.includes('domov') || text.includes('glavna')) {
            window.location.href = '/';
            speak('Vračam se na domačo stran.');
            return;
        }
        
        if (text.includes('nazaj')) {
            window.history.back();
            speak('Korak nazaj.');
            return;
        }
        
        // ===== BRANJE =====
        if (text.includes('preberi') || text.includes('beri')) {
            readCurrentContent();
            return;
        }
        
        if (text.includes('nehaj') || text.includes('ustavi')) {
            stopReading();
            return;
        }
        
        // ===== ISKANJE =====
        if (text.includes('poišči')) {
            const searchTerm = text.replace(/poišči|isci|najdi/g, '').trim();
            if (searchTerm) {
                performSearch(searchTerm);
            } else {
                speak('Kaj naj iščem?');
            }
            return;
        }
        
        // ===== ZAPISKI =====
        if (text.includes('zapiši')) {
            const note = text.replace(/zapiši|zapis|shrani/g, '').trim();
            if (note) {
                saveVoiceNote(note);
            } else {
                speak('Kaj naj zapišem?');
            }
            return;
        }
        
        // ===== GLASOVNI DNEVNIK =====
        if (text.includes('moj dnevnik') || text.includes('glasovni dnevnik')) {
            showVoiceDiary();
            return;
        }
        
        // ===== NASTAVITVE =====
        if (text.includes('nastavitve')) {
            showVoiceSettings();
            return;
        }
        
        // ===== POMOČ =====
        if (text.includes('pomoč') || text.includes('pomoc') || text.includes('kaj lahko')) {
            showHelp();
            speak('Glasovni ukazi: preberi, zapiši, poišči, nazaj, domov, moj dnevnik, nastavitve.');
            return;
        }
        
        // ===== AKTIVACIJA/DEAKTIVACIJA =====
        if (text.includes('aktiviraj') && !isListening) {
            startListening();
            return;
        }
        
        if (text.includes('deaktiviraj') && isListening) {
            stopListening();
            return;
        }
        
        // ===== MODUL SPECIFIČNI UKAZI =====
        if (text.includes('vstopi v') || text.includes('odpri modul')) {
            const moduleName = text.replace(/vstopi v|odpri modul|pojdi v/g, '').trim();
            if (moduleName) {
                window.location.href = `/?modul=${encodeURIComponent(moduleName)}`;
                speak(`Vstopam v modul ${moduleName}`);
            }
            return;
        }
        
        // Ne prepoznam
        speak('Ukaza ne razumem. Reci "pomoč" za seznam ukazov.');
    }
    
    // ========== BRANJE VSEBINE ==========
    function readCurrentContent() {
        // Poišči glavno vsebino strani
        const contentSelectors = [
            'main', 
            '.content', 
            '.vsebina', 
            'article',
            '.puerilis-card',
            '.antiquus-card',
            '[class*="vsebina"]'
        ];
        
        let textToRead = '';
        
        for (const selector of contentSelectors) {
            const elements = document.querySelectorAll(selector);
            if (elements.length > 0) {
                elements.forEach(el => {
                    textToRead += el.innerText + ' ';
                });
                break;
            }
        }
        
        // Če ni specifične vsebine, preberi body
        if (!textToRead) {
            textToRead = document.body.innerText;
        }
        
        // Omeji dolžino (max 3000 znakov)
        if (textToRead.length > 3000) {
            textToRead = textToRead.substring(0, 3000) + '... To je konec.';
        }
        
        speak(textToRead);
    }
    
    function speak(text) {
        if (!synth) {
            console.warn('Speech synthesis not supported');
            return;
        }
        
        if (currentUtterance) {
            synth.cancel();
        }
        
        currentUtterance = new SpeechSynthesisUtterance(text);
        currentUtterance.lang = settings.lang;
        currentUtterance.rate = settings.rate;
        currentUtterance.pitch = settings.pitch;
        
        currentUtterance.onend = () => {
            currentUtterance = null;
        };
        
        synth.speak(currentUtterance);
    }
    
    function stopReading() {
        if (synth) {
            synth.cancel();
            currentUtterance = null;
            speak('Branje ustavljeno.');
        }
    }
    
    // ========== ISKANJE ==========
    function performSearch(term) {
        // Poskusi najti na trenutni strani
        const bodyText = document.body.innerText.toLowerCase();
        if (bodyText.includes(term.toLowerCase())) {
            speak(`Beseda ${term} je na tej strani.`);
            // Označi prvo najdbo
            highlightSearchTerm(term);
        } else {
            // Preusmeri na iskanje po modulih
            window.location.href = `/?iskanje=${encodeURIComponent(term)}`;
            speak(`Prenašam na iskanje po ${term}`);
        }
    }
    
    function highlightSearchTerm(term) {
        const regex = new RegExp(`(${term})`, 'gi');
        const walker = document.createTreeWalker(
            document.body,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: function(node) {
                    if (node.parentElement && 
                        (node.parentElement.tagName === 'SCRIPT' || 
                         node.parentElement.tagName === 'STYLE')) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    return NodeFilter.FILTER_ACCEPT;
                }
            }
        );
        
        const nodesToReplace = [];
        while (walker.nextNode()) {
            if (regex.test(walker.currentNode.textContent)) {
                nodesToReplace.push(walker.currentNode);
            }
        }
        
        nodesToReplace.forEach(node => {
            const span = document.createElement('span');
            span.innerHTML = node.textContent.replace(regex, '<mark style="background:#e8c84a;color:#0a0806;">$1</mark>');
            node.parentNode.replaceChild(span, node);
        });
        
        setTimeout(() => {
            const marks = document.querySelectorAll('mark');
            marks.forEach(mark => {
                const parent = mark.parentNode;
                parent.replaceChild(document.createTextNode(mark.textContent), mark);
                parent.normalize();
            });
        }, 5000);
    }
    
    // ========== ZAPISKI ==========
    async function saveVoiceNote(note) {
        try {
            const response = await fetch('/SISTEM/api.php?pot=glasovni/zapisi', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    prepis: note,
                    tip: 'zapisek',
                    kontekst: window.location.pathname
                })
            });
            
            const data = await response.json();
            
            if (data.uspeh) {
                speak('Zapisek shranjen v tvoj glasovni dnevnik.');
                showIndicator('📝 Zapisek shranjen');
            } else {
                speak('Napaka pri shranjevanju.');
            }
        } catch (e) {
            console.error(e);
            speak('Napaka pri povezavi.');
        }
    }
    
    async function showVoiceDiary() {
        try {
            const response = await fetch('/SISTEM/api.php?pot=glasovni/dnevnik?limit=10');
            const data = await response.json();
            
            if (data.uspeh && data.vnosi.length > 0) {
                const diaryText = data.vnosi.map((v, i) => `${i+1}. ${v.prepis}`).join('. ');
                speak(`Imaš ${data.vnosi.length} glasovnih zapiskov. ${diaryText}`);
                
                // Prikaži modal
                showDiaryModal(data.vnosi);
            } else {
                speak('Glasovni dnevnik je prazen.');
            }
        } catch (e) {
            console.error(e);
            speak('Napaka pri branju dnevnika.');
        }
    }
    
    function showDiaryModal(entries) {
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #1a1410;
            border: 2px solid #b8960c;
            border-radius: 12px;
            padding: 20px;
            max-width: 500px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 10001;
            color: #d4c5a9;
        `;
        modal.innerHTML = `
            <h3>📜 Glasovni dnevnik</h3>
            ${entries.map(e => `<p><small>${e.ustvarjeno}</small><br>${e.prepis}</p><hr>`).join('')}
            <button onclick="this.parentElement.remove()" style="background:#b8960c;border:none;padding:8px16px;border-radius:4px;cursor:pointer;">Zapri</button>
        `;
        document.body.appendChild(modal);
    }
    
    // ========== NASTAVITVE ==========
    function showVoiceSettings() {
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #1a1410;
            border: 2px solid #b8960c;
            border-radius: 12px;
            padding: 20px;
            width: 300px;
            z-index: 10001;
            color: #d4c5a9;
        `;
        modal.innerHTML = `
            <h3>⚙️ Glasovne nastavitve</h3>
            <label>Hitrost branja: <input type="range" id="voice-rate" min="0.5" max="2" step="0.1" value="${settings.rate}"></label><br>
            <label>Višina glasu: <input type="range" id="voice-pitch" min="0.5" max="2" step="0.1" value="${settings.pitch}"></label><br>
            <label><input type="checkbox" id="voice-auto"> Samodejno poslušanje</label><br>
            <button id="voice-save-settings" style="background:#b8960c;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;margin-top:10px;">Shrani</button>
            <button id="voice-close-settings" style="background:transparent;border:1px solid #b8960c;padding:8px 16px;border-radius:4px;cursor:pointer;margin-top:10px;">Zapri</button>
        `;
        
        document.body.appendChild(modal);
        
        document.getElementById('voice-rate').addEventListener('input', (e) => {
            settings.rate = parseFloat(e.target.value);
        });
        document.getElementById('voice-pitch').addEventListener('input', (e) => {
            settings.pitch = parseFloat(e.target.value);
        });
        document.getElementById('voice-auto').addEventListener('change', (e) => {
            settings.autoListen = e.target.checked;
        });
        document.getElementById('voice-save-settings').onclick = () => saveSettingsToBackend();
        document.getElementById('voice-close-settings').onclick = () => modal.remove();
    }
    
    async function saveSettingsToBackend() {
        try {
            const response = await fetch('/SISTEM/api.php?pot=glasovni/nastavitve', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    hitrost: settings.rate,
                    visina: settings.pitch,
                    samodejno_poslusanje: settings.autoListen ? 1 : 0
                })
            });
            
            if (response.ok) {
                speak('Nastavitve shranjene.');
                document.querySelector('.voice-settings-modal')?.remove();
            }
        } catch (e) {
            console.error(e);
        }
    }
    
    async function loadSettings() {
        try {
            const response = await fetch('/SISTEM/api.php?pot=glasovni/nastavitve');
            const data = await response.json();
            if (data.uspeh && data.nastavitve) {
                settings.rate = data.nastavitve.hitrost || 1.0;
                settings.pitch = data.nastavitve.visina || 1.0;
                settings.autoListen = data.nastavitve.samodejno_poslusanje === 1;
                settings.lang = data.nastavitve.jezik || 'sl-SI';
                if (recognition) recognition.lang = settings.lang;
            }
        } catch (e) {
            console.log('Uporabljam privzete nastavitve');
        }
    }
    
    function onKeyPress(event) {
        // Ctrl + Space ali Ctrl + P
        if ((event.ctrlKey && event.code === 'Space') || (event.ctrlKey && event.code === 'KeyP')) {
            event.preventDefault();
            toggleListening();
        }
        
        // ESC ustavi branje
        if (event.code === 'Escape') {
            stopReading();
        }
    }
    
    function showUnsupportedMessage() {
        const msg = document.createElement('div');
        msg.style.cssText = `
            position: fixed;
            bottom: 90px;
            right: 20px;
            background: #8b0000;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            z-index: 10000;
        `;
        msg.innerHTML = '⚠️ Glasovno upravljanje ni podprto v tem brskalniku. Uporabi Chrome, Edge ali Safari.';
        document.body.appendChild(msg);
        setTimeout(() => msg.remove(), 5000);
    }
    
    // ========== JAVNI API ==========
    return {
        init: init,
        startListening: startListening,
        stopListening: stopListening,
        speak: speak,
        stopReading: stopReading
    };
})();

// Avtomatska inicializacija
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => VoiceControl.init());
} else {
    VoiceControl.init();
}