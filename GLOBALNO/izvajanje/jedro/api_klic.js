const AstraAPI = {
    async klic(akcija, podatki = {}) {
        try {
            // Uporabi relativno pot - vedno deluje
            const url = window.location.pathname.replace(/\/[^\/]*$/, '') + '/?svet=SISTEM';
            
            const odg = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ akcija, podatki }),
            });
            
            if (!odg.ok) {
                const text = await odg.text();
                console.error('API napaka:', text);
                return { status: 'error', napaka: 'HTTP ' + odg.status };
            }
            
            return await odg.json();
        } catch (err) {
            console.error('API klic napaka:', err);
            return { status: 'error', napaka: err.message };
        }
    },

    async prijava(email, geslo) { 
        const rez = await this.klic('prijava', { email, geslo }); 
        console.log('Prijava odgovor:', rez);
        return rez;
    },
    
    async odjava() { return this.klic('odjava'); },
    async pinguj() { return this.klic('ping'); },
    async prijaviUporabnika() { return this.klic('pridobi_uporabnika'); },
};