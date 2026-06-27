📁 voice.js – samostojen glasovni vtičnik
javascript
// ============================================================
// voice.js – glasovni vnos in branje odgovorov
// ============================================================

(function() {
  let recognition = null;
  let micActive = false;
  let onResultCallback = null;
  let onStopCallback = null;

  // Inicializacija
  function init() {
    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
      console.warn('Brskalnik ne podpira SpeechRecognition.');
      return;
    }
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    recognition.lang = 'sl-SI';
    recognition.continuous = true;
    recognition.interimResults = false;

    recognition.onresult = function(event) {
      const last = event.results[event.results.length - 1];
      if (last.isFinal && onResultCallback) {
        const text = last[0].transcript.trim();
        if (text) onResultCallback(text);
      }
    };

    recognition.onerror = function(event) {
      if (event.error === 'not-allowed') {
        console.warn('Dovoljenje za mikrofon zavrnjeno.');
        stopMic();
      }
      if (onStopCallback) onStopCallback();
    };

    recognition.onend = function() {
      if (micActive) {
        // Če je še vedno aktivno, poskusi ponovno zagnati
        try { recognition.start(); } catch(e) {}
      }
      if (onStopCallback) onStopCallback();
    };
  }

  // Zaženi mikrofon
  function startMic(callback) {
    if (!recognition) init();
    if (!recognition) return;

    // Prosi za dovoljenje
    navigator.mediaDevices.getUserMedia({ audio: true })
      .then(() => {
        micActive = true;
        try { recognition.start(); } catch(e) {}
        if (callback) callback(true);
      })
      .catch(() => {
        console.warn('Dostop do mikrofona zavrnjen.');
        if (callback) callback(false);
      });
  }

  // Ustavi mikrofon
  function stopMic() {
    micActive = false;
    try { if (recognition) recognition.stop(); } catch(e) {}
  }

  // Nastavi callback, ko je prepoznan govor
  function onVoice(callback) {
    if (typeof callback === 'function') onResultCallback = callback;
  }

  // Nastavi callback, ko se mikrofon ustavi
  function onStop(callback) {
    if (typeof callback === 'function') onStopCallback = callback;
  }

  // Preveri, ali je mikrofon aktiven
  function isActive() {
    return micActive;
  }

  // ============================================================
  // BRANJE BESEDILA (govori nazaj)
  // ============================================================
  function speak(text, lang = 'sl-SI', rate = 0.9, pitch = 1) {
    if (!window.speechSynthesis) return;
    window.speechSynthesis.cancel(); // prekini morebitno prejšnje branje
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = lang;
    utterance.rate = rate;
    utterance.pitch = pitch;
    window.speechSynthesis.speak(utterance);
  }

  // ============================================================
  // JAVNI API
  // ============================================================
  window.Voice = {
    startMic: startMic,
    stopMic: stopMic,
    onVoice: onVoice,
    onStop: onStop,
    isActive: isActive,
    speak: speak
  };

})();
✅ Kako uporabiti:
1. Shrani kot voice.js v mapo (npr. js/voice.js).
2. Vključi v svojo HTML stran:
html
<script src="js/voice.js"></script>
3. Uporabi v svojem JavaScriptu:
javascript
// Ko stran naložiš
document.addEventListener('DOMContentLoaded', function() {

  // Nastavi callback za prepoznan govor
  Voice.onVoice(function(tekst) {
    console.log('Rekel si:', tekst);
    // Tukaj pošlji tekst v API ali v klepet
    document.getElementById('vnos').value = tekst;
    document.getElementById('poslji-gumb').click();
  });

  // Ko se mikrofon ustavi (neobvezno)
  Voice.onStop(function() {
    console.log('Mikrofon ustavljen.');
  });

  // Gumb za vklop mikrofona
  document.getElementById('mic-gumb').addEventListener('click', function() {
    if (Voice.isActive()) {
      Voice.stopMic();
      this.textContent = '🎤';
    } else {
      Voice.startMic(function(uspeh) {
        if (uspeh) {
          document.getElementById('mic-gumb').textContent = '⏹';
        }
      });
    }
  });

  // Branje odgovora na glas
  function preberiOdgovor(besedilo) {
    Voice.speak(besedilo, 'sl-SI', 0.9);
  }

});
📦 Kaj vsebuje voice.js:
Funkcija	Opis
Voice.startMic(callback)	Vklopi mikrofon, callback dobi true/false
Voice.stopMic()	Ustavi mikrofon
Voice.onVoice(callback)	Nastavi funkcijo, ki se kliče ob prepoznanem govoru
Voice.onStop(callback)	Nastavi funkcijo, ko se mikrofon ustavi
Voice.isActive()	Vrne true/false, ali je mikrofon vključen
Voice.speak(text)	Prebere besedilo na glas (slovenščina)