/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/frontend/runtime/jedro/misticni_avatar.js
 * v116 (15.06.2026 06:44)
 * ---------------------------------------------------------
 * OPIS: Generiranje mističnih avatarjev glede na uporabnikovo ime in energijo
 * ---------------------------------------------------------
 */
const MisticniAvatar = (function() {
    'use strict';
    
    // Mistični elementi za avatar
    const ozadja = [
        '🌙', '✨', '⭐', '🌟', '🌌', '🌀', '🔮', '🕯️', '🌿', '🌸', '🍃', '💫'
    ];
    
    const bitja = [
        '🦉', '🦋', '🐉', '🦊', '🐺', '🐦', '🕊️', '🐚', '🧚', '🧝', '🧙'
    ];
    
    const aure = [
        '#c4a0ff', '#e8c84a', '#ff6b9d', '#4fc3f7', '#81c784', '#ffb74d', '#ba68c8'
    ];
    
    function generirajIzImena(ime, energija = null) {
        if (!ime || ime.length === 0) {
            return { ikona: '🌫️', barva: '#a0a0a0', ozadje: '🌌' };
        }
        
        // Izračunaj hash iz imena
        let hash = 0;
        for (let i = 0; i < ime.length; i++) {
            hash = ((hash << 5) - hash) + ime.charCodeAt(i);
            hash |= 0;
        }
        
        const absHash = Math.abs(hash);
        const indexOzadja = absHash % ozadja.length;
        const indexBitja = Math.floor(absHash / ozadja.length) % bitja.length;
        const indexBarve = Math.floor(absHash / (ozadja.length * bitja.length)) % aure.length;
        
        let ikona = bitja[indexBitja];
        let ozadjeIkon = ozadja[indexOzadja];
        let barva = aure[indexBarve];
        
        // Če je podana energija, vpliva na izgled
        if (energija) {
            const energijaHash = Math.abs(energija) % 100;
            if (energijaHash > 80) {
                ikona = '👑' + ikona;
                ozadjeIkon = '🌟';
            } else if (energijaHash > 50) {
                ozadjeIkon = '✨';
            } else if (energijaHash < 20) {
                ikona = '🌱' + ikona;
                ozadjeIkon = '🍃';
            }
        }
        
        return {
            ikona: ikona,
            ozadje: ozadjeIkon,
            barva: barva,
            ozadjeBarva: barva + '20'
        };
    }
    
    function ustvariCanvasAvatar(ime, element, energija = null) {
        const avatar = generirajIzImena(ime, energija);
        
        if (!element) return avatar;
        
        element.innerHTML = '';
        element.style.backgroundColor = avatar.ozadjeBarva;
        element.style.borderRadius = '50%';
        element.style.display = 'flex';
        element.style.alignItems = 'center';
        element.style.justifyContent = 'center';
        element.style.flexDirection = 'column';
        element.style.padding = '1rem';
        
        const ozadjeSpan = document.createElement('span');
        ozadjeSpan.textContent = avatar.ozadje;
        ozadjeSpan.style.fontSize = '2.5rem';
        ozadjeSpan.style.filter = 'drop-shadow(0 0 5px ' + avatar.barva + ')';
        
        const ikonaSpan = document.createElement('span');
        ikonaSpan.textContent = avatar.ikona;
        ikonaSpan.style.fontSize = '3rem';
        ikonaSpan.style.marginTop = '-0.5rem';
        ikonaSpan.style.filter = 'drop-shadow(0 0 8px ' + avatar.barva + ')';
        
        const imeSpan = document.createElement('span');
        imeSpan.textContent = ime.length > 12 ? ime.substring(0, 10) + '...' : ime;
        imeSpan.style.fontSize = '0.7rem';
        imeSpan.style.marginTop = '0.5rem';
        imeSpan.style.color = avatar.barva;
        imeSpan.style.fontFamily = 'Cinzel, serif';
        
        element.appendChild(ozadjeSpan);
        element.appendChild(ikonaSpan);
        element.appendChild(imeSpan);
        
        return avatar;
    }
    
    return {
        generiraj: generirajIzImena,
        ustvariCanvas: ustvariCanvasAvatar
    };
})();