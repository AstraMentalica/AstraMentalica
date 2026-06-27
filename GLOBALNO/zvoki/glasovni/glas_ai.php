<?php
// ============================================
// GRADNIK: AI asistent (OpenRouter)
// ============================================

$AI_KLJUC = '';
?>
<script>
const AI_KLJUC = '<?= $AI_KLJUC ?>';

(async function() {
    async function vprasajAI(vprasanje) {
        try {
            let odg = await fetch('https://openrouter.ai/api/v1/chat/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + AI_KLJUC
                },
                body: JSON.stringify({
                    model: 'google/gemini-2.0-flash-exp:free',
                    messages: [{ role: 'user', content: vprasanje }]
                })
            });
            let data = await odg.json();
            return data.choices?.[0]?.message?.content || 'Žal ne morem odgovoriti.';
        } catch(e) {
            return 'Napaka: ' + e.message;
        }
    }
    
    window.addEventListener('glas-ukaz', async (e) => {
        let u = e.detail.ukaz.toLowerCase();
        
        if (u.includes('vprašaj') || u.includes('kaj je') || u.includes('kako') || u.includes('povej mi')) {
            let vprasanje = e.detail.ukaz;
            window.glasGovori('Razmišljam...');
            let odgovor = await vprasajAI(vprasanje);
            window.glasGovori(odgovor.substring(0, 500));
            
            // Prikaži tudi v konzoli
            console.log('AI:', odgovor);
            return;
        }
        
        if (u.includes('pozdravi') || u.includes('živjo')) {
            window.glasGovori('Pozdravljen! Kako ti lahko pomagam?');
            return;
        }
    });
})();
</script>