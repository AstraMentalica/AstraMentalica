2. runtime/portali.js – Magični in skrivni portali
javascript
// Sistem portalov – magični in skrivni prehodi
window.Portali = {
  portali: [],
  aktivniPortal: null,
  
  iniciacija() {
    this.ustvariPortale();
    this.prikaziGumbZaPortale();
  },
  
  ustvariPortale() {
    this.portali = [
      {
        ime: "Portal modrosti",
        tip: "magični",
        barva: "#4a90e2",
        simbol: "🌀",
        lokacija: { x: 15, y: 50 },
        odklenjen: true,
        akcija: () => {
          magičnoObvestilo("📚 Portal modrosti ti odpira knjige znanja!");
          UmetnaInteligenca?.govori("Modrost prihaja. Postavljam ti čarobno knjižnico.");
          this.odpriKnjiznico();
        }
      },
      {
        ime: "Portal domišljije",
        tip: "magični",
        barva: "#e91e63",
        simbol: "🎨",
        lokacija: { x: 85, y: 50 },
        odklenjen: true,
        akcija: () => {
          magičnoObvestilo("🎨 Portal domišljije sprošča tvojo ustvarjalnost!");
          this.dodajUstvarjalneGradnike();
        }
      },
      {
        ime: "Skriti portal nočnih senc",
        tip: "skrivni",
        barva: "#2c3e50",
        simbol: "🌙",
        lokacija: { x: 50, y: 85 },
        odklenjen: false,
        zahteva: "energija >= 50",
        skrivnost: "Pojavi se le ob polni energiji",
        akcija: () => {
          magičnoObvestilo("🌙 Vstopil si v kraljestvo senc! Skrivni gradniki so tvoji.");
          this.odkleniSkrivneGradnike();
        }
      },
      {
        ime: "Kristalni portal",
        tip: "magični",
        barva: "#d4af37",
        simbol: "💎",
        lokacija: { x: 50, y: 20 },
        odklenjen: true,
        akcija: () => {
          magičnoObvestilo("💎 Kristalni portal ti podarja 10 kristalov!");
          UmetnaInteligenca?.dodajKristale(10);
        }
      },
      {
        ime: "Skriti portal velike sove",
        tip: "skrivni",
        barva: "#9b59b6",
        simbol: "🦉",
        lokacija: { x: 90, y: 20 },
        odklenjen: false,
        zahteva: "avatar == 'Zlata Sova'",
        skrivnost: "Pokaže se le, ko je aktivirana Zlata Sova",
        akcija: () => {
          magičnoObvestilo("🦉 Sova ti šepeta skrivnosti prihodnosti!");
          this.pokaziPrerokbo();
        }
      }
    ];
  },
  
  prikaziGumbZaPortale() {
    const gumb = document.createElement('button');
    gumb.id = 'gumb-portali';
    gumb.className = 'gumb-portali';
    gumb.innerHTML = '🌀 Portali';
    gumb.onclick = () => this.prikaziVsePortale();
    document.querySelector('.peskovnik-orodja')?.appendChild(gumb);
  },
  
  prikaziVsePortale() {
    const ploca = document.createElement('div');
    ploca.id = 'ploca-portalov';
    ploca.className = 'ploca-portalov';
    ploca.innerHTML = '<div class="naslov-portalov">🌀 Čarobni portali 🌀</div><div class="portali-mreza"></div><button class="zapri-portale" id="zapri-portale">✖ Zapri</button>';
    const mreza = ploca.querySelector('.portali-mreza');
    
    this.portali.forEach(portal => {
      // Preveri, če je skrivni portal odklenjen
      let odklenjen = portal.odklenjen;
      if (!odklenjen && portal.zahteva) {
        odklenjen = this.preveriPogoj(portal.zahteva);
      }
      
      const karton = document.createElement('div');
      karton.className = `karton-portala ${odklenjen ? 'odklenjen' : 'zaklenjen'} ${portal.tip}`;
      karton.style.borderColor = portal.barva;
      karton.innerHTML = `
        <div class="portal-simbol" style="background:${portal.barva}20">${portal.simbol}</div>
        <div class="portal-ime">${portal.ime}</div>
        <div class="portal-tip">${portal.tip === 'magični' ? '✨ Magični' : '🌙 Skrivni'}</div>
        ${!odklenjen ? `<div class="portal-pogoj">🔒 ${portal.skrivnost}</div>` : ''}
        ${odklenjen ? `<button class="odpri-portal" data-ime="${portal.ime}">🔮 Odpri portal</button>` : ''}
      `;
      mreza.appendChild(karton);
    });
    
    document.body.appendChild(ploca);
    
    document.querySelectorAll('.odpri-portal').forEach(gumb => {
      gumb.onclick = (e) => {
        const ime = e.target.dataset.ime;
        this.odpriPortal(ime);
        ploca.remove();
      };
    });
    
    document.getElementById('zapri-portale')?.addEventListener('click', () => ploca.remove());
  },
  
  preveriPogoj(pogoj) {
    if (pogoj === "energija >= 50") {
      const energija = parseInt(document.getElementById('energijski-napolnjenost')?.style.width || "0");
      return energija >= 50;
    }
    if (pogoj === "avatar == 'Zlata Sova'") {
      return UmetnaInteligenca?.aktivniAvatar?.ime === "Zlata Sova";
    }
    return false;
  },
  
  odpriPortal(ime) {
    const portal = this.portali.find(p => p.ime === ime);
    if (!portal) return;
    
    magičnoObvestilo(`🌀 Odpira se ${portal.ime}...`);
    UmetnaInteligenca?.govori(`${portal.ime} se odpira. Vstopi.`);
    
    // Vizualni efekt
    const efekt = document.createElement('div');
    efekt.className = 'portal-efekt';
    efekt.style.background = `radial-gradient(circle, ${portal.barva}, transparent)`;
    document.body.appendChild(efekt);
    setTimeout(() => efekt.remove(), 2000);
    
    // Izvedi akcijo portala
    if (portal.akcija) portal.akcija();
    
    this.aktivniPortal = portal;
  },
  
  odpriKnjiznico() {
    const knjiznica = document.createElement('div');
    knjiznica.className = 'knjiznica';
    knjiznica.innerHTML = `
      <div class="knjiznica-vsebina">
        <h2>📚 Čarobna knjižnica</h2>
        <p>✨ "Ustvarjalnost nima meja" - Modri Zmaj</p>
        <p>🌟 "Vsak gradnik je tvoje otroštvo" - Zlata Sova</p>
        <p>🍄 "Nasmeh je najmočnejši čarovnija" - Veseli Goban</p>
        <button class="zapri-knjiznico">✖ Zapri</button>
      </div>
    `;
    document.body.appendChild(knjiznica);
    document.querySelector('.zapri-knjiznico')?.addEventListener('click', () => knjiznica.remove());
  },
  
  dodajUstvarjalneGradnike() {
    const noveVrste = ["risalnik", "glasbeni-studenček", "peskovnik-sveta"];
    const nakljucna = noveVrste[Math.floor(Math.random() * noveVrste.length)];
    Peskovnik?.dodajGradnik(nakljucna);
    magičnoObvestilo("🎨 Ustvarjalni gradnik je dodan!");
  },
  
  odkleniSkrivneGradnike() {
    localStorage.setItem("skrivniGradnikiOdklenjeni", "true");
    magičnoObvestilo("🔮 Odklenil si skrivne gradnike! Osveži stran.");
  },
  
  pokaziPrerokbo() {
    const prerokbe = [
      "V bližnji prihodnosti boš ustvaril nekaj čudovitega.",
      "Skrivni portal se bo odprel, ko boš najbolj potreboval navdih.",
      "Tvoj peskovnik bo postal znan med čarovniki."
    ];
    const nakljucna = prerokbe[Math.floor(Math.random() * prerokbe.length)];
    magičnoObvestilo(`🦉 Prerokba: ${nakljucna}`);
  }
};
3. Dodatni CSS slogi za UI, varuhe, avatarje in portale
Dodaj v vmesnik/css/osnova.css:
css
/* VARUHI */
.ploca-varuhov, .ploca-avatarjev, .ploca-portalov {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: var(--barva-ozadja);
  border-radius: 32px;
  padding: 24px;
  z-index: 2000;
  max-width: 90vw;
  max-height: 85vh;
  overflow-y: auto;
  box-shadow: 0 0 50px rgba(0,0,0,0.5);
  border: 2px solid var(--barva-poudarka);
}

