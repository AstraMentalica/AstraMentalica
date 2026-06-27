/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/frontend/runtime/init/zagon.js
 * v111 (27.5.2026 07:45)
 * ---------------------------------------------------------
 * OPIS: Bootstrap zagon – najzgodnejša inicializacija
 * ---------------------------------------------------------
 */
(function() {
    'use strict';
    
    function bootstrap() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof Aplikacija !== 'undefined' && Aplikacija.zacni) {
                    Aplikacija.zacni();
                }
            });
        } else {
            if (typeof Aplikacija !== 'undefined' && Aplikacija.zacni) {
                Aplikacija.zacni();
            }
        }
    }
    
    bootstrap();
})();
