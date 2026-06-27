/**
 * ============================================================
 * POT: GLOBALNO/frontend/glasovni_ui.js
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * NIVO: FRONTEND
 *
 * NAMEN:
 *     UI komponente za glasovni sistem.
 *     Gumb za branje, gumb za poslušanje, vizualizacija.
 *     Odvisno od glasovno.js.
 *
 * JAVNE FUNKCIJE:
 *     - GlasovniUI.gumb_beri(element, besedilo, opcije)
 *         Doda gumb "Preberi" na element
 *     - GlasovniUI.gumb_vnos(inputElement, opcije)
 *         Doda gumb "Govori" na input polje
 *     - GlasovniUI.vizualizator(canvasElement)
 *         Riše zvočno valovanje ob poslušanju
 *     - GlasovniUI.inicializiraj_vse()
 *         Samodejno doda glasovne gumbe na vse [data-beri] in [data-glasovni-vnos]
 *
 * PREPOVEDI:
 *     - Brez fetch klicev
 *     - Brez poslovne logike
 *
 * STATUS: Stabilno
 * DATUM:  9.6.2026
 * OZNAKE: frontend, glasovno, ui, komponente
 * ============================================================
 */

'use strict';

window.GlasovniUI = (function () {

    // --------------------------------------------------------
    // STILI (vbrizgani enkrat)
    // --------------------------------------------------------
    var _stiliDodani = false;

    function _dodaj_stile() {
        if (_stiliDodani) return;
        _stiliDodani = true;

        var css = `
/* ── GLASOVNI UI ─────────────────────────────────── */
.gl-gumb {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 6px 13px;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 999px;
    color: var(--be-d, #8a7f70);
    font-size: .78rem;
    cursor: pointer;
    transition: all .2s;
    user-select: none;
    white-space: nowrap;
    font-family: inherit;
}
.gl-gumb:hover {
    background: rgba(255,255,255,.09);
    color: var(--be-s, #f0e8d5);
    border-color: rgba(255,255,255,.18);
}
.gl-gumb.aktiven {
    background: var(--zl-d, rgba(232,200,74,.12));
    border-color: var(--zl, #e8c84a);
    color: var(--zl, #e8c84a);
}
.gl-gumb.bere {
    background: var(--mod-d, rgba(92,155,224,.12));
    border-color: var(--mod, #5c9be0);
    color: var(--mod, #5c9be0);
    animation: gl-utripa .9s ease-in-out infinite alternate;
}
.gl-gumb.napaka {
    background: var(--rd-d, rgba(244,67,54,.12));
    border-color: var(--rd, #f44336);
    color: var(--rd, #f44336);
}
.gl-gumb-ikona { font-size: .9rem; }

@keyframes gl-utripa {
    from { opacity: 1; }
    to   { opacity: .55; }
}

/* Gumb za vnos (v input polju) */
.gl-vnos-ovoj {
    position: relative;
    display: flex;
    align-items: center;
}
.gl-vnos-ovoj input,
.gl-vnos-ovoj textarea {
    padding-right: 2.8rem !important;
}
.gl-vnos-gumb {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.1rem;
    color: var(--be-d, #8a7f70);
    padding: 4px;
    border-radius: 50%;
    transition: all .2s;
    line-height: 1;
    display: flex;
    align-items: center;
}
.gl-vnos-gumb:hover { color: var(--zl, #e8c84a); background: var(--zl-d, rgba(232,200,74,.12)); }
.gl-vnos-gumb.posluša { color: var(--zl, #e8c84a); animation: gl-utripa .7s ease-in-out infinite alternate; }
.gl-vnos-gumb.napaka  { color: var(--rd, #f44336); }

/* Tooltip sporocila */
.gl-tooltip {
    position: fixed;
    bottom: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    background: var(--pov, #0e0e2e);
    border: 1px solid var(--rob, rgba(255,255,255,.07));
    border-radius: 999px;
    padding: 8px 18px;
    font-size: .8rem;
    color: var(--be, #d4c5a9);
    z-index: 9999;
    pointer-events: none;
    box-shadow: 0 4px 20px rgba(0,0,0,.5);
    animation: gl-pojavi .2s ease;
    white-space: nowrap;
}
.gl-tooltip.napaka { color: var(--rd, #f44336); border-color: var(--rd, #f44336); }

@keyframes gl-pojavi {
    from { opacity: 0; transform: translateX(-50%) translateY(6px); }
    to   { opacity: 1; transform: translateX(-50%) translateY(0); }
}

/* Valovni vizualizator */
.gl-val-ovoj {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    height: 28px;
}
.gl-val-palica {
    width: 3px;
    border-radius: 2px;
    background: var(--zl, #e8c84a);
    opacity: .6;
    animation: gl-val .8s ease-in-out infinite alternate;
}
.gl-val-palica:nth-child(1) { animation-delay: 0s;    height: 8px; }
.gl-val-palica:nth-child(2) { animation-delay: .1s;   height: 16px; }
.gl-val-palica:nth-child(3) { animation-delay: .2s;   height: 24px; }
.gl-val-palica:nth-child(4) { animation-delay: .15s;  height: 18px; }
.gl-val-palica:nth-child(5) { animation-delay: .05s;  height: 10px; }

@keyframes gl-val {
    from { transform: scaleY(0.3); opacity: .4; }
    to   { transform: scaleY(1);   opacity: 1; }
}
        `;

        var el = document.createElement('style');
        el.textContent = css;
        document.head.appendChild(el);
    }

    // --------------------------------------------------------
    // TOOLTIP SPOROCILA
    // --------------------------------------------------------
    var _tooltip = null;
    var _tooltipTimeout = null;

    function _prikazi_tooltip(sporocilo, tip, trajanje) {
        if (_tooltip) _tooltip.remove();
        clearTimeout(_tooltipTimeout);

        _tooltip = document.createElement('div');
        _tooltip.className = 'gl-tooltip' + (tip === 'napaka' ? ' napaka' : '');
        _tooltip.textContent = sporocilo;
        document.body.appendChild(_tooltip);

        _tooltipTimeout = setTimeout(function () {
            if (_tooltip) { _tooltip.remove(); _tooltip = null; }
        }, trajanje || 3000);
    }

    // --------------------------------------------------------
    // VALOVNI VIZUALIZATOR
    // --------------------------------------------------------
    function valovni_vizualizator() {
        var ovoj = document.createElement('div');
        ovoj.className = 'gl-val-ovoj';
        for (var i = 0; i < 5; i++) {
            var p = document.createElement('div');
            p.className = 'gl-val-palica';
            ovoj.appendChild(p);
        }
        return ovoj;
    }

    // --------------------------------------------------------
    // GUMB BERI (TTS)
    // --------------------------------------------------------

    /**
     * Doda gumb "Preberi na glas" poleg elementa.
     *
     * @param {HTMLElement} vsebnik   – element kamor se vstavi gumb
     * @param {string|function} vir   – besedilo ali fn() ki vrne besedilo
     * @param {object} opcije
     *   opcije.mesto       – 'pred' | 'za' (privzeto: 'za')
     *   opcije.jezik       – 'sl-SI'
     *   opcije.hitrost     – 0.9
     *   opcije.ob_zacetku  – callback
     *   opcije.ob_koncu    – callback
     */
    function gumb_beri(vsebnik, vir, opcije) {
        _dodaj_stile();
        opcije = opcije || {};

        if (!Glasovno.je_podprto().tts) return;

        var gumb = document.createElement('button');
        gumb.type = 'button';
        gumb.className = 'gl-gumb';
        gumb.innerHTML = '<span class="gl-gumb-ikona">🔊</span><span>Preberi</span>';
        gumb.title = 'Preberi na glas';

        gumb.addEventListener('click', function () {
            if (Glasovno.je_bere()) {
                Glasovno.ustavi();
                gumb.classList.remove('bere');
                gumb.innerHTML = '<span class="gl-gumb-ikona">🔊</span><span>Preberi</span>';
                return;
            }

            var besedilo = typeof vir === 'function' ? vir() : (vir || vsebnik.textContent || '');
            if (!besedilo.trim()) return;

            gumb.classList.add('bere');
            gumb.innerHTML = '<span class="gl-gumb-ikona">⏹</span><span>Ustavi</span>';
            _prikazi_tooltip('🔊 Berem...', 'info', 99999);

            Glasovno.beri(besedilo, {
                jezik:   opcije.jezik   || 'sl-SI',
                hitrost: opcije.hitrost || 0.9,
                ob_zacetku: function () {
                    if (opcije.ob_zacetku) opcije.ob_zacetku();
                },
                ob_koncu: function () {
                    gumb.classList.remove('bere');
                    gumb.innerHTML = '<span class="gl-gumb-ikona">🔊</span><span>Preberi</span>';
                    if (_tooltip) { _tooltip.remove(); _tooltip = null; }
                    if (opcije.ob_koncu) opcije.ob_koncu();
                },
                ob_napaki: function (sporocilo) {
                    gumb.classList.remove('bere');
                    gumb.classList.add('napaka');
                    gumb.innerHTML = '<span class="gl-gumb-ikona">⚠</span><span>Napaka</span>';
                    _prikazi_tooltip(sporocilo, 'napaka');
                    setTimeout(function () {
                        gumb.classList.remove('napaka');
                        gumb.innerHTML = '<span class="gl-gumb-ikona">🔊</span><span>Preberi</span>';
                    }, 3000);
                },
            });
        });

        if (opcije.mesto === 'pred') {
            vsebnik.parentNode.insertBefore(gumb, vsebnik);
        } else {
            vsebnik.parentNode.insertBefore(gumb, vsebnik.nextSibling);
        }

        return gumb;
    }

    // --------------------------------------------------------
    // GUMB GLASOVNI VNOS (STT)
    // --------------------------------------------------------

    /**
     * Doda gumb za glasovni vnos znotraj input/textarea polja.
     *
     * @param {HTMLInputElement|HTMLTextAreaElement} polje
     * @param {object} opcije
     *   opcije.jezik        – 'sl-SI'
     *   opcije.neprekinjen  – false
     *   opcije.ob_rezultatu – fn(besedilo) po končnem vnosu
     *   opcije.nacin        – 'zamenjaj' | 'dodaj' (privzeto: 'dodaj')
     */
    function gumb_vnos(polje, opcije) {
        _dodaj_stile();
        opcije = opcije || {};

        if (!Glasovno.je_podprto().stt) return;

        // Ovij polje v pozicijski vsebnik
        var ovoj = document.createElement('div');
        ovoj.className = 'gl-vnos-ovoj';
        polje.parentNode.insertBefore(ovoj, polje);
        ovoj.appendChild(polje);

        var gumb = document.createElement('button');
        gumb.type = 'button';
        gumb.className = 'gl-vnos-gumb';
        gumb.innerHTML = '🎙';
        gumb.title = 'Govori (glasovni vnos)';
        ovoj.appendChild(gumb);

        var val = valovni_vizualizator();
        var poslusa = false;

        gumb.addEventListener('click', function () {
            if (poslusa) {
                Glasovno.ustavi_poslušanje();
                return;
            }

            poslusa = true;
            gumb.classList.add('posluša');
            gumb.innerHTML = '⏹';
            gumb.title = 'Ustavi poslušanje';
            _prikazi_tooltip('🎙 Poslušam... govorite', 'info', 99999);

            Glasovno.poslusaj(function (besedilo, jeVmesno) {
                if (jeVmesno) {
                    // Vmesni prikaz – ne shranjujemo še
                    polje.setAttribute('placeholder', besedilo + '...');
                } else {
                    // Končni rezultat
                    polje.removeAttribute('placeholder');
                    if (opcije.nacin === 'zamenjaj') {
                        polje.value = besedilo;
                    } else {
                        var obstoječe = polje.value;
                        polje.value = obstoječe
                            ? obstoječe + ' ' + besedilo
                            : besedilo;
                    }
                    // Sproži input event (za reactive frameworks)
                    polje.dispatchEvent(new Event('input', { bubbles: true }));
                    polje.dispatchEvent(new Event('change', { bubbles: true }));

                    if (opcije.ob_rezultatu) opcije.ob_rezultatu(besedilo);
                }
            }, {
                jezik:      opcije.jezik     || 'sl-SI',
                neprekinjen: opcije.neprekinjen || false,
                ob_zacetku: function () {
                    ovoj.appendChild(val);
                },
                ob_koncu: function () {
                    poslusa = false;
                    gumb.classList.remove('posluša');
                    gumb.innerHTML = '🎙';
                    gumb.title = 'Govori (glasovni vnos)';
                    if (val.parentNode) val.remove();
                    if (_tooltip) { _tooltip.remove(); _tooltip = null; }
                },
                ob_napaki: function (sporocilo) {
                    poslusa = false;
                    gumb.classList.remove('posluša');
                    gumb.classList.add('napaka');
                    gumb.innerHTML = '⚠';
                    if (val.parentNode) val.remove();
                    _prikazi_tooltip(sporocilo, 'napaka');
                    setTimeout(function () {
                        gumb.classList.remove('napaka');
                        gumb.innerHTML = '🎙';
                    }, 3000);
                },
            });
        });

        return gumb;
    }

    // --------------------------------------------------------
    // SAMODEJNO INICIALIZIRANJE
    // --------------------------------------------------------

    /**
     * Samodejno doda glasovne gumbe na vse elemente z:
     *   data-beri          → gumb za branje
     *   data-glasovni-vnos → gumb za glasovni vnos
     */
    function inicializiraj_vse() {
        _dodaj_stile();

        // TTS: elementi z data-beri
        document.querySelectorAll('[data-beri]').forEach(function (el) {
            var jezikAtr = el.getAttribute('data-beri-jezik') || 'sl-SI';
            gumb_beri(el, null, { jezik: jezikAtr });
        });

        // STT: polja z data-glasovni-vnos
        document.querySelectorAll('[data-glasovni-vnos]').forEach(function (el) {
            var jezikAtr = el.getAttribute('data-glasovni-jezik') || 'sl-SI';
            var nacinAtr = el.getAttribute('data-glasovni-nacin') || 'dodaj';
            gumb_vnos(el, { jezik: jezikAtr, nacin: nacinAtr });
        });
    }

    // --------------------------------------------------------
    // JAVNI VMESNIK
    // --------------------------------------------------------
    return {
        gumb_beri:         gumb_beri,
        gumb_vnos:         gumb_vnos,
        inicializiraj_vse: inicializiraj_vse,
        valovni_vizualizator: valovni_vizualizator,
    };

})();

// Samodejni zagon po nalaganju DOM
document.addEventListener('DOMContentLoaded', function () {
    GlasovniUI.inicializiraj_vse();
});
