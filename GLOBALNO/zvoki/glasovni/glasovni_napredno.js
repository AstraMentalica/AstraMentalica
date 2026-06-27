/**
 * ============================================================
 * POT: GLOBALNO/frontend/glasovni_napredno.js
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * NIVO: FRONTEND
 *
 * NAMEN:
 *     Napredni glasovni sistem – poveže frontend z backend
 *     TTS (ElevenLabs/Azure) in STT (Whisper) storitvami.
 *     Snemanje z MediaRecorder API, pošiljanje na /api.
 *
 * RAZREDI:
 *     NapredniTTS  – bere besedilo prek backend (ElevenLabs/Azure)
 *     NapredniSTT  – snema mikrofon, pošlje Whisperju
 *     GlasovniPanel – UI komponenta za celoten glasovni vmesnik
 *
 * ODVISNOSTI:
 *     - glasovni_ui.js (za fallback in stile)
 *
 * STATUS: Stabilno
 * DATUM:  9.6.2026
 * OZNAKE: frontend, glasovno, tts, stt, elevenlabs, whisper
 * ============================================================
 */

'use strict';

// ============================================================
// NAPREDNI TTS – backend (ElevenLabs / Azure / OpenAI)
// ============================================================

class NapredniTTS {

    constructor(opcije) {
        this.opcije = Object.assign({
            ponudnik:  'auto',
            glas:      null,
            hitrost:   1.0,
            predpomni: true,
            jezik:     'sl',
        }, opcije || {});

        this._avdioEl   = null;
        this._bere      = false;
        this._predpomnilnik = new Map();
    }

    async beri(besedilo, opcije) {
        opcije = Object.assign({}, this.opcije, opcije || {});
        this.ustavi();

        // Lokalni predpomnilnik (ista seja)
        const kljuc = this._kljuc(besedilo, opcije);
        let avdioUrl = this._predpomnilnik.get(kljuc);

        if (!avdioUrl) {
            opcije.ob_stanje?.('nalaga');

            try {
                const odziv = await fetch('/api?akcija=tts_sintetiziraj', {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body:    JSON.stringify({
                        besedilo,
                        ponudnik:  opcije.ponudnik,
                        glas:      opcije.glas,
                        hitrost:   opcije.hitrost,
                        predpomni: opcije.predpomni,
                    }),
                });

                if (!odziv.ok) throw new Error(`HTTP ${odziv.status}`);
                const json = await odziv.json();

                if (json.status !== 'success') {
                    throw new Error(json.sporocilo || 'TTS napaka');
                }

                // base64 → Blob URL
                const binStr  = atob(json.avdio);
                const bytes   = new Uint8Array(binStr.length);
                for (let i = 0; i < binStr.length; i++) bytes[i] = binStr.charCodeAt(i);
                const blob    = new Blob([bytes], { type: 'audio/mpeg' });
                avdioUrl      = URL.createObjectURL(blob);

                // Shrani v predpomnilnik (max 50)
                if (this._predpomnilnik.size > 50) {
                    const prvKljuc = this._predpomnilnik.keys().next().value;
                    URL.revokeObjectURL(this._predpomnilnik.get(prvKljuc));
                    this._predpomnilnik.delete(prvKljuc);
                }
                this._predpomnilnik.set(kljuc, avdioUrl);

            } catch (e) {
                opcije.ob_napaki?.(e.message);
                return;
            }
        }

        // Predvajaj
        this._avdioEl = new Audio(avdioUrl);
        this._bere    = true;

        opcije.ob_zacetku?.();
        opcije.ob_stanje?.('bere');

        this._avdioEl.onended = () => {
            this._bere = false;
            opcije.ob_koncu?.();
            opcije.ob_stanje?.('miruje');
        };

        this._avdioEl.onerror = (e) => {
            this._bere = false;
            opcije.ob_napaki?.('Napaka pri predvajanju avdia.');
            opcije.ob_stanje?.('napaka');
        };

        try {
            await this._avdioEl.play();
        } catch (e) {
            // Autoplay blokada – ponudi gumb
            opcije.ob_napaki?.('Klikni stran za omogočitev avdia.');
            this._bere = false;
        }
    }

