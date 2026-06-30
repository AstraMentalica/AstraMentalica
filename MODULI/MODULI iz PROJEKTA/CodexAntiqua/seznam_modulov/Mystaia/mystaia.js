/**
 * Mystaia - JavaScript za interaktivno funkcionalnost
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializacija košarice
    inicializirajKosarico();
    
    // Dodajanje izdelkov v košarico
    const gumbiDodaj = document.querySelectorAll('.dodaj-kosarico');
    gumbiDodaj.forEach(gumb => {
        gumb.addEventListener('click', function() {
            const idIzdelka = this.getAttribute('data-id');
            dodajVKosarico(idIzdelka);
        });
    });
    
    // Upravljanje količine izdelkov v košarici
    const gumbiKolicina = document.querySelectorAll('.kolicina-gumb');
    gumbiKolicina.forEach(gumb => {
        gumb.addEventListener('click', function() {
            spremeniKolicino(this);
        });
    });
});

/**
 * Inicializira košarico iz localStorage
 */
function inicializirajKosarico() {
    let kosarica = JSON.parse(localStorage.getItem('mystaia_kosarica')) || [];
    posodobiStevecKosarice(kosarica.length);
    return kosarica;
}

/**
 * Doda izdelek v košarico
 * @param {string} idIzdelka - ID izdelka
 */
function dodajVKosarico(idIzdelka) {
    let kosarica = inicializirajKosarico();
    
    // Preveri, če izdelek že obstaja v košarici
    const obstojecIndex = kosarica.findIndex(item => item.id === idIzdelka);
    
    if (obstojecIndex !== -1) {
        // Povečaj količino obstoječega izdelka
        kosarica[obstojecIndex].kolicina += 1;
    } else {
        // Doda nov izdelek v košarico
        kosarica.push({
            id: idIzdelka,
            kolicina: 1
        });
    }
    
    // Shrani posodobljeno košarico
    localStorage.setItem('mystaia_kosarica', JSON.stringify(kosarica));
    posodobiStevecKosarice(kosarica.length);
    
    // Prikaži obvestilo
    prikaziObvestilo('Izdelek je bil dodan v košarico!', 'success');
}

/**
 * Posodobi števec izdelkov v košarici
 * @param {number} stevilo - Število izdelkov v košarici
 */
function posodobiStevecKosarice(stevilo) {
    const stevec = document.getElementById('stevec-kosarice');
    if (stevec) {
        stevec.textContent = stevilo;
        stevec.style.display = stevilo > 0 ? 'inline-block' : 'none';
    }
}

/**
 * Prikaže obvestilo uporabniku
 * @param {string} sporocilo - Besedilo obvestila
 * @param {string} tip - Tip obvestila (success, error, warning)
 */
function prikaziObvestilo(sporocilo, tip = 'success') {
    // Ustvari element za obvestilo
    const obvestilo = document.createElement('div');
    obvestilo.className = `obvestilo obvestilo-${tip}`;
    obvestilo.innerHTML = `
        <span>${sporocilo}</span>
        <button class="zapri-obvestilo">&times;</button>
    `;
    
    // Dodaj obvestilo na stran
    document.body.appendChild(obvestilo);
    
    // Animiraj prikaz
    setTimeout(() => {
        obvestilo.classList.add('prikazi');
    }, 100);
    
    // Zapri obvestilo ob kliku
    const zapriGumb = obvestilo.querySelector('.zapri-obvestilo');
    zapriGumb.addEventListener('click', function() {
        obvestilo.classList.remove('prikazi');
        setTimeout(() => {
            document.body.removeChild(obvestilo);
        }, 300);
    });
    
    // Samodejno zapri po 5 sekundah
    setTimeout(() => {
        if (document.body.contains(obvestilo)) {
            obvestilo.classList.remove('prikazi');
            setTimeout(() => {
                document.body.removeChild(obvestilo);
            }, 300);
        }
    }, 5000);
}

/**
 * Spremeni količino izdelka v košarici
 * @param {HTMLElement} gumb - Gumb, ki je sprožil dogodek
 */
function spremeniKolicino(gumb) {
    const container = gumb.closest('.kolicina-container');
    const input = container.querySelector('.kolicina-input');
    let vrednost = parseInt(input.value);
    const akcija = gumb.getAttribute('data-akcija');
    
    if (akcija === 'povecaj') {
        vrednost += 1;
    } else if (akcija === 'zmanjsaj' && vrednost > 1) {
        vrednost -= 1;
    }
    
    input.value = vrednost;
    
    // Posodobi košarico
    const idIzdelka = container.getAttribute('data-id');
    posodobiKolicinoVKosarici(idIzdelka, vrednost);
}

/**
 * Posodobi količino izdelka v košarici
 * @param {string} idIzdelka - ID izdelka
 * @param {number} novaKolicina - Nova količina
 */
function posodobiKolicinoVKosarici(idIzdelka, novaKolicina) {
    let kosarica = inicializirajKosarico();
    const index = kosarica.findIndex(item => item.id === idIzdelka);
    
    if (index !== -1) {
        if (novaKolicina > 0) {
            kosarica[index].kolicina = novaKolicina;
        } else {
            // Odstrani izdelek, če je količina 0
            kosarica.splice(index, 1);
        }
        
        localStorage.setItem('mystaia_kosarica', JSON.stringify(kosarica));
        posodobiStevecKosarice(kosarica.length);
        
        // Če smo na strani s košarico, osveži cene
        if (window.location.pathname.includes('kosarica.php')) {
            osveziCeneKosarice();
        }
    }
}

/**
 * Osveži prikaz cen v košarici
 */
function osveziCeneKosarice() {
    // Ta funkcija bi se povezala s strežnikom za pridobitev najnovejših cen
    // Za zdaj samo simulirajmo osvežitev
    console.log('Osvežujem cene košarice...');
}

// Stili za obvestila (dodani neposredno v JavaScript za enostavnost)
const obvestilaStili = document.createElement('style');
obvestilaStili.textContent = `
    .obvestilo {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        background-color: #4CAF50;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-width: 300px;
    }
    
    .obvestilo-success {
        background-color: #4CAF50;
    }
    
    .obvestilo-error {
        background-color: #F44336;
    }
    
    .obvestilo-warning {
        background-color: #FF9800;
    }
    
    .obvestilo.prikazi {
        transform: translateX(0);
    }
    
    .zapri-obvestilo {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        margin-left: 15px;
    }
`;

document.head.appendChild(obvestilaStili);