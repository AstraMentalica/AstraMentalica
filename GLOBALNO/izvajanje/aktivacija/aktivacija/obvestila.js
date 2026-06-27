/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/frontend/ui/aktivacija/obvestila.js
 * v111 (27.5.2026 07:45)
 * ---------------------------------------------------------
 * OPIS: Obvestila (toast/notifikacije)
 * ---------------------------------------------------------
 */
const Obvestila = (function() {
    'use strict';
    
    let kontejner = null;
    
    function ustvariKontejner() {
        if (kontejner) return kontejner;
        
        kontejner = document.createElement('div');
        kontejner.className = 'obvestila-kontejner';
        kontejner.style.position = 'fixed';
        kontejner.style.bottom = '20px';
        kontejner.style.right = '20px';
        kontejner.style.zIndex = '9999';
        document.body.appendChild(kontejner);
        
        return kontejner;
    }
    
    function prikazi(sporocilo, tip = 'info', trajanje = 3000) {
        const kontejner = ustvariKontejner();
        
        const obvestilo = document.createElement('div');
        obvestilo.className = `obvestilo obvestilo-${tip}`;
        obvestilo.innerHTML = `
            <div class="obvestilo-vsebina">
                <span class="obvestilo-ikona">${getIkona(tip)}</span>
                <span class="obvestilo-besedilo">${sporocilo}</span>
                <button class="obvestilo-zapri">&times;</button>
            </div>
        `;
        
        obvestilo.style.backgroundColor = getBarva(tip);
        obvestilo.style.color = '#fff';
        obvestilo.style.padding = '12px 16px';
        obvestilo.style.marginBottom = '10px';
        obvestilo.style.borderRadius = '8px';
        obvestilo.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
        obvestilo.style.animation = 'slideIn 0.3s ease';
        
        const zapriGumb = obvestilo.querySelector('.obvestilo-zapri');
        zapriGumb.style.background = 'none';
        zapriGumb.style.border = 'none';
        zapriGumb.style.color = '#fff';
        zapriGumb.style.fontSize = '1.2rem';
        zapriGumb.style.cursor = 'pointer';
        zapriGumb.style.marginLeft = '12px';
        
        zapriGumb.addEventListener('click', () => {
            obvestilo.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => obvestilo.remove(), 300);
        });
        
        kontejner.appendChild(obvestilo);
        
        if (trajanje > 0) {
            setTimeout(() => {
                if (obvestilo.parentNode) {
                    obvestilo.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => obvestilo.remove(), 300);
                }
            }, trajanje);
        }
        
        return obvestilo;
    }
    
    function getIkona(tip) {
        switch(tip) {
            case 'success': return '✓';
            case 'error': return '✗';
            case 'warning': return '⚠';
            default: return 'ℹ';
        }
    }
    
    function getBarva(tip) {
        switch(tip) {
            case 'success': return '#27ae60';
            case 'error': return '#e74c3c';
            case 'warning': return '#f39c12';
            default: return '#3498db';
        }
    }
    
    function uspeh(sporocilo, trajanje = 3000) {
        return prikazi(sporocilo, 'success', trajanje);
    }
    
    function napaka(sporocilo, trajanje = 5000) {
        return prikazi(sporocilo, 'error', trajanje);
    }
    
    function opozorilo(sporocilo, trajanje = 4000) {
        return prikazi(sporocilo, 'warning', trajanje);
    }
    
    function info(sporocilo, trajanje = 3000) {
        return prikazi(sporocilo, 'info', trajanje);
    }
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
    
    return {
        prikazi: prikazi,
        uspeh: uspeh,
        napaka: napaka,
        opozorilo: opozorilo,
        info: info
    };
})();
