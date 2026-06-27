/**
 * ============================================================
 * POT: GLOBALNO/vmesnik/js/zemljevi.js
 * 📅 VERZIJA: v1.0 (24.6.2026)
 * ============================================================
 *
 * 📰 NAMEN:
 *     JavaScript za zemljeve (svetovni zemljevid) –
 *     interaktivni sistem za navigacijo med moduli.
 *
 * ODVISNOSTI:
 *     - GLOBALNO/vmesnik/css/spremenljivke.css
 *     - GLOBALNO/vmesnik/css/zemljevi.css
 *
 * STATUS: Stabilno
 * ============================================================
 */

(function() {
    'use strict';

    // ============================================================
    // ZEMLJEVI – GLAVNA FUNKCIJA
    // ============================================================

    /**
     * Inicializira zemljeve na strani.
     */
    function inicializirajZemljeve() {
        const zemljeviOkvir = document.getElementById('zemljevi');
        if (!zemljeviOkvir) {
            return;
        }

        // Dodaj animacijo ob nalaganju
        const kartice = zemljeviOkvir.querySelectorAll('.zemljevi-kartica');
        kartice.forEach((kartica, index) => {
            kartica.style.animationDelay = `${index * 0.05}s`;
        });

        // Označi aktivno kartico
        oznaciAktivnoKartico(zemljeviOkvir);

        // Dodaj event listenerje za hover efekte
        dodajHoverEfekte(zemljeviOkvir);
    }

    // ============================================================
    // POMOŽNE FUNKCIJE
    // ============================================================

    /**
     * Označi trenutno aktivno kartico glede na URL.
     */
    function oznaciAktivnoKartico(zemljeviOkvir) {
        const trenutniUrl = new URL(window.location.href);
        const modulParam = trenutniUrl.searchParams.get('modul');
        const potParam = trenutniUrl.searchParams.get('pot');
        
        if (!modulParam && !potParam) {
            return;
        }

        const kartice = zemljeviOkvir.querySelectorAll('.zemljevi-kartica');
        kartice.forEach(kartica => {
            const href = kartica.getAttribute('href');
            if (!href) return;
            
            // Preveri modul parameter
            if (modulParam && href.includes('modul=' + encodeURIComponent(modulParam))) {
                kartica.classList.add('zemljevi-kartica-aktivna');
                return;
            }
            
            // Preveri pot parameter (za auth strani)
            if (potParam && href.includes('pot=' + encodeURIComponent(potParam))) {
                kartica.classList.add('zemljevi-kartica-aktivna');
            }
        });
    }

    /**
     * Dodaj hover efekte za kartice.
     */
    function dodajHoverEfekte(zemljeviOkvir) {
        const kartice = zemljeviOkvir.querySelectorAll('.zemljevi-kartica');
        
        kartice.forEach(kartica => {
            // Zvok ali vibracija ob kliku (če je podprto)
            kartica.addEventListener('click', function(e) {
                // Preveri, ali je mobilna naprava
                if ('vibrate' in navigator) {
                    navigator.vibrate(10);
                }
            });

            // Focus efekti za dostopnost
            kartica.addEventListener('focus', function() {
                this.classList.add('zemljevi-kartica-aktivna');
            });

            kartica.addEventListener('blur', function() {
                // Odstrani aktivno stanje, če ni dejansko aktivna
                const href = this.getAttribute('href');
                const trenutniUrl = new URL(window.location.href);
                const modulParam = trenutniUrl.searchParams.get('modul');
                
                if (href && modulParam && !href.includes('modul=' + encodeURIComponent(modulParam))) {
                    this.classList.remove('zemljevi-kartica-aktivna');
                }
            });
        });
    }

    // ============================================================
    // TOAST SPOROČILA (za feedback)
    // ============================================================

    /**
     * Prikaže toast sporočilo ob preklopu modula.
     */
    function prikaziToastSporocilo(sporocilo) {
        // Preveri, ali že obstaja toast
        let toast = document.querySelector('.zemljevi-toast');
        
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'zemljevi-toast';
            document.body.appendChild(toast);
        }

        toast.textContent = sporocilo;
        toast.classList.add('zemljevi-toast-vidno');

        // Skrij po 2 sekundah
        setTimeout(() => {
            toast.classList.remove('zemljevi-toast-vidno');
        }, 2000);
    }

    // ============================================================
    // OBDELAVA STRANI (SPA obnašanje)
    // ============================================================

    /**
     * Obdelava navigacijo brez ponovnega nalaganja (SPA).
     */
    function obdelavaNavigacije() {
        // Preveri, ali je podprta History API
        if (!window.history || !window.history.pushState) {
            return;
        }

        // Uporabi click event delegation za zemljeve kartice
        document.addEventListener('click', function(e) {
            const kartica = e.target.closest('.zemljevi-kartica');
            if (!kartica) {
                return;
            }

            // Preveri, ali je to notranja povezava
            const href = kartica.getAttribute('href');
            if (!href || href.startsWith('http') || href.startsWith('//')) {
                return;
            }

            // Za SPA obnašanje (če je podprto)
            // Trenutno uporabljamo standardno navigacijo
            // V prihodnosti lahko dodamo AJAX nalaganje
        });
    }

    // ============================================================
    // OBDELAVA GLAVNE ZMAGE (window events)
    // ============================================================

    /**
     * Inicializira zemljeve ko je DOM pripravljen.
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', inicializirajZemljeve);
        } else {
            inicializirajZemljeve();
        }

        // Obdelava navigacije
        obdelavaNavigacije();

        // Osveži aktivno kartico ob spremembi URL-ja (npr. back/forward)
        window.addEventListener('popstate', function() {
            const zemljeviOkvir = document.getElementById('zemljevi');
            if (zemljeviOkvir) {
                oznaciAktivnoKartico(zemljeviOkvir);
            }
        });
    }

    // ============================================================
    // ZAGON
    // ============================================================

    // Začni inicializacijo
    init();

    // Izvozi funkcije za uporabo v drugih delih kode
    window.Zemljevi = {
        inicializiraj: inicializirajZemljeve,
        oznaciAktivno: oznaciAktivnoKartico,
        prikaziToast: prikaziToastSporocilo
    };

})();