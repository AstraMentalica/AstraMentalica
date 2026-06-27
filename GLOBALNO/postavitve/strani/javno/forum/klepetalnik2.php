<?php
if (!defined('SISTEM_VARNOST')) die();

// Pridobi trenutni AI gonilnik za prikaz
$trenutniGonilnik = $_ENV['AI_GONILNIK'] ?? 'simulacija';
$gonilniki = ['gemini', 'azure', 'huggingface', 'simulacija'];
$seja = new Seja();
$zgodovina = $seja->pridobi('klepet_zgodovina', []);
?>
<div class="klepetalnik" id="klepetalnik" style="display: flex; flex-direction: column; height: 600px; max-width: 800px; margin: 0 auto; border: 1px solid var(--siva); border-radius: var(--rob); overflow: hidden; background: var(--bela);">
    <!-- Glava -->
    <div style="background: var(--primarna); color: white; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0;">🤖 AI Klepetalnik</h3>
        <div>
            <select id="aiGonilnik" style="background: rgba(255,255,255,0.2); border: none; color: white; padding: 0.3rem 0.6rem; border-radius: 20px;">
                <?php foreach ($gonilniki as $g): ?>
                <option value="<?= $g ?>" <?= $trenutniGonilnik === $g ? 'selected' : '' ?>><?= ucfirst($g) ?></option>
                <?php endforeach; ?>
            </select>
            <button id="pobrisiZgodovino" style="background: none; border: none; color: white; cursor: pointer;" title="Počisti zgodovino">🗑</button>
        </div>
    </div>
    
    <!-- Zgodovina pogovora -->
    <div id="klepetZgodovina" style="flex: 1; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 0.5rem;">
        <?php foreach ($zgodovina as $z): ?>
            <div class="sporocilo <?= $z['vloga'] === 'uporabnik' ? 'uporabnik' : 'asistent' ?>" style="max-width: 80%; padding: 0.5rem 1rem; border-radius: 18px; <?= $z['vloga'] === 'uporabnik' ? 'align-self: flex-end; background: var(--primarna); color: white;' : 'align-self: flex-start; background: var(--siva-svetla); color: var(--besedilo);' ?>">
                <div><?= nl2br(htmlspecialchars($z['sporocilo'])) ?></div>
                <div style="font-size: 0.6rem; opacity: 0.7; margin-top: 0.2rem;"><?= date('H:i', strtotime($z['cas'])) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Vnosna vrstica -->
    <div style="padding: 1rem; border-top: 1px solid var(--siva-svetla); display: flex; gap: 0.5rem; background: var(--bela);">
        <textarea id="klepetVnos" rows="1" placeholder="Napiši ali povej sporočilo..." style="flex: 1; padding: 0.5rem; border: 1px solid var(--siva); border-radius: 20px; resize: none; font-family: inherit;"></textarea>
        <button id="glasovniGumb" style="background: var(--sekundarna); border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer;" title="Glasovni vnos">🎤</button>
        <button id="posljiGumb" style="background: var(--primarna); border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer;" title="Pošlji">📤</button>
    </div>
    
    <!-- Status (za glasovno prepoznavo) -->
    <div id="glasStatus" style="font-size: 0.7rem; padding: 0.3rem 1rem; background: var(--siva-svetla); color: var(--siva-temna);"></div>
</div>

