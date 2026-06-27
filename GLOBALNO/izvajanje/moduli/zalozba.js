1. runtime/zalozba.js – Shranjevanje vseh uporabnikov
javascript
// Lokalna "baza" v localStorage – vsebuje vse uporabnike
window.Zalozba = {
  // Ključi v localStorage
  KLJUC_UPORABNIKI: "trznica_vsi_uporabniki",
  KLJUC_TRENUTNI: "trznica_trenutni_uporabnik",
  
  // Pridobi vse uporabnike
  vsiUporabniki() {
    const podatki = localStorage.getItem(this.KLJUC_UPORABNIKI);
    if (!podatki) {
      // Začetni podatki
      const zacetni = {
        "gost": {
          ime: "gost",
          geslo: "", // brez gesla za gosta
          kristali: 100,
          odklenjeniAvatarji: ["Čarovnik Merlin", "Vila Cvetka"],
          mojiPeskovniki: [],
          mojiIzdelki: [],
          ocena: 0,
          steviloProdaj: 0
        }
      };
      localStorage.setItem(this.KLJUC_UPORABNIKI, JSON.stringify(zacetni));
      return zacetni;
    }
    return JSON.parse(podatki);
  },
  
  // Shrani vse uporabnike
  shraniVseUporabnike(uporabniki) {
    localStorage.setItem(this.KLJUC_UPORABNIKI, JSON.stringify(uporabniki));
  },
  
  // Pridobi trenutnega uporabnika
  trenutniUporabnik() {
    const ime = localStorage.getItem(this.KLJUC_TRENUTNI) || "gost";
    const vsi = this.vsiUporabniki();
    return vsi[ime] || vsi["gost"];
  },
  
  // Nastavi trenutnega uporabnika
  nastaviTrenutnega(ime) {
    localStorage.setItem(this.KLJUC_TRENUTNI, ime);
  },
  
  // Registracija novega uporabnika
  registracija(ime, geslo) {
    const vsi = this.vsiUporabniki();
    if (vsi[ime]) return { uspeh: false, sporocilo: "Uporabnik že obstaja!" };
    
    vsi[ime] = {
      ime: ime,
      geslo: geslo,
      kristali: 50, // začetni kristali
      odklenjeniAvatarji: ["Čarovnik Merlin", "Vila Cvetka"],
      mojiPeskovniki: [], // ID-ji peskovnikov, ki jih je ustvaril
      mojiIzdelki: [], // izdelki, ki jih prodaja
      ocena: 0,
      steviloProdaj: 0
    };
    this.shraniVseUporabnike(vsi);
    return { uspeh: true, sporocilo: "Registracija uspešna!" };
  },
  
  // Prijava
  prijava(ime, geslo) {
    const vsi = this.vsiUporabniki();
    const uporabnik = vsi[ime];
    if (!uporabnik) return { uspeh: false, sporocilo: "Uporabnik ne obstaja!" };
    if (uporabnik.geslo !== geslo && ime !== "gost") return { uspeh: false, sporocilo: "Napačno geslo!" };
    
    this.nastaviTrenutnega(ime);
    return { uspeh: true, sporocilo: `Pozdravljen, ${ime}!` };
  },
  
  // Dodaj kristale uporabniku
  dodajKristale(ime, kolicina) {
    const vsi = this.vsiUporabniki();
    if (vsi[ime]) {
      vsi[ime].kristali += kolicina;
      this.shraniVseUporabnike(vsi);
      if (ime === this.trenutniUporabnik().ime) {
        this.posodobiUI();
      }
    }
  },
  
  // Odštej kristale
  odstejKristale(ime, kolicina) {
    const vsi = this.vsiUporabniki();
    if (vsi[ime] && vsi[ime].kristali >= kolicina) {
      vsi[ime].kristali -= kolicina;
      this.shraniVseUporabnike(vsi);
      if (ime === this.trenutniUporabnik().ime) {
        this.posodobiUI();
      }
      return true;
    }
    return false;
  },
  
  // Shrani uporabnikov peskovnik
  shraniPeskovnik(ime, peskovnikPodatki) {
    const vsi = this.vsiUporabniki();
    if (vsi[ime]) {
      const idPeskovnika = "pes_" + Date.now() + "_" + Math.random().toString(36).substr(2, 6);
      const novPeskovnik = {
        id: idPeskovnika,
        ime: peskovnikPodatki.ime || "Moj peskovnik",
        datum: new Date().toISOString(),
        gradniki: peskovnikPodatki.gradniki,
        tema: peskovnikPodatki.tema,
        postavitev: peskovnikPodatki.postavitev,
        zaProdajo: false,
        cena: 0
      };
      vsi[ime].mojiPeskovniki.push(novPeskovnik);
      this.shraniVseUporabnike(vsi);
      return idPeskovnika;
    }
    return null;
  },
  
  // Naloži uporabnikov peskovnik
  naloziPeskovnik(ime, idPeskovnika) {
    const vsi = this.vsiUporabniki();
    const uporabnik = vsi[ime];
    if (uporabnik) {
      const peskovnik = uporabnik.mojiPeskovniki.find(p => p.id === idPeskovnika);
      if (peskovnik) return peskovnik;
    }
    return null;
  },
  
  // Daj izdelek v prodajo
  dajVProdajo(ime, idPeskovnika, cena) {
    const vsi = this.vsiUporabniki();
    const uporabnik = vsi[ime];
    if (uporabnik) {
      const peskovnik = uporabnik.mojiPeskovniki.find(p => p.id === idPeskovnika);
      if (peskovnik) {
        peskovnik.zaProdajo = true;
        peskovnik.cena = cena;
        
        // Dodaj med izdelke
        uporabnik.mojiIzdelki.push({
          id: idPeskovnika,
          ime: peskovnik.ime,
          cena: cena,
          prodajalec: ime,
          datum: new Date().toISOString()
        });
        
        this.shraniVseUporabnike(vsi);
        return true;
      }
    }
    return false;
  },
  
  // Kupi izdelek od drugega uporabnika
  kupiIzdelek(kupcevoIme, prodajalcevoIme, idPeskovnika, cena) {
    const vsi = this.vsiUporabniki();
    const kupec = vsi[kupcevoIme];
    const prodajalec = vsi[prodajalcevoIme];
    
    if (!kupec || !prodajalec) return { uspeh: false, sporocilo: "Uporabnik ne obstaja" };
    if (kupec.kristali < cena) return { uspeh: false, sporocilo: "Nimaš dovolj kristalov!" };
    
    // Poišči peskovnik pri prodajalcu
    const peskovnik = prodajalec.mojiPeskovniki.find(p => p.id === idPeskovnika);
    if (!peskovnik || !peskovnik.zaProdajo) return { uspeh: false, sporocilo: "Izdelek ni več v prodaji" };
    
    // Odštej kupcu, dodaj prodajalcu
    kupec.kristali -= cena;
    prodajalec.kristali += cena;
    prodajalec.steviloProdaj++;
    
    // Kloniraj peskovnik za kupca
    const kupljenPeskovnik = JSON.parse(JSON.stringify(peskovnik));
    kupljenPeskovnik.id = "kup_" + Date.now() + "_" + Math.random().toString(36).substr(2, 6);
    kupljenPeskovnik.zaProdajo = false;
    kupljenPeskovnik.kupljenOd = prodajalcevoIme;
    kupljenPeskovnik.datumNakupa = new Date().toISOString();
    
    kupec.mojiPeskovniki.push(kupljenPeskovnik);
    
    // Odstrani iz prodajalčevih izdelkov
    prodajalec.mojiIzdelki = prodajalec.mojiIzdelki.filter(i => i.id !== idPeskovnika);
    peskovnik.zaProdajo = false;
    
    this.shraniVseUporabnike(vsi);
    
    return { uspeh: true, sporocilo: `Kupil si peskovnik "${peskovnik.ime}" za ${cena} kristalov!` };
  },
  
  // Pridobi vse izdelke v prodaji
  vsiIzdelki() {
    const vsi = this.vsiUporabniki();
    const izdelki = [];
    for (const ime in vsi) {
      const uporabnik = vsi[ime];
      if (uporabnik.mojiIzdelki) {
        izdelki.push(...uporabnik.mojiIzdelki.map(i => ({
          ...i,
          prodajalecIme: ime,
          prodajalecOcena: uporabnik.ocena,
          prodajalecStevilo: uporabnik.steviloProdaj
        })));
      }
    }
    return izdelki.sort((a, b) => new Date(b.datum) - new Date(a.datum));
  },
  
  posodobiUI() {
    const trenutni = this.trenutniUporabnik();
    const stevecKristalov = document.getElementById('stevec-kristalov');
    if (stevecKristalov) stevecKristalov.textContent = `💎 ${trenutni.kristali}`;
    
    const imeUporabnika = document.getElementById('ime-uporabnika');
    if (imeUporabnika) imeUporabnika.textContent = trenutni.ime;
  }
};