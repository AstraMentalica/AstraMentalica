/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/frontend/runtime/nalagalnik/tema.js
 * v111 (27.5.2026 07:45)
 * ---------------------------------------------------------
 * OPIS: Nalagalnik tem – dinamično nalaganje CSS tem
 * ---------------------------------------------------------
 */
const NalagalnikTem = (function() {
    'use strict';
    
    let trenutnaTema = 'standard';
    const teme = ['standard', 'minimal', 'mystic', 'astra'];
    
    function nalozi() {
        const shranjena = localStorage.getItem('aktivna_tema');
        if (shranjena && teme.includes(shranjena)) {
            nastavi(shranjena);
        } else {
            nastavi('standard');
        }
    }
    
    function nastavi(imeTeme) {
        if (!teme.includes(imeTeme)) {
            console.warn(`Tema '${imeTeme}' ne obstaja`);
            return;
        }
        
        let link = document.querySelector('link[data-tema]');
        if (!link) {
            link = document.createElement('link');
            link.rel = 'stylesheet';
            link.setAttribute('data-tema', imeTeme);
            document.head.appendChild(link);
        }
        
        link.href = `/GLOBALNO/vmesnik/teme/${imeTeme}/slog.css`;
        trenutnaTema = imeTeme;
        localStorage.setItem('aktivna_tema', imeTeme);
        document.body.setAttribute('data-tema', imeTeme);
        
        window.dispatchEvent(new CustomEvent('tema:spremenjena', { detail: { tema: imeTeme } }));
    }
    
    function trenutna() {
        return trenutnaTema;
    }
    
    function ustvariMenjalnik(element) {
        if (!element) return;
        
        element.innerHTML = '';
        
        teme.forEach(tema => {
            const gumb = document.createElement('button');
            gumb.textContent = tema.charAt(0).toUpperCase() + tema.slice(1);
            gumb.classList.add('gumb', 'gumb-tema');
            if (tema === trenutnaTema) {
                gumb.classList.add('aktivno');
            }
            gumb.addEventListener('click', () => {
                nastavi(tema);
                document.querySelectorAll('.gumb-tema').forEach(btn => btn.classList.remove('aktivno'));
                gumb.classList.add('aktivno');
            });
            element.appendChild(gumb);
        });
    }
    
    document.addEventListener('DOMContentLoaded', nalozi);
    
    return {
        nalozi: nalozi,
        nastavi: nastavi,
        trenutna: trenutna,
        ustvariMenjalnik: ustvariMenjalnik
    };
})();
