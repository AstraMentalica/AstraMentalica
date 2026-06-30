/**
 * GLOBALNO/skripte/magic_portali.js
 * Magični efekti za portale — zvezde, portal, meglica, ogenj, voda, luna, sonce
 * Avtomatska aktivacija glede na uro, dan, lunino fazo
 */

window.MagicPortali = {

    aktivni: [],

    init(portali) {
        if (!Array.isArray(portali) || !portali.length) return;
        portali.forEach(p => {
            if (!p.aktiven) return;
            if (this.jeAktiven(p)) {
                setTimeout(() => this.aktiviraj(p), 1500);
            }
            // Preveri vsako minuto
            setInterval(() => {
                if (this.jeAktiven(p) && !this.aktivni.includes(p.id)) {
                    this.aktiviraj(p);
                }
            }, 60000);
        });
    },

    jeAktiven(p) {
        const zdaj  = new Date();
        const ura   = zdaj.getHours();
        const dan   = zdaj.getDate();

        if (p.ure) {
            const ure = p.ure.split(',').map(Number);
            if (!ure.includes(ura)) return false;
        }
        if (p.dnevi) {
            const dnevi = p.dnevi.split(',').map(Number);
            if (!dnevi.includes(dan)) return false;
        }
        if (p.polna_luna && !this.jePolnaLuna()) return false;

        return true;
    },

    jePolnaLuna() {
        // Preprosta aproksimacija lunine faze
        const ref  = new Date(2000, 0, 6); // znana polna luna
        const zdaj = new Date();
        const dni  = (zdaj - ref) / 86400000;
        const faza = ((dni % 29.53) + 29.53) % 29.53;
        return faza >= 13 && faza <= 16; // ±1.5 dan od polne
    },

    aktiviraj(p) {
        this.aktivni.push(p.id);
        const efekti = {
            stars:  () => this.efektZvezde(),
            portal: () => this.efektPortal(),
            mist:   () => this.efektMeglica(),
            fire:   () => this.efektOgenj(),
            water:  () => this.efektVoda(),
            moon:   () => this.efektLuna(),
            sun:    () => this.efektSonce(),
        };
        (efekti[p.efekt] || efekti.stars)();

        if (p.nagrada) {
            setTimeout(() => this.prikaziNagrado(p), 800);
        }

        // Deaktiviraj po 1 uri
        setTimeout(() => {
            this.aktivni = this.aktivni.filter(id => id !== p.id);
        }, 3600000);
    },

    // ── EFEKTI ───────────────────────────────────────────────────

    efektZvezde() {
        const el = this._kontejner('magic-stars');
        for (let i = 0; i < 80; i++) {
            const s = document.createElement('div');
            s.textContent = ['✨','⭐','✦','·'][Math.floor(Math.random()*4)];
            s.style.cssText = `
                position:absolute;
                left:${Math.random()*100}%;
                top:${Math.random()*100}%;
                font-size:${Math.random()*24+8}px;
                animation: mzv_plava ${2+Math.random()*3}s ${Math.random()*2}s ease-in-out infinite;
                opacity:${0.3+Math.random()*0.7};
                pointer-events:none;
            `;
            el.appendChild(s);
        }
        this._css('mzv_plava','0%,100%{transform:translateY(0) rotate(0deg);opacity:.4} 50%{transform:translateY(-25px) rotate(180deg);opacity:1}');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 6000);
    },

    efektPortal() {
        const el = this._kontejner('magic-portal');
        el.innerHTML = `
            <div style="
                position:absolute;top:50%;left:50%;
                transform:translate(-50%,-50%);
                width:280px;height:280px;
                background:radial-gradient(circle,rgba(167,139,250,.25),transparent 70%);
                border-radius:50%;
                animation:mzv_pulz 2s ease-in-out infinite;
                display:flex;align-items:center;justify-content:center;
                font-size:4rem;
            ">
                <span style="animation:mzv_vrts 4s linear infinite;display:inline-block">🌀</span>
            </div>`;
        this._css('mzv_pulz','0%,100%{transform:translate(-50%,-50%) scale(.85);opacity:.5} 50%{transform:translate(-50%,-50%) scale(1.15);opacity:1}');
        this._css('mzv_vrts','from{transform:rotate(0deg)} to{transform:rotate(360deg)}');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 6000);
    },

    efektMeglica() {
        const el = this._kontejner('magic-mist');
        el.style.cssText += `
            background:linear-gradient(135deg,rgba(167,139,250,.15),rgba(103,232,249,.1));
            backdrop-filter:blur(6px);
            animation:mzv_megla 5s ease-in-out forwards;
        `;
        this._css('mzv_megla','0%{opacity:0;backdrop-filter:blur(0)} 20%{opacity:1;backdrop-filter:blur(6px)} 80%{opacity:1} 100%{opacity:0;backdrop-filter:blur(0)}');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 5000);
    },

    efektOgenj() {
        const el = this._kontejner('magic-fire');
        el.style.cssText += `
            bottom:0;top:auto;height:160px;
            background:linear-gradient(0deg,rgba(251,146,60,.35),transparent);
            animation:mzv_ogenj .6s ease-in-out infinite alternate;
        `;
        this._css('mzv_ogenj','from{opacity:.4;background:linear-gradient(0deg,rgba(239,68,68,.3),transparent)} to{opacity:1;background:linear-gradient(0deg,rgba(251,146,60,.5),transparent)}');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 5000);
    },

    efektVoda() {
        const el = this._kontejner('magic-water');
        el.style.cssText += `
            background:radial-gradient(ellipse at 50% 60%,rgba(103,232,249,.2),transparent 70%);
            animation:mzv_voda 3s ease-in-out infinite;
        `;
        this._css('mzv_voda','0%,100%{transform:scale(1);opacity:.3} 50%{transform:scale(1.08);opacity:.7}');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 5000);
    },

    efektLuna() {
        const el = document.createElement('div');
        el.textContent = '🌙';
        el.style.cssText = `
            position:fixed;top:16px;right:80px;
            font-size:3.5rem;z-index:9999;
            pointer-events:none;
            animation:mzv_luna 2s ease-in-out infinite alternate;
        `;
        this._css('mzv_luna','from{filter:drop-shadow(0 0 4px #c9a96e);opacity:.7} to{filter:drop-shadow(0 0 20px #e8d5a8);opacity:1}');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 8000);
    },

    efektSonce() {
        const el = document.createElement('div');
        el.textContent = '☀️';
        el.style.cssText = `
            position:fixed;top:16px;right:80px;
            font-size:3.5rem;z-index:9999;
            pointer-events:none;
            animation:mzv_sonce 3s linear infinite;
        `;
        this._css('mzv_sonce','from{transform:rotate(0deg) scale(1)} 50%{transform:rotate(180deg) scale(1.1)} to{transform:rotate(360deg) scale(1)}');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 8000);
    },

    prikaziNagrado(p) {
        const el = document.createElement('div');
        el.style.cssText = `
            position:fixed;top:50%;left:50%;
            transform:translate(-50%,-50%);
            background:var(--pov,#111827);
            border:1px solid rgba(201,169,110,.4);
            border-radius:16px;padding:32px 40px;
            z-index:10001;text-align:center;
            box-shadow:0 0 60px rgba(201,169,110,.15);
            animation:mzv_pop .4s cubic-bezier(.34,1.56,.64,1) forwards;
            max-width:380px;font-family:'Jost',sans-serif;
        `;
        el.innerHTML = `
            <div style="font-size:2.5rem;margin-bottom:12px">✦</div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#e8d5a8;margin-bottom:8px">
                Portal aktiviran
            </div>
            <p style="color:#8a8678;font-size:.875rem;margin-bottom:20px">
                ${this._esc(p.nagrada)}
            </p>
            ${p.modul ? `<a href="/?svet=MODULI&pot=${this._esc(p.modul)}"
                style="background:#c9a96e;color:#080b12;padding:10px 24px;border-radius:8px;
                text-decoration:none;font-size:.85rem;display:inline-block;margin-bottom:12px">
                Odpri modul →</a><br>` : ''}
            <button onclick="this.closest('[style]').remove()"
                style="background:transparent;border:1px solid rgba(255,255,255,.1);color:#8a8678;
                padding:6px 18px;border-radius:8px;cursor:pointer;font-size:.78rem;margin-top:8px">
                Zapri
            </button>
        `;
        this._css('mzv_pop','from{opacity:0;transform:translate(-50%,-50%) scale(.85)} to{opacity:1;transform:translate(-50%,-50%) scale(1)}');
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 12000);
    },

    // ── POMOČNIKI ─────────────────────────────────────────────

    _kontejner(razred) {
        const el = document.createElement('div');
        el.className = razred;
        el.style.cssText = 'position:fixed;inset:0;z-index:9999;pointer-events:none;overflow:hidden;';
        return el;
    },

    _css(ime, keyframes) {
        if (document.getElementById('mzv_' + ime)) return;
        const s = document.createElement('style');
        s.id = 'mzv_' + ime;
        s.textContent = `@keyframes ${ime} { ${keyframes} }`;
        document.head.appendChild(s);
    },

    _esc(str) {
        const d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    },
};