<script>
// ============================================================
// KLEPETALNIK – glasovni vnos, AI klic, govorjenje odgovorov
// ============================================================
(function() {
    const zgodovinaDiv = document.getElementById('klepetZgodovina');
    const vnos = document.getElementById('klepetVnos');
    const posljiGumb = document.getElementById('posljiGumb');
    const glasGumb = document.getElementById('glasovniGumb');
    const glasStatus = document.getElementById('glasStatus');
    const gonilnikSelect = document.getElementById('aiGonilnik');
    const pobrisiGumb = document.getElementById('pobrisiZgodovino');
    
    let prepoznavanje = null;
    let trenutnoPrepoznavanjeAktivno = false;
    
    // Inicializacija glasovnega prepoznavanja
    function initGlas() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            glasStatus.innerText = '⚠️ Vaš brskalnik ne podpira glasovnega vnosa.';
            glasGumb.disabled = true;
            return;
        }
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        prepoznavanje = new SpeechRecognition();
        prepoznavanje.lang = 'sl-SI';
        prepoznavanje.interimResults = false;
        prepoznavanje.continuous = false;
        
        prepoznavanje.onstart = function() {
            trenutnoPrepoznavanjeAktivno = true;
            glasStatus.innerText = '🎤 Poslušam ... Govorite.';
            glasGumb.style.opacity = '0.5';
        };
        prepoznavanje.onend = function() {
            trenutnoPrepoznavanjeAktivno = false;
            glasStatus.innerText = '';
            glasGumb.style.opacity = '1';
        };
        prepoznavanje.onresult = function(event) {
            let prepis = event.results[0][0].transcript;
            vnos.value = prepis;
            glasStatus.innerText = `✅ Prepoznano: "${prepis}"`;
            setTimeout(() => { if (glasStatus.innerText.startsWith('✅')) glasStatus.innerText = ''; }, 3000);
            // Samodejno pošlji po glasovnem vnosu
            posljiSporocilo();
        };
        prepoznavanje.onerror = function(event) {
            glasStatus.innerText = '❌ Napaka pri prepoznavi: ' + event.error;
            trenutnoPrepoznavanjeAktivno = false;
            glasGumb.style.opacity = '1';
        };
    }
    
    // Glasovni vnos
    function startListening() {
        if (!prepoznavanje) {
            glasStatus.innerText = 'Glasovno prepoznavanje ni na voljo.';
            return;
        }
        if (trenutnoPrepoznavanjeAktivno) {
            prepoznavanje.stop();
        }
        try {
            prepoznavanje.start();
        } catch(e) {
            console.log(e);
        }
    }
    
    // Govorjenje besedila
    function govori(besedilo) {
        if (!window.speechSynthesis) return;
        let u = new SpeechSynthesisUtterance(besedilo);
        u.lang = 'sl-SI';
        u.rate = 0.9;
        window.speechSynthesis.cancel(); // prekini prejšnje
        window.speechSynthesis.speak(u);
    }
    
    // Prikaz sporočila v zgodovini
    function dodajSporocilo(vloga, besedilo, cas = null) {
        const div = document.createElement('div');
        div.className = 'sporocilo ' + vloga;
        const casStr = cas ? new Date(cas).toLocaleTimeString() : new Date().toLocaleTimeString();
        div.innerHTML = `<div>${besedilo.replace(/\n/g, '<br>')}</div><div style="font-size: 0.6rem; opacity: 0.7; margin-top: 0.2rem;">${casStr}</div>`;
        div.style.maxWidth = '80%';
        div.style.padding = '0.5rem 1rem';
        div.style.borderRadius = '18px';
        if (vloga === 'uporabnik') {
            div.style.alignSelf = 'flex-end';
            div.style.background = 'var(--primarna)';
            div.style.color = 'white';
        } else {
            div.style.alignSelf = 'flex-start';
            div.style.background = 'var(--siva-svetla)';
            div.style.color = 'var(--besedilo)';
        }
        zgodovinaDiv.appendChild(div);
        // Pomakni se na dno
        zgodovinaDiv.scrollTop = zgodovinaDiv.scrollHeight;
    }
    
    // Pošlji sporočilo AI-ju
    async function posljiSporocilo() {
        let tekst = vnos.value.trim();
        if (!tekst) return;
        
        // Onemogoči gumbe med pošiljanjem
        posljiGumb.disabled = true;
        glasGumb.disabled = true;
        
        // Prikaži uporabnikovo sporočilo
        dodajSporocilo('uporabnik', tekst);
        vnos.value = '';
        
        // Pridobi trenutno zgodovino iz DOM (ali iz seje – najbolje iz seje, ampak za enkrat kar iz DOM)
        let zgodovina = [];
        document.querySelectorAll('#klepetZgodovina .sporocilo').forEach(el => {
            let vloga = el.classList.contains('uporabnik') ? 'uporabnik' : 'asistent';
            let besedilo = el.querySelector('div:first-child').innerText;
            zgodovina.push({ vloga: vloga, sporocilo: besedilo });
        });
        
        // Pridobi izbran AI gonilnik
        let gonilnik = gonilnikSelect.value;
        // Shrani v .env? Za zdaj samo pošlji v API, da uporabi tistega iz .env, ali pa pošlji kot parameter.
        // Ker API uporablja VeckratniAI, ki bere .env, ne moremo kar preklopiti iz JS brez shranjevanja.
        // Zato bomo preklop gonilnika shranili v sejo preko ločenega klica.
        // Poenostavitev: pokličemo API, ki uporablja trenutni gonilnik iz .env, zato ob spremembi selecta pošljemo zahtevo za spremembo.
        
        // Prikaži "piše" indikator
        const indikator = document.createElement('div');
        indikator.className = 'sporocilo asistent';
        indikator.style.alignSelf = 'flex-start';
        indikator.style.background = 'var(--siva-svetla)';
        indikator.style.padding = '0.5rem 1rem';
        indikator.style.borderRadius = '18px';
        indikator.innerText = '✍️ Pišem ...';
        zgodovinaDiv.appendChild(indikator);
        zgodovinaDiv.scrollTop = zgodovinaDiv.scrollHeight;
        
        try {
            const odziv = await fetch('/api/v1/klepet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    sporocilo: tekst,
                    zgodovina: zgodovina.slice(0, -1) // brez zadnjega (ki je pravkar dodan)
                })
            });
            const podatki = await odziv.json();
            indikator.remove();
            if (podatki.uspeh) {
                dodajSporocilo('asistent', podatki.odgovor);
                // Preberi odgovor na glas
                govori(podatki.odgovor);
            } else {
                dodajSporocilo('asistent', 'Napaka: ' + (podatki.napaka || 'Neznana napaka.'));
            }
        } catch (err) {
            indikator.remove();
            dodajSporocilo('asistent', 'Napaka pri povezavi s strežnikom.');
        } finally {
            posljiGumb.disabled = false;
            glasGumb.disabled = false;
        }
    }
    
    // Sprememba AI gonilnika (shrani v sejo preko API klica)
    async function spremeniGonilnik(gonilnik) {
        const odziv = await fetch('/api/v1/spremeni_ai_gonilnik.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ gonilnik: gonilnik })
        });
        if (!odziv.ok) {
            alert('Napaka pri spreminjanju gonilnika.');
        }
    }
    
    // Počisti zgodovino
    function pobrisiZgodovino() {
        if (confirm('Počisti celotno zgodovino klepeta?')) {
            while (zgodovinaDiv.firstChild) {
                zgodovinaDiv.removeChild(zgodovinaDiv.firstChild);
            }
            // Shrani prazno zgodovino v sejo preko API
            fetch('/api/v1/pobrisi_zgodovino.php', { method: 'POST' });
        }
    }
    
    // Event listenerji
    posljiGumb.addEventListener('click', posljiSporocilo);
    glasGumb.addEventListener('click', startListening);
    pobrisiGumb.addEventListener('click', pobrisiZgodovino);
    gonilnikSelect.addEventListener('change', (e) => spremeniGonilnik(e.target.value));
    vnos.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            posljiSporocilo();
        }
    });
    
    // Inicializacija glasovnega prepoznavanja
    initGlas();
})();
</script>

<style>
.klepetalnik .sporocilo {
    word-break: break-word;
    white-space: pre-wrap;
}
.klepetalnik textarea {
    overflow-y: hidden;
}
</style>