<?php
/**
 * ============================================================
 * POT: ASTRA/prikaz/astra_glasovi.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: ASTRA (admin)
 * 📰 NAMEN: Admin stran – upravljanje kloniranih glasov (XTTS).
 *           Snemaj svoj glas direktno v brskalniku ali naloži
 *           datoteko. Test sinteze. Status lokalnega servisa.
 * ✅ DOVOLJENO: echo, HTML
 * 🚫 PREPOVEDI: Brez SQL, vsa logika gre prek /api
 * 📌 STATUS: Stabilno
 * 👤 AVTOR: AstraMentalica Mojster
 * 🌐 JEZIK: sl
 * 🏷️ OZNAKE: astra, admin, glasovno, xtts, kloniranje
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

$csrf = $vsebina['csrf'] ?? '';
?>

<div class="glasovi-ovoj">

    <!-- Status servisa -->
    <section class="kartica" style="margin-bottom: var(--razmik-l);">
        <div class="flex-med">
            <h2 class="kartica-naslov" style="margin:0;">🎙 Lokalni glasovni servis</h2>
            <div id="servisStatus" class="znacka znacka-modra">Preverjam...</div>
        </div>
        <p style="color:var(--besedilo-d); font-size:var(--velikost-s); margin-top:var(--razmik-s);">
            XTTS-v2 voice cloning + faster-whisper. Brezplačno, lokalno, brez pošiljanja podatkov tretjim osebam.
            <code>http://127.0.0.1:8088</code>
        </p>
    </section>

    <!-- Snemanje novega glasu -->
    <section class="kartica" style="margin-bottom: var(--razmik-l);">
        <h2 class="kartica-naslov">🎤 Posnemi nov glas</h2>
        <p style="color:var(--besedilo-d); font-size:var(--velikost-s); margin-bottom:var(--razmik-m);">
            Posnemi 10–30 sekund naravnega govora v slovenščini. Mirno okolje, brez glasbe v ozadju.
            Predlagaj besedilo spodaj — preberi ga umirjeno, kot da pripoveduješ zgodbo.
        </p>

        <div class="vzorec-besedilo" id="vzorecBesedilo">
            "Pozdravljen na poti samospoznavanja. Vsaka pot se začne v tišini, vsaka zvezda
            nosi svojo zgodbo. Astrologija nam pomaga razumeti vzorce, ki se ponavljajo v
            našem življenju, in nas usmerja k globljemu spoznanju samega sebe. V tem
            trenutku, ko se ustaviš in poslušaš, se nekaj v tebi prebuja."
        </div>

        <div class="snemanje-ovoj">
            <button class="gumb gumb-primarni" id="gumbSnemaj">
                <span id="snemajIkona">🎙</span>
                <span id="snemajNapis">Začni snemanje</span>
            </button>
            <div class="snemanje-cas" id="snemanjeCas" style="display:none;">00:00</div>
            <div class="gl-val" id="snemanjeVal" style="display:none;">
                <div class="gl-vp"></div><div class="gl-vp"></div><div class="gl-vp"></div>
                <div class="gl-vp"></div><div class="gl-vp"></div>
            </div>
        </div>

        <!-- Predogled posnetka -->
        <div id="posnetekPredogled" style="display:none; margin-top:var(--razmik-m);">
            <audio id="posnetekAudio" controls style="width:100%; margin-bottom:var(--razmik-m);"></audio>

            <div class="obrazec" style="max-width:400px;">
                <div class="obrazec-skupina">
                    <label class="obrazec-oznaka">ID glasu (brez presledkov)</label>
                    <input type="text" class="vnos" id="glasId" placeholder="privzeti" value="privzeti">
                </div>
                <div class="obrazec-skupina">
                    <label class="obrazec-oznaka">Naziv</label>
                    <input type="text" class="vnos" id="glasNaziv" placeholder="Moj glas">
                </div>
                <div class="flex flex-presledek">
                    <button class="gumb gumb-primarni" id="gumbShraniGlas">💾 Shrani glas</button>
                    <button class="gumb gumb-sekundarni" id="gumbZavrziPosnetek">🗑 Zavrži</button>
                </div>
            </div>
        </div>

        <!-- Ali naloži datoteko -->
        <div class="locilo-mini">
            <span>ali naloži obstoječo datoteko</span>
        </div>
        <input type="file" id="datotekaVnos" accept="audio/*" class="vnos">
    </section>

    <!-- Seznam glasov -->
    <section class="kartica" style="margin-bottom: var(--razmik-l);">
        <h2 class="kartica-naslov">📋 Razpoložljivi glasovi</h2>
        <div id="seznamGlasov">
            <p style="color:var(--besedilo-d); font-size:var(--velikost-s);">Nalagam...</p>
        </div>
    </section>

    <!-- Test sinteze -->
    <section class="kartica">
        <h2 class="kartica-naslov">🔊 Test sinteze</h2>
        <div class="obrazec">
            <div class="obrazec-skupina">
                <label class="obrazec-oznaka">Besedilo za test</label>
                <textarea class="vnos" id="testBesedilo" rows="3">Pozdravljen na poti samospoznavanja. To je test sinteze govora z mojim glasom.</textarea>
            </div>
            <div class="flex flex-presledek" style="align-items:flex-end;">
                <div class="obrazec-skupina" style="flex:1;">
                    <label class="obrazec-oznaka">Glas</label>
                    <select class="vnos" id="testGlas">
                        <option value="privzeti">privzeti</option>
                    </select>
                </div>
                <button class="gumb gumb-primarni" id="gumbTest" style="margin-bottom:0;">▶ Predvajaj</button>
            </div>
        </div>
        <audio id="testAudio" controls style="width:100%; margin-top:var(--razmik-m); display:none;"></audio>
        <div id="testStatus" class="sporocilo skrij" style="margin-top:var(--razmik-m);"></div>
    </section>

</div>

<style>
.vzorec-besedilo {
    background: var(--zlata-dim);
    border: 1px solid rgba(232,200,74,0.25);
    border-radius: var(--rob-m);
    padding: var(--razmik-l);
    font-style: italic;
    color: var(--besedilo);
    font-size: var(--velikost-l);
    line-height: 1.8;
    margin-bottom: var(--razmik-l);
}

.snemanje-ovoj {
    display: flex;
    align-items: center;
    gap: var(--razmik-l);
}

#gumbSnemaj.snema {
    background: var(--rdeca);
    color: #fff;
    animation: snemaj-utrip 1s ease-in-out infinite alternate;
}

@keyframes snemaj-utrip {
    from { opacity: 1; }
    to   { opacity: 0.6; }
}

.snemanje-cas {
    font-family: var(--pisava-mono);
    font-size: var(--velikost-l);
    color: var(--rdeca);
    font-weight: 600;
}

.gl-val { display: flex; align-items: center; gap: 3px; height: 28px; }
.gl-vp { width: 3px; border-radius: 2px; background: var(--rdeca); animation: glv .8s ease-in-out infinite alternate; }
.gl-vp:nth-child(1) { height: 8px;  animation-delay: 0s; }
.gl-vp:nth-child(2) { height: 16px; animation-delay: .1s; }
.gl-vp:nth-child(3) { height: 24px; animation-delay: .2s; }
.gl-vp:nth-child(4) { height: 18px; animation-delay: .15s; }
.gl-vp:nth-child(5) { height: 10px; animation-delay: .05s; }
@keyframes glv { from { transform: scaleY(.3); opacity: .4; } to { transform: scaleY(1); opacity: 1; } }

.locilo-mini {
    display: flex;
    align-items: center;
    gap: var(--razmik-m);
    margin: var(--razmik-l) 0 var(--razmik-m);
    color: var(--besedilo-m);
    font-size: var(--velikost-xs);
}
.locilo-mini::before, .locilo-mini::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--rob);
}

.glas-vrstica {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--razmik-m);
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--rob);
    border-radius: var(--rob-m);
    margin-bottom: var(--razmik-s);
}
.glas-vrstica-info { display: flex; flex-direction: column; }
.glas-vrstica-id { font-weight: 600; color: var(--besedilo-s); font-size: var(--velikost-m); }
.glas-vrstica-opis { font-size: var(--velikost-xs); color: var(--besedilo-d); }
</style>

<script>
(function() {
    var csrf = <?= json_encode($csrf) ?>;
    var API  = '/api?akcija=';

    // --------------------------------------------------------
    // STATUS SERVISA
    // --------------------------------------------------------
    function preveriStatus() {
        fetch(API + 'glasovno_status')
            .then(r => r.json())
            .then(j => {
                var el = document.getElementById('servisStatus');
                if (j.dosegljiv) {
                    el.className = 'znacka znacka-zelena';
                    el.textContent = '✓ Dosegljiv';
                } else {
                    el.className = 'znacka znacka-rdeca';
                    el.textContent = '✗ Ni dosegljiv';
                }
            })
            .catch(() => {
                var el = document.getElementById('servisStatus');
                el.className = 'znacka znacka-rdeca';
                el.textContent = '✗ Napaka povezave';
            });
    }
    preveriStatus();

    // --------------------------------------------------------
    // SEZNAM GLASOV
    // --------------------------------------------------------
    function naloziGlasove() {
        fetch(API + 'glasovno_glasovi')
            .then(r => r.json())
            .then(j => {
                var ovoj = document.getElementById('seznamGlasov');
                var sel  = document.getElementById('testGlas');
                ovoj.innerHTML = '';
                sel.innerHTML = '';

                var glasovi = j.glasovi || {};
                if (Object.keys(glasovi).length === 0) {
                    ovoj.innerHTML = '<p style="color:var(--besedilo-d); font-size:var(--velikost-s);">Ni shranjenih glasov. Posnemi prvega zgoraj.</p>';
                }

                Object.keys(glasovi).forEach(function(id) {
                    var g = glasovi[id];
                    var vrstica = document.createElement('div');
                    vrstica.className = 'glas-vrstica';
                    vrstica.innerHTML =
                        '<div class="glas-vrstica-info">' +
                            '<span class="glas-vrstica-id">' + id + '</span>' +
                            '<span class="glas-vrstica-opis">' + (g.naziv || '') + (g.opis ? ' — ' + g.opis : '') + '</span>' +
                        '</div>' +
                        '<button class="gumb gumb-sekundarni gumb-m" data-izbrisi="' + id + '">🗑 Izbriši</button>';
                    ovoj.appendChild(vrstica);

                    var opt = document.createElement('option');
                    opt.value = id;
                    opt.textContent = g.naziv || id;
                    sel.appendChild(opt);
                });

                ovoj.querySelectorAll('[data-izbrisi]').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var id = btn.getAttribute('data-izbrisi');
                        if (!confirm('Izbriši glas "' + id + '"?')) return;
                        fetch(API + 'glasovno_izbrisi_glas', {
                            method: 'POST',
                            headers: {'Content-Type':'application/json'},
                            body: JSON.stringify({csrf: csrf, id: id})
                        }).then(() => naloziGlasove());
                    });
                });
            })
            .catch(() => {
                document.getElementById('seznamGlasov').innerHTML =
                    '<p style="color:var(--rdeca); font-size:var(--velikost-s);">Napaka pri nalaganju glasov. Servis morda ni dosegljiv.</p>';
            });
    }
    naloziGlasove();

    // --------------------------------------------------------
    // SNEMANJE
    // --------------------------------------------------------
    var recorder, kosi = [], stream, casInterval, casSekunde = 0;
    var snemaButton = document.getElementById('gumbSnemaj');
    var snemajIkona = document.getElementById('snemajIkona');
    var snemajNapis = document.getElementById('snemajNapis');
    var snemanjeCas = document.getElementById('snemanjeCas');
    var snemanjeVal = document.getElementById('snemanjeVal');
    var posnetekPredogled = document.getElementById('posnetekPredogled');
    var posnetekAudio = document.getElementById('posnetekAudio');
    var trenutniBlob = null;

    snemaButton.addEventListener('click', function() {
        if (!recorder || recorder.state === 'inactive') {
            zacniSnemanje();
        } else {
            ustaviSnemanje();
        }
    });

    function zacniSnemanje() {
        navigator.mediaDevices.getUserMedia({ audio: { channelCount: 1, sampleRate: 22050 } })
            .then(function(s) {
                stream = s;
                kosi = [];
                var mime = ['audio/webm;codecs=opus','audio/webm','audio/ogg'].find(m => MediaRecorder.isTypeSupported(m)) || '';
                recorder = new MediaRecorder(s, mime ? {mimeType: mime} : {});

                recorder.ondataavailable = function(e) { if (e.data.size > 0) kosi.push(e.data); };
                recorder.onstop = function() {
                    s.getTracks().forEach(t => t.stop());
                    trenutniBlob = new Blob(kosi, { type: recorder.mimeType || 'audio/webm' });
                    posnetekAudio.src = URL.createObjectURL(trenutniBlob);
                    posnetekPredogled.style.display = 'block';
                };

                recorder.start();
                snemaButton.classList.add('snema');
                snemajIkona.textContent = '⏹';
                snemajNapis.textContent = 'Ustavi snemanje';
                snemanjeCas.style.display = 'block';
                snemanjeVal.style.display = 'flex';
                casSekunde = 0;
                posnetekPredogled.style.display = 'none';

                casInterval = setInterval(function() {
                    casSekunde++;
                    var min = String(Math.floor(casSekunde/60)).padStart(2,'0');
                    var sek = String(casSekunde%60).padStart(2,'0');
                    snemanjeCas.textContent = min + ':' + sek;
                    if (casSekunde >= 60) ustaviSnemanje();
                }, 1000);
            })
            .catch(function(e) {
                alert('Napaka dostopa do mikrofona: ' + e.message);
            });
    }

    function ustaviSnemanje() {
        if (recorder && recorder.state !== 'inactive') recorder.stop();
        clearInterval(casInterval);
        snemaButton.classList.remove('snema');
        snemajIkona.textContent = '🎙';
        snemajNapis.textContent = 'Začni snemanje';
        snemanjeVal.style.display = 'none';
    }

    document.getElementById('gumbZavrziPosnetek').addEventListener('click', function() {
        trenutniBlob = null;
        posnetekPredogled.style.display = 'none';
        snemanjeCas.style.display = 'none';
        snemanjeCas.textContent = '00:00';
    });

    document.getElementById('datotekaVnos').addEventListener('change', function(e) {
        var f = e.target.files[0];
        if (!f) return;
        trenutniBlob = f;
        posnetekAudio.src = URL.createObjectURL(f);
        posnetekPredogled.style.display = 'block';
    });

    // --------------------------------------------------------
    // SHRANI GLAS
    // --------------------------------------------------------
    document.getElementById('gumbShraniGlas').addEventListener('click', function() {
        if (!trenutniBlob) { alert('Ni posnetka.'); return; }

        var id    = document.getElementById('glasId').value.trim();
        var naziv = document.getElementById('glasNaziv').value.trim();

        if (!id) { alert('Vpiši ID glasu.'); return; }

        var formData = new FormData();
        formData.append('avdio', trenutniBlob, 'vzorec.webm');
        formData.append('id', id);
        formData.append('naziv', naziv || id);
        formData.append('csrf', csrf);

        var btn = this;
        btn.disabled = true;
        btn.textContent = '⏳ Nalagam...';

        fetch(API + 'glasovno_kloniraj', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(j => {
                btn.disabled = false;
                btn.textContent = '💾 Shrani glas';
                if (j.status === 'success') {
                    alert('Glas shranjen: ' + id);
                    posnetekPredogled.style.display = 'none';
                    naloziGlasove();
                } else {
                    alert('Napaka: ' + (j.sporocilo || 'neznana'));
                }
            })
            .catch(e => {
                btn.disabled = false;
                btn.textContent = '💾 Shrani glas';
                alert('Napaka povezave: ' + e.message);
            });
    });

    // --------------------------------------------------------
    // TEST SINTEZE
    // --------------------------------------------------------
    document.getElementById('gumbTest').addEventListener('click', function() {
        var besedilo = document.getElementById('testBesedilo').value.trim();
        var glas     = document.getElementById('testGlas').value;
        var status   = document.getElementById('testStatus');
        var audio    = document.getElementById('testAudio');

        if (!besedilo) return;

        var btn = this;
        btn.disabled = true;
        btn.textContent = '⏳ Sintetiziram...';
        status.className = 'sporocilo sporocilo-info';
        status.textContent = 'Pošiljam zahtevo...';

        fetch(API + 'tts_sintetiziraj', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ besedilo: besedilo, glas: glas, ponudnik: 'lokalni', predpomni: false })
        })
        .then(r => r.json())
        .then(j => {
            btn.disabled = false;
            btn.textContent = '▶ Predvajaj';

            if (j.status !== 'success') {
                status.className = 'sporocilo sporocilo-napaka';
                status.textContent = '✗ ' + (j.sporocilo || 'Napaka');
                return;
            }

            var binStr = atob(j.avdio);
            var bytes  = new Uint8Array(binStr.length);
            for (var i=0;i<binStr.length;i++) bytes[i] = binStr.charCodeAt(i);
            var blob = new Blob([bytes], { type: 'audio/' + (j.format === 'wav' ? 'wav' : 'mpeg') });

            audio.src = URL.createObjectURL(blob);
            audio.style.display = 'block';
            audio.play();

            status.className = 'sporocilo sporocilo-uspeh';
            status.textContent = '✓ Ponudnik: ' + j.ponudnik + (j.glas ? ' · Glas: ' + j.glas : '');
        })
        .catch(e => {
            btn.disabled = false;
            btn.textContent = '▶ Predvajaj';
            status.className = 'sporocilo sporocilo-napaka';
            status.textContent = '✗ Napaka povezave: ' + e.message;
        });
    });
})();
</script>
