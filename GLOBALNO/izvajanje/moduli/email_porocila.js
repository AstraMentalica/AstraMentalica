2. runtime/email_porocila.js – E-poštna poročila
javascript
// E-poštna poročila (simulacija – potrebuje backend)
window.EmailPoročila = {
  emailUporabnika: null,
  
  nastaviEmail(email) {
    this.emailUporabnika = email;
    localStorage.setItem("uporabnikov_email", email);
    magičnoObvestilo(`📧 Email nastavljen: ${email}`);
  },
  
  pridobiEmail() {
    return this.emailUporabnika || localStorage.getItem("uporabnikov_email");
  },
  
  // Pošlji tedensko poročilo
  posljiTedenskoPoročilo() {
    const email = this.pridobiEmail();
    if (!email) {
      this.prikaziObrazecZaEmail();
      return;
    }
    
    const podatki = this.zberiTedenskePodatke();
    const vsebina = this.ustvariHtmlPoročilo(podatki, 'tedensko');
    
    // Simulacija pošiljanja (v resnici bi klical backend API)
    console.log("📧 Tedensko poročilo za:", email);
    console.log("Vsebina:", vsebina);
    
    // Shrani v lokalno shrambo za pregled
    localStorage.setItem("zadnje_porocilo_tedensko", JSON.stringify({
      datum: new Date().toISOString(),
      vsebina: vsebina,
      email: email
    }));
    
    magičnoObvestilo(`📧 Tedensko poročilo pripravljeno! (Poslano na: ${email})`);
    this.prikaziPredogledPoročila(podatki, 'tedensko');
  },
  
  // Pošlji mesečno poročilo
  posljiMesecnoPoročilo() {
    const email = this.pridobiEmail();
    if (!email) {
      this.prikaziObrazecZaEmail();
      return;
    }
    
    const podatki = this.zberiMesečnePodatke();
    const vsebina = this.ustvariHtmlPoročilo(podatki, 'mesečno');
    
    localStorage.setItem("zadnje_porocilo_mesecno", JSON.stringify({
      datum: new Date().toISOString(),
      vsebina: vsebina,
      email: email
    }));
    
    magičnoObvestilo(`📧 Mesečno poročilo pripravljeno! (Poslano na: ${email})`);
    this.prikaziPredogledPoročila(podatki, 'mesečno');
  },
  
  zberiTedenskePodatke() {
    const zgodovina = window.Merilci?.naloziZgodovino() || [];
    const zadnjih7 = zgodovina.slice(-7 * 24 * 60);
    
    const dnevneVrednosti = [];
    for (let i = 0; i < 7; i++) {
      const dan = zadnjih7.filter(d => {
        const datum = new Date(d.cas);
        return datum.getDate() === new Date(Date.now() - i * 86400000).getDate();
      });
      const povprecje = dan.reduce((a,b) => a + b.nervoza, 0) / (dan.length || 1);
      dnevneVrednosti.unshift(povprecje);
    }
    
    const knjigePrebrane = window.Knjiznica?.knjige?.filter(k => {
      return new Date(k.datum) > new Date(Date.now() - 7 * 86400000);
    }).length || 0;
    
    const noviIzdelki = window.Zalozba?.vsiIzdelki()?.filter(i => {
      return new Date(i.datum) > new Date(Date.now() - 7 * 86400000);
    }).length || 0;
    
    const horoskop = window.Horoskop?.pridobiZaDan(new Date()) || {};
    const numerologija = window.Numerologija?.izracunajZaDan(new Date()) || {};
    
    return {
      obdobje: "tedensko",
      povprecnaNervoza: dnevneVrednosti.reduce((a,b) => a + b, 0) / 7,
      najvisjaNervoza: Math.max(...dnevneVrednosti),
      najnizjaNervoza: Math.min(...dnevneVrednosti),
      knjigePrebrane: knjigePrebrane,
      noviIzdelki: noviIzdelki,
      horoskop: horoskop.znak || "Razišči",
      numerologija: numerologija.stevilo || "?",
      nasvet: this.dajNasvetZaPoročilo(dnevneVrednosti)
    };
  },
  
  zberiMesečnePodatke() {
    const zgodovina = window.Merilci?.naloziZgodovino() || [];
    const zadnjih30 = zgodovina.slice(-30 * 24 * 60);
    
    const povprecje = zadnjih30.reduce((a,b) => a + b.nervoza, 0) / (zadnjih30.length || 1);
    
    const knjigeNapisane = window.Knjiznica?.knjige?.filter(k => {
      return new Date(k.datum) > new Date(Date.now() - 30 * 86400000);
    }).length || 0;
    
    const prodaje = window.Zalozba?.vsiUporabniki ? 
      Object.values(window.Zalozba.vsiUporabniki()).reduce((a,b) => a + (b.steviloProdaj || 0), 0) : 0;
    
    return {
      obdobje: "mesečno",
      povprecnaNervoza: povprecje,
      skupnihMeritev: zadnjih30.length,
      knjigeNapisane: knjigeNapisane,
      skupajProdaj: prodaje,
      nasvet: this.dajNasvetZaMesec(povprecje, knjigeNapisane)
    };
  },
  
  dajNasvetZaPoročilo(vrednosti) {
    const povprecje = vrednosti.reduce((a,b) => a + b, 0) / vrednosti.length;
    if (povprecje > 60) return "Ta teden si bil bolj nemiren. Priporočam več odprtih portalov za sprostitev.";
    if (povprecje < 30) return "Zelo umirjen teden! Tvoja ustvarjalnost je verjetno na vrhuncu.";
    return "Uravnotežen teden. Nadaljuj z raziskovanjem čarobnega sveta!";
  },
  
  dajNasvetZaMesec(nervoza, knjige) {
    if (knjige > 5) return "Čestitke! Napisal si veliko knjig. Tvoj pisateljski talent cveti!";
    if (nervoza < 40) return "Zelo sproščen mesec. Tvoje ravnovesje je pohvalno!";
    return "Dober mesec. Poskusi naslednji mesec napisati kakšno knjigo več.";
  },
  
  ustvariHtmlPoročilo(podatki, tip) {
    return `
      <!DOCTYPE html>
      <html>
      <head>
        <meta charset="UTF-8">
        <title>Čarobno poročilo – Peskovnik</title>
        <style>
          body { font-family: 'Georgia', serif; background: #1a1a2e; color: #e9dccd; padding: 40px; }
          .container { max-width: 600px; margin: 0 auto; background: #2a1e36; border-radius: 20px; padding: 30px; border: 1px solid #d4af37; }
          h1 { color: #d4af37; text-align: center; }
          .stat { background: #1e192b; border-radius: 12px; padding: 15px; margin: 15px 0; }
          .nasvet { background: #d4af37; color: #1a1a2e; padding: 15px; border-radius: 12px; margin-top: 20px; }
          .footer { text-align: center; margin-top: 30px; font-size: 12px; opacity: 0.7; }
        </style>
      </head>
      <body>
        <div class="container">
          <h1>🔮 Čarobno ${tip === 'tedensko' ? 'tedensko' : 'mesečno'} poročilo</h1>
          <p>Pozdravljen, čarovnik! Tukaj so tvoji magični podatki.</p>
          
          <div class="stat">
            <h3>📊 Statistika ${tip === 'tedensko' ? 'tedna' : 'meseca'}</h3>
            <p>🌀 Povprečna nervoza: ${podatki.povprecnaNervoza?.toFixed(1) || 0}%</p>
            ${tip === 'tedensko' ? `
              <p>📈 Najvišja nervoza: ${podatki.najvisjaNervoza?.toFixed(1) || 0}%</p>
              <p>📉 Najnižja nervoza: ${podatki.najnizjaNervoza?.toFixed(1) || 0}%</p>
              <p>📚 Knjige prebrane: ${podatki.knjigePrebrane || 0}</p>
              <p>🏪 Novi izdelki na tržnici: ${podatki.noviIzdelki || 0}</p>
              <p>⭐ Horoskopski znak: ${podatki.horoskop}</p>
              <p>🔢 Numerološko število: ${podatki.numerologija}</p>
            ` : `
              <p>📊 Skupaj meritev: ${podatki.skupnihMeritev || 0}</p>
              <p>📚 Knjige napisane: ${podatki.knjigeNapisane || 0}</p>
              <p>🏪 Skupaj prodaj: ${podatki.skupajProdaj || 0}</p>
            `}
          </div>
          
          <div class="nasvet">
            <h3>✨ Magični nasvet ✨</h3>
            <p>${podatki.nasvet || "Nadaljuj z ustvarjanjem!"}</p>
          </div>
          
          <div class="footer">
            <p>🌙 Čarobni peskovnik – Tvoj osebni magični svet</p>
            <p>📅 ${new Date().toLocaleString('sl-SI')}</p>
          </div>
        </div>
      </body>
      </html>
    `;
  },
  
  prikaziPredogledPoročila(podatki, tip) {
    const modal = document.createElement('div');
    modal.className = 'trznica-modal';
    modal.innerHTML = `
      <div class="trznica-vsebina" style="max-width:700px">
        <h2>📧 Predogled poročila</h2>
        <div style="max-height:500px; overflow-y:auto; background:#1e192b; border-radius:16px; padding:16px">
          ${this.ustvariHtmlPoročilo(podatki, tip)}
        </div>
        <div class="izdelek-gumbi" style="margin-top:16px">
          <button id="poslji-ponovno" class="varnostni-gumb">📧 Pošlji ponovno</button>
          <button class="zapri-modal">✖ Zapri</button>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    document.getElementById('poslji-ponovno')?.addEventListener('click', () => {
      if (tip === 'tedensko') this.posljiTedenskoPoročilo();
      else this.posljiMesecnoPoročilo();
    });
    
    modal.querySelector('.zapri-modal')?.addEventListener('click', () => modal.remove());
  },
  
  prikaziObrazecZaEmail() {
    const modal = document.createElement('div');
    modal.className = 'trznica-modal';
    modal.innerHTML = `
      <div class="trznica-vsebina" style="max-width:400px">
        <h2>📧 Nastavi e-pošto</h2>
        <p>Za prejemanje poročil vnesi svoj e-poštni naslov.</p>
        <input type="email" id="email-input" placeholder="tvoj@email.com" style="width:100%; padding:10px; margin:10px 0; border-radius:12px">
        <div class="izdelek-gumbi">
          <button id="shrani-email" class="varnostni-gumb">💾 Shrani</button>
          <button class="zapri-modal">✖ Prekliči</button>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    document.getElementById('shrani-email')?.addEventListener('click', () => {
      const email = document.getElementById('email-input')?.value;
      if (email && email.includes('@')) {
        this.nastaviEmail(email);
        modal.remove();
        magičnoObvestilo("📧 Email shranjen! Zdaj lahko pošiljaš poročila.");
      } else {
        magičnoObvestilo("❌ Vnesi veljaven e-poštni naslov.");
      }
    });
    
    modal.querySelector('.zapri-modal')?.addEventListener('click', () => modal.remove());
  },
  
  prikaziPloscoPoročil() {
    const modal = document.createElement('div');
    modal.className = 'trznica-modal';
    modal.innerHTML = `
      <div class="trznica-vsebina" style="max-width:500px">
        <h2>📧 E-poštna poročila</h2>
        <div class="varnostne-moznosti">
          <div class="moznost">
            <h3>📧 Trenutni email</h3>
            <p>${this.pridobiEmail() || "Ni nastavljen"}</p>
            <button id="spremeni-email" class="varnostni-gumb">✏️ Spremeni email</button>
          </div>
          <div class="moznost">
            <h3>📆 Tedensko poročilo</h3>
            <button id="tedensko-porocilo" class="varnostni-gumb">📊 Pošlji tedensko</button>
          </div>
          <div class="moznost">
            <h3>📅 Mesečno poročilo</h3>
            <button id="mesecno-porocilo" class="varnostni-gumb">📈 Pošlji mesečno</button>
          </div>
        </div>
        <button class="zapri-modal">✖ Zapri</button>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    document.getElementById('spremeni-email')?.addEventListener('click', () => {
      modal.remove();
      this.prikaziObrazecZaEmail();
    });
    
    document.getElementById('tedensko-porocilo')?.addEventListener('click', () => {
      this.posljiTedenskoPoročilo();
    });
    
    document.getElementById('mesecno-porocilo')?.addEventListener('click', () => {
      this.posljiMesecnoPoročilo();
    });
    
    modal.querySelector('.zapri-modal')?.addEventListener('click', () => modal.remove());
  }
};