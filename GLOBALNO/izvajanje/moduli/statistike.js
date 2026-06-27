2. runtime/statistike.js – Povezave z numerologijo in horoskopom
javascript
// Statistike in povezave z neznanim
window.Statistike = {
  dnevneNapovedi: [],
  vzorci: [],
  
  obdelajMeritev(meritev) {
    const danes = new Date().toDateString();
    
    // Pridobi dnevne napovedi
    const napoved = this.pridobiNapovedZaDan(danes);
    
    // Poišči vzorce
    this.analizirajVzorce(meritev, napoved);
  },
  
  pridobiNapovedZaDan(datum) {
    // Povezava z numerologijo in horoskopom
    const numerologija = window.Numerologija?.izracunajZaDan(datum) || {};
    const horoskop = window.Horoskop?.pridobiZaDan(datum) || {};
    const vreme = window.Vreme?.pridobiVreme() || {};
    
    return {
      numeroloskoStevilo: numerologija.stevilo,
      horoskopskiZnak: horoskop.znak,
      vreme: vreme.opis,
      temperatura: vreme.temperatura
    };
  },
  
  analizirajVzorce(meritev, napoved) {
    // Iskanje korelacij med vedenjem in napovedmi
    const vzorec = {
      datum: new Date().toISOString(),
      nervoza: meritev.nervoza,
      hitrost: meritev.hitrostKlikov,
      energija: meritev.energija,
      numeroloskoStevilo: napoved.numeroloskoStevilo,
      horoskop: napoved.horoskopskiZnak,
      vreme: napoved.vreme
    };
    
    this.vzorci.push(vzorec);
    this.obdrziZadnjihVzorcev();
    this.shraniVzorce();
    
    // Preveri anomalije
    this.preveriAnomalije(vzorec);
  },
  
  preveriAnomalije(vzorec) {
    // Nenavadno visoka nervoza ob določenem horoskopu
    const podobni = this.vzorci.filter(v => v.horoskop === vzorec.horoskop);
    const povprecnaNervoza = podobni.reduce((a,b) => a + b.nervoza, 0) / podobni.length;
    
    if (vzorec.nervoza > povprecnaNervoza + 30) {
      magičnoObvestilo(`🔮 Zvezde kažejo, da si danes bolj nemiren kot običajno.`);
      this.pokaziNasvet(vzorec);
    }
    
    // Povezava z numerologijo
    if (vzorec.numeroloskoStevilo === 7 && vzorec.nervoza < 20) {
      magičnoObvestilo(`✨ Število 7 ti prinaša notranji mir. Danes si posebej umirjen.`);
    }
  },
  
  pokaziNasvet(vzorec) {
    const nasveti = {
      'oven': 'Ognjeni znaki so danes bolj nemirni. Priporočam meditacijo ob kristalu.',
      'bik': 'Zemeljski znaki potrebujejo stabilnost. Dodaj si kak gradnik za sprostitev.',
      'dvojček': 'Zračni znaki so razburkani. Poskusi pisati knjigo z glasom.',
      'rak': 'Vodni znaki čutijo valove. Pokliči svojega varuha.',
      'lev': 'Ognjeni kralj, tvoja energija je močna. Usmeri jo v ustvarjanje.',
      'devica': 'Analiziraš preveč. Sprosti se ob magičnem portalu.',
      'tehtnica': 'Iščeš ravnovesje. Ustvari si simetričen peskovnik.',
      'škorpijon': 'Tvoja globoka čustva so danes na površju. Raziskuj skrivnosti.',
      'strelec': 'Pustolovščina te vabi. Odpri nov portal.',
      'kozorog': 'Tvoja ambicija je velika. Prodaj svoj peskovnik na tržnici.',
      'vodnar': 'Inovativnost je tvoje ime. Ustvari nekaj popolnoma novega.',
      'ribi': 'Sanjariš. Zapiši svoje sanje v knjižnico.'
    };
    
    const znak = vzorec.horoskop?.toLowerCase();
    if (znak && nasveti[znak]) {
      magičnoObvestilo(`🔮 Astrološki nasvet: ${nasveti[znak]}`);
    }
  },
  
  obdrziZadnjihVzorcev() {
    if (this.vzorci.length > 1000) {
      this.vzorci = this.vzorci.slice(-500);
    }
  },
  
  shraniVzorce() {
    localStorage.setItem("statistike_vzorci", JSON.stringify(this.vzorci));
  },
  
  naloziVzorce() {
    const shranjeni = localStorage.getItem("statistike_vzorci");
    if (shranjeni) {
      this.vzorci = JSON.parse(shranjeni);
    }
  },
  
  // Napoved za uporabnika na podlagi vzorcev
  napovediZaUporabnika() {
    const zadnji = this.vzorci.slice(-10);
    const povprecje = {
      nervoza: zadnji.reduce((a,b) => a + b.nervoza, 0) / zadnji.length,
      energija: zadnji.reduce((a,b) => a + b.energija, 0) / zadnji.length
    };
    
    let napoved = "";
    if (povprecje.nervoza > 60) {
      napoved = "V zadnjem času si bolj nemiren. Priporočam sprostitvene portale.";
    } else if (povprecje.energija > 70) {
      napoved = "Tvoja ustvarjalnost je na vrhuncu! Odličen čas za prodajo peskovnikov.";
    } else {
      napoved = "Si v fazi raziskovanja. Knjižnica ti bo prinesla navdih.";
    }
    
    return napoved;
  }
};