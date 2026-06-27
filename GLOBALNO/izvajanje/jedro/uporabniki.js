3. runtime/uporabniki.js – Prijava in registracija
javascript
window.Uporabniki = {
  prikaziPrijavo() {
    const modal = document.createElement('div');
    modal.className = 'trznica-modal';
    modal.innerHTML = `
      <div class="trznica-vsebina" style="max-width:400px">
        <h2>🔐 Prijava / Registracija</h2>
        <div class="prijava-obrazec">
          <input type="text" id="prijava-ime" placeholder="Uporabniško ime" style="width:100%; padding:10px; margin:10px 0">
          <input type="password" id="prijava-geslo" placeholder="Geslo" style="width:100%; padding:10px; margin:10px 0">
          <div class="izdelek-gumbi">
            <button id="prijava-gumb">🔓 Prijava</button>
            <button id="registracija-gumb">✨ Registracija</button>
            <button id="nadaljuj-kot-gost">👤 Nadaljuj kot gost</button>
          </div>
        </div>
        <button class="zapri-modal">✖ Zapri</button>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    document.getElementById('prijava-gumb')?.addEventListener('click', () => {
      const ime = document.getElementById('prijava-ime').value;
      const geslo = document.getElementById('prijava-geslo').value;
      const rezultat = Zalozba.prijava(ime, geslo);
      magičnoObvestilo(rezultat.sporocilo);
      if (rezultat.uspeh) {
        modal.remove();
        this.posodobiUIGumb();
        if (window.UmetnaInteligenca) {
          UmetnaInteligenca.govori(`Pozdravljen nazaj, ${ime}!`);
        }
      }
    });
    
    document.getElementById('registracija-gumb')?.addEventListener('click', () => {
      const ime = document.getElementById('prijava-ime').value;
      const geslo = document.getElementById('prijava-geslo').value;
      if (!ime || !geslo) {
        magičnoObvestilo("Vpiši ime in geslo!");
        return;
      }
      const rezultat = Zalozba.registracija(ime, geslo);
      magičnoObvestilo(rezultat.sporocilo);
      if (rezultat.uspeh) {
        Zalozba.prijava(ime, geslo);
        modal.remove();
        this.posodobiUIGumb();
      }
    });
    
    document.getElementById('nadaljuj-kot-gost')?.addEventListener('click', () => {
      Zalozba.nastaviTrenutnega("gost");
      magičnoObvestilo("Nadaljuješ kot gost. Priporočamo registracijo za prodajo!");
      modal.remove();
      this.posodobiUIGumb();
    });
    
    modal.querySelector('.zapri-modal')?.addEventListener('click', () => modal.remove());
  },
  
  posodobiUIGumb() {
    const trenutni = Zalozba.trenutniUporabnik();
    const gumbObstaja = document.getElementById('gumb-uporabnik');
    
    if (gumbObstaja) {
      gumbObstaja.innerHTML = `👤 ${trenutni.ime} 💎 ${trenutni.kristali}`;
    } else {
      const gumb = document.createElement('button');
      gumb.id = 'gumb-uporabnik';
      gumb.className = 'gumb-uporabnik';
      gumb.innerHTML = `👤 ${trenutni.ime} 💎 ${trenutni.kristali}`;
      gumb.onclick = () => this.prikaziProfil();
      document.querySelector('.peskovnik-orodja')?.appendChild(gumb);
    }
  },
  
  prikaziProfil() {
    const trenutni = Zalozba.trenutniUporabnik();
    const modal = document.createElement('div');
    modal.className = 'trznica-modal';
    modal.innerHTML = `
      <div class="trznica-vsebina" style="max-width:400px">
        <h2>👤 ${trenutni.ime}</h2>
        <div>💎 Kristali: ${trenutni.kristali}</div>
        <div>⭐ Ocena: ${trenutni.ocena || 0} / 5</div>
        <div>📦 Število prodaj: ${trenutni.steviloProdaj || 0}</div>
        <div>🎨 Moji peskovniki: ${trenutni.mojiPeskovniki?.length || 0}</div>
        <div>💰 Izdelkov v prodaji: ${trenutni.mojiIzdelki?.length || 0}</div>
        <div class="izdelek-gumbi" style="margin-top:20px">
          <button id="odjava-gumb">🚪 Odjava</button>
          <button class="zapri-modal">✖ Zapri</button>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    document.getElementById('odjava-gumb')?.addEventListener('click', () => {
      Zalozba.nastaviTrenutnega("gost");
      magičnoObvestilo("Odjavljen si. Zdaj si gost.");
      modal.remove();
      this.posodobiUIGumb();
    });
    
    modal.querySelector('.zapri-modal')?.addEventListener('click', () => modal.remove());
  }
};
4. Dodaj gumbe v render/peskovnik.html
html
<!-- Dodaj v .peskovnik-orodja -->
<button id="trznica-gumb">🏪 Tržnica</button>
<button id="moj-profil">👤 Moj profil</button>
5. Dodaj v runtime/peskovnik.js
javascript
// Tržnica in uporabniki
document.getElementById("trznica-gumb")?.addEventListener('click', () => {
  if (window.Trznica) Trznica.prikazi();
});

