/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/frontend/ui/aktivacija/obrazci.js
 * v111 (27.5.2026 07:45)
 * ---------------------------------------------------------
 * OPIS: Pomožne funkcije za delo z obrazci
 * ---------------------------------------------------------
 */
const Obrazci = (function() {
    'use strict';
    
    function serializiraj(form) {
        const podatki = {};
        const formData = new FormData(form);
        
        for (let [kljuc, vrednost] of formData.entries()) {
            if (podatki[kljuc] !== undefined) {
                if (!Array.isArray(podatki[kljuc])) {
                    podatki[kljuc] = [podatki[kljuc]];
                }
                podatki[kljuc].push(vrednost);
            } else {
                podatki[kljuc] = vrednost;
            }
        }
        
        return podatki;
    }
    
    function napolni(form, podatki) {
        for (let [kljuc, vrednost] of Object.entries(podatki)) {
            const element = form.querySelector(`[name="${kljuc}"]`);
            if (element) {
                if (element.type === 'checkbox') {
                    element.checked = !!vrednost;
                } else if (element.type === 'radio') {
                    const radio = form.querySelector(`[name="${kljuc}"][value="${vrednost}"]`);
                    if (radio) radio.checked = true;
                } else {
                    element.value = vrednost;
                }
            }
        }
    }
    
    function pocisti(form) {
        const elementi = form.querySelectorAll('input, select, textarea');
        elementi.forEach(el => {
            if (el.type === 'checkbox' || el.type === 'radio') {
                el.checked = false;
            } else {
                el.value = '';
            }
        });
    }
    
    function prikaziNapake(form, napake) {
        form.querySelectorAll('.napaka-polja').forEach(el => el.remove());
        
        for (let [polje, sporocilo] of Object.entries(napake)) {
            const element = form.querySelector(`[name="${polje}"]`);
            if (element) {
                const napakaSpan = document.createElement('span');
                napakaSpan.className = 'napaka-polja';
                napakaSpan.textContent = sporocilo;
                napakaSpan.style.color = '#e74c3c';
                napakaSpan.style.fontSize = '0.8rem';
                napakaSpan.style.marginTop = '0.25rem';
                element.parentNode.insertBefore(napakaSpan, element.nextSibling);
                element.classList.add('polje-napaka');
            }
        }
    }
    
    function pocistiNapake(form) {
        form.querySelectorAll('.napaka-polja').forEach(el => el.remove());
        form.querySelectorAll('.polje-napaka').forEach(el => el.classList.remove('polje-napaka'));
    }
    
    function onSubmit(form, callback) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            pocistiNapake(form);
            
            const podatki = serializiraj(form);
            const rezultat = await callback(podatki);
            
            if (rezultat && rezultat.napake) {
                prikaziNapake(form, rezultat.napake);
            }
            
            return rezultat;
        });
    }
    
    return {
        serializiraj: serializiraj,
        napolni: napolni,
        pocisti: pocisti,
        prikaziNapake: prikaziNapake,
        pocistiNapake: pocistiNapake,
        onSubmit: onSubmit
    };
})();