    ustavi() {
        if (this._avdioEl) {
            this._avdioEl.pause();
            this._avdioEl.currentTime = 0;
            this._avdioEl = null;
        }
        this._bere = false;
    }

    jeBere() { return this._bere; }

    _kljuc(besedilo, opcije) {
        return btoa(encodeURIComponent(
            besedilo.slice(0, 80) + '|' + opcije.ponudnik + '|' + opcije.glas + '|' + opcije.hitrost
        ));
    }
}

// ============================================================
// NAPREDNI STT – Whisper prek backend
// ============================================================

class NapredniSTT {

    constructor(opcije) {
        this.opcije = Object.assign({
            jezik:      'sl',
            maxSekund:  60,
            format:     'webm',
            ob_vmesni:  null, // fn(besedilo) – vmesni prikaz
            ob_rezultat:null, // fn(besedilo, zaupanje)
            ob_napaki:  null,
            ob_stanje:  null, // fn('miruje'|'caka'|'posluša'|'obdeluje')
        }, opcije || {});

        this._recorder   = null;
        this._posluša    = false;
        this._kosi       = [];
        this._timeout    = null;

        // Vzporedni browser STT za vmesni prikaz
        this._browserSTT = null;
        this._inicializiraBrowserSTT();
    }

    _inicializiraBrowserSTT() {
        const Rec = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!Rec) return;

        this._browserSTT           = new Rec();
        this._browserSTT.lang      = this.opcije.jezik + '-SI';
        this._browserSTT.continuous     = false;
        this._browserSTT.interimResults = true;
        this._browserSTT.maxAlternatives = 1;

        this._browserSTT.onresult = (e) => {
            for (let i = e.resultIndex; i < e.results.length; i++) {
                const besedilo = e.results[i][0].transcript;
                if (!e.results[i].isFinal && this.opcije.ob_vmesni) {
                    this.opcije.ob_vmesni(besedilo);
                }
            }
        };
    }

    async zacni() {
        if (this._posluša) return;

        try {
            const tok = await navigator.mediaDevices.getUserMedia({ audio: {
                channelCount:   1,
                sampleRate:     16000, // Optimalno za Whisper
                echoCancellation: true,
                noiseSuppression: true,
                autoGainControl:  true,
            }});

            const mimeType = this._najdiFmt();
            this._recorder = new MediaRecorder(tok, mimeType ? { mimeType } : {});
            this._kosi     = [];
            this._posluša  = true;

            this._recorder.ondataavailable = (e) => {
                if (e.data.size > 0) this._kosi.push(e.data);
            };

            this._recorder.onstop = async () => {
                tok.getTracks().forEach(t => t.stop());
                await this._obdelajPosnetek();
            };

            this._recorder.start(200); // Kosi vsakih 200ms

            // Browser STT za vmesni prikaz
            this._browserSTT?.start();

            this.opcije.ob_stanje?.('posluša');

            // Avtomatska ustavitev po maxSekund
            this._timeout = setTimeout(() => this.ustavi(), this.opcije.maxSekund * 1000);

        } catch (e) {
            this._posluša = false;
            const sporocila = {
                'NotAllowedError':  'Dostop do mikrofona zavrnjen. Dovoli v nastavitvah brskalnika.',
                'NotFoundError':    'Mikrofon ni zaznan.',
                'NotReadableError': 'Mikrofon je že v uporabi.',
            };
            this.opcije.ob_napaki?.(sporocila[e.name] || e.message);
            this.opcije.ob_stanje?.('napaka');
        }
    }

    ustavi() {
        clearTimeout(this._timeout);
        this._browserSTT?.stop();

        if (this._recorder && this._posluša) {
            this._posluša = false;
            this._recorder.stop();
            this.opcije.ob_stanje?.('obdeluje');
        }
    }

    jePoslusa() { return this._posluša; }

    async _obdelajPosnetek() {
        if (!this._kosi.length) {
            this.opcije.ob_stanje?.('miruje');
            return;
        }

        const mimeType = this._recorder?.mimeType || 'audio/webm';
        const blob     = new Blob(this._kosi, { type: mimeType });

        // Pretvori v base64
        const base64 = await new Promise((res) => {
            const r = new FileReader();
            r.onload = () => res(r.result.split(',')[1]);
            r.readAsDataURL(blob);
        });

        try {
            const odziv = await fetch('/api?akcija=stt_prepoznaj', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({
                    avdio:   base64,
                    jezik:   this.opcije.jezik,
                    format:  this._najdiFormatIme(mimeType),
                }),
            });

            if (!odziv.ok) throw new Error(`HTTP ${odziv.status}`);
            const json = await odziv.json();

            if (json.status !== 'success') throw new Error(json.sporocilo || 'STT napaka');

            this.opcije.ob_rezultat?.(json.besedilo, json.zaupanje);
            this.opcije.ob_stanje?.('miruje');

        } catch (e) {
            this.opcije.ob_napaki?.(e.message);
            this.opcije.ob_stanje?.('napaka');
        }

        this._kosi = [];
    }

    _najdiFmt() {
        const formati = [
            'audio/webm;codecs=opus',
            'audio/webm',
            'audio/ogg;codecs=opus',
            'audio/mp4',
        ];
        return formati.find(f => MediaRecorder.isTypeSupported(f)) || '';
    }

    _najdiFormatIme(mimeType) {
        if (mimeType.includes('webm')) return 'webm';
        if (mimeType.includes('ogg'))  return 'ogg';
        if (mimeType.includes('mp4'))  return 'mp4';
        return 'webm';
    }
}

