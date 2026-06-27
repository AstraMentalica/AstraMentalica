1. runtime/vizualizacija.js – Grafi in tedenski pregledi
javascript
// Vizualizacija statistik – grafi in pregledi
window.Vizualizacija = {
  grafKontekst: null,
  trenutniGraf: null,

  // Pokaži nadzorno ploščo z grafi
  prikaziNadzornoPlosco() {
    const modal = document.createElement('div');
    modal.className = 'statistika-modal';
    modal.innerHTML = `
      <div class="statistika-vsebina">
        <div class="statistika-glavni">
          <div class="statistika-naslov">
            <h2>📊 Nadzorna plošča – Tvoja statistika</h2>
            <button id="zapri-statistiko" class="zapri-statistiko">✖</button>
          </div>
          
          <!-- Zavihki -->
          <div class="statistika-zavihki">
            <button class="stat-zavihek aktiven" data-view="dnevno">📅 Dnevno</button>
            <button class="stat-zavihek" data-view="tedensko">📆 Tedensko</button>
            <button class="stat-zavihek" data-view="mesecno">📅 Mesečno</button>
            <button class="stat-zavihek" data-view="numerologija">🔢 Numerologija</button>
            <button class="stat-zavihek" data-view="astro">⭐ Astrologija</button>
            <button class="stat-zavihek" data-view="izvoz">📥 Izvoz podatkov</button>
          </div>
          
          <!-- Vsebina grafov -->
          <div id="statistika-vsebina" class="statistika-vsebina-container">
            ${this.prikaziDnevniPogled()}
          </div>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    // Dogodki za zavihke
    document.querySelectorAll('.stat-zavihek').forEach(zavihek => {
      zavihek.onclick = (e) => {
        document.querySelectorAll('.stat-zavihek').forEach(z => z.classList.remove('aktiven'));
        zavihek.classList.add('aktiven');
        const view = zavihek.dataset.view;
        this.prikaziPogled(view);
      };
    });
    
    document.getElementById('zapri-statistiko')?.addEventListener('click', () => modal.remove());
  },
  
  prikaziPogled(view) {
    const container = document.getElementById('statistika-vsebina');
    if (!container) return;
    
    switch(view) {
      case 'dnevno':
        container.innerHTML = this.prikaziDnevniPogled();
        this.narisiDnevniGraf();
        break;
      case 'tedensko':
        container.innerHTML = this.prikaziTedenskiPogled();
        this.narisiTedenskiGraf();
        break;
      case 'mesecno':
        container.innerHTML = this.prikaziMesecniPogled();
        this.narisiMesecniGraf();
        break;
      case 'numerologija':
        container.innerHTML = this.prikaziNumeroloskiPogled();
        break;
      case 'astro':
        container.innerHTML = this.prikaziAstroPogled();
        break;
      case 'izvoz':
        container.innerHTML = this.prikaziIzvozniPogled();
        break;
    }
  },
  
  prikaziDnevniPogled() {
    const danes = new Date().toLocaleDateString('sl-SI');
    const podatki = window.Merilci?.povprecjaDanes() || {};
    
    return `
      <div class="statistika-sekcija">
        <h3>📅 Dnevna statistika – ${danes}</h3>
        <div class="statistika-kartice">
          <div class="stat-kartica">
            <div class="stat-vrednost">${Math.round(podatki.povprecnaNervoza || 0)}%</div>
            <div class="stat-label">Povprečna nervoza</div>
          </div>
          <div class="stat-kartica">
            <div class="stat-vrednost">${(podatki.povprecnaHitrostKlikov || 0).toFixed(1)}/s</div>
            <div class="stat-label">Hitrost klikov</div>
          </div>
          <div class="stat-kartica">
            <div class="stat-vrednost">${Math.round(podatki.povprecnaEnergija || 0)}%</div>
            <div class="stat-label">Povprečna energija</div>
          </div>
          <div class="stat-kartica">
            <div class="stat-vrednost">${Math.round((podatki.skupniCas || 0) / 60)} min</div>
            <div class="stat-label">Čas na strani</div>
          </div>
        </div>
        <div class="graf-container">
          <canvas id="dnevni-graf" width="600" height="300"></canvas>
        </div>
        <div class="statistika-opombe">
          <p>✨ ${this.dajDnevniNasvet(podatki)}</p>
        </div>
      </div>
    `;
  },
  
  dajDnevniNasvet(podatki) {
    const nervoza = podatki.povprecnaNervoza || 0;
    const energija = podatki.povprecnaEnergija || 0;
    
    if (nervoza > 70) return "Danes si bil precej nemiren. Priporočam odprtje sprostitvenega portala.";
    if (nervoza < 30 && energija > 60) return "Super dan! Tvoja ustvarjalnost je na vrhuncu. Prodaj kak peskovnik!";
    if (energija < 30) return "Energija je nizka. Poskusi pisati knjigo z glasom za navdih.";
    return "Danes si v ravnovesju. Nadaljuj z raziskovanjem!";
  },
  
  prikaziTedenskiPogled() {
    const tedenskiPodatki = this.pridobiTedenskePodatke();
    
    return `
      <div class="statistika-sekcija">
        <h3>📆 Tedenski pregled</h3>
        <div class="tedenski-summary">
          <div class="summary-kartica">
            <div class="summary-vrednost">${tedenskiPodatki.povprecjeNervoze.toFixed(1)}%</div>
            <div class="summary-label">Povprečna nervoza (teden)</div>
          </div>
          <div class="summary-kartica">
            <div class="summary-vrednost">${tedenskiPodatki.najvisjaNervoza}%</div>
            <div class="summary-label">Najvišja nervoza</div>
          </div>
          <div class="summary-kartica">
            <div class="summary-vrednost">${tedenskiPodatki.najnizjaNervoza}%</div>
            <div class="summary-label">Najnižja nervoza</div>
          </div>
        </div>
        <div class="graf-container">
          <canvas id="tedenski-graf" width="600" height="300"></canvas>
        </div>
        <div class="tedenski-nasvet">
          <h4>🔮 Tedenski nasvet:</h4>
          <p>${this.dajTedenskiNasvet(tedenskiPodatki)}</p>
        </div>
      </div>
    `;
  },
  
  pridobiTedenskePodatke() {
    const zgodovina = window.Merilci?.naloziZgodovino() || [];
    const zadnjih7Dni = zgodovina.slice(-7 * 24 * 60); // 7 dni (vsako minuto)
    
    const dnevneVrednosti = [];
    for (let i = 0; i < 7; i++) {
      const dan = zadnjih7Dni.filter(d => {
        const datum = new Date(d.cas);
        return datum.getDate() === new Date(Date.now() - i * 86400000).getDate();
      });
      
      const povprecje = dan.reduce((a,b) => a + b.nervoza, 0) / (dan.length || 1);
      dnevneVrednosti.unshift(povprecje);
    }
    
    return {
      dnevneVrednosti: dnevneVrednosti,
      povprecjeNervoze: dnevneVrednosti.reduce((a,b) => a + b, 0) / 7,
      najvisjaNervoza: Math.max(...dnevneVrednosti),
      najnizjaNervoza: Math.min(...dnevneVrednosti)
    };
  },
  
  dajTedenskiNasvet(podatki) {
    if (podatki.povprecjeNervoze > 60) {
      return "Ta teden si bil precej pod stresom. Naslednji teden poskusi več meditacije ob kristalu.";
    }
    if (podatki.povprecjeNervoze < 30) {
      return "Zelo umirjen teden! Tvoja ustvarjalnost je verjetno na vrhuncu.";
    }
    return "Teden je bil uravnotežen. Odličen čas za nove projekte!";
  },
  
  prikaziMesecniPogled() {
    return `
      <div class="statistika-sekcija">
        <h3>📅 Mesečni pregled</h3>
        <div class="graf-container">
          <canvas id="mesecni-graf" width="600" height="300"></canvas>
        </div>
        <p>✨ Mesečna statistika bo na voljo po 30 dneh uporabe. Nadaljuj z ustvarjanjem!</p>
      </div>
    `;
  },
  
  prikaziNumeroloskiPogled() {
    const numerologija = window.Numerologija?.izracunajZaDan(new Date()) || {};
    const imePodatki = window.Numerologija?.izracunajIme(
      window.Zalozba?.trenutniUporabnik()?.ime || "gost"
    ) || {};
    
    return `
      <div class="statistika-sekcija">
        <h3>🔢 Numerološka analiza</h3>
        <div class="numerologija-kartice">
          <div class="numerologija-kartica">
            <div class="numerologija-stevilka">${numerologija.stevilo || '?'}</div>
            <div class="numerologija-label">Današnje število</div>
            <div class="numerologija-pomen">${numerologija.pomen || 'Razišči svojo številko'}</div>
          </div>
          <div class="numerologija-kartica">
            <div class="numerologija-stevilka">${imePodatki || '?'}</div>
            <div class="numerologija-label">Število tvojega imena</div>
            <div class="numerologija-pomen">${this.pomenImena(imePodatki)}</div>
          </div>
        </div>
        <div class="numerologija-povezave">
          <h4>🔗 Povezava z vedenjem:</h4>
          <p>${this.povezavaNumerologijaMerilci(numerologija.stevilo)}</p>
        </div>
      </div>
    `;
  },
  
  pomenImena(stevilo) {
    const pomeni = {
      1: "Vodja, samostojen, inovativen",
      2: "Diplomat, miroljuben, občutljiv",
      3: "Ustvarjalen, družaben, optimističen",
      4: "Praktičen, zanesljiv, delaven",
      5: "Pustolovsk, svobodomiseln, vsestranski",
      6: "Skrben, odgovoren, ljubeč",
      7: "Analitičen, duhoven, modrec",
      8: "Ambiciozen, močan, uspešen",
      9: "Sočuten, humanitaren, idealist"
    };
    return pomeni[stevilo] || "Skrivnostna duša";
  },
  
  povezavaNumerologijaMerilci(stevilo) {
    const povezave = {
      1: "Ljudje s številom 1 so bolj aktivni. Tvoja hitrost klikov je verjetno nadpovprečna.",
      3: "Število 3 prinaša ustvarjalnost. Tvoja interakcija z gradniki je pogostejša.",
      5: "Število 5 ljubi spremembe. Pogosto odpiraš nove portale.",
      7: "Število 7 je duhovno. Veliko časa preživiš v knjižnici.",
      8: "Število 8 je poslovno. Uspešen si na tržnici."
    };
    return povezave[stevilo] || "Tvoje število se povezuje z vsemi vidiki portala.";
  },
  
  prikaziAstroPogled() {
    const horoskop = window.Horoskop?.pridobiZaDan(new Date()) || {};
    const znak = horoskop.znak;
    
    return `
      <div class="statistika-sekcija">
        <h3>⭐ Astrološka analiza – ${znak}</h3>
        <div class="astro-kartice">
          <div class="astro-kartica">
            <div class="astro-znak">${znak}</div>
            <div class="astro-napoved">${horoskop.dnevnaNapoved || 'Danes je dan magije.'}</div>
            <div class="astro-energija">✨ Energija: ${horoskop.energija || 75}%</div>
            <div class="astro-sreca">🍀 Srečna številka: ${horoskop.srečnaStevilka || 7}</div>
            <div class="astro-barva">🎨 Srečna barva: ${horoskop.srečnaBarva || 'zlata'}</div>
          </div>
        </div>
        <div class="astro-povezave">
          <h4>🔗 Povezava z vedenjem:</h4>
          <p>${this.povezavaAstroMerilci(znak)}</p>
        </div>
      </div>
    `;
  },
  
  povezavaAstroMerilci(znak) {
    const povezave = {
      "Oven": "Ovni so hitri in odločni. Tvoja hitrost klikov je verjetno visoka.",
      "Rak": "Raki so čustveni. Tvoja nervoza niha z luninimi fazami.",
      "Lev": "Levi so ustvarjalni. Veliko časa preživiš v peskovniku.",
      "Ribi": "Ribi so sanjavi. Veliko bereš v knjižnici.",
      "Devica":"Device so analitične. Pogosto pregleduješ statistike."
    };
    return povezave[znak] || "Tvoj horoskopski znak se sklada z vsemi magičnimi elementi.";
  },
  
  prikaziIzvozniPogled() {
    return `
      <div class="statistika-sekcija">
        <h3>📥 Izvoz podatkov</h3>
        <div class="izvoz-kartice">
          <div class="izvoz-kartica">
            <h4>📊 Izvozi dnevno statistiko</h4>
            <button id="izvoz-dnevno" class="izvoz-gumb">CSV – Dnevno</button>
          </div>
          <div class="izvoz-kartica">
            <h4>📆 Izvozi tedensko statistiko</h4>
            <button id="izvoz-tedensko" class="izvoz-gumb">CSV – Tedensko</button>
          </div>
          <div class="izvoz-kartica">
            <h4>📚 Izvozi knjižnico</h4>
            <button id="izvoz-knjige" class="izvoz-gumb">CSV – Knjige</button>
          </div>
          <div class="izvoz-kartica">
            <h4>🏪 Izvozi tržnico</h4>
            <button id="izvoz-trznica" class="izvoz-gumb">CSV – Izdelki</button>
          </div>
          <div class="izvoz-kartica">
            <h4>🔮 Izvozi vse podatke</h4>
            <button id="izvoz-vse" class="izvoz-gumb glavni">📦 Izvozi vse (ZIP)</button>
          </div>
        </div>
      </div>
    `;
  },
  
  // Grafi s Canvas API
  narisiDnevniGraf() {
    const canvas = document.getElementById('dnevni-graf');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const zgodovina = window.Merilci?.naloziZgodovino() || [];
    const zadnjih24 = zgodovina.slice(-1440); // 24 ur
    
    const podatki = [];
    for (let i = 0; i < 24; i++) {
      const ura = zadnjih24.filter(d => new Date(d.cas).getHours() === i);
      const povprecje = ura.reduce((a,b) => a + b.nervoza, 0) / (ura.length || 1);
      podatki.push(povprecje);
    }
    
    this.narisiGraf(ctx, podatki, 'dnevni-graf', '#d4af37');
  },
  
  narisiTedenskiGraf() {
    const canvas = document.getElementById('tedenski-graf');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const podatki = this.pridobiTedenskePodatke().dnevneVrednosti;
    this.narisiGraf(ctx, podatki, 'tedenski-graf', '#9b59b6');
  },
  
  narisiGraf(ctx, podatki, id, barva) {
    const width = 600;
    const height = 300;
    canvas.width = width;
    canvas.height = height;
    
    ctx.clearRect(0, 0, width, height);
    
    // Ozadje
    ctx.fillStyle = '#1a1a2e';
    ctx.fillRect(0, 0, width, height);
    
    // Mreža
    ctx.strokeStyle = '#333';
    ctx.lineWidth = 0.5;
    for (let i = 0; i <= 4; i++) {
      const y = height - (i * height / 4);
      ctx.beginPath();
      ctx.moveTo(40, y);
      ctx.lineTo(width - 20, y);
      ctx.stroke();
    }
    
    // Črta
    ctx.beginPath();
    ctx.strokeStyle = barva;
    ctx.lineWidth = 2;
    
    const korak = (width - 60) / (podatki.length - 1);
    const maxVrednost = Math.max(...podatki, 1);
    
    for (let i = 0; i < podatki.length; i++) {
      const x = 40 + i * korak;
      const y = height - 20 - (podatki[i] / maxVrednost) * (height - 40);
      
      if (i === 0) {
        ctx.moveTo(x, y);
      } else {
        ctx.lineTo(x, y);
      }
    }
    ctx.stroke();
    
    // Točke
    ctx.fillStyle = barva;
    for (let i = 0; i < podatki.length; i++) {
      const x = 40 + i * korak;
      const y = height - 20 - (podatki[i] / maxVrednost) * (height - 40);
      ctx.beginPath();
      ctx.arc(x, y, 3, 0, 2 * Math.PI);
      ctx.fill();
    }
    
    // Napisi
    ctx.fillStyle = '#fff';
    ctx.font = '12px Arial';
    ctx.fillText('Čas', width - 40, height - 5);
    ctx.save();
    ctx.translate(15, height / 2);
    ctx.rotate(-Math.PI / 2);
    ctx.fillText('Nervoza (%)', -30, 0);
    ctx.restore();
  }
};