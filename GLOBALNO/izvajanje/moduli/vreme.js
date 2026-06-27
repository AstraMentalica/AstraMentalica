5. runtime/vreme.js – Povezava z vremenom
javascript
// Pridobivanje vremenskih podatkov (simulacija + API)
window.Vreme = {
  trenutnoVreme: null,
  
  async pridobiVreme() {
    // Poskus z API (če ni na voljo, simulacija)
    try {
      // Uporabi prost API (brez ključa)
      const odgovor = await fetch('https://wttr.in/?format=%C+%t+%w');
      const podatki = await odgovor.text();
      const deli = podatki.split(' ');
      
      this.trenutnoVreme = {
        opis: deli[0] || "jasno",
        temperatura: deli[1] || "15°C",
        veter: deli[2] || "0 km/h",
        vpliv: this.vplivVremena(deli[0])
      };
    } catch (e) {
      // Simulacija
      const vremena = ["jasno", "oblačno", "deževno", "megleno", "sončno", "vetrovno"];
      const nakljucno = vremena[Math.floor(Math.random() * vremena.length)];
      this.trenutnoVreme = {
        opis: nakljucno,
        temperatura: `${Math.floor(Math.random() * 25) + 5}°C`,
        veter: `${Math.floor(Math.random() * 30)} km/h`,
        vpliv: this.vplivVremena(nakljucno)
      };
    }
    
    return this.trenutnoVreme;
  },
  
  vplivVremena(vreme) {
    const vplivi = {
      "jasno": { energija: +20, nervoza: -10, nasvet: "Sončen dan – tvoja energija je visoka!" },
      "sončno": { energija: +25, nervoza: -15, nasvet: "Sončni žarki ti dajejo moč!" },
      "oblačno": { energija: -5, nervoza: +5, nasvet: "Oblačno nebo – poišči navdih v knjižnici." },
      "deževno": { energija: -15, nervoza: +10, nasvet: "Dež prinaša čustva – odpri topli portal." },
      "megleno": { energija: -10, nervoza: +15, nasvet: "Megla skriva skrivnosti – razišči jih." },
      "vetrovno": { energija: +5, nervoza: +20, nasvet: "Veter prinaša spremembe – bodi pozoren." }
    };
    return vplivi[vreme] || { energija: 0, nervoza: 0, nasvet: "Vreme je danes čarobno." };
  },
  
  async posodobiInPovezi() {
    await this.pridobiVreme();
    
    // Poveži z merilci
    if (window.Merilci && this.trenutnoVreme.vpliv) {
      if (this.trenutnoVreme.vpliv.energija > 0) {
        Merilci.povecajNervozo(-5);
      } else if (this.trenutnoVreme.vpliv.energija < 0) {
        Merilci.povecajNervozo(5);
      }
    }
    
    // Obvestilo
    magičnoObvestilo(`🌤️ Vreme: ${this.trenutnoVreme.opis}, ${this.trenutnoVreme.temperatura}. ${this.trenutnoVreme.vpliv.nasvet}`);
    
    return this.trenutnoVreme;
  }
};