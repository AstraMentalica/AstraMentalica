1. runtime/glasovni_ukazi.js
javascript
// Glasovno upravljanje peskovnika - vse v slovenščini
window.GlasovniPomočnik = (function() {
  let prepoznavalnik = null;
  let aktiven = false;
  let povratnaInfo = null; // funkcija za obvestila

  function nastaviPovratnoInfo(funkcija) {
    povratnaInfo = funkcija;
  }

  function zaženi() {
    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
      if (povratnaInfo) povratnaInfo("❌ Brskalnik ne podpira glasovnih ukazov");
      return false;
    }

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    prepoznavalnik = new SpeechRecognition();
    prepoznavalnik.lang = 'sl-SI'; // Slovenščina
    prepoznavalnik.continuous = true;
    prepoznavalnik.interimResults = false;

    prepoznavalnik.onresult = (dogodek) => {
      const zadnji = dogodek.results.length - 1;
      const ukaz = dogodek.results[zadnji][0].transcript.toLowerCase().trim();
      if (povratnaInfo) povratnaInfo(`🎤 Rekel si: "${ukaz}"`);
      obdelajUkaz(ukaz);
    };

    prepoznavalnik.onerror = (napaka) => {
      if (povratnaInfo) povratnaInfo(`🎤 Napaka: ${napaka.error}`);
    };

    prepoznavalnik.start();
    aktiven = true;
    if (povratnaInfo) povratnaInfo("🎤 Glasovno upravljanje aktivirano! Reci 'pomoč' za seznam ukazov.");
    return true;
  }

  function ustavi() {
    if (prepoznavalnik && aktiven) {
      prepoznavalnik.stop();
      aktiven = false;
      if (povratnaInfo) povratnaInfo("🎤 Glasovno upravljanje ustavljeno");
    }
  }

  function obdelajUkaz(ukaz) {
    // Seznam ukazov v slovenščini
    if (ukaz.includes("dodaj besedilo") || ukaz === "besedilo") {
      if (window.Peskovnik) Peskovnik.dodajGradnik("besedilo");
    }
    else if (ukaz.includes("dodaj gumb") || ukaz === "gumb") {
      if (window.Peskovnik) Peskovnik.dodajGradnik("gumb");
    }
    else if (ukaz.includes("dodaj sliko") || ukaz === "slika") {
      if (window.Peskovnik) Peskovnik.dodajGradnik("slika");
    }
    else if (ukaz.includes("odstrani zadnji") || ukaz === "zadnji") {
      if (window.Peskovnik) Peskovnik.odstraniZadnji();
    }
    else if (ukaz.includes("zbriši vse") || ukaz === "zbriši" || ukaz === "počisti") {
      if (window.Peskovnik) Peskovnik.zbrišiVse();
    }
    else if (ukaz.includes("shrani") || ukaz === "shrani") {
      if (window.Peskovnik) Peskovnik.shraniVse();
    }
    else if (ukaz.includes("naloži") || ukaz === "naloži") {
      if (window.Peskovnik) Peskovnik.naložiIzShrambe();
    }
    else if (ukaz.includes("tema svetla") || ukaz === "svetla") {
      if (window.preklopiNaTemo) preklopiNaTemo("svetla");
    }
    else if (ukaz.includes("tema temna") || ukaz === "temna") {
      if (window.preklopiNaTemo) preklopiNaTemo("temna");
    }
    else if (ukaz.includes("tema mistična") || ukaz === "mistična" || ukaz === "misticna") {
      if (window.preklopiNaTemo) preklopiNaTemo("misticna");
    }
    else if (ukaz.includes("kristal") || ukaz === "kristal") {
      magičnoObvestilo("✨ Kristal žari! ✨");
      const kristal = document.getElementById("kristal");
      if (kristal) {
        kristal.style.animation = "none";
        setTimeout(() => kristal.style.animation = "utripKristala 3s infinite, vrtiKristal 12s infinite linear", 10);
      }
    }
    else if (ukaz.includes("povečaj zadnji") || ukaz === "povečaj") {
      if (window.Peskovnik) Peskovnik.povečajZadnji();
    }
    else if (ukaz.includes("pomoč") || ukaz === "pomoč") {
      const seznam = `
🎤 Glasovni ukazi (slovenščina):
• dodaj besedilo
• dodaj gumb
• dodaj sliko
• odstrani zadnji
• zbriši vse
• shrani / naloži
• tema svetla / temna / mistična
• kristal
• povečaj zadnji
• pomoč
      `;
      magičnoObvestilo(seznam);
    }
  }

  return { zaženi, ustavi, nastaviPovratnoInfo };
})();
2. Dodatek v runtime/pogon.js (nove funkcije)
Dodaj te funkcije v window.Peskovnik:

javascript
// V pogon.js dodaj pod razdelek Peskovnik:

odstraniZadnji() {
  const vsiGradniki = document.querySelectorAll(".gradnik");
  if (vsiGradniki.length > 0) {
    vsiGradniki[vsiGradniki.length - 1].remove();
    this.shraniVse();
    magičnoObvestilo("Zadnji gradnik odstranjen");
  } else {
    magičnoObvestilo("Ni gradnikov za odstraniti");
  }
},

povečajZadnji() {
  const vsiGradniki = document.querySelectorAll(".gradnik");
  if (vsiGradniki.length > 0) {
    const zadnji = vsiGradniki[vsiGradniki.length - 1];
    const trenutni = zadnji.style.gridColumn;
    zadnji.style.gridColumn = trenutni === "span 2" ? "auto" : "span 2";
    this.shraniVse();
    magičnoObvestilo("Zadnji gradnik povečan");
  }
}
3. Dodatek v runtime/menjalnik_tem.js
javascript
// Dodaj globalno funkcijo za preklop teme iz glasovnih ukazov
window.preklopiNaTemo = (imeTeme) => {
  const temeDovoljene = ['svetla', 'temna', 'misticna'];
  if (temeDovoljene.includes(imeTeme)) {
    document.getElementById('tema-slog').href = `../vmesnik/css/teme/${imeTeme}.css`;
    localStorage.setItem('uporabnikovaTema', imeTeme);
    magičnoObvestilo(`Tema: ${imeTeme}`);
  }
};
🖱️ Drag & Drop (povleci in spusti) – popolnoma slovensko