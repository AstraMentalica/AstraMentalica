9. runtime/menjalnik_tem.js
javascript
const teme = ['misticna', 'svetla', 'temna'];
let trenutnaTemaIndex = 0;
const gumbTema = document.getElementById('gumb-tema');
const temaLink = document.getElementById('tema-slog');

function preklopiTemo() {
  trenutnaTemaIndex = (trenutnaTemaIndex + 1) % teme.length;
  const novaTema = teme[trenutnaTemaIndex];
  temaLink.href = `../vmesnik/css/teme/${novaTema}.css`;
  localStorage.setItem('uporabnikovaTema', novaTema);
  magičnoObvestilo(`Tema: ${novaTema}`);
}

if (gumbTema) {
  gumbTema.onclick = preklopiTemo;
}

const shranjenaTema = localStorage.getItem('uporabnikovaTema');
if (shranjenaTema && teme.includes(shranjenaTema)) {
  temaLink.href = `../vmesnik/css/teme/${shranjenaTema}.css`;
}
10. runtime/pogon.js (jedro peskovnika – vse slovensko)
javascript
window.Peskovnik = {
  gradniki: [],
  posoda: null,

  zaženi(idPosode) {
    this.posoda = document.getElementById(idPosode);
    this.naložiIzShrambe();
  },

  dodajGradnik(vrsta) {
    const id = "gr_" + Date.now() + "_" + Math.random().toString(36).substr(2, 6);
    let vsebinaHTML = "";
    if (vrsta === "besedilo") vsebinaHTML = `<p contenteditable="true">Napiši svoje sporočilo...</p>`;
    if (vrsta === "gumb") vsebinaHTML = `<button class="moj-gumb" contenteditable="false">✨ Čarobni gumb</button>`;
    if (vrsta === "slika") vsebinaHTML = `<img src="https://picsum.photos/200/120?random=${id}" width="100%" style="border-radius:16px">`;

    const gradnikHTML = `
      <div class="gradnik" data-id="${id}" data-vrsta="${vrsta}">
        <div class="vsebina-gradnika">${vsebinaHTML}</div>
        <div class="gradnik-gumbi">
          <button class="odstrani-grd">🗑 Odstrani</button>
          <button class="povečaj-grd">🔍 Povečaj</button>
        </div>
      </div>
    `;
    this.posoda.insertAdjacentHTML("beforeend", gradnikHTML);
    this._pripniDogodke(this.posoda.lastElementChild);
    this.shraniVse();
    magičnoObvestilo(`Dodan ${vrsta}`);
  },

  _pripniDogodke(gradnikElement) {
    const odstraniGumb = gradnikElement.querySelector(".odstrani-grd");
    if (odstraniGumb) odstraniGumb.onclick = () => {
      gradnikElement.remove();
      this.shraniVse();
      magičnoObvestilo("Gradnik odstranjen");
    };
    const povecajGumb = gradnikElement.querySelector(".povečaj-grd");
    if (povecajGumb) povecajGumb.onclick = () => {
      const trenutni = gradnikElement.style.gridColumn;
      gradnikElement.style.gridColumn = trenutni === "span 2" ? "auto" : "span 2";
      this.shraniVse();
    };
  },

  shraniVse() {
    const seznam = [];
    document.querySelectorAll(".gradnik").forEach(g => {
      seznam.push({
        id: g.dataset.id,
        vrsta: g.dataset.vrsta,
        vsebina: g.querySelector(".vsebina-gradnika").innerHTML,
        sirina: g.style.gridColumn || "auto"
      });
    });
    localStorage.setItem("peskovnik_podatki", JSON.stringify(seznam));
  },

  naložiIzShrambe() {
    const podatki = localStorage.getItem("peskovnik_podatki");
    if (!podatki) return;
    const seznam = JSON.parse(podatki);
    this.posoda.innerHTML = "";
    seznam.forEach(p => {
      const gradnikHTML = `
        <div class="gradnik" data-id="${p.id}" data-vrsta="${p.vrsta}" style="grid-column: ${p.sirina}">
          <div class="vsebina-gradnika">${p.vsebina}</div>
          <div class="gradnik-gumbi">
            <button class="odstrani-grd">🗑 Odstrani</button>
            <button class="povečaj-grd">🔍 Povečaj</button>
          </div>
        </div>
      `;
      this.posoda.insertAdjacentHTML("beforeend", gradnikHTML);
      this._pripniDogodke(this.posoda.lastElementChild);
    });
    magičnoObvestilo("Peskovnik naložen");
  },

  zbrišiVse() {
    if (confirm("Želiš zbrisati vse gradnike?")) {
      this.posoda.innerHTML = "";
      localStorage.removeItem("peskovnik_podatki");
      magičnoObvestilo("Peskovnik je prazen ✨");
    }
  }
};
11. runtime/peskovnik.js (povezava gumbov)
javascript
document.addEventListener("DOMContentLoaded", () => {
  Peskovnik.zaženi("peskovnik-platno");

  document.getElementById("dodaj-besedilo").onclick = () => Peskovnik.dodajGradnik("besedilo");
  document.getElementById("dodaj-gumb").onclick = () => Peskovnik.dodajGradnik("gumb");
  document.getElementById("dodaj-sliko").onclick = () => Peskovnik.dodajGradnik("slika");
  document.getElementById("shrani-peskovnik").onclick = () => Peskovnik.shraniVse();
  document.getElementById("nalozi-peskovnik").onclick = () => Peskovnik.naložiIzShrambe();
  document.getElementById("zbrisi-vse").onclick = () => Peskovnik.zbrišiVse();

  // Kristal pokaže magično sporočilo
  const kristal = document.getElementById("kristal");
  if (kristal) {
    kristal.onclick = () => magičnoObvestilo("Kristal blešči 🌟 Tvoj domišljiji ni meja");
  }
});
✅ Končni povzetek – vse je slovensko:
Gradniki so besedilo, gumb, slika

