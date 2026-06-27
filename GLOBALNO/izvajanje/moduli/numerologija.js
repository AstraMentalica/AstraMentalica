3. runtime/numerologija.js – Povezava z numerologijo
javascript
// Numerološki izračuni
window.Numerologija = {
  izracunajZaDan(datum) {
    const d = new Date(datum);
    const dan = d.getDate();
    const mesec = d.getMonth() + 1;
    const leto = d.getFullYear();
    
    const vsota = dan + mesec + leto;
    const stevilo = this.zmanjsajNaEnomestno(vsota);
    
    const pomeni = {
      1: "Novi začetki, neodvisnost, vodenje",
      2: "Partnerstvo, ravnovesje, sodelovanje",
      3: "Ustvarjalnost, komunikacija, veselje",
      4: "Stabilnost, red, trdo delo",
      5: "Sprememba, svoboda, pustolovščina",
      6: "Ljubezen, družina, odgovornost",
      7: "Duhovnost, modrost, notranji mir",
      8: "Moč, uspeh, materialno bogastvo",
      9: "Humanitarnost, zaključek, modrost"
    };
    
    return {
      stevilo: stevilo,
      pomen: pomeni[stevilo] || "Skrivnostno število",
      energija: this.energijaStevila(stevilo)
    };
  },
  
  zmanjsajNaEnomestno(stevilo) {
    while (stevilo > 9) {
      stevilo = stevilo.toString().split('').reduce((a,b) => a + parseInt(b), 0);
    }
    return stevilo;
  },
  
  energijaStevila(stevilo) {
    const energije = {1:85, 2:60, 3:90, 4:50, 5:95, 6:70, 7:40, 8:100, 9:80};
    return energije[stevilo] || 65;
  },
  
  izracunajIme(ime) {
    const vrednosti = {a:1, b:2, c:3, d:4, e:5, f:6, g:7, h:8, i:9, j:1, k:2, l:3, m:4, n:5, o:6, p:7, q:8, r:9, s:1, t:2, u:3, v:4, w:5, x:6, y:7, z:8};
    const vsota = ime.toLowerCase().split('').reduce((a,b) => a + (vrednosti[b] || 0), 0);
    return this.zmanjsajNaEnomestno(vsota);
  }
};