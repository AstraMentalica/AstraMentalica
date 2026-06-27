<?php
// ============================================
// GRADNIK: Mikrofon (samo zajem glasu)
// ============================================
?>
<style>
.glas-mik {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: #1a1a2e;
    border: 2px solid #c8a84b;
    color: #c8a84b;
    font-size: 24px;
    cursor: pointer;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}
.glas-mik.poslusan {
    background: #c8a84b;
    color: #1a1a2e;
    animation: pulziraj 1s infinite;
}
@keyframes pulziraj {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
</style>

<button id="glasMikrofon" class="glas-mik">🎤</button>

<script>
(function() {
    let poslusanje = false;
    let prepoznava = null;
    let zadnjiUkaz = '';
    
    if ('webkitSpeechRecognition' in window) {
        prepoznava = new webkitSpeechRecognition();
        prepoznava.continuous = true;
        prepoznava.interimResults = true;
        prepoznava.lang = 'sl-SI';
        
        prepoznava.onresult = (e) => {
            let koncno = '';
            for (let i = e.resultIndex; i < e.results.length; i++) {
                if (e.results[i].isFinal) koncno += e.results[i][0].transcript;
            }
            if (koncno && koncno !== zadnjiUkaz) {
                zadnjiUkaz = koncno;
                // Pošlji dogodek vsem poslušalcem
                window.dispatchEvent(new CustomEvent('glas-ukaz', { detail: { ukaz: koncno } }));
            }
        };
        
        prepoznava.onend = () => {
            poslusanje = false;
            document.getElementById('glasMikrofon').classList.remove('poslusan');
        };
        
        document.getElementById('glasMikrofon').onclick = () => {
            if (poslusanje) prepoznava.stop();
            else prepoznava.start();
            poslusanje = !poslusanje;
            document.getElementById('glasMikrofon').classList.toggle('poslusan');
        };
    }
})();
</script>