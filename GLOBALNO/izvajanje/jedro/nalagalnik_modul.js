/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/frontend/runtime/nalagalnik/modul.js
 * v111 (27.5.2026 07:45)
 * ---------------------------------------------------------
 * OPIS: Nalagalnik modulov – dinamično nalaganje JS modulov
 * ---------------------------------------------------------
 */
const NalagalnikModulov = (function() {
    'use strict';
    
    const nalozeni = {};
    
    async function nalozi(imeModula, potModula = null) {
        if (nalozeni[imeModula]) {
            return nalozeni[imeModula];
        }
        
        const pot = potModula || `/GLOBALNO/frontend/moduli/${imeModula}/modul.js`;
        
        try {
            const modul = await import(pot);
            nalozeni[imeModula] = modul;
            
            if (modul.zagon) {
                await modul.zagon();
            }
            
            window.dispatchEvent(new CustomEvent('modul:nalozen', { detail: { ime: imeModula, modul: modul } }));
            return modul;
        } catch (e) {
            console.error(`Napaka pri nalaganju modula ${imeModula}:`, e);
            return null;
        }
    }
    
    function jeNalozen(imeModula) {
        return !!nalozeni[imeModula];
    }
    
    function pridobi(imeModula) {
        return nalozeni[imeModula] || null;
    }
    
    return {
        nalozi: nalozi,
        jeNalozen: jeNalozen,
        pridobi: pridobi
    };
})();
