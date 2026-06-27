/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/frontend/runtime/init/aplikacija.js
 * v111 (27.5.2026 07:45)
 * ---------------------------------------------------------
 * OPIS: Inicializacija aplikacije – zagon vseh komponent
 * ---------------------------------------------------------
 */
const Aplikacija = (function() {
    'use strict';
    
    let zagnana = false;
    let komponente = {};
    
    async function zacni() {
        if (zagnana) {
            console.warn('Aplikacija je že zagnana');
            return;
        }
        
        console.log('🚀 ASTRAMENTALICA – zaganjanje aplikacije...');
        
        try {
            if (typeof Sistem !== 'undefined' && Sistem.init) {
                await Sistem.init({
                    debug: window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
                });
                komponente.sistem = Sistem;
            }
            
            if (typeof API !== 'undefined') {
                komponente.api = API;
            }
            
            if (typeof NalagalnikTem !== 'undefined') {
                NalagalnikTem.nalozi();
                komponente.teme = NalagalnikTem;
            }
            
            if (typeof NalagalnikModulov !== 'undefined') {
                komponente.moduli = NalagalnikModulov;
            }
            
            if (typeof Modal !== 'undefined') {
                komponente.modal = Modal;
            }
            
            zagnana = true;
            window.dispatchEvent(new CustomEvent('aplikacija:zagnana', { detail: { komponente: Object.keys(komponente) } }));
            console.log('✅ Aplikacija zagnana');
        } catch (e) {
            console.error('❌ Napaka pri zagonu aplikacije:', e);
        }
    }
    
    function ustavi() {
        if (!zagnana) return;
        
        window.dispatchEvent(new CustomEvent('aplikacija:ustavljena'));
        zagnana = false;
        komponente = {};
        console.log('Aplikacija ustavljena');
    }
    
    function jeZagnana() {
        return zagnana;
    }
    
    function pridobiKomponento(ime) {
        return komponente[ime] || null;
    }
    
    return {
        zacni: zacni,
        ustavi: ustavi,
        jeZagnana: jeZagnana,
        pridobiKomponento: pridobiKomponento
    };
})();

document.addEventListener('DOMContentLoaded', () => Aplikacija.zacni());
