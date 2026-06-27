2. runtime/izvoz_podatkov.js – CSV izvoz
javascript
// Izvoz podatkov v CSV in shranjevanje
window.IzvozPodatkov = {
  
  // Izvozi dnevno statistiko
  izvoziDnevno() {
    const zgodovina = window.Merilci?.naloziZgodovino() || [];
    const danes = zgodovina.filter(d => new Date(d.cas).toDateString() === new Date().toDateString());
    
    const csvVrstice = [
      ['Čas', 'Nervoza (%)', 'Hitrost klikov', 'Energija (%)', 'Globina scrolla', 'Čas na strani (s)']
    ];
    
    danes.forEach(d => {
      csvVrstice.push([
        new Date(d.cas).toLocaleString('sl-SI'),
        d.nervoza,
        d.hitrostKlikov,
        d.energija,
        d.globinaScrolla,
        d.casNaStrani
      ]);
    });
    
    this.prenesiCSV(csvVrstice, `statistika_dnevna_${this.datumString()}.csv`);
    magičnoObvestilo("📊 Dnevna statistika izvožena!");
  },
  
  // Izvozi tedensko statistiko
  izvoziTedensko() {
    const zgodovina = window.Merilci?.naloziZgodovino() || [];
    const zadnjih7 = zgodovina.slice(-7 * 24 * 60);
    
    const csvVrstice = [
      ['Čas', 'Nervoza (%)', 'Hitrost klikov', 'Energija (%)']
    ];
    
    zadnjih7.forEach(d => {
      csvVrstice.push([
        new Date(d.cas).toLocaleString('sl-SI'),
        d.nervoza,
        d.hitrostKlikov,
        d.energija
      ]);
    });
    
    this.prenesiCSV(csvVrstice, `statistika_tedenska_${this.datumString()}.csv`);
    magičnoObvestilo("📆 Tedenska statistika izvožena!");
  },
  
  // Izvozi knjige
  izvoziKnjige() {
    const knjige = window.Knjiznica?.knjige || [];
    
    const csvVrstice = [
      ['ID', 'Naslov', 'Avtor', 'Žanr', 'Priljubljenost', 'Datum', 'Skrivnost']
    ];
    
    knjige.forEach(k => {
      csvVrstice.push([
        k.id,
        k.naslov,
        k.avtor,
        k.zanr,
        k.priljubljenost,
        new Date(k.datum).toLocaleString('sl-SI'),
        k.skrivnost ? 'Da' : 'Ne'
      ]);
    });
    
    this.prenesiCSV(csvVrstice, `knjiznica_${this.datumString()}.csv`);
    magičnoObvestilo(`📚 Izvoženih ${knjige.length} knjig!`);
  },
  
  // Izvozi tržnico
  izvoziTrznico() {
    const izdelki = window.Zalozba?.vsiIzdelki() || [];
    
    const csvVrstice = [
      ['Ime izdelka', 'Prodajalec', 'Cena (kristali)', 'Datum', 'Ocena prodajalca']
    ];
    
    izdelki.forEach(i => {
      csvVrstice.push([
        i.ime,
        i.prodajalecIme,
        i.cena,
        new Date(i.datum).toLocaleString('sl-SI'),
        i.prodajalecOcena || 0
      ]);
    });
    
    this.prenesiCSV(csvVrstice, `trznica_${this.datumString()}.csv`);
    magičnoObvestilo(`🏪 Izvoženih ${izdelki.length} izdelkov!`);
  },
  
  // Izvozi vse podatke (več CSV-jev)
  izvoziVse() {
    this.izvoziDnevno();
    setTimeout(() => this.izvoziTedensko(), 100);
    setTimeout(() => this.izvoziKnjige(), 200);
    setTimeout(() => this.izvoziTrznico(), 300);
    setTimeout(() => this.izvoziMerilce(), 400);
    
    magičnoObvestilo("📦 Izvoz vseh podatkov se je začel. Prejeli boš več CSV datotek.");
  },
  
  // Izvozi surove merilce
  izvoziMerilce() {
    const kliki = window.Merilci?.senzorji?.kliki || [];
    const gibanje = window.Merilci?.senzorji?.gibanjeMiske || [];
    
    const csvKliki = [['Čas', 'X', 'Y', 'Element']];
    kliki.forEach(k => {
      csvKliki.push([
        new Date(k.cas).toLocaleString('sl-SI'),
        k.x,
        k.y,
        k.element
      ]);
    });
    
    this.prenesiCSV(csvKliki, `kliki_${this.datumString()}.csv`);
    
    const csvGibanje = [['Čas', 'X', 'Y', 'Hitrost']];
    gibanje.forEach(g => {
      csvGibanje.push([
        new Date(g.cas).toLocaleString('sl-SI'),
        g.x,
        g.y,
        g.hitrost?.toFixed(2)
      ]);
    });
    
    this.prenesiCSV(csvGibanje, `gibanje_miske_${this.datumString()}.csv`);
  },
  
  // Pomožna funkcija za prenos CSV
  prenesiCSV(vrstice, imeDatoteke) {
    const csvVsebina = vrstice.map(v => v.join(',')).join('\n');
    const blob = new Blob(["\uFEFF" + csvVsebina], { type: 'text/csv;charset=utf-8;' });
    const povezava = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    povezava.href = url;
    povezava.setAttribute('download', imeDatoteke);
    document.body.appendChild(povezava);
    povezava.click();
    document.body.removeChild(povezava);
    URL.revokeObjectURL(url);
  },
  
  datumString() {
    const d = new Date();
    return `${d.getFullYear()}-${d.getMonth()+1}-${d.getDate()}_${d.getHours()}-${d.getMinutes()}`;
  }
};
3. Dodatni CSS za statistike
css
/* STATISTIKE */
.statistika-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.85);
  backdrop-filter: blur(12px);
  z-index: 4000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.statistika-vsebina {
  background: var(--barva-ozadja);
  border-radius: 32px;
  width: 90%;
  max-width: 1000px;
  height: 85%;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  border: 2px solid var(--barva-poudarka);
}

