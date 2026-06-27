/**
 * ============================================================
 * POT: GLOBALNO/frontend/glasovno.js
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * NIVO: FRONTEND
 *
 * NAMEN:
 *     Glasovni sistem – branje besedila (TTS) in glasovni vnos (STT).
 *     Web Speech API – brez zunanjih odvisnosti, brez strežnika.
 *
 * JAVNE FUNKCIJE:
 *     - Glasovno.beri(besedilo, opcije)   – TTS: bere besedilo na glas
 *     - Glasovno.ustavi()                 – ustavi branje
 *     - Glasovno.poslusaj(callback, opcije) – STT: posluša in vrne besedilo
 *     - Glasovno.ustavi_poslušanje()      – ustavi poslušanje
 *     - Glasovno.je_podprto()             – preveri podporo brskalnika
 *     - Glasovno.glasovi()                – seznam razpoložljivih glasov
 *
 * PREPOVEDI:
 *     - Brez fetch klicev tukaj
 *     - Brez DOM manipulacije UI elementov (to je v glasovni_ui.js)
 *
 * STATUS: Stabilno
 * DATUM:  9.6.2026
 * AVTOR:  AstraMentalica Mojster
 * JEZIK:  sl
 * OZNAKE: frontend, glasovno, tts, stt, speech
 * ============================================================
 */

'use strict';