// ============================================================
// GLASOVNI PANEL – UI komponenta
// ============================================================

class GlasovniPanel {
    /**
     * @param {HTMLElement} vsebnik  – element kjer se panel prikaže
     * @param {object}      opcije
     *   opcije.glas       – 'adam'|'rachel'|'petra'|'rok'|...
     *   opcije.ponudnik   – 'elevenlabs'|'azure'|'auto'
     *   opcije.ob_vnos    – fn(besedilo) ko uporabnik konča govoriti
     */
    constructor(vsebnik, opcije) {
        this.vsebnik = vsebnik;
        this.opcije  = Object.assign({
            glas:     'adam',
            ponudnik: 'auto',
            ob_vnos:  null,
        }, opcije || {});

        this.tts = new NapredniTTS({ ponudnik: this.opcije.ponudnik });
        this.stt = null;

        this._gradi();
    }

    _gradi() {
        this.vsebnik.innerHTML = `
<div class="gl-panel">

  <!-- TTS GUMB BRANJA -->
  <div class="gl-panel-vrstica">
    <button class="gl-panel-gumb" id="glBeriGumb" title="Preberi besedilo na glas">
      <span class="gl-panel-ik">🔊</span>
      <span class="gl-panel-nm">Preberi</span>
    </button>

    <!-- STT GUMB SNEMANJA -->
    <button class="gl-panel-gumb gl-panel-mikro" id="glMikroGumb" title="Govori – Whisper bo prepoznal slovenščino">
      <span class="gl-panel-ik">🎙</span>
      <span class="gl-panel-nm">Govori</span>
    </button>

    <!-- IZBIRA GLASU -->
    <select class="gl-panel-select" id="glGlasSelect" title="Izberi glas">
      <optgroup label="ElevenLabs (naravno)">
        <option value="el:adam">Adam – moški, globok</option>
        <option value="el:rachel">Rachel – ženski, umirjen</option>
        <option value="el:bella">Bella – ženski, mehak</option>
        <option value="el:josh">Josh – moški, mlad</option>
      </optgroup>
      <optgroup label="Azure Neural (sl-SI)">
        <option value="az:rok">Rok – moški, Neural</option>
        <option value="az:petra">Petra – ženski, Neural</option>
      </optgroup>
    </select>
  </div>

  <!-- VAL VIZUALIZATOR -->
  <div class="gl-panel-val" id="glVal" style="display:none">
    <div class="gl-vp"></div><div class="gl-vp"></div><div class="gl-vp"></div>
    <div class="gl-vp"></div><div class="gl-vp"></div>
    <span class="gl-val-napis" id="glValNapis">Poslušam...</span>
  </div>

  <!-- VMESNI PRIKAZ -->
  <div class="gl-panel-vmesni" id="glVmesni"></div>

</div>

<style>
.gl-panel{display:flex;flex-direction:column;gap:8px}
.gl-panel-vrstica{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
.gl-panel-gumb{display:inline-flex;align-items:center;gap:5px;padding:6px 14px;
  background:var(--kar,rgba(255,255,255,.04));border:1px solid var(--rob,rgba(255,255,255,.07));
  border-radius:999px;color:var(--be-d,#8a7f70);font-size:.78rem;cursor:pointer;
  transition:all .2s;font-family:inherit}
.gl-panel-gumb:hover{background:var(--kar-h,rgba(255,255,255,.08));color:var(--be-s,#f0e8d5)}
.gl-panel-gumb.aktiven{background:var(--zl-d,rgba(232,200,74,.12));
  border-color:var(--zl,#e8c84a);color:var(--zl,#e8c84a)}
.gl-panel-gumb.bere{background:var(--mod-d,rgba(92,155,224,.12));
  border-color:var(--mod,#5c9be0);color:var(--mod,#5c9be0);
  animation:glut .9s ease-in-out infinite alternate}
.gl-panel-gumb.posluša{background:var(--rd-d,rgba(244,67,54,.08));
  border-color:rgba(244,67,54,.4);color:#ff6b6b;
  animation:glut .7s ease-in-out infinite alternate}
.gl-panel-gumb.nalaga{opacity:.6;cursor:wait}
@keyframes glut{from{opacity:1}to{opacity:.5}}
.gl-panel-ik{font-size:.9rem}
.gl-panel-select{padding:6px 10px;background:var(--kar,rgba(255,255,255,.04));
  border:1px solid var(--rob,rgba(255,255,255,.07));border-radius:8px;
  color:var(--be,#d4c5a9);font-size:.75rem;cursor:pointer;outline:none;font-family:inherit}
.gl-panel-select:focus{border-color:var(--zl,#e8c84a)}
.gl-panel-val{display:flex;align-items:center;gap:4px;height:28px}
.gl-vp{width:3px;border-radius:2px;background:var(--zl,#e8c84a);
  animation:glval .8s ease-in-out infinite alternate}
.gl-vp:nth-child(1){height:8px;animation-delay:0s}
.gl-vp:nth-child(2){height:16px;animation-delay:.1s}
.gl-vp:nth-child(3){height:24px;animation-delay:.2s}
.gl-vp:nth-child(4){height:18px;animation-delay:.15s}
.gl-vp:nth-child(5){height:10px;animation-delay:.05s}
@keyframes glval{from{transform:scaleY(.3);opacity:.4}to{transform:scaleY(1);opacity:1}}
.gl-val-napis{font-size:.72rem;color:var(--be-d,#8a7f70);margin-left:6px;font-style:italic}
.gl-panel-vmesni{font-size:.8rem;color:var(--be-d,#8a7f70);font-style:italic;
  min-height:20px;padding:0 4px;transition:opacity .2s}
</style>`;

        this._gumbBeri  = this.vsebnik.querySelector('#glBeriGumb');
        this._gumbMikro = this.vsebnik.querySelector('#glMikroGumb');
        this._glasSelect = this.vsebnik.querySelector('#glGlasSelect');
        this._val       = this.vsebnik.querySelector('#glVal');
        this._vmesni    = this.vsebnik.querySelector('#glVmesni');
        this._valNapis  = this.vsebnik.querySelector('#glValNapis');

        this._gumbBeri.addEventListener('click',  () => this._klikBeri());
        this._gumbMikro.addEventListener('click', () => this._klikMikro());
    }