.statistika-zavihki {
  display: flex;
  gap: 8px;
  padding: 12px 24px;
  background: var(--barva-peskovnika);
  flex-wrap: wrap;
}

.stat-zavihek {
  padding: 8px 16px;
  background: transparent;
  border: none;
  color: var(--barva-besedila);
  cursor: pointer;
  border-radius: 30px;
  transition: 0.2s;
}

.stat-zavihek.aktiven {
  background: var(--barva-poudarka);
  color: white;
}

.statistika-vsebina-container {
  flex: 1;
  overflow-y: auto;
  padding: 24px;
}

.statistika-kartice, .tedenski-summary, .numerologija-kartice, .astro-kartice, .izvoz-kartice {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-kartica, .summary-kartica, .numerologija-kartica, .astro-kartica, .izvoz-kartica {
  background: var(--barva-gradnika);
  border-radius: 20px;
  padding: 20px;
  text-align: center;
  border: 1px solid var(--barva-obrobe);
}

.stat-vrednost, .summary-vrednost, .numerologija-stevilka {
  font-size: 2.5rem;
  font-weight: bold;
  color: var(--barva-poudarka);
}

.graf-container {
  background: var(--barva-gradnika);
  border-radius: 20px;
  padding: 20px;
  margin: 20px 0;
  text-align: center;
}

canvas {
  max-width: 100%;
  height: auto;
  border-radius: 12px;
}

.izvoz-gumb {
  padding: 10px 20px;
  background: var(--barva-poudarka);
  border: none;
  border-radius: 30px;
  color: white;
  cursor: pointer;
  margin-top: 10px;
}

.izvoz-gumb.glavni {
  background: linear-gradient(135deg, #d4af37, #9b59b6);
}
4. Posodobi runtime/peskovnik.js – dodaj gumbe
javascript
// Gumb za nadzorno ploščo
const gumbStatistika = document.createElement('button');
gumbStatistika.id = 'gumb-statistika';
gumbStatistika.className = 'gumb-statistika';
gumbStatistika.innerHTML = '📊 Statistika';
gumbStatistika.onclick = () => {
  if (window.Vizualizacija) Vizualizacija.prikaziNadzornoPlosco();
};
document.querySelector('.peskovnik-orodja')?.appendChild(gumbStatistika);

// Povezava izvoznih gumbov (ko se odpre pogled)
document.addEventListener('click', (e) => {
  if (e.target.id === 'izvoz-dnevno') IzvozPodatkov?.izvoziDnevno();
  if (e.target.id === 'izvoz-tedensko') IzvozPodatkov?.izvoziTedensko();
  if (e.target.id === 'izvoz-knjige') IzvozPodatkov?.izvoziKnjige();
  if (e.target.id === 'izvoz-trznica') IzvozPodatkov?.izvoziTrznico();
  if (e.target.id === 'izvoz-vse') IzvozPodatkov?.izvoziVse();
});
✅ Zdaj imaš POPOLEN analitični sistem:
Komponenta	Opis
Dnevni pregled	Grafi nervoze, hitrosti, energije
Tedenski pregled	Povprečja in trendi čez teden
Mesečni pregled	Dolgoročna statistika
Numerološka analiza	Povezava števil z vedenjem
Astrološka analiza	Povezava horoskopa z merilci
Izvoz CSV	Dnevno, tedensko, knjige, tržnica, merilci
Izvoz vsega	Več CSV datotek hkrati
Vizualizacija	Grafi z Canvas API
🌟 Celoten sistem zdaj vključuje 13 popolnih modulov:
Peskovnik z gradniki

Glasovno upravljanje

Umetna inteligenca (varuhi, avatarji)

Magični in skrivni portali

Tržnica (prodaja/nakup)

Knjižnica (branje/pisanje z glasom)

Skriti zavihki

Biometrični merilci (kliki, miška, nervoza)

Numerologija

Horoskop

Vremenske povezave

Neznano (AI vzorci)

Statistike in izvoz (grafi, CSV)

Vse po slovensko, vse povezano, vse čarobno! 📊🔮✨

Želiš, da dodam še sistem za varnostno kopiranje v oblak, e-poštna poročila ali API za zunanje povezave?

lahko
Odlično! Varnostno kopiranje v oblak, e-poštna poročila in API za zunanje povezave – to bo profesionalna platforma!

Nadaljujem tam, kjer smo končali.

☁️ Dodatne komponente za oblak, e-pošto in API
text
GLOBALNO/frontend/
  runtime/
    ### NOVO ###
    varnostno_kopiranje.js   # shranjevanje v oblak (Google Drive, Dropbox)
    email_porocila.js        # tedenska/mesečna poročila na e-pošto
    api_povezave.js          # REST API za zunanje aplikacije
    sync_modul.js            # sinhronizacija med napravami