Spremenljivke/funkcije: Peskovnik, dodajGradnik, shraniVse, magičnoObvestilo, zbranaEnergija, AmbientniZvok

CSS razredi: .kristal, .energijski-trak, .magični-delec, .magicno-obvestilo

Teme: misticna, svetla, temna

Vse deluje takoj, ko postaviš pravilno pot do zvočne datoteke (/zvoki/ambient/meditacija.mp3).
Če želiš povleci in spusti (drag & drop), dodam še povleci_spusti.js – samo reci. 🎯

Si zadovoljen? Lahko dodam še koledar, vreme, števec, beležko – vse v slovenščini.

ja drag and drog absolutno želim, 3d  bi tudi želel.. 


pa ... 
pa .. ful si želim, da bi lahko stran opravljal kar z glasom
To je naslednja raven! Peskovnik, ki ga upravljaš z glasom, povlečeš in spustiš z miško, pa še 3D efekti – to bo res izjemno.

Vse bom dal v slovenščini, tudi glasovne ukaze.

🎤 Glasovno upravljanje – ukazi v slovenščini
Uporabnik bo rekel:

Ukaz	Kaj naredi
"dodaj besedilo"	doda besedilni gradnik
"dodaj gumb"	doda gumb
"dodaj sliko"	doda sliko
"odstrani zadnji"	odstrani zadnji gradnik
"zbriši vse"	počisti peskovnik
"shrani"	shrani postavitev
"naloži"	naloži shranjeno
"tema svetla"	preklopi na svetlo temo
"tema temna"	preklopi na temno
"tema mistična"	preklopi na mistično
"kristal"	aktivira kristal
"povečaj zadnji"	poveča zadnji gradnik
"pomoč"	izpiše vse ukaze
📦 Dodatne datoteke