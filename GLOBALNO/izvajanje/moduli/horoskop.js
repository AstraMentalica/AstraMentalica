4. runtime/horoskop.js – Astrološke povezave
javascript
// Horoskopski izračuni
window.Horoskop = {
  znaki: [
    "Oven", "Bik", "Dvojček", "Rak", "Lev", "Devica",
    "Tehtnica", "Škorpijon", "Strelec", "Kozorog", "Vodnar", "Ribi"
  ],
  
  pridobiZnak(datum) {
    const d = new Date(datum);
    const dan = d.getDate();
    const mesec = d.getMonth() + 1;
    
    if ((mesec === 3 && dan >= 21) || (mesec === 4 && dan <= 19)) return "Oven";
    if ((mesec === 4 && dan >= 20) || (mesec === 5 && dan <= 20)) return "Bik";
    if ((mesec === 5 && dan >= 21) || (mesec === 6 && dan <= 20)) return "Dvojček";
    if ((mesec === 6 && dan >= 21) || (mesec === 7 && dan <= 22)) return "Rak";
    if ((mesec === 7 && dan >= 23) || (mesec === 8 && dan <= 22)) return "Lev";
    if ((mesec === 8 && dan >= 23) || (mesec === 9 && dan <= 22)) return "Devica";
    if ((mesec === 9 && dan >= 23) || (mesec === 10 && dan <= 22)) return "Tehtnica";
    if ((mesec === 10 && dan >= 23) || (mesec === 11 && dan <= 21)) return "Škorpijon";
    if ((mesec === 11 && dan >= 22) || (mesec === 12 && dan <= 21)) return "Strelec";
    if ((mesec === 12 && dan >= 22) || (mesec === 1 && dan <= 19)) return "Kozorog";
    if ((mesec === 1 && dan >= 20) || (mesec === 2 && dan <= 18)) return "Vodnar";
    return "Ribi";
  },
  
  pridobiZaDan(datum) {
    const znak = this.pridobiZnak(datum);
    const napovedi = this.napovediZaZnak(znak);
    
    return {
      znak: znak,
      dnevnaNapoved: napovedi.napoved,
      energija: napovedi.energija,
      srečnaStevilka: Math.floor(Math.random() * 9) + 1,
      srečnaBarva: this.srecneBarve[znak]
    };
  },
  
  napovediZaZnak(znak) {
    const napovedi = {
      "Oven": { napoved: "Danes si poln energije. Odličen dan za ustvarjanje novih gradnikov.", energija: 90 },
      "Bik": { napoved: "Počasi a zanesljivo. Prodaja na tržnici ti bo uspela.", energija: 70 },
      "Dvojček": { napoved: "Komunikacija je tvoja moč. Piši knjigo z glasom.", energija: 85 },
      "Rak": { napoved: "Čustva so močna. Pokliči varuha za podporo.", energija: 60 },
      "Lev": { napoved: "Tvoj čas je! Pokaži svoje peskovnike svetu.", energija: 95 },
      "Devica": { napoved: "Podrobnosti so pomembne. Uredi svoj peskovnik do potankosti.", energija: 75 },
      "Tehtnica": { napoved: "Iščeš harmonijo. Ustvari simetrično postavitev.", energija: 80 },
      "Škorpijon": { napoved: "Skrivnosti te vabijo. Razišči skrite portale.", energija: 65 },
      "Strelec": { napoved: "Pustolovščina kliče. Odpri nov magični portal.", energija: 100 },
      "Kozorog": { napoved: "Disciplina se obrestuje. Tvoji izdelki bodo uspešni.", energija: 85 },
      "Vodnar": { napoved: "Inovativnost je tvoja prednost. Ustvari nekaj edinstvenega.", energija: 90 },
      "Ribi": { napoved: "Sanjarjenje je ustvarjalnost. Zapiši svoje sanje.", energija: 70 }
    };
    return napovedi[znak] || { napoved: "Danes je poln magičnih možnosti.", energija: 75 };
  },
  
  srecneBarve: {
    "Oven": "rdeča", "Bik": "zelena", "Dvojček": "rumena", "Rak": "bela",
    "Lev": "zlata", "Devica": "rjava", "Tehtnica": "modra", "Škorpijon": "črna",
    "Strelec": "vijolična", "Kozorog": "siva", "Vodnar": "turkizna", "Ribi": "roza"
  }
};