.karton-varuha, .karton-avatarja, .karton-portala {
  background: var(--barva-gradnika);
  border-radius: 24px;
  padding: 16px;
  margin: 12px;
  text-align: center;
  transition: 0.2s;
  cursor: pointer;
}

.karton-varuha:hover, .karton-avatarja:hover, .karton-portala:hover {
  transform: translateY(-5px);
}

.aktivni-varuh, .aktivni-avatar {
  position: fixed;
  bottom: 100px;
  right: 20px;
  background: var(--barva-gradnika);
  border-radius: 60px;
  padding: 16px 24px;
  display: flex;
  gap: 16px;
  z-index: 1000;
  border-left: 6px solid;
  box-shadow: 0 5px 25px rgba(0,0,0,0.3);
  backdrop-filter: blur(10px);
}

/* PORTAL EFEKT */
.portal-efekt {
  position: fixed;
  top: 50%;
  left: 50%;
  width: 300px;
  height: 300px;
  transform: translate(-50%, -50%);
  border-radius: 50%;
  animation: portalValovanje 1s ease-out forwards;
  pointer-events: none;
  z-index: 1500;
}

@keyframes portalValovanje {
  0% { transform: translate(-50%, -50%) scale(0); opacity: 0.8; }
  100% { transform: translate(-50%, -50%) scale(5); opacity: 0; }
}

