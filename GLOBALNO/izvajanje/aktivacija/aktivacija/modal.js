/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/frontend/ui/aktivacija/modal.js
 * v111 (27.5.2026 07:45)
 * ---------------------------------------------------------
 * OPIS: Modalno okno (dialog)
 * ---------------------------------------------------------
 */
const Modal = (function() {
    'use strict';
    
    let aktivniModal = null;
    
    function ustvari(options = {}) {
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.style.display = 'none';
        
        const vsebina = document.createElement('div');
        vsebina.className = 'modal-vsebina';
        
        const zapriGumb = document.createElement('span');
        zapriGumb.className = 'modal-zapri';
        zapriGumb.innerHTML = '&times;';
        zapriGumb.addEventListener('click', () => zapri(modal));
        
        const naslov = document.createElement('h2');
        naslov.className = 'modal-naslov';
        naslov.textContent = options.naslov || 'Okno';
        
        const telo = document.createElement('div');
        telo.className = 'modal-telo';
        if (options.vsebina) {
            if (typeof options.vsebina === 'string') {
                telo.innerHTML = options.vsebina;
            } else {
                telo.appendChild(options.vsebina);
            }
        }
        
        const noge = document.createElement('div');
        noge.className = 'modal-noge';
        
        if (options.gumbi) {
            options.gumbi.forEach(gumb => {
                const btn = document.createElement('button');
                btn.textContent = gumb.besedilo;
                btn.className = `gumb ${gumb.razred || ''}`;
                btn.addEventListener('click', () => {
                    if (gumb.callback) gumb.callback();
                    zapri(modal);
                });
                noge.appendChild(btn);
            });
        }
        
        const zapriBtn = document.createElement('button');
        zapriBtn.textContent = 'Zapri';
        zapriBtn.className = 'gumb gumb-zapri';
        zapriBtn.addEventListener('click', () => zapri(modal));
        noge.appendChild(zapriBtn);
        
        vsebina.appendChild(zapriGumb);
        vsebina.appendChild(naslov);
        vsebina.appendChild(telo);
        vsebina.appendChild(noge);
        modal.appendChild(vsebina);
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) zapri(modal);
        });
        
        document.body.appendChild(modal);
        
        return modal;
    }
    
    function odpri(modal) {
        if (aktivniModal) {
            zapri(aktivniModal);
        }
        modal.style.display = 'flex';
        aktivniModal = modal;
        document.body.classList.add('modal-odprt');
        
        window.dispatchEvent(new CustomEvent('modal:odprt', { detail: { modal } }));
    }
    
    function zapri(modal) {
        modal.style.display = 'none';
        if (aktivniModal === modal) {
            aktivniModal = null;
        }
        document.body.classList.remove('modal-odprt');
        
        window.dispatchEvent(new CustomEvent('modal:zaprt', { detail: { modal } }));
    }
    
    function sporocilo(besedilo, tip = 'info', trajanje = 3000) {
        const modal = ustvari({
            naslov: tip === 'error' ? 'Napaka' : (tip === 'success' ? 'Uspeh' : 'Obvestilo'),
            vsebina: `<p>${besedilo}</p>`,
            gumbi: [{ besedilo: 'V redu', razred: 'gumb-primaren' }]
        });
        
        odpri(modal);
        
        if (trajanje > 0 && tip !== 'error') {
            setTimeout(() => zapri(modal), trajanje);
        }
    }
    
    return {
        ustvari: ustvari,
        odpri: odpri,
        zapri: zapri,
        sporocilo: sporocilo
    };
})();
