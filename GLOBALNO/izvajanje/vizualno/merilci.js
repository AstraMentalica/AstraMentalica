1. runtime/merilci.js – Biometrični senzorji
javascript
// Napredni merilci uporabnikovega vedenja
window.Merilci = {
  // Senzorji
  senzorji: {
    kliki: [],
    gibanjeMiske: [],
    nervoza: 0,
    hitrostKlikov: 0,
    zamiki: [],
    globinaScrolla: 0,
    casNaStrani: 0,
    interakcijeZGradniki: [],
    glasovniUkazi: [],
    energija: 0
  },
  
  // Začetek merjenja
  zacetekMerjenja() {
    this.pripniSenzorje();
    this.zacniPeriodicnoMerjenje();
    this.naloziDnevnoStatistiko();
    
    magičnoObvestilo("📊 Merilci aktivirani – tvoja energija se spremlja za magične napovedi");
  },
  
  pripniSenzorje() {
    // Merjenje klikov
    document.addEventListener('click', (e) => {
      const klik = {
        cas: Date.now(),
        x: e.clientX,
        y: e.clientY,
        element: e.target.tagName,
        sila: e.detail || 1,
        tip: 'klik'
      };
      this.senzorji.kliki.push(klik);
      this.analizirajKlik(klik);
      this.obdrziZadnjih(this.senzorji.kliki, 200);
    });
    
    // Merjenje gibanja miške (nervoza)
    let zadnjiX = 0, zadnjiY = 0;
    let zadnjiCas = Date.now();
    
    document.addEventListener('mousemove', (e) => {
      const trenutniCas = Date.now();
      const razlikaCasa = trenutniCas - zadnjiCas;
      const razdalja = Math.hypot(e.clientX - zadnjiX, e.clientY - zadnjiY);
      const hitrost = razdalja / razlikaCasa;
      
      this.senzorji.gibanjeMiske.push({
        cas: trenutniCas,
        x: e.clientX,
        y: e.clientY,
        hitrost: hitrost || 0
      });
      
      // Izračun nervoze (hitro, nestrpno gibanje)
      if (hitrost > 2 && razlikaCasa < 50) {
        this.povecajNervozo(0.5);
      }
      
      zadnjiX = e.clientX;
      zadnjiY = e.clientY;
      zadnjiCas = trenutniCas;
      this.obdrziZadnjih(this.senzorji.gibanjeMiske, 500);
    });
    
    // Merjenje zamikov (počasno razmišljanje)
    let zadnjaInterakcija = Date.now();
    setInterval(() => {
      const zdaj = Date.now();
      const zamik = zdaj - zadnjaInterakcija;
      if (zamik > 5000) { // več kot 5 sekund brez interakcije
        this.senzorji.zamiki.push({
          cas: zdaj,
          trajanje: zamik,
          tip: 'premisljevanje'
        });
        this.povecajNervozo(-0.3); // umirjanje
      }
      zadnjaInterakcija = zdaj;
      this.obdrziZadnjih(this.senzorji.zamiki, 50);
    }, 3000);
    
    // Merjenje scrolla
    window.addEventListener('scroll', () => {
      const scrollVisina = document.documentElement.scrollHeight - window.innerHeight;
      const scrollPozicija = window.scrollY;
      this.senzorji.globinaScrolla = (scrollPozicija / scrollVisina) * 100;
    });
    
    // Merjenje interakcij z gradniki
    document.addEventListener('click', (e) => {
      const gradnik = e.target.closest('.gradnik');
      if (gradnik) {
        this.senzorji.interakcijeZGradniki.push({
          cas: Date.now(),
          tip: 'gradnik',
          akcija: e.target.classList.contains('odstrani-grd') ? 'odstrani' :
                  e.target.classList.contains('povečaj-grd') ? 'povečaj' : 'klik',
          id: gradnik.dataset.id
        });
      }
    });
  },
  
  povecajNervozo(kolicina) {
    this.senzorji.nervoza = Math.min(100, Math.max(0, this.senzorji.nervoza + kolicina));
    this.posodobiUINervozo();
    
    // Alarm ob visoki nervozi
    if (this.senzorji.nervoza > 80 && !this.opozorjeno) {
      magičnoObvestilo("🌊 Čutiš nemir? Vdihni globoko in prikliči varuha.");
      if (window.UmetnaInteligenca) {
        UmetnaInteligenca.govori("Opazim, da si nemiren. Pokliči svojega varuha za pomoč.");
      }
      this.opozorjeno = true;
      setTimeout(() => this.opozorjeno = false, 30000);
    }
  },
  
  posodobiUINervozo() {
    const barNervoze = document.getElementById('bar-nervoze');
    if (barNervoze) {
      barNervoze.style.width = `${this.senzorji.nervoza}%`;
      barNervoze.style.background = `linear-gradient(90deg, #4caf50, #ff9800, #f44336)`;
    }
    
    const stevilkaNervoze = document.getElementById('stevilka-nervoze');
    if (stevilkaNervoze) {
      stevilkaNervoze.textContent = Math.round(this.senzorji.nervoza);
    }
  },
  
  analizirajKlik(klik) {
    // Analiza sile klika (približna)
    const sila = klik.sila;
    if (sila > 1) {
      this.povecajNervozo(1);
    }
    
    // Hitrost klikov
    const zadnjiKliki = this.senzorji.kliki.slice(-10);
    if (zadnjiKliki.length >= 2) {
      const casovnaRazlika = zadnjiKliki[zadnjiKliki.length-1].cas - zadnjiKliki[zadnjiKliki.length-2].cas;
      const hitrost = 1000 / casovnaRazlika;
      this.senzorji.hitrostKlikov = Math.min(10, hitrost);
      
      if (hitrost > 5) {
        this.povecajNervozo(0.8);
      }
    }
  },
  
  zacniPeriodicnoMerjenje() {
    // Čas na strani
    let startCas = Date.now();
    setInterval(() => {
      this.senzorji.casNaStrani = (Date.now() - startCas) / 1000;
    }, 1000);
    
    // Shranjevanje statistike vsako minuto
    setInterval(() => {
      this.shraniTrenutnoStanje();
    }, 60000);
    
    // Energija iz peskovnika
    setInterval(() => {
      const energijaElement = document.getElementById('energijski-napolnjenost');
      if (energijaElement) {
        this.senzorji.energija = parseFloat(energijaElement.style.width) || 0;
      }
    }, 2000);
  },
  
  shraniTrenutnoStanje() {
    const stanje = {
      cas: Date.now(),
      nervoza: this.senzorji.nervoza,
      hitrostKlikov: this.senzorji.hitrostKlikov,
      globinaScrolla: this.senzorji.globinaScrolla,
      casNaStrani: this.senzorji.casNaStrani,
      energija: this.senzorji.energija,
      steviloKlikov: this.senzorji.kliki.length,
      steviloInterakcij: this.senzorji.interakcijeZGradniki.length
    };
    
    // Shrani v zgodovino
    const zgodovina = this.naloziZgodovino();
    zgodovina.push(stanje);
    this.obdrziZadnjih(zgodovina, 1440); // 24 ur (vsako minuto)
    localStorage.setItem("merilci_zgodovina", JSON.stringify(zgodovina));
    
    // Pošlji v statistiko
    if (window.Statistike) {
      window.Statistike.obdelajMeritev(stanje);
    }
  },
  
  naloziZgodovino() {
    const shranjena = localStorage.getItem("merilci_zgodovina");
    return shranjena ? JSON.parse(shranjena) : [];
  },
  
  obdrziZadnjih(array, st) {
    if (array.length > st) {
      array.splice(0, array.length - st);
    }
  },
  
  // Pridobi povprečja za danes
  povprecjaDanes() {
    const zgodovina = this.naloziZgodovino();
    const danes = new Date().toDateString();
    const danesPodatki = zgodovina.filter(z => new Date(z.cas).toDateString() === danes);
    
    if (danesPodatki.length === 0) return null;
    
    const povprecja = {
      povprecnaNervoza: danesPodatki.reduce((a,b) => a + b.nervoza, 0) / danesPodatki.length,
      povprecnaHitrostKlikov: danesPodatki.reduce((a,b) => a + b.hitrostKlikov, 0) / danesPodatki.length,
      povprecnaEnergija: danesPodatki.reduce((a,b) => a + b.energija, 0) / danesPodatki.length,
      skupniCas: danesPodatki[danesPodatki.length-1]?.casNaStrani || 0,
      steviloMeritev: danesPodatki.length
    };
    
    return povprecja;
  },
  
  // Dodaj UI elemente
  dodajUIMerilce() {
    const ploca = document.createElement('div');
    ploca.className = 'merilci-ploca';
    ploca.innerHTML = `
      <div class="merilec-nervoza">
        <div class="merilec-naslov">🌊 Nervoza</div>
        <div class="merilec-bar">
          <div id="bar-nervoze" class="merilec-napolnjenost" style="width:0%"></div>
        </div>
        <div class="merilec-stevilka"><span id="stevilka-nervoze">0</span>%</div>
      </div>
      <div class="merilec-hitrost">
        <div class="merilec-naslov">⚡ Hitrost klikov</div>
        <div class="merilec-stevilka"><span id="hitrost-klikov">0</span>/s</div>
      </div>
    `;
    document.body.appendChild(ploca);
    
    // Posodabljanje hitrosti klikov
    setInterval(() => {
      const hitrostElement = document.getElementById('hitrost-klikov');
      if (hitrostElement) {
        hitrostElement.textContent = this.senzorji.hitrostKlikov.toFixed(1);
      }
    }, 1000);
  }
};