/* KNJIŽNICA */
.knjiznica {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.8);
  backdrop-filter: blur(8px);
  z-index: 3000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.knjiznica-vsebina {
  background: var(--barva-gradnika);
  border-radius: 32px;
  padding: 32px;
  max-width: 500px;
  text-align: center;
}
4. Posodobi runtime/glasovni_ukazi.js
Dodaj nove ukaze v funkcijo obdelajUkaz:

javascript
// Dodaj v obdelajUkaz(ukaz) po ostalih ukazih:

else if (ukaz.includes("varuh") || ukaz.includes("pokliči varuha") || ukaz === "varuh") {
  if (window.UmetnaInteligenca) UmetnaInteligenca.prikaziVaruhe();
}
else if (ukaz.includes("avatar") || ukaz.includes("avatarji")) {
  if (window.UmetnaInteligenca) UmetnaInteligenca.prikaziAvatarje();
}
else if (ukaz.includes("portali") || ukaz === "portali") {
  if (window.Portali) Portali.prikaziVsePortale();
}
else if (ukaz.includes("kristali") || ukaz.includes("koliko kristalov")) {
  if (window.UmetnaInteligenca) UmetnaInteligenca.odgovoriNaGlasovniUkaz(ukaz);
}
else if (ukaz.includes("kdo si") || ukaz.includes("predstavi se")) {
  if (window.UmetnaInteligenca) UmetnaInteligenca.odgovoriNaGlasovniUkaz(ukaz);
}
else if (ukaz.includes("nasvet") || ukaz.includes("pomagaj")) {
  if (window.UmetnaInteligenca) UmetnaInteligenca.odgovoriNaGlasovniUkaz(ukaz);
}
5. Posodobi render/peskovnik.html – dodaj kristale in skripte
html
<!-- Dodaj poleg energijskega traku -->
<div class="stevec-kristalov" id="stevec-kristalov">💎 0</div>

<!-- Nove skripte na koncu bodyja -->
<script src="../runtime/umetna_inteligenca.js"></script>
<script src="../runtime/portali.js"></script>
<script src="../runtime/avatarji.js"></script>
<script src="../runtime/varuhi.js"></script>
<script src="../runtime/skrivnosti.js"></script>