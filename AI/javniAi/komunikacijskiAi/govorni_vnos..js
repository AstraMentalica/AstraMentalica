<!-- Gumb za mikrofon -->
<button id="mic-toggle" onclick="toggleMic()" style="padding:6px 12px;border-radius:7px;font-size:16px;cursor:pointer;border:1px solid #252a3d;background:#0a0d14;color:#d4c5a9;">
  🎤 Mikrofon izklopljen
</button>

<script>
// ── GLASOVNI VNOS ──────────────────────────────────────────────
let micActive = false;
let recognition = null;
let synth = window.speechSynthesis;

function toggleMic() {
  if (!recognition) {
    // Ustvari recognizer, če še ne obstaja
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
      alert('Ta brskalnik ne podpira glasovnega vnosa. Uporabi Chrome/Edge.');
      return;
    }
    recognition = new SpeechRecognition();
    recognition.lang = 'sl-SI';
    recognition.continuous = true;      // ← NE IZKLJUČI SE
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;

    recognition.onresult = function(event) {
      const zadnji = event.results.length - 1;
      const prepis = event.results[zadnji][0].transcript.trim();
      if (!prepis) return;
      
      // Vstavi v trenutno vnosno polje (za aktivni tab)
      const aktivniKljuc = aktivniTab || 'nadzorni';
      const vhod = document.getElementById('vhod-' + aktivniKljuc);
      if (vhod) {
        vhod.value = prepis;
        // Samodejno pošlji agentu
        if (typeof posljiiAgent === 'function') {
          posljiiAgent(aktivniKljuc);
        }
      }
    };

    recognition.onerror = function(event) {
      // Če je napaka "not-allowed" – uporabnik je zavrnil mikrofon
      if (event.error === 'not-allowed') {
        alert('Dovoli dostop do mikrofona v brskalniku.');
        micActive = false;
        posodobiGumb();
      }
    };
  }

  // Preklopi stanje
  micActive = !micActive;
  if (micActive) {
    try {
      recognition.start();
      posodobiGumb('🎤 Mikrofon AKTIVEN (neprekinjeno)');
      // Preberi naglas
      if (synth) {
        const msg = new SpeechSynthesisUtterance('Mikrofon aktiven');
        msg.lang = 'sl-SI';
        msg.rate = 0.9;
        synth.speak(msg);
      }
    } catch(e) {
      // Če je že aktiviran, ga ne moremo znova zagnati
      micActive = true;
      posodobiGumb('🎤 Mikrofon AKTIVEN (neprekinjeno)');
    }
  } else {
    try {
      recognition.stop();
      posodobiGumb('🎤 Mikrofon izklopljen');
      if (synth) {
        const msg = new SpeechSynthesisUtterance('Mikrofon izklopljen');
        msg.lang = 'sl-SI';
        msg.rate = 0.9;
        synth.speak(msg);
      }
    } catch(e) {
      // Ignoriraj
    }
  }
}

function posodobiGumb(besedilo) {
  const gumb = document.getElementById('mic-toggle');
  if (gumb) {
    gumb.textContent = besedilo || (micActive ? '🎤 Mikrofon AKTIVEN' : '🎤 Mikrofon izklopljen');
    gumb.style.borderColor = micActive ? '#27ae60' : '#252a3d';
    gumb.style.background = micActive ? '#0a2a1a' : '#0a0d14';
  }
}

// ── ODGOVARJANJE Z GLASOM ──────────────────────────────────────
function preberiNaglas(besedilo) {
  if (!synth) return;
  // Ustavi morebitno prejšnje branje
  synth.cancel();
  const msg = new SpeechSynthesisUtterance(besedilo);
  msg.lang = 'sl-SI';
  msg.rate = 0.85;   // nekoliko počasneje
  msg.pitch = 1.0;
  // Počakaj malo, da se konča
  setTimeout(() => synth.speak(msg), 200);
}
function govori(tekst) {
    const glas = new SpeechSynthesisUtterance(tekst);
    glas.lang = 'sl-SI';
    glas.rate = 0.9;
    speechSynthesis.speak(glas);
}
// ── POVEZAVA Z OBSTOJEČO FUNKCIJO ─────────────────────────────
// Če imaš funkcijo dodajSporAgent, jo lahko preklopiš, da prebere naglas
// Primer: po dodajanju AI odgovora preberi besedilo
const originalDodajSpor = window.dodajSporAgent;
if (typeof window.dodajSporAgent === 'function') {
  window.dodajSporAgent = function(kljuc, vsebina, tip) {
    originalDodajSpor(kljuc, vsebina, tip);
    if (tip === 'ai' && micActive) {
      // Preberi samo čisti tekst (brez markdown)
      const cistText = vsebina.replace(/[#*_`]/g, '').trim();
      if (cistText.length > 0) {
        preberiNaglas(cistText);
      }
    }
  };
}

// ── OB NALAGANJU STRANI ──────────────────────────────────────
window.onload = function() {
  // Gumb za mikrofon postavi zraven vnosnega polja (lahko tudi drugam)
  const vnosnaPolja = document.querySelectorAll('[id^="vhod-"]');
  if (vnosnaPolja.length > 0) {
    // Dodaj gumb poleg zadnjega vnosnega polja
    const zadnji = vnosnaPolja[vnosnaPolja.length - 1];
    const container = zadnji.closest('div[style*="display:flex"]') || zadnji.parentNode;
    const micGumb = document.createElement('button');
    micGumb.id = 'mic-toggle';
    micGumb.textContent = '🎤 Mikrofon izklopljen';
    micGumb.onclick = toggleMic;
    micGumb.style.cssText = 'padding:6px 12px;border-radius:7px;font-size:16px;cursor:pointer;border:1px solid #252a3d;background:#0a0d14;color:#d4c5a9;margin-left:4px;';
    container.appendChild(micGumb);
  }
  posodobiGumb();
};
// Počakaj 1 sekundo, da se stran naloži, nato zaženi mikrofon
setTimeout(() => {
  if (navigator.mediaDevices) {
    navigator.mediaDevices.getUserMedia({ audio: true })
      .then(() => {
        // Dovoljenje za mikrofon je bilo dano
        toggleMic(); // samodejno vklopi
      })
      .catch(() => {
        // Uporabnik je zavrnil – ne vklopi
      });
  }
}, 1000);
</script>
