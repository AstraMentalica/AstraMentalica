// ── TAROT – vmesnik/js/tarot.js ──────────────────────────────
// KLJUČNO: mešanje se zgodi TUKAJ, na klientu, ko uporabnik
// klikne "Premešaj". Strežnik nikoli ne generira naključja
// za izbiro kart — samo validira in interpretira, kar je
// uporabnik že izbral.

(function () {
    'use strict';

    let vseKarte = [];
    let premesaneKarte = [];
    let izbraneKarte = []; // { id, obrnjena }

    const stSirjenja = {
        ena_karta: 1,
        tri_karte: 3,
        keltski_kriz: 10,
    };

    function nakljucnoMesaj(seznam) {
        // Fisher-Yates – izvaja se v brskalniku uporabnika
        const kopija = [...seznam];
        for (let i = kopija.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [kopija[i], kopija[j]] = [kopija[j], kopija[i]];
        }
        return kopija;
    }

    async function naloziKarte() {
        const odziv = await fetch('modul.php?akcija=karte');
        const data = await odziv.json();
        if (data.status === 'uspeh') {
            vseKarte = data.vsebina.karte;
        }
    }

    function premesajInPrikazi() {
        premesaneKarte = nakljucnoMesaj(vseKarte);
        izbraneKarte = [];

        const sirjenje = document.getElementById('tarotSirjenje').value;
        const potrebno = stSirjenja[sirjenje] || 3;

        const ovoj = document.getElementById('tarotKarteIzbor');
        ovoj.innerHTML = '';

        premesaneKarte.slice(0, 22).forEach((karta) => {
            const el = document.createElement('div');
            el.className = 'tarot-hrbet';
            el.dataset.id = karta.id;
            el.textContent = '🂠';
            el.title = `Izberi karto (potrebno: ${potrebno})`;
            el.addEventListener('click', () => izberiKarto(el, karta, potrebno));
            ovoj.appendChild(el);
        });

        document.querySelector('.navodilo').textContent =
            `Izberi ${potrebno} kart (klikni nanje).`;
    }

    function izberiKarto(el, karta, potrebno) {
        if (el.classList.contains('izbrana')) {
            return;
        }
        if (izbraneKarte.length >= potrebno) {
            return;
        }

        // Naključna obrnjenost (RX) se določi TUKAJ, na klientu, ob izboru
        const rx = Math.random() < 0.3;

        izbraneKarte.push({ id: karta.id, rx });
        el.classList.add('izbrana');
        el.textContent = rx ? '🔄' : '✓';

        const posljiGumb = document.getElementById('tarotPosljiGumb');
        posljiGumb.disabled = izbraneKarte.length !== potrebno;
    }

    async function posljiVedezevanje() {
        const vprasanje = document.getElementById('tarotVprasanje').value.trim();
        const sirjenje  = document.getElementById('tarotSirjenje').value;

        if (!vprasanje) {
            alert('Vnesi vprašanje.');
            return;
        }

        const odziv = await fetch('modul.php?akcija=vedezi', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ vprasanje, sirjenje, karte: izbraneKarte }),
        });

        const data = await odziv.json();
        const rezultatDiv = document.getElementById('tarotRezultat');
        rezultatDiv.style.display = 'block';

        if (data.status === 'uspeh') {
            const v = data.vsebina;
            rezultatDiv.innerHTML = `
                <h3>${v.interpretacija.naslov}</h3>
                <p>${v.interpretacija.besedilo}</p>
                <ul>
                    ${v.interpretacija.pozicije.map(p =>
                        `<li><strong>${p.oznaka ? p.oznaka + ': ' : ''}</strong>${p.karta}${p.rx ? ' (RX)' : ''} — ${p.pomen}</li>`
                    ).join('')}
                </ul>
            `;
        } else {
            rezultatDiv.innerHTML = `<p class="napaka">${data.sporocilo}</p>`;
        }
    }

    document.addEventListener('DOMContentLoaded', async () => {
        await naloziKarte();
        document.getElementById('tarotMesajGumb').addEventListener('click', premesajInPrikazi);
        document.getElementById('tarotPosljiGumb').addEventListener('click', posljiVedezevanje);
        document.getElementById('tarotSirjenje').addEventListener('change', premesajInPrikazi);
    });
})();
