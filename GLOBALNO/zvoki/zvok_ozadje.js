/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/frontend/runtime/jedro/zvok_ozadje.js
 * v116 (15.06.2026 06:44)
 * ---------------------------------------------------------
 * OPIS: Mistična glasba v ozadju z možnostjo izklopa
 * ---------------------------------------------------------
 */
const ZvokOzadje = (function() {
    'use strict';
    
    let audio = null;
    let jeVklopljen = localStorage.getItem('zvok_ozadje') !== 'false';
    let trenutniVir = '/VSEBINA/zvoki/misticna_glasba.mp3';
    
    function init(vir = null) {
        if (vir) trenutniVir = vir;
        
        if (!audio) {
            audio = new Audio(trenutniVir);
            audio.loop = true;
            audio.volume = 0.3;
        }
        
        if (jeVklopljen) {
            const promise = audio.play();
            if (promise !== undefined) {
                promise.catch(e => {
                    console.log('Avtomatsko predvajanje ni dovoljeno, potreben je klik uporabnika.');
                });
            }
        }
        
        // Ustvari kontrolnik, če ne obstaja
        if (!document.getElementById('zvokKontrolnik')) {
            ustvariKontrolnik();
        }
    }
    
    function ustvariKontrolnik() {
        const kontrolnik = document.createElement('div');
        kontrolnik.id = 'zvokKontrolnik';
        kontrolnik.style.position = 'fixed';
        kontrolnik.style.bottom = '20px';
        kontrolnik.style.left = '20px';
        kontrolnik.style.zIndex = '1000';
        kontrolnik.style.background = 'rgba(0,0,0,0.5)';
        kontrolnik.style.backdropFilter = 'blur(10px)';
        kontrolnik.style.borderRadius = '50px';
        kontrolnik.style.padding = '8px 16px';
        kontrolnik.style.display = 'flex';
        kontrolnik.style.alignItems = 'center';
        kontrolnik.style.gap = '10px';
        kontrolnik.style.cursor = 'pointer';
        
        const ikona = document.createElement('span');
        ikona.id = 'zvokIkona';
        ikona.textContent = jeVklopljen ? '🔊' : '🔇';
        ikona.style.fontSize = '1.2rem';
        
        const besedilo = document.createElement('span');
        besedilo.textContent = 'Mistična glasba';
        besedilo.style.fontSize = '0.8rem';
        besedilo.style.color = '#e8c84a';
        
        kontrolnik.appendChild(ikona);
        kontrolnik.appendChild(besedilo);
        
        kontrolnik.addEventListener('click', () => {
            jeVklopljen = !jeVklopljen;
            localStorage.setItem('zvok_ozadje', jeVklopljen);
            ikona.textContent = jeVklopljen ? '🔊' : '🔇';
            
            if (jeVklopljen) {
                audio.play().catch(e => console.log('Napaka pri predvajanju:', e));
            } else {
                audio.pause();
            }
        });
        
        document.body.appendChild(kontrolnik);
    }
    
    function vklopi() {
        jeVklopljen = true;
        localStorage.setItem('zvok_ozadje', 'true');
        document.getElementById('zvokIkona').textContent = '🔊';
        audio.play().catch(e => console.log(e));
    }
    
    function izklopi() {
        jeVklopljen = false;
        localStorage.setItem('zvok_ozadje', 'false');
        document.getElementById('zvokIkona').textContent = '🔇';
        audio.pause();
    }
    
    function nastaviGlasnost(vrednost) {
        if (audio) audio.volume = Math.min(1, Math.max(0, vrednost));
    }
    
    return {
        init: init,
        vklopi: vklopi,
        izklopi: izklopi,
        nastaviGlasnost: nastaviGlasnost,
        jeVklopljen: () => jeVklopljen
    };
})();