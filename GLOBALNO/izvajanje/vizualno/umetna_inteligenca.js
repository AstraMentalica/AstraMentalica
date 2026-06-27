1. runtime/umetna_inteligenca.js – Glavna UI z varuhi in avatarji
javascript
// Umetna inteligenca za peskovnik – varuhi in avatarji
window.UmetnaInteligenca = {
  varuhi: [],
  aktivniVaruh: null,
  avatarji: [],
  aktivniAvatar: null,
  govorilec: null,
  
  // Inicializacija
  iniciacija() {
    this.ustvariGovorilca();
    this.naložiVaruhe();
    this.naložiAvatarje();
    this.pokaziPozdrav();
  },
  
  // Govorni sintetizator (UI govori)
  ustvariGovorilca() {
    if ('speechSynthesis' in window) {
      this.govorilec = new SpeechSynthesisUtterance();
      this.govorilec.lang = 'sl-SI';
      this.govorilec.rate = 0.95;
      this.govorilec.pitch = 1.1;
    }
  },
  
  govori(besedilo, ton = 'prijazen') {
    if (!this.govorilec) return;
    this.govorilec.text = besedilo;
    
    // Različni toni glede na varuha
    if (ton === 'moder') this.govorilec.pitch = 0.9;
    else if (ton === 'vesel') this.govorilec.pitch = 1.3;
    else if (ton === 'skrivnosten') this.govorilec.pitch = 0.8, this.govorilec.rate = 0.8;
    else this.govorilec.pitch = 1.1;
    
    window.speechSynthesis.cancel();
    window.speechSynthesis.speak(this.govorilec);
  },
  
  // VARUHI
  naložiVaruhe() {
    this.varuhi = [
      {
        ime: "Modri Zmaj",
        vloga: "varuh znanja",
        energija: 100,
        sposobnosti: ["odgovarja na vprašanja", "razlaga magijo", "ščiti skrivnosti"],
        barva: "#4a90e2",
        simbol: "🐉",
        glas: "moder"
      },
      {
        ime: "Zlata Sova",
        vloga: "varuh modrosti",
        energija: 100,
        sposobnosti: ["vidi prihodnost", "svetuje pri odločitvah", "odkrije skrite poti"],
        barva: "#d4af37",
        simbol: "🦉",
        glas: "moder"
      },
      {
        ime: "Šepetajoči Duh",
        vloga: "varuh skrivnosti",
        energija: 100,
        sposobnosti: ["odklepa skrivne portale", "šepeta namige", "prikliče magijo"],
        barva: "#9b59b6",
        simbol: "👻",
        glas: "skrivnosten"
      },
      {
        ime: "Veseli Goban",
        vloga: "varuh zabave",
        energija: 100,
        sposobnosti: ["doda humor", "spodbuja ustvarjalnost", "odpira igre"],
        barva: "#2ecc71",
        simbol: "🍄",
        glas: "vesel"
      }
    ];
  },
  
  prikaziVaruhe() {
    const ploca = document.createElement('div');
    ploca.id = 'ploca-varuhov';
    ploca.className = 'ploca-varuhov';
    ploca.innerHTML = '<div class="naslov-varuhov">✨ Varuhi peskovnika ✨</div>';
    
    this.varuhi.forEach((varuh, idx) => {
      const karton = document.createElement('div');
      karton.className = 'karton-varuha';
      karton.style.borderColor = varuh.barva;
      karton.innerHTML = `
        <div class="simbol-varuha" style="background:${varuh.barva}20">${varuh.simbol}</div>
        <div class="ime-varuha">${varuh.ime}</div>
        <div class="vloga-varuha">${varuh.vloga}</div>
        <div class="sposobnosti-varuha">${varuh.sposobnosti.join(' • ')}</div>
        <button class="poklici-varuha" data-ime="${varuh.ime}">🔮 Pokliči</button>
      `;
      ploca.appendChild(karton);
    });
    
    document.body.appendChild(ploca);
    
    // Dogodki za gumbe
    document.querySelectorAll('.poklici-varuha').forEach(gumb => {
      gumb.onclick = (e) => {
        const ime = e.target.dataset.ime;
        this.aktivirajVaruha(ime);
        document.getElementById('ploca-varuhov')?.remove();
      };
    });
  },
  
  aktivirajVaruha(ime) {
    const varuh = this.varuhi.find(v => v.ime === ime);
    if (!varuh) return;
    
    this.aktivniVaruh = varuh;
    magičnoObvestilo(`${varuh.simbol} ${varuh.ime} te je prišel varovat!`);
    this.govori(`Jaz sem ${varuh.ime}, ${varuh.vloga}. ${this.dajNagovor(varuh)}`, varuh.glas);
    
    // Prikaži varuha na strani
    this.prikaziAktivnegaVaruha(varuh);
  },
  
  dajNagovor(varuh) {
    const nagovori = {
      "Modri Zmaj": "Tvoje znanje bom varoval z ognjem modrosti.",
      "Zlata Sova": "Vidim tvojo pot. Naj ti svetujem.",
      "Šepetajoči Duh": "Skrivnosti ti bom razkril, a le če si pripravljen.",
      "Veseli Goban": "Čas je za veselje in čarovnijo! Hop!"
    };
    return nagovori[varuh.ime] || "Tu sem, da ti pomagam.";
  },
  
  prikaziAktivnegaVaruha(varuh) {
    const obstojeci = document.getElementById('aktivni-varuh');
    if (obstojeci) obstojeci.remove();
    
    const okvir = document.createElement('div');
    okvir.id = 'aktivni-varuh';
    okvir.className = 'aktivni-varuh';
    okvir.style.borderColor = varuh.barva;
    okvir.style.background = `linear-gradient(135deg, ${varuh.barva}20, ${varuh.barva}05)`;
    okvir.innerHTML = `
      <div class="varuh-levo">
        <div class="varuh-simbol" style="text-shadow:0 0 15px ${varuh.barva}">${varuh.simbol}</div>
        <div class="varuh-ime">${varuh.ime}</div>
      </div>
      <div class="varuh-desno">
        <div class="varuh-sporocilo" id="varuh-sporocilo">Klikni me za nasvet</div>
        <div class="varuh-energija">
          <div class="varuh-energijski-trak">
            <div class="varuh-energija-napolnjenost" style="width:${varuh.energija}%; background:${varuh.barva}"></div>
          </div>
        </div>
      </div>
      <button class="zapri-varuha" id="zapri-varuha">✖</button>
    `;
    document.body.appendChild(okvir);
    
    // Sporočilo ob kliku
    document.getElementById('varuh-sporocilo')?.addEventListener('click', () => {
      const nasvet = this.dajNasvet(varuh);
      magičnoObvestilo(`${varuh.simbol} ${nasvet}`);
      this.govori(nasvet, varuh.glas);
    });
    
    document.getElementById('zapri-varuha')?.addEventListener('click', () => {
      okvir.remove();
      this.aktivniVaruh = null;
    });
  },
  
  dajNasvet(varuh) {
    const nasveti = {
      "Modri Zmaj": "Dodaj več gradnikov in ustvari svoj svet!",
      "Zlata Sova": "Skrivni portali se odprejo ponoči...",
      "Šepetajoči Duh": "Poskusi reči 'odpri skrivnost' z glasom.",
      "Veseli Goban": "Povleci gradnike in glej, kaj se zgodi!"
    };
    return nasveti[varuh.ime] || "Ustvarjaj, raziskuj, čaraj!";
  },
  
  // AVATARJI
  naložiAvatarje() {
    this.avatarji = [
      { ime: "Čarovnik Merlin", vrsta: "čarovnik", moč: 85, cena: 0, odklenjen: true, simbol: "🧙", barva: "#6c3483" },
      { ime: "Vila Cvetka", vrsta: "vila", moč: 70, cena: 0, odklenjen: true, simbol: "🧚", barva: "#e91e63" },
      { ime: "Zmajček Škrlj", vrsta: "zmaj", moč: 95, cena: 50, odklenjen: false, simbol: "🐉", barva: "#e67e22" },
      { ime: "Senčni Vitez", vrsta: "vitez", moč: 100, cena: 100, odklenjen: false, simbol: "⚔️", barva: "#2c3e50" },
      { ime: "Mesečni Volkodlak", vrsta: "volkodlak", moč: 90, cena: 75, odklenjen: false, simbol: "🐺", barva: "#bdc3c7" }
    ];
    this.naloziOdklenjeneAvatarje();
  },
  
  naloziOdklenjeneAvatarje() {
    const shranjeni = localStorage.getItem("odklenjeniAvatarji");
    if (shranjeni) {
      const odklenjeni = JSON.parse(shranjeni);
      this.avatarji.forEach(av => {
        if (odklenjeni.includes(av.ime)) av.odklenjen = true;
      });
    }
  },
  
  shraniOdklenjeneAvatarje() {
    const odklenjeni = this.avatarji.filter(av => av.odklenjen).map(av => av.ime);
    localStorage.setItem("odklenjeniAvatarji", JSON.stringify(odklenjeni));
  },
  
  prikaziAvatarje() {
    const ploca = document.createElement('div');
    ploca.id = 'ploca-avatarjev';
    ploca.className = 'ploca-avatarjev';
    ploca.innerHTML = '<div class="naslov-avatarjev">🌟 Tvoji avatarji 🌟</div><div class="avatarji-mreza"></div>';
    const mreza = ploca.querySelector('.avatarji-mreza');
    
    this.avatarji.forEach(av => {
      const karton = document.createElement('div');
      karton.className = `karton-avatarja ${av.odklenjen ? 'odklenjen' : 'zaklenjen'}`;
      karton.innerHTML = `
        <div class="avatar-simbol" style="background:${av.barva}20">${av.simbol}</div>
        <div class="avatar-ime">${av.ime}</div>
        <div class="avatar-moc">⚡ Moč: ${av.moč}</div>
        ${!av.odklenjen ? `<div class="avatar-cena">🔮 Cena: ${av.cena} kristalov</div>` : ''}
        ${!av.odklenjen ? `<button class="odkleni-avatar" data-ime="${av.ime}" data-cena="${av.cena}">✨ Odkleni</button>` : `<button class="izberi-avatar" data-ime="${av.ime}">👤 Izberi</button>`}
      `;
      mreza.appendChild(karton);
    });
    
    document.body.appendChild(ploca);
    
    // Odklepanje avatarjev
    document.querySelectorAll('.odkleni-avatar').forEach(gumb => {
      gumb.onclick = (e) => {
        const ime = e.target.dataset.ime;
        const cena = parseInt(e.target.dataset.cena);
        this.odkleniAvatar(ime, cena);
        ploca.remove();
        this.prikaziAvatarje();
      };
    });
    
    // Izberi avatar
    document.querySelectorAll('.izberi-avatar').forEach(gumb => {
      gumb.onclick = (e) => {
        const ime = e.target.dataset.ime;
        this.aktivirajAvatar(ime);
        ploca.remove();
      };
    });
  },
  
  odkleniAvatar(ime, cena) {
    const kristali = parseInt(localStorage.getItem("uporabnikoviKristali") || "0");
    if (kristali >= cena) {
      const avatar = this.avatarji.find(a => a.ime === ime);
      if (avatar) {
        avatar.odklenjen = true;
        localStorage.setItem("uporabnikoviKristali", kristali - cena);
        this.shraniOdklenjeneAvatarje();
        magičnoObvestilo(`🎉 Odklenil si ${avatar.ime}! Porabil si ${cena} kristalov.`);
        this.govori(`Čestitke! ${avatar.ime} je zdaj tvoj.`);
        this.posodobiStevecKristalov();
      }
    } else {
      magičnoObvestilo(`❌ Nimaš dovolj kristalov! Potrebuješ še ${cena - kristali} kristalov.`);
    }
  },
  
  aktivirajAvatar(ime) {
    const avatar = this.avatarji.find(a => a.ime === ime);
    if (!avatar || !avatar.odklenjen) return;
    
    this.aktivniAvatar = avatar;
    magičnoObvestilo(`${avatar.simbol} Avatar ${avatar.ime} je aktiviran!`);
    this.govori(`Jaz sem ${avatar.ime}. Tvoj duhovni spremljevalec.`);
    
    // Prikaži avatarja
    const okvir = document.getElementById('aktivni-avatar') || document.createElement('div');
    okvir.id = 'aktivni-avatar';
    okvir.className = 'aktivni-avatar';
    okvir.style.borderColor = avatar.barva;
    okvir.innerHTML = `
      <div class="avatar-slika">${avatar.simbol}</div>
      <div class="avatar-info">
        <div class="avatar-ime">${avatar.ime}</div>
        <div class="avatar-moc-trenutna">Moč: ${avatar.moč}</div>
      </div>
      <button class="zapri-avatar" id="zapri-avatar">✖</button>
    `;
    if (!document.getElementById('aktivni-avatar')) document.body.appendChild(okvir);
    
    document.getElementById('zapri-avatar')?.addEventListener('click', () => {
      okvir.remove();
      this.aktivniAvatar = null;
    });
  },
  
  posodobiStevecKristalov() {
    const stevec = document.getElementById('stevec-kristalov');
    if (stevec) {
      const kristali = localStorage.getItem("uporabnikoviKristali") || "0";
      stevec.textContent = `💎 ${kristali}`;
    }
  },
  
  dodajKristale(kolicina) {
    const trenutni = parseInt(localStorage.getItem("uporabnikoviKristali") || "0");
    localStorage.setItem("uporabnikoviKristali", trenutni + kolicina);
    this.posodobiStevecKristalov();
    magičnoObvestilo(`💎 Dobil si ${kolicina} kristalov!`);
  },
  
  pokaziPozdrav() {
    setTimeout(() => {
      this.govori("Pozdravljen, čarovnik. Tvoj peskovnik te čaka. Pokliči varuha z besedo 'varuh' ali reci 'avatarji' za svoje like.", "prijazen");
    }, 1500);
  },
  
  // Pomoč za glasovne ukaze
  odgovoriNaGlasovniUkaz(ukaz) {
    if (ukaz.includes("varuh") || ukaz.includes("pokliči varuha")) {
      this.prikaziVaruhe();
    }
    else if (ukaz.includes("avatar") || ukaz.includes("avatarji")) {
      this.prikaziAvatarje();
    }
    else if (ukaz.includes("kristali") || ukaz.includes("koliko kristalov")) {
      const kristali = localStorage.getItem("uporabnikoviKristali") || "0";
      this.govori(`Imaš ${kristali} kristalov.`);
      magičnoObvestilo(`💎 ${kristali} kristalov`);
    }
    else if (ukaz.includes("kdo si") || ukaz.includes("predstavi se")) {
      if (this.aktivniVaruh) {
        this.govori(`Jaz sem ${this.aktivniVaruh.ime}, ${this.aktivniVaruh.vloga}.`);
      } else {
        this.govori("Jaz sem tvoja umetna inteligenca. Pokliči varuha, da te bolje spoznam.");
      }
    }
    else if (ukaz.includes("nasvet") || ukaz.includes("pomagaj")) {
      if (this.aktivniVaruh) {
        const nasvet = this.dajNasvet(this.aktivniVaruh);
        this.govori(nasvet, this.aktivniVaruh.glas);
      } else {
        this.govori("Najprej pokliči varuha z ukazom 'varuh'.");
      }
    }
  }
};