    // Nastavi besedilo za branje od zunaj
    nastavi_besedilo(besedilo) {
        this._besedilo = besedilo;
    }

    _klikBeri() {
        if (this.tts.jeBere()) {
            this.tts.ustavi();
            this._gumbBeri.className = 'gl-panel-gumb';
            this._gumbBeri.innerHTML = '<span class="gl-panel-ik">🔊</span><span class="gl-panel-nm">Preberi</span>';
            return;
        }

        const besedilo = this._besedilo
            || document.querySelector('[data-beri-vsebina]')?.textContent
            || '';

        if (!besedilo.trim()) return;

        const [ponudnik, glasId] = this._razcleni_glas();

        this.tts.beri(besedilo, {
            ponudnik: ponudnik,
            glas:     glasId,
            ob_stanje: (stanje) => {
                const mapa = {
                    nalaga:  { cls: 'nalaga', ik: '⏳', nm: 'Nalagam...' },
                    bere:    { cls: 'bere',   ik: '⏹',  nm: 'Ustavi' },
                    miruje:  { cls: '',       ik: '🔊', nm: 'Preberi' },
                    napaka:  { cls: '',       ik: '🔊', nm: 'Preberi' },
                };
                const m = mapa[stanje] || mapa.miruje;
                this._gumbBeri.className = 'gl-panel-gumb ' + m.cls;
                this._gumbBeri.innerHTML = `<span class="gl-panel-ik">${m.ik}</span><span class="gl-panel-nm">${m.nm}</span>`;
            },
            ob_napaki: (sp) => this._tooltipNapaka(sp),
        });
    }

