// Codex modul - specifična JavaScript logika za samostojno delovanje

// Inicializacija Codex modula
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.codex-container')) {
        inicializirajCodex();
    }
});

function inicializirajCodex() {
    console.log("Inicializiram Codex modul v samostojnem načinu");
    
    // Nastavi navigacijo
    const navigacijskePovezave = document.querySelectorAll('.codex-nav-link');
    navigacijskePovezave.forEach(povezava => {
        povezava.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Odstrani aktivni razred od vseh povezav
            navigacijskePovezave.forEach(p => p.classList.remove('active'));
            
            // Dodaj aktivni razred trenutni povezavi
            this.classList.add('active');
            
            // Prikaži ustrezno poglavje
            const idPoglavja = this.getAttribute('data-chapter');
            const poglavja = document.querySelectorAll('.codex-chapter');
            poglavja.forEach(poglavje => {
                poglavje.style.display = 'none';
            });
            
            const aktivnoPoglavje = document.getElementById(idPoglavja);
            if (aktivnoPoglavje) {
                aktivnoPoglavje.style.display = 'block';
            }
        });
    });
    
    // Inicializacija iskanja
    const iskalniVnos = document.querySelector('.codex-search-input');
    if (iskalniVnos) {
        iskalniVnos.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                const poizvedba = this.value.trim();
                if (poizvedba.length > 2) {
                    poisciVCodexu(poizvedba);
                }
            }
        });
    }
    
    // Naloži začetno vsebino
    naloziZacetnoVsebino();
}

function naloziZacetnoVsebino() {
    // Simulacija nalaganja vsebine
    console.log("Nalagam začetno vsebino Codex modula v samostojnem načinu");
}

function poisciVCodexu(poizvedba) {
    console.log(`Iščem v Codexu: ${poizvedba}`);
    
    // Simulacija iskanja
    setTimeout(() => {
        const nakljucnoStevilo = Math.floor(Math.random() * 5) + 1;
        alert(`Najdenih ${nakljucnoStevilo} rezultatov za "${poizvedba}"\n\nTo je demo različica - iskanje je simulirano.`);
    }, 500);
}

// Javne funkcije za globalni dostop
if (typeof AstraMentalica === 'undefined') {
    var AstraMentalica = {};
}

AstraMentalica.moduli = AstraMentalica.moduli || {};
AstraMentalica.moduli.codex = {
    poisci: poisciVCodexu,
    nalozi: function() {
        console.log("Naložen Codex v samostojnem načinu");
        inicializirajCodex();
    }
};