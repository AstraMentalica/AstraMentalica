// ============================================================
// KNJIŽNICA VARUHOV – Centralna logika
// ============================================================

const KnjiznicaVaruhov = {
  varuhi: null,
  trenutniVaruh: null,
  zgodovina: [],
  
  // Inicializacija
  init: async function() {
    await this.naloziVaruhe();
    this.ustvariUI();
    this.nastaviGlasovno();
  },
  
  // Naloži varuhe iz JSON
  naloziVaruhe: async function() {
    const odgovor = await fetch('varuhi.json');
    const data = await odgovor.json();
    this.varuhi = data.varuhi;
    this.zivali = data.zivali;
  },
  
  // Ustvari UI (gumbi za varuhe)
  ustvariUI: function() {
    const container = document.getElementById('varuhi-kontejner');
    if (!container) return;
    
    container.innerHTML = '';
    this.varuhi.forEach(varuh => {
      const gumb = document.createElement('button');
      gumb.className = `varuh-gumb ${this.trenutniVaruh === varuh.id ? 'aktiven' : ''}`;
      gumb.style.borderColor = varuh.barva;
      gumb.innerHTML = `
        <div style="font-size:24px">${varuh.ikona}</div>
        <div style="font-size:11px">${varuh.ime}</div>
      `;
      gumb.onclick = () => this.izberiVaruha(varuh.id);
      container.appendChild(gumb);
    });
  },
  
  // Izberi varuha
  izberiVaruha: function(id) {
    this.trenutniVaruh = id;
    const varuh = this.varuhi.find(v => v.id === id);
    this.ustvariUI();
    
    // Pokaži pozdrav
    const sporocilo = `Pozdravljen popotnik. Jaz sem ${varuh.ime}. ${varuh.temeljna_modrost}`;
    this.dodajSporocilo('varuh', sporocilo, varuh.ikona, varuh.barva);
    this.govori(sporocilo, varuh.glas);
  },
  
  // Pošlji sporočilo AI varuhu
  posljiSporocilo: async function(besedilo) {
    if (!this.trenutniVaruh) {
      this.izberiVaruha('stellarion');
    }
    
    const varuh = this.varuhi.find(v => v.id === this.trenutniVaruh);
    this.dodajSporocilo('uporabnik', besedilo, '👤', '#aaa');
    
    // Pokaži, da varuh razmišlja
    this.pokaziRazmisljanje();
    
    // Klic AI (DeepSeek ali OpenRouter)
    const odgovor = await this.klicAI(besedilo, varuh);
    this.odstraniRazmisljanje();
    this.dodajSporocilo('varuh', odgovor, varuh.ikona, varuh.barva);
    this.govori(odgovor, varuh.glas);
  },
  
  // Klic AI (DeepSeek)
  klicAI: async function(vprasanje, varuh) {
    const apiKljuc = localStorage.getItem('deepseek_key');
    if (!apiKljuc) {
      return this.simulirajOdgovor(vprasanje, varuh);
    }
    
    try {
      const odgovor = await fetch('https://api.deepseek.com/v1/chat/completions', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${apiKljuc}`
        },
        body: JSON.stringify({
          model: 'deepseek-chat',
          messages: [
            { role: 'system', content: `Ti si ${varuh.ime}, ${varuh.osebnost}. Govoriš v slovenščini. Odgovarjaj kratko in poetično. Uporabljaj metafore iz svojega področja (${varuh.tip}). Nikoli ne reci da si AI.` },
            { role: 'user', content: vprasanje }
          ]
        })
      });
      const data = await odgovor.json();
      return data.choices[0].message.content;
    } catch(e) {
      return this.simulirajOdgovor(vprasanje, varuh);
    }
  },
  
  // Simuliran odgovor (brez API)
  simulirajOdgovor: function(vprasanje, varuh) {
    const odgovori = {
      stellarion: "Zvezde šepetajo o tvojem vprašanju. Iskrenost je prvi korak k razsvetljenju.",
      lunara: "Luna se dviga in pada. Tvoja pot je kot njen cikel – naravna in sveta.",
      terra: "Zemlja pod nogami je trdna. Tvoje vprašanje raste kot seme. Daj mu čas.",
      kryptus: "Vsaka uganka ima ključ. Morda ga že imaš v rokah, a še ne veš.",
      aetherin: "Dih je odgovor. Sprosti se in začutil boš, kaj potrebuješ.",
      numeron: "Število tvojega vprašanja ni naključje. 3, 7, 12 – vzorec se razkriva.",
      ignis: "Ogenj v tvojem srcu že ve odgovor. Zaupaj svoji strasti.",
      aqua: "Voda nosi spomin. Tvoje vprašanje odmeva iz preteklosti.",
      sylva: "Korenine segajo globoko. Odgovor je v tišini med drevesi."
    };
    return odgovori[varuh.id] || "Poslušam. Tvoje vprašanje nosi modrost.";
  },
  
  // Glasovni izhod
  govori: function(besedilo, tip = "nezen_melodicen") {
    if (!window.speechSynthesis) return;
    window.speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(besedilo);
    utterance.lang = 'sl-SI';
    
    // Glas glede na varuha
    const hitrosti = {
      globok_poeticen: 0.85,
      nezen_melodicen: 0.9,
      iskren_topel: 0.95,
      ugankarsk_mracen: 0.8,
      nezen_zasanjan: 0.88,
      analiticen_misticen: 0.92,
      strasten_odlocen: 1.0,
      umirjen_tekoč: 0.87,
      zemeljski_mir: 0.9
    };
    
    utterance.rate = hitrosti[tip] || 0.9;
    utterance.pitch = tip === 'ugankarsk_mracen' ? 0.7 : 1.0;
    window.speechSynthesis.speak(utterance);
  },
  
  // UI pomožne funkcije
  dodajSporocilo: function(vloga, besedilo, ikona, barva) {
    const container = document.getElementById('pogovor');
    if (!container) return;
    
    const div = document.createElement('div');
    div.className = `sporocilo ${vloga}`;
    div.innerHTML = `
      <div class="ikona" style="color:${barva}">${ikona}</div>
      <div class="besedilo">${besedilo}</div>
    `;
    container.appendChild(div);
    div.scrollIntoView({ behavior: 'smooth' });
  },
  
  pokaziRazmisljanje: function() {
    const container = document.getElementById('pogovor');
    if (!container) return;
    this.razmisljanjeEl = document.createElement('div');
    this.razmisljanjeEl.className = 'sporocilo varuh razmislja';
    this.razmisljanjeEl.innerHTML = `<div class="ikona">🌀</div><div class="besedilo"><span class="pikice"></span></div>`;
    container.appendChild(this.razmisljanjeEl);
  },
  
  odstraniRazmisljanje: function() {
    if (this.razmisljanjeEl) this.razmisljanjeEl.remove();
  },
  
  // Glasovni vnos
  nastaviGlasovno: function() {
    if (!('webkitSpeechRecognition' in window)) return;
    const recognition = new webkitSpeechRecognition();
    recognition.lang = 'sl-SI';
    recognition.continuous = false;
    
    const gumb = document.getElementById('glas-gumb');
    if (!gumb) return;
    
    gumb.onclick = () => {
      recognition.start();
      gumb.classList.add('poslusan');
    };
    
    recognition.onresult = (e) => {
      const besedilo = e.results[0][0].transcript;
      document.getElementById('vnos').value = besedilo;
      this.posljiSporocilo(besedilo);
      gumb.classList.remove('poslusan');
    };
    
    recognition.onend = () => gumb.classList.remove('poslusan');
  }
};

// Zagon ob nalaganju
document.addEventListener('DOMContentLoaded', () => KnjiznicaVaruhov.init());