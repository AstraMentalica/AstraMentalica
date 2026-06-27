<?php
// ============================================
// GRADNIK: Obdelava glasovnih ukazov
// ============================================
?>
<script>
(function() {
    let govorim = false;
    
    function govori(besedilo) {
        if (govorim) window.speechSynthesis.cancel();
        let govor = new SpeechSynthesisUtterance(besedilo);
        govor.lang = 'sl-SI';
        govor.rate = 0.9;
        govor.onstart = () => { govorim = true; };
        govor.onend = () => { govorim = false; };
        window.speechSynthesis.speak(govor);
    }
    
    function izvediUkaz(ukaz) {
        let u = ukaz.toLowerCase().trim();
        console.log('Ukaz:', u);
        
        // ========== UKAZI ZA STRAN ==========
        
        // Pomakni gor/dol
        if (u.includes('pomakni gor') || u.includes('scroll gor')) {
            window.scrollBy(0, -300);
            govori('Pomaknem gor');
            return;
        }
        if (u.includes('pomakni dol') || u.includes('scroll dol')) {
            window.scrollBy(0, 300);
            govori('Pomaknem dol');
            return;
        }
        if (u.includes('na vrh') || u.includes('začetek')) {
            window.scrollTo(0, 0);
            govori('Na vrh strani');
            return;
        }
        if (u.includes('na dno') || u.includes('konec')) {
            window.scrollTo(0, document.body.scrollHeight);
            govori('Na dno strani');
            return;
        }
        
        // ========== UKAZI ZA Klik ==========
        if (u.includes('klikni') || u.includes('pritisni')) {
            let kaj = u.replace(/klikni|pritisni/gi, '').trim();
            let elementi = document.querySelectorAll('a, button, [onclick], .btn, .gumb, .card, .kartica');
            for (let el of elementi) {
                if (el.innerText.toLowerCase().includes(kaj) || el.id.toLowerCase().includes(kaj)) {
                    el.click();
                    govori('Kliknem ' + kaj);
                    return;
                }
            }
            govori('Ni našel ' + kaj);
            return;
        }
        
        // ========== UKAZI ZA ODPIRANJE ==========
        let moduli = ['aeternum', 'stelaris', 'numyra', 'synera', 'orakleum', 'mystaia', 'codex', 'celestara', 'aetheris'];
        for (let m of moduli) {
            if (u.includes('odpri ' + m)) {
                let pot = 'MODULI/osnovni/' + m.charAt(0).toUpperCase() + m.slice(1) + '/index.php';
                fetch(pot).then(r => { if(r.ok) location.href=pot; else location.href=m+'.php'; });
                govori('Opiram ' + m);
                return;
            }
        }
        
        if (u.includes('domov') || u.includes('glavna')) {
            location.href = 'index.php';
            govori('Vračam se domov');
            return;
        }
        
        if (u.includes('nazaj') || u.includes('prejšnja')) {
            history.back();
            govori('Nazaj');
            return;
        }
        
        // ========== UKAZI ZA BRANJE ==========
        if (u.includes('preberi vse') || u.includes('preberi stran')) {
            let tekst = document.body.innerText;
            govori(tekst.substring(0, 2000));
            return;
        }
        
        if (u.includes('preberi naslov') || u.includes('kaj je naslov')) {
            govori(document.title);
            return;
        }
        
        // ========== POMOČ ==========
        if (u.includes('pomoč') || u.includes('kaj lahko') || u.includes('ukazi')) {
            let pom = '🎤 UKAZI:\n';
            pom += '• "pomakni gor/dol" - scroll\n';
            pom += '• "na vrh/dno" - skok\n';
            pom += '• "klikni [gumb]" - klikne\n';
            pom += '• "odpri [modul]" - odpre modul\n';
            pom += '• "preberi vse" - prebere stran\n';
            pom += '• "pomoč" - ta seznam\n';
            alert(pom);
            govori('Ukazi so prikazani v oknu');
            return;
        }
        
        // Če nič ne ustreza
        govori('Ukaza ne poznam. Reci pomoč za seznam.');
    }
    
    // Poslušaj glasovne ukaze
    window.addEventListener('glas-ukaz', (e) => {
        izvediUkaz(e.detail.ukaz);
    });
    
    // Izpostavi govori funkcijo za druge gradnike
    window.glasGovori = govori;
})();
</script>