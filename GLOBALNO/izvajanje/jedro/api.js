/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/frontend/runtime/jedro/api.js
 * v111 (27.5.2026 07:45)
 * ---------------------------------------------------------
 * OPIS: API klici – wrapper za komunikacijo s sistemom
 * ---------------------------------------------------------
 */
const API = (function() {
    'use strict';
    
    async function zahteva(akcija, podatki = {}, metoda = 'POST') {
        return await Sistem.posljiAPI(akcija, podatki, metoda);
    }
    
    async function prijava(email, geslo, zapomniSe = false) {
        const odziv = await zahteva('prijava', { email, geslo, zapomni_se: zapomniSe });
        
        if (odziv.status === 'uspeh' && odziv.vsebina?.uporabnik) {
            Sistem.setToken(odziv.vsebina.token, zapomniSe);
            Sistem.nastaviUporabnika(odziv.vsebina.uporabnik);
        }
        
        return odziv;
    }
    
    async function registracija(podatki) {
        return await zahteva('registracija', podatki);
    }
    
    async function odjava() {
        const odziv = await zahteva('odjava', {});
        Sistem.removeToken();
        Sistem.nastaviUporabnika(null);
        return odziv;
    }
    
    async function profil() {
        return await zahteva('profil', {}, 'GET');
    }
    
    async function posodobiProfil(podatki) {
        const odziv = await zahteva('posodobi_profil', podatki);
        
        if (odziv.status === 'uspeh' && odziv.vsebina?.uporabnik) {
            Sistem.nastaviUporabnika(odziv.vsebina.uporabnik);
        }
        
        return odziv;
    }
    
    async function spremeniGeslo(staroGeslo, novoGeslo) {
        return await zahteva('spremeni_geslo', { staro_geslo: staroGeslo, novo_geslo: novoGeslo });
    }
    
    async function moduli(aktivniOnly = true) {
        return await zahteva('moduli', { aktivni_only: aktivniOnly }, 'GET');
    }
    
    async function modulIzvedi(imeModula, akcija, podatki = {}) {
        return await zahteva('modul_izvedi', { modul: imeModula, akcija: akcija, podatki: podatki });
    }
    
    async function obnoviGeslo(email) {
        return await zahteva('obnovi_geslo', { email });
    }
    
    return {
        zahteva: zahteva,
        prijava: prijava,
        registracija: registracija,
        odjava: odjava,
        profil: profil,
        posodobiProfil: posodobiProfil,
        spremeniGeslo: spremeniGeslo,
        moduli: moduli,
        modulIzvedi: modulIzvedi,
        obnoviGeslo: obnoviGeslo
    };
})();
