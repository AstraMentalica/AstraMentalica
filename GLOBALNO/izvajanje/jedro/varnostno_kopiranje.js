1. runtime/varnostno_kopiranje.js – Shranjevanje v oblak
javascript
// Varnostno kopiranje v oblak (lokalno shranjevanje + priprava za Google Drive/Dropbox)
window.VarnostnoKopiranje = {
  
  // Ustvari varnostno kopijo vseh podatkov
  ustvariKopijo() {
    const podatki = this.zberiVsePodatke();
    const jsonPodatki = JSON.stringify(podatki, null, 2);
    const datum = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
    
    // Shrani lokalno
    localStorage.setItem("varnostna_kopija_zadnja", jsonPodatki);
    localStorage.setItem("varnostna_kopija_datum", new Date().toISOString());
    
    // Ustvari datoteko za prenos
    this.prenesiKotDatoteko(jsonPodatki, `varnostna_kopija_${datum}.json`);
    
    magičnoObvestilo("💾 Varnostna kopija ustvarjena in shranjena lokalno!");
    
    return { uspeh: true, podatki: podatki, datum: datum };
  },
  
  // Zberi vse podatke iz celotnega portala
  zberiVsePodatke() {
    const uporabniki = JSON.parse(localStorage.getItem("trznica_vsi_uporabniki") || "{}");
    const trenutni = localStorage.getItem("trznica_trenutni_uporabnik") || "gost";
    const knjige = JSON.parse(localStorage.getItem("knjiznica_knjige") || "[]");
    const skritiZavihki = JSON.parse(localStorage.getItem("knjiznica_skriti_zavihki") || "[]");
    const merilci = JSON.parse(localStorage.getItem("merilci_zgodovina") || "[]");
    const vzorci = JSON.parse(localStorage.getItem("statistike_vzorci") || "[]");
    const neznano = JSON.parse(localStorage.getItem("neznano_vzorci") || "[]");
    const peskovnik = localStorage.getItem("peskovnik_podatki") || "[]";
    const tema = localStorage.getItem("uporabnikovaTema") || "misticna";
    const kristali = localStorage.getItem("uporabnikoviKristali") || "0";
    const avatarji = localStorage.getItem("odklenjeniAvatarji") || "[]";
    
    return {
      verzija: "1.0",
      datum: new Date().toISOString(),
      uporabniki: uporabniki,
      trenutniUporabnik: trenutni,
      knjige: knjige,
      skritiZavihki: skritiZavihki,
      merilci: merilci,
      statistikeVzorci: vzorci,
      neznanoVzorci: neznano,
      peskovnik: peskovnik,
      tema: tema,
      kristali: kristali,
      odklenjeniAvatarji: avatarji
    };
  },
  
  // Obnovi iz varnostne kopije
  obnoviIzKopije(datoteka) {
    const bralnik = new FileReader();
    bralnik.onload = (e) => {
      try {
        const podatki = JSON.parse(e.target.result);
        this.naloziPodatke(podatki);
        magičnoObvestilo("✅ Varnostna kopija uspešno obnovljena!");
        setTimeout(() => location.reload(), 1500);
      } catch (err) {
        magičnoObvestilo("❌ Napaka pri obnovi: neveljavna datoteka");
      }
    };
    bralnik.readAsText(datoteka);
  },
  
  naloziPodatke(podatki) {
    if (podatki.uporabniki) localStorage.setItem("trznica_vsi_uporabniki", JSON.stringify(podatki.uporabniki));
    if (podatki.trenutniUporabnik) localStorage.setItem("trznica_trenutni_uporabnik", podatki.trenutniUporabnik);
    if (podatki.knjige) localStorage.setItem("knjiznica_knjige", JSON.stringify(podatki.knjige));
    if (podatki.skritiZavihki) localStorage.setItem("knjiznica_skriti_zavihki", JSON.stringify(podatki.skritiZavihki));
    if (podatki.merilci) localStorage.setItem("merilci_zgodovina", JSON.stringify(podatki.merilci));
    if (podatki.statistikeVzorci) localStorage.setItem("statistike_vzorci", JSON.stringify(podatki.statistikeVzorci));
    if (podatki.neznanoVzorci) localStorage.setItem("neznano_vzorci", JSON.stringify(podatki.neznanoVzorci));
    if (podatki.peskovnik) localStorage.setItem("peskovnik_podatki", podatki.peskovnik);
    if (podatki.tema) localStorage.setItem("uporabnikovaTema", podatki.tema);
    if (podatki.kristali) localStorage.setItem("uporabnikoviKristali", podatki.kristali);
    if (podatki.odklenjeniAvatarji) localStorage.setItem("odklenjeniAvatarji", podatki.odklenjeniAvatarji);
  },
  
  prenesiKotDatoteko(vsebina, ime) {
    const blob = new Blob([vsebina], { type: "application/json" });
    const povezava = document.createElement("a");
    const url = URL.createObjectURL(blob);
    povezava.href = url;
    povezava.download = ime;
    document.body.appendChild(povezava);
    povezava.click();
    document.body.removeChild(povezava);
    URL.revokeObjectURL(url);
  },
  
  // Pripravi za Google Drive (odpre okno za shranjevanje)
  shraniNaGoogleDrive() {
    const podatki = this.zberiVsePodatke();
    const jsonPodatki = JSON.stringify(podatki);
    const blob = new Blob([jsonPodatki], { type: "application/json" });
    const datoteka = new File([blob], `peskovnik_kopija_${Date.now()}.json`, { type: "application/json" });
    
    // Google Drive API (potrebuje OAuth, simulacija)
    magičnoObvestilo("🔐 Za shranjevanje na Google Drive potrebuješ povezavo. Funkcija v razvoju.");
    
    // Simulacija: prenos datoteke
    this.prenesiKotDatoteko(jsonPodatki, `peskovnik_google_drive_${Date.now()}.json`);
  },
  
  prikaziPloscoZaKopiranje() {
    const modal = document.createElement('div');
    modal.className = 'trznica-modal';
    modal.innerHTML = `
      <div class="trznica-vsebina" style="max-width:500px">
        <h2>☁️ Varnostno kopiranje v oblak</h2>
        <div class="varnostne-moznosti">
          <div class="moznost">
            <h3>💾 Lokalna kopija</h3>
            <button id="ustvari-kopijo" class="varnostni-gumb">📥 Ustvari kopijo</button>
            <button id="obnovi-kopijo" class="varnostni-gumb">📤 Obnovi iz kopije</button>
            <input type="file" id="datoteka-za-obnovo" accept=".json" style="display:none">
          </div>
          <div class="moznost">
            <h3>☁️ Oblak (priprava)</h3>
            <button id="google-drive" class="varnostni-gumb">🔵 Google Drive</button>
            <button id="dropbox" class="varnostni-gumb">🔴 Dropbox</button>
          </div>
          <div class="moznost">
            <h3>📊 Zadnja kopija</h3>
            <div id="zadnja-kopija-info">${this.zadnjaKopijaInfo()}</div>
          </div>
        </div>
        <button class="zapri-modal">✖ Zapri</button>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    document.getElementById('ustvari-kopijo')?.addEventListener('click', () => {
      this.ustvariKopijo();
      const info = document.getElementById('zadnja-kopija-info');
      if (info) info.innerHTML = this.zadnjaKopijaInfo();
    });
    
    document.getElementById('obnovi-kopijo')?.addEventListener('click', () => {
      document.getElementById('datoteka-za-obnovo')?.click();
    });
    
    document.getElementById('datoteka-za-obnovo')?.addEventListener('change', (e) => {
      if (e.target.files[0]) this.obnoviIzKopije(e.target.files[0]);
    });
    
    document.getElementById('google-drive')?.addEventListener('click', () => this.shraniNaGoogleDrive());
    document.getElementById('dropbox')?.addEventListener('click', () => {
      magičnoObvestilo("📦 Dropbox povezava v pripravi. Zaenkrat uporabi lokalno kopijo.");
    });
    
    modal.querySelector('.zapri-modal')?.addEventListener('click', () => modal.remove());
  },
  
  zadnjaKopijaInfo() {
    const datum = localStorage.getItem("varnostna_kopija_datum");
    if (datum) {
      return `<p>📅 ${new Date(datum).toLocaleString('sl-SI')}</p><p>💾 Varnostna kopija obstaja</p>`;
    }
    return "<p>📅 Še ni ustvarjene kopije</p>";
  }
};