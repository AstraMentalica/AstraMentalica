<?php
// ============================================
// GRADNIK: Samodejno preverjanje in klici
// ============================================
?>
<script>
(function() {
    let aktivno = false;
    let interval = null;
    
    function samodejnoPreveri() {
        if (!aktivno) return;
        
        // Preveri, če je kaj novega
        fetch(location.href, { method: 'HEAD' }).then(() => {
            let sporocilo = 'Še vedno sem tukaj. Trenutni čas je ' + new Date().toLocaleTimeString();
            window.glasGovori(sporocilo);
        });
    }
    
    function zacniSamodejno(minute = 5) {
        if (interval) clearInterval(interval);
        aktivno = true;
        interval = setInterval(samodejnoPreveri, minute * 60 * 1000);
        window.glasGovori('Samodejno preverjanje vklopljeno. Klic vsakih ' + minute + ' minut.');
    }
    
    function ustaviSamodejno() {
        if (interval) clearInterval(interval);
        aktivno = false;
        interval = null;
        window.glasGovori('Samodejno preverjanje ustavljeno.');
    }
    
    // Ukazi
    window.addEventListener('glas-ukaz', (e) => {
        let u = e.detail.ukaz.toLowerCase();
        
        if (u.includes('začni klicati') || u.includes('vklopi samodejno')) {
            let min = 5;
            let match = u.match(/(\d+)\s*minut/);
            if (match) min = parseInt(match[1]);
            zacniSamodejno(min);
            return;
        }
        
        if (u.includes('ustavi klicanje') || u.includes('izklopi samodejno')) {
            ustaviSamodejno();
            return;
        }
        
        if (u.includes('kaj je novega') || u.includes('preveri')) {
            samodejnoPreveri();
            return;
        }
    });
})();
</script>