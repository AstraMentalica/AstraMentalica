/**
 * DATOTEKA: puerilis.js
 * NAMEN:    Otroška verzija — interaktivne zgodbe, glas, večerni način
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 */

const Puerilis = (function() {
    'use strict';
    
    // ========== GLASOVNO BRANJE (počasneje, višje) ==========
    let synth = window.speechSynthesis;
    let speaking = false;
    
    function govori(tekst, pocasi = true) {
        if (!synth) {
            console.warn('Bralnik ni podprt.');
            return;
        }
        
        if (speaking) {
            synth.cancel();
        }
        
        let utterance = new SpeechSynthesisUtterance(tekst);
        utterance.lang = 'sl-SI';
        utterance.rate = pocasi ? 0.75 : 0.9;  // Počasneje za otroke
        utterance.pitch = 1.3;  // Višji glas
        utterance.volume = 1;
        
        utterance.onstart = () => { speaking = true; };
        utterance.onend = () => { speaking = false; };
        utterance.onerror = () => { speaking = false; };
        
        synth.speak(utterance);
    }
    
    function nehajGovoriti() {
        if (synth && speaking) {
            synth.cancel();
            speaking = false;
        }
    }
    
    // ========== VEČERNI NAČIN ==========
    let nightMode = localStorage.getItem('puerilis_night') === 'true';
    
    function toggleNightMode() {
        nightMode = !nightMode;
        if (nightMode) {
            document.body.classList.add('night-mode');
            document.querySelector('.puerilis-bedtime-icon').textContent = '☀️';
        } else {
            document.body.classList.remove('night-mode');
            document.querySelector('.puerilis-bedtime-icon').textContent = '🌙';
        }
        localStorage.setItem('puerilis_night', nightMode);
        
        // Sporoči backendu (če želimo shraniti)
        fetch('/SISTEM/api.php?pot=api/modul/Aeternum', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                akcija: 'puerilis_vecemi_nacin',
                podatki: { vklopi: nightMode }
            })
        }).catch(e => console.log('Night mode saved locally'));
    }
    
    // ========== ZVOČNA SLIKICA ==========
    function playSoundCard(id, naslov, vsebina, barva) {
        // Animacija
        const card = document.querySelector(`[data-id="${id}"]`);
        if (card) {
            card.classList.add('puerilis-bounce');
            setTimeout(() => card.classList.remove('puerilis-bounce'), 1000);
        }
        
        // Preberi zgodbo
        govori(naslov + '. ' + vsebina);
        
        // Spremeni barvo teme začasno
        const originalBg = document.body.style.background;
        const colorMap = {
            'zlata': '#ffb703',
            'modra': '#48cae4',
            'zelena': '#95d5b2',
            'roza': '#ffb5a7'
        };
        document.body.style.transition = 'background 0.5s';
        document.body.style.background = colorMap[barva] || '#ffb703';
        setTimeout(() => document.body.style.background = '', 500);
    }
    
    // ========== ZGODBA DNEVA ==========
    async function showStoryOfTheDay(starost = 7) {
        try {
            const response = await fetch('/SISTEM/api.php?pot=api/modul/Aeternum', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    akcija: 'puerilis_zgodba_dneva',
                    podatki: { starost: starost }
                })
            });
            const data = await response.json();
            
            if (data.uspeh && data.zgodba) {
                const modal = document.createElement('div');
                modal.className = 'puerilis-story-modal';
                modal.style.cssText = `
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: ${nightMode ? '#2d2d44' : '#ffffffdd'};
                    backdrop-filter: blur(20px);
                    border-radius: 40px;
                    padding: 2rem;
                    max-width: 500px;
                    width: 90%;
                    z-index: 1000;
                    text-align: center;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                `;
                modal.innerHTML = `
                    <div style="font-size: 3rem;">${data.zgodba.slika_pogled ? '📖' : '⭐'}</div>
                    <h2>${data.uveceritev}</h2>
                    <h3>${data.zgodba.naslov}</h3>
                    <p>${data.zgodba.povzetek || data.zgodba.vsebina.substring(0, 200)}...</p>
                    <div style="margin-top: 1rem;">
                        <button class="puerilis-btn" onclick="this.parentElement.parentElement.remove(); Puerilis.govori('${data.zgodba.vsebina.replace(/'/g, "\\'")}')">📖 Preberi mi</button>
                        <button class="puerilis-btn puerilis-btn-gold" onclick="this.parentElement.parentElement.remove()">✨ Zapri</button>
                    </div>
                `;
                document.body.appendChild(modal);
            }
        } catch(e) {
            console.error('Napaka pri zgodbi dneva:', e);
        }
    }
    
    // ========== IGRA (preprosto iskanje besed) ==========
    function wordSearch() {
        const tekst = prompt('Katero besedo iščeš na tej strani?');
        if (tekst && tekst.trim()) {
            const body = document.body.innerText;
            if (body.toLowerCase().includes(tekst.toLowerCase())) {
                alert(`⭐ Bravo! Beseda "${tekst}" je na tej strani! ⭐`);
                govori(`Našel si besedo ${tekst}. Bravo!`);
            } else {
                alert(`🌙 Žal besede "${tekst}" ni na tej strani. Poskusi drugje. 🌙`);
                govori(`Besede ${tekst} ni tukaj.`);
            }
        }
    }
    
    // ========== INICIALIZACIJA ==========
    function init() {
        // Nastavi večerni način iz localStorage
        if (nightMode) {
            document.body.classList.add('night-mode');
        }
        
        // Dodaj večerni gumb
        const bedtimeBtn = document.createElement('div');
        bedtimeBtn.className = 'puerilis-bedtime-btn';
        bedtimeBtn.innerHTML = `<span class="puerilis-bedtime-icon">${nightMode ? '☀️' : '🌙'}</span> <span>Večerni način</span>`;
        bedtimeBtn.onclick = toggleNightMode;
        document.body.appendChild(bedtimeBtn);
        
        // Dodaj gumb za zgodbo dneva
        const storyBtn = document.createElement('div');
        storyBtn.className = 'puerilis-bedtime-btn';
        storyBtn.style.right = '100px';
        storyBtn.style.background = '#e8a87c';
        storyBtn.innerHTML = '⭐ Zgodba dneva';
        storyBtn.onclick = () => showStoryOfTheDay(7);
        document.body.appendChild(storyBtn);
        
        // Dodaj gumb za iskanje besed
        const searchBtn = document.createElement('div');
        searchBtn.className = 'puerilis-bedtime-btn';
        searchBtn.style.right = '180px';
        searchBtn.style.background = '#48cae4';
        searchBtn.innerHTML = '🔍 Igra besed';
        searchBtn.onclick = wordSearch;
        document.body.appendChild(searchBtn);
        
        console.log('Otroška verzija aktivirana — Puerilis 🌟');
    }
    
    // Javni API
    return {
        init: init,
        govori: govori,
        nehajGovoriti: nehajGovoriti,
        playSoundCard: playSoundCard,
        showStoryOfTheDay: showStoryOfTheDay,
        toggleNightMode: toggleNightMode,
        wordSearch: wordSearch
    };
})();

// Ob nalaganju
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => Puerilis.init());
} else {
    Puerilis.init();
}