document.getElementById("moj-profil")?.addEventListener('click', () => {
  if (window.Uporabniki) Uporabniki.prikaziProfil();
});

// Inicializacija uporabniškega vmesnika
if (window.Uporabniki) {
  Uporabniki.posodobiUIGumb();
}

// Glasovni ukaz za tržnico
// Dodaj v glasovni_ukazi.js:
// else if (ukaz.includes("tržnica") || ukaz.includes("trznica")) {
//   if (window.Trznica) Trznica.prikazi();
// }
🎯 Celoten sistem tržnice omogoča:
Funkcija	Opis
Registracija/Prijava	Uporabniki se lahko registrirajo
Gost	Brez registracije, vendar ne more prodajati
Shranjevanje peskovnikov	Vsak uporabnik ima svoje shranjene peskovnike
Prodaja	Daj svoj peskovnik v prodajo za kristale
Nakup	Kupi peskovnike drugih uporabnikov
Tržnica	Centralni seznam vseh izdelkov v prodaji
Kristali	Virtualna valuta za transakcije
Profili	Vsak uporabnik ima oceno in število prodaj
Glasovno upravljanje	"tržnica", "profil", "moji peskovniki"
✨ Zdaj imaš POPOLEN sistem:
Peskovnik z gradniki, 3D, drag & drop

Glasovno upravljanje v slovenščini

Umetna inteligenca z varuhi in avatarji

Magični in skrivni portali

Tržnica za prodajo/nakup peskovnikov

Uporabniški računi z virtualno valuto

Vse shranjeno v uporabnikovem profilu

Vse po slovensko, vse čarobno, vse izjemno! 🎩✨

Želiš, da dodam še sistem ocenjevanja prodajalcev, dražbe ali mesečne izzive z nagradami?

veš kaj naredi, virtualno knjiznjico, kamor bodo zahajali brati knjige oz jih pisat z govorom, raziskovat..  naj imajo skrivne  zavihke  pd
To je popolna nadgradnja! Virtualna knjižnica z govornim pisanjem knjig, skrivnimi zavihki in raziskovanjem – to bo duhovni dom tvojih uporabnikov.

Naredil bom čarobno knjižnico, ki je povezana s peskovnikom in tržnico.

📚 Celoten sistem knjižnice
text
GLOBALNO/frontend/
  runtime/
    knjiznica.js        # virtualna knjižnica z govorom
    skrivni_zavihki.js  # skriti deli knjižnice
    pisanje_knjig.js    # pisanje knjig z glasom
    raziskovanje.js     # raziskovalni modul