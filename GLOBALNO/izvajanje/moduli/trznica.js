2. runtime/trznica.js – Tržnica in oglasna deska
javascript
// Tržnica peskovnikov
window.Trznica = {
  prikazi() {
    const izdelki = window.Zalozba?.vsiIzdelki() || [];
    
    const modal = document.createElement('div');
    modal.id = 'trznica-modal';
    modal.className = 'trznica-modal';
    modal.innerHTML = `
      <div class="trznica-vsebina">
        <div class="trznica-glavni">
          <h2>🏪 Čarobna tržnica</h2>
          <div class="trznica-izdelki" id="trznica-izdelki">
            ${this.izdelkiHTML(izdelki)}
          </div>
          <div class="trznica-gumbi">
            <button id="moji-izdelki-gumb">📦 Moji izdelki</button>
            <button id="moji-peskovniki-gumb">🎨 Moji peskovniki</button>
            <button id="zapri-trznico">✖ Zapri</button>
          </div>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    document.getElementById('zapri-trznico')?.addEventListener('click', () => modal.remove());
    document.getElementById('moji-izdelki-gumb')?.addEventListener('click', () => this.prikaziMojeIzdelke());
    document.getElementById('moji-peskovniki-gumb')?.addEventListener('click', () => this.prikaziMojePeskovnike());
    
    // Dogodki za gumbe za nakup
    document.querySelectorAll('.kupi-gumb').forEach(gumb => {
      gumb.addEventListener('click', (e) => {
        const prodajalec = e.target.dataset.prodajalec;
        const id = e.target.dataset.id;
        const cena = parseInt(e.target.dataset.cena);
        this.opravilNakup(prodajalec, id, cena, modal);
      });
    });
  },
  
  izdelkiHTML(izdelki) {
    if (izdelki.length === 0) {
      return '<div class="ni-izdelkov">✨ Ni izdelkov v prodaji. Bodisi prvi prodajalec! ✨</div>';
    }
    
    return izdelki.map(izdelek => `
      <div class="trznica-kartica">
        <div class="izdelek-ime">🎨 ${izdelek.ime}</div>
        <div class="izdelek-prodajalec">👤 Prodajalec: ${izdelek.prodajalecIme} ${this.zvezdice(izdelek.prodajalecOcena)}</div>
        <div class="izdelek-cena">💎 ${izdelek.cena} kristalov</div>
        <div class="izdelek-datum">📅 ${new Date(izdelek.datum).toLocaleDateString('sl-SI')}</div>
        <button class="kupi-gumb" data-prodajalec="${izdelek.prodajalecIme}" data-id="${izdelek.id}" data-cena="${izdelek.cena}">🔮 Kupi</button>
      </div>
    `).join('');
  },
  
  zvezdice(ocena) {
    const zvezde = Math.round(ocena || 0);
    return '⭐'.repeat(zvezde) + '☆'.repeat(5 - zvezde);
  },
  
  opravilNakup(prodajalec, id, cena, modal) {
    const trenutni = Zalozba.trenutniUporabnik();
    if (trenutni.ime === prodajalec) {
      magičnoObvestilo("❌ Ne moreš kupiti svojega izdelka!");
      return;
    }
    
    const rezultat = Zalozba.kupiIzdelek(trenutni.ime, prodajalec, id, cena);
    magičnoObvestilo(rezultat.sporocilo);
    
    if (rezultat.uspeh) {
      if (window.UmetnaInteligenca) {
        UmetnaInteligenca.govori(`${rezultat.sporocilo} Zdaj lahko naložiš peskovnik.`);
      }
      modal?.remove();
      this.prikazi();
    }
  },
  
  prikaziMojeIzdelke() {
    const trenutni = Zalozba.trenutniUporabnik();
    const mojiIzdelki = trenutni.mojiIzdelki || [];
    
    const modal = document.createElement('div');
    modal.className = 'trznica-modal';
    modal.innerHTML = `
      <div class="trznica-vsebina">
        <h2>📦 Moji izdelki v prodaji</h2>
        <div class="trznica-izdelki">
          ${mojiIzdelki.length === 0 ? '<div>Ni izdelkov v prodaji.</div>' : 
            mojiIzdelki.map(iz => `
              <div class="trznica-kartica">
                <div>🎨 ${iz.ime}</div>
                <div>💎 ${iz.cena} kristalov</div>
                <button class="umakni-izdelek" data-id="${iz.id}">❌ Umakni iz prodaje</button>
              </div>
            `).join('')
          }
        </div>
        <button class="zapri-modal">✖ Zapri</button>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    document.querySelectorAll('.umakni-izdelek').forEach(gumb => {
      gumb.onclick = (e) => {
        const id = e.target.dataset.id;
        this.umakniIzProdaje(id);
        modal.remove();
        this.prikaziMojeIzdelke();
      };
    });
    
    modal.querySelector('.zapri-modal')?.addEventListener('click', () => modal.remove());
  },
  
  umakniIzProdaje(idPeskovnika) {
    const trenutni = Zalozba.trenutniUporabnik();
    const vsi = Zalozba.vsiUporabniki();
    const uporabnik = vsi[trenutni.ime];
    
    if (uporabnik) {
      const peskovnik = uporabnik.mojiPeskovniki.find(p => p.id === idPeskovnika);
      if (peskovnik) {
        peskovnik.zaProdajo = false;
        uporabnik.mojiIzdelki = uporabnik.mojiIzdelki.filter(i => i.id !== idPeskovnika);
        Zalozba.shraniVseUporabnike(vsi);
        magičnoObvestilo("Izdelek umaknjen iz prodaje.");
      }
    }
  },
  
  prikaziMojePeskovnike() {
    const trenutni = Zalozba.trenutniUporabnik();
    const mojiPeskovniki = trenutni.mojiPeskovniki || [];
    
    const modal = document.createElement('div');
    modal.className = 'trznica-modal';
    modal.innerHTML = `
      <div class="trznica-vsebina">
        <h2>🎨 Moji peskovniki</h2>
        <div class="trznica-izdelki">
          ${mojiPeskovniki.map(p => `
            <div class="trznica-kartica">
              <div>🎨 ${p.ime}</div>
              <div>📅 ${new Date(p.datum).toLocaleDateString('sl-SI')}</div>
              <div>${p.zaProdajo ? `💎 V prodaji za ${p.cena} kristalov` : 'Ni v prodaji'}</div>
              <div class="izdelek-gumbi">
                <button class="nalozi-peskovnik" data-id="${p.id}">📂 Naloži</button>
                ${!p.zaProdajo ? `<button class="daj-v-prodajo" data-id="${p.id}" data-ime="${p.ime}">💰 Daj v prodajo</button>` : ''}
                <button class="zbrisi-peskovnik" data-id="${p.id}">🗑 Zbriši</button>
              </div>
            </div>
          `).join('')}
        </div>
        <button class="shrani-trenutni" id="shrani-trenutni-peskovnik">💾 Shrani trenutni peskovnik</button>
        <button class="zapri-modal">✖ Zapri</button>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    // Naloži peskovnik
    document.querySelectorAll('.nalozi-peskovnik').forEach(gumb => {
      gumb.onclick = (e) => {
        const id = e.target.dataset.id;
        this.naloziPeskovnik(id);
        modal.remove();
      };
    });
    
    // Daj v prodajo
    document.querySelectorAll('.daj-v-prodajo').forEach(gumb => {
      gumb.onclick = (e) => {
        const id = e.target.dataset.id;
        const ime = e.target.dataset.ime;
        this.prikaziCenik(id, ime);
        modal.remove();
      };
    });
    
    // Zbriši peskovnik
    document.querySelectorAll('.zbrisi-peskovnik').forEach(gumb => {
      gumb.onclick = (e) => {
        if (confirm("Res želiš zbrisati ta peskovnik?")) {
          const id = e.target.dataset.id;
          this.zbrisiPeskovnik(id);
          modal.remove();
          this.prikaziMojePeskovnike();
        }
      };
    });
    
    // Shrani trenutni peskovnik
    document.getElementById('shrani-trenutni-peskovnik')?.addEventListener('click', () => {
      this.shraniTrenutniPeskovnik();
      modal.remove();
      this.prikaziMojePeskovnike();
    });
    
    modal.querySelector('.zapri-modal')?.addEventListener('click', () => modal.remove());
  },
  
  prikaziCenik(idPeskovnika, imePeskovnika) {
    const modal = document.createElement('div');
    modal.className = 'trznica-modal';
    modal.innerHTML = `
      <div class="trznica-vsebina" style="max-width:400px">
        <h2>💰 Daj v prodajo: ${imePeskovnika}</h2>
        <label>Cena v kristalih:</label>
        <input type="number" id="cena-peskovnika" min="1" max="1000" value="50" style="width:100%; padding:10px; margin:10px 0">
        <div class="izdelek-gumbi">
          <button id="potrdi-prodajo" data-id="${idPeskovnika}">✅ Potrdi</button>
          <button class="zapri-modal">✖ Prekliči</button>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    document.getElementById('potrdi-prodajo')?.addEventListener('click', (e) => {
      const id = e.target.dataset.id;
      const cena = parseInt(document.getElementById('cena-peskovnika').value);
      if (cena > 0) {
        this.dajVProdajo(id, cena);
      }
      modal.remove();
    });
    
    modal.querySelector('.zapri-modal')?.addEventListener('click', () => modal.remove());
  },
  
  dajVProdajo(idPeskovnika, cena) {
    const trenutni = Zalozba.trenutniUporabnik();
    const uspeh = Zalozba.dajVProdajo(trenutni.ime, idPeskovnika, cena);
    if (uspeh) {
      magičnoObvestilo(`Peskovnik je zdaj v prodaji za ${cena} kristalov!`);
    } else {
      magičnoObvestilo("Napaka pri dajanju v prodajo.");
    }
  },
  
  shraniTrenutniPeskovnik() {
    // Zberi trenutno stanje peskovnika
    const gradniki = [];
    document.querySelectorAll('.gradnik').forEach(g => {
      gradniki.push({
        id: g.dataset.id,
        vrsta: g.dataset.vrsta,
        vsebina: g.querySelector('.vsebina-gradnika')?.innerHTML || "",
        sirina: g.style.gridColumn || "auto"
      });
    });
    
    const ime = prompt("Ime tvojega peskovnika:", "Moj čarobni peskovnik");
    if (!ime) return;
    
    const tema = document.getElementById('tema-slog')?.href.split('/').pop().replace('.css', '') || "misticna";
    
    const peskovnikPodatki = {
      ime: ime,
      datum: new Date().toISOString(),
      gradniki: gradniki,
      tema: tema,
      postavitev: "mreza"
    };
    
    const id = Zalozba.shraniPeskovnik(Zalozba.trenutniUporabnik().ime, peskovnikPodatki);
    if (id) {
      magičnoObvestilo(`Peskovnik "${ime}" shranjen!`);
    } else {
      magičnoObvestilo("Napaka pri shranjevanju.");
    }
  },
  
  naloziPeskovnik(idPeskovnika) {
    const trenutni = Zalozba.trenutniUporabnik();
    const peskovnik = Zalozba.naloziPeskovnik(trenutni.ime, idPeskovnika);
    
    if (peskovnik) {
      // Počisti trenutni peskovnik
      const platno = document.getElementById('peskovnik-platno');
      if (platno) platno.innerHTML = "";
      
      // Naloži gradnike
      if (peskovnik.gradniki) {
        peskovnik.gradniki.forEach(g => {
          const gradnikHTML = `
            <div class="gradnik" data-id="${g.id || Date.now()}" data-vrsta="${g.vrsta}" style="grid-column: ${g.sirina || 'auto'}">
              <div class="vsebina-gradnika">${g.vsebina || ''}</div>
              <div class="gradnik-gumbi">
                <button class="odstrani-grd">🗑 Odstrani</button>
                <button class="povečaj-grd">🔍 Povečaj</button>
              </div>
            </div>
          `;
          platno.insertAdjacentHTML("beforeend", gradnikHTML);
          if (window.Peskovnik) {
            window.Peskovnik._pripniDogodke(platno.lastElementChild);
          }
        });
      }
      
      // Naloži temo
      if (peskovnik.tema && window.preklopiNaTemo) {
        window.preklopiNaTemo(peskovnik.tema);
      }
      
      magičnoObvestilo(`Naložen peskovnik: ${peskovnik.ime}`);
    } else {
      magičnoObvestilo("Napaka pri nalaganju peskovnika.");
    }
  },
  
  zbrisiPeskovnik(idPeskovnika) {
    const trenutni = Zalozba.trenutniUporabnik();
    const vsi = Zalozba.vsiUporabniki();
    const uporabnik = vsi[trenutni.ime];
    
    if (uporabnik) {
      uporabnik.mojiPeskovniki = uporabnik.mojiPeskovniki.filter(p => p.id !== idPeskovnika);
      uporabnik.mojiIzdelki = uporabnik.mojiIzdelki.filter(i => i.id !== idPeskovnika);
      Zalozba.shraniVseUporabnike(vsi);
      magičnoObvestilo("Peskovnik zbrisan.");
    }
  }
};