window.Glasovno = (function () {

    // --------------------------------------------------------
    // STANJE
    // --------------------------------------------------------
    var _bere        = false;
    var _posluša     = false;
    var _recognition = null;
    var _utterance   = null;
    var _poslusajCB  = null;
    var _vmesniCB    = null;

    // --------------------------------------------------------
    // PODPORA
    // --------------------------------------------------------
    function je_podprto() {
        return {
            tts: 'speechSynthesis' in window,
            stt: 'SpeechRecognition' in window || 'webkitSpeechRecognition' in window,
        };
    }

    // --------------------------------------------------------
    // GLASOVI (TTS)
    // --------------------------------------------------------
    function glasovi() {
        if (!je_podprto().tts) return [];
        return window.speechSynthesis.getVoices();
    }

    function _najdi_glas(jezik) {
        var vsi = glasovi();
        var jezikKoda = jezik || 'sl-SI';

        // 1. Točno ujemanje (sl-SI)
        var glas = vsi.find(function (g) { return g.lang === jezikKoda; });
        if (glas) return glas;

        // 2. Delno ujemanje (sl)
        var krat = jezikKoda.split('-')[0];
        glas = vsi.find(function (g) { return g.lang.startsWith(krat); });
        if (glas) return glas;

        // 3. Google glasovi (kakovostnejši)
        glas = vsi.find(function (g) { return g.name.includes('Google'); });
        if (glas) return glas;

        // 4. Katerikoli
        return vsi[0] || null;
    }

    // --------------------------------------------------------
    // TTS – BRANJE
    // --------------------------------------------------------

    /**
     * Bere besedilo na glas.
     *
     * @param {string}   besedilo
     * @param {object}   opcije
     *   opcije.jezik        – 'sl-SI' (privzeto) | 'en-US' | ...
     *   opcije.hitrost      – 0.5 – 2.0 (privzeto: 0.9)
     *   opcije.višina       – 0.5 – 2.0 (privzeto: 1.0)
     *   opcije.glasnost     – 0.0 – 1.0 (privzeto: 1.0)
     *   opcije.ob_zacetku   – callback ko se začne
     *   opcije.ob_koncu     – callback ko se konča
     *   opcije.ob_napaki    – callback ob napaki
     *   opcije.ob_besedi    – callback ob vsaki besedi (highlight)
     */
    function beri(besedilo, opcije) {
        opcije = opcije || {};

        if (!je_podprto().tts) {
            if (opcije.ob_napaki) opcije.ob_napaki('TTS ni podprt v tem brskalniku.');
            return;
        }

        // Ustavi morebitno prejšnje branje
        ustavi();

        // Razdeli dolgo besedilo na stavke (omejitev brskalnikov ~200 znakov)
        var stavki = _razdeli_na_stavke(besedilo);
        var trenutni = 0;

        function preberiStavek() {
            if (trenutni >= stavki.length) {
                _bere = false;
                if (opcije.ob_koncu) opcije.ob_koncu();
                return;
            }

            _utterance = new SpeechSynthesisUtterance(stavki[trenutni]);
            _utterance.lang   = opcije.jezik   || 'sl-SI';
            _utterance.rate   = opcije.hitrost  || 0.9;
            _utterance.pitch  = opcije.višina   || 1.0;
            _utterance.volume = opcije.glasnost || 1.0;

            var glas = _najdi_glas(_utterance.lang);
            if (glas) _utterance.voice = glas;

            _utterance.onstart = function () {
                _bere = true;
                if (trenutni === 0 && opcije.ob_zacetku) opcije.ob_zacetku();
            };

            _utterance.onboundary = function (e) {
                if (e.name === 'word' && opcije.ob_besedi) {
                    opcije.ob_besedi(e.charIndex, e.charLength || 0);
                }
            };

            _utterance.onend = function () {
                trenutni++;
                preberiStavek();
            };

            _utterance.onerror = function (e) {
                _bere = false;
                if (e.error !== 'interrupted' && opcije.ob_napaki) {
                    opcije.ob_napaki('Napaka TTS: ' + e.error);
                }
            };

            window.speechSynthesis.speak(_utterance);
        }

        preberiStavek();
    }

    function ustavi() {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }
        _bere = false;
        _utterance = null;
    }

    function je_bere() { return _bere; }

    function _razdeli_na_stavke(besedilo) {
        // Razdeli po piki, vprašaju, klicaju — ohrani ločilo
        var stavki = besedilo.match(/[^.!?]+[.!?]*/g) || [besedilo];
        // Združi kratke stavke (pod 20 znakov)
        var zdruzen = [];
        var zbirnik = '';
        stavki.forEach(function (s) {
            zbirnik += s;
            if (zbirnik.length > 100) {
                zdruzen.push(zbirnik.trim());
                zbirnik = '';
            }
        });
        if (zbirnik.trim()) zdruzen.push(zbirnik.trim());
        return zdruzen.length ? zdruzen : [besedilo];
    }

    // --------------------------------------------------------
    // STT – POSLUŠANJE
    // --------------------------------------------------------

    /**
     * Začne poslušati mikrofon in vrne prepoznano besedilo.
     *
     * @param {function} callback    – fn(besedilo, vmesno)
     *   besedilo – prepoznani niz
     *   vmesno   – true = vmesni rezultat, false = končni
     * @param {object}   opcije
     *   opcije.jezik        – 'sl-SI' (privzeto)
     *   opcije.neprekinjen  – true = posluša neprekinjeno (privzeto: false)
     *   opcije.vmesni       – true = vrača vmesne rezultate (privzeto: true)
     *   opcije.ob_zacetku   – callback ko začne
     *   opcije.ob_koncu     – callback ko konča
     *   opcije.ob_napaki    – callback ob napaki
     *   opcije.ob_glasnosti – callback z glasnostjo (0–1)
     */
    function poslusaj(callback, opcije) {
        opcije = opcije || {};

        var podpora = je_podprto();
        if (!podpora.stt) {
            if (opcije.ob_napaki) opcije.ob_napaki('STT ni podprt v tem brskalniku (Chrome/Edge priporočen).');
            return;
        }

        // Ustavi morebitno prejšnje poslušanje
        ustavi_poslušanje();

        var Rec = window.SpeechRecognition || window.webkitSpeechRecognition;
        _recognition = new Rec();

        _recognition.lang            = opcije.jezik     || 'sl-SI';
        _recognition.continuous      = opcije.neprekinjen !== undefined ? opcije.neprekinjen : false;
        _recognition.interimResults  = opcije.vmesni    !== undefined ? opcije.vmesni : true;
        _recognition.maxAlternatives = 1;

        _poslusajCB = callback;

        _recognition.onstart = function () {
            _posluša = true;
            if (opcije.ob_zacetku) opcije.ob_zacetku();
        };

        _recognition.onresult = function (e) {
            var vmesno = '';
            var koncno = '';

            for (var i = e.resultIndex; i < e.results.length; i++) {
                var besedilo = e.results[i][0].transcript;
                if (e.results[i].isFinal) {
                    koncno += besedilo;
                } else {
                    vmesno += besedilo;
                }
            }

            if (vmesno && callback) callback(vmesno, true);
            if (koncno && callback) callback(koncno, false);
        };

        _recognition.onsoundlevel = function (e) {
            if (opcije.ob_glasnosti) {
                // Normaliziramo na 0–1
                var nivo = Math.min(1, Math.max(0, (e.value + 100) / 100));
                opcije.ob_glasnosti(nivo);
            }
        };

        _recognition.onend = function () {
            _posluša = false;
            if (opcije.ob_koncu) opcije.ob_koncu();
        };

        _recognition.onerror = function (e) {
            _posluša = false;
            var sporocila = {
                'not-allowed':      'Dostop do mikrofona zavrnjen. Dovoli dostop v nastavitvah brskalnika.',
                'no-speech':        'Ni zaznane govorne aktivnosti.',
                'network':          'Napaka omrežja pri prepoznavi govora.',
                'aborted':          'Poslušanje prekinjeno.',
                'audio-capture':    'Mikrofon ni zaznan ali ni dostopen.',
                'service-not-allowed': 'Storitev prepoznave govora ni dovoljena.',
            };
            var sporocilo = sporocila[e.error] || 'Napaka prepoznave govora: ' + e.error;
            if (opcije.ob_napaki) opcije.ob_napaki(sporocilo);
        };

        _recognition.start();
    }

    function ustavi_poslušanje() {
        if (_recognition) {
            try { _recognition.stop(); } catch (e) { /* že ustavljen */ }
            _recognition = null;
        }
        _posluša = false;
    }

    function je_poslusanje() { return _posluša; }

    // --------------------------------------------------------
    // JAVNI VMESNIK
    // --------------------------------------------------------
    return {
        beri:              beri,
        ustavi:            ustavi,
        je_bere:           je_bere,
        poslusaj:          poslusaj,
        ustavi_poslušanje: ustavi_poslušanje,
        je_poslusanje:     je_poslusanje,
        glasovi:           glasovi,
        je_podprto:        je_podprto,
    };

})();