    _klikMikro() {
        if (!this.stt) {
            this.stt = new NapredniSTT({
                jezik: 'sl',
                ob_vmesni: (b) => {
                    this._vmesni.textContent = b + '...';
                    this._vmesni.style.opacity = '0.7';
                },
                ob_rezultat: (besedilo, zaupanje) => {
                    this._vmesni.textContent = '';
                    this.opcije.ob_vnos?.(besedilo);
                    this._val.style.display = 'none';
                },
                ob_stanje: (stanje) => {
                    const mapa = {
                        posluša:   { cls: 'posluša', ik: '⏹', nm: 'Ustavi',    val: true,  nap: 'Poslušam...' },
                        obdeluje:  { cls: 'nalaga',  ik: '⏳', nm: 'Obdelujem',  val: false, nap: '' },
                        miruje:    { cls: '',        ik: '🎙', nm: 'Govori',     val: false, nap: '' },
                        napaka:    { cls: '',        ik: '🎙', nm: 'Govori',     val: false, nap: '' },
                    };
                    const m = mapa[stanje] || mapa.miruje;
                    this._gumbMikro.className = 'gl-panel-gumb gl-panel-mikro ' + m.cls;
                    this._gumbMikro.innerHTML = `<span class="gl-panel-ik">${m.ik}</span><span class="gl-panel-nm">${m.nm}</span>`;
                    this._val.style.display = m.val ? 'flex' : 'none';
                    if (m.nap) this._valNapis.textContent = m.nap;
                },
                ob_napaki: (sp) => {
                    this._tooltipNapaka(sp);
                    this._val.style.display = 'none';
                },
            });
        }

        if (this.stt.jePoslusa()) {
            this.stt.ustavi();
        } else {
            this._vmesni.textContent = '';
            this.stt.zacni();
        }
    }

    _razcleni_glas() {
        const vrednost = this._glasSelect?.value || 'el:adam';
        const [prefix, glasId] = vrednost.split(':');
        const ponudnikMapa = { el: 'elevenlabs', az: 'azure' };
        return [ponudnikMapa[prefix] || 'auto', glasId];
    }

    _tooltipNapaka(sporocilo) {
        const el = document.createElement('div');
        el.className = 'gl-tooltip gl-tooltip-napaka';
        el.style.cssText = 'position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);background:var(--pov,#0e0e2e);border:1px solid #f44336;border-radius:999px;padding:8px 18px;font-size:.8rem;color:#f44336;z-index:9999;white-space:nowrap;box-shadow:0 4px 20px rgba(0,0,0,.5)';
        el.textContent = sporocilo;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 4000);
    }
}

// Izvozi globalno
window.NapredniTTS = NapredniTTS;
window.NapredniSTT = NapredniSTT;
window.GlasovniPanel = GlasovniPanel;
