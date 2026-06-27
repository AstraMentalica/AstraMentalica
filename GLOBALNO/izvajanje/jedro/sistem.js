/**
 * ---------------------------------------------------------
 * POT: GLOBALNO/frontend/runtime/jedro/sistem.js
 * v111 (27.5.2026 07:45)
 * ---------------------------------------------------------
 * OPIS: Jedrne sistemske funkcije za frontend runtime
 * ---------------------------------------------------------
 */
const Sistem = (function() {
    'use strict';
    
    let config = {
        debug: false,
        apiUrl: 'api.php',
        token: null,
        moduli: {}
    };
    
    function log(level, message, data) {
        if (!config.debug && level === 'debug') return;
        
        const timestamp = new Date().toISOString();
        const prefix = `[${timestamp}] [SISTEM] [${level.toUpperCase()}]`;
        
        if (data) {
            console[level](prefix, message, data);
        } else {
            console[level](prefix, message);
        }
    }
    
    function debug(message, data) { log('debug', message, data); }
    function info(message, data) { log('info', message, data); }
    function warn(message, data) { log('warn', message, data); }
    function error(message, data) { log('error', message, data); }
    
    function getToken() {
        return config.token || localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    }
    
    function setToken(token, remember = false) {
        config.token = token;
        if (remember) {
            localStorage.setItem('auth_token', token);
        } else {
            sessionStorage.setItem('auth_token', token);
        }
    }
    
    function removeToken() {
        config.token = null;
        localStorage.removeItem('auth_token');
        sessionStorage.removeItem('auth_token');
    }
    
    async function posljiAPI(endpoint, data = {}, method = 'POST') {
        const token = getToken();
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        
        const options = {
            method: method,
            headers: headers,
            credentials: 'same-origin'
        };
        
        if (method !== 'GET') {
            options.body = JSON.stringify(data);
        } else if (Object.keys(data).length > 0) {
            const params = new URLSearchParams(data);
            endpoint += '?' + params.toString();
        }
        
        try {
            const response = await fetch(config.apiUrl + '?' + new URLSearchParams({akcija: endpoint}), options);
            const result = await response.json();
            
            if (result.status === 'napaka' && result.status_koda === 401) {
                removeToken();
                window.dispatchEvent(new CustomEvent('sistem:odjava', { detail: result }));
            }
            
            return result;
        } catch (err) {
            error('API napaka:', err);
            return {
                status: 'napaka',
                status_koda: 500,
                sporocilo: 'Napaka pri povezavi s strežnikom',
                napake: [err.message]
            };
        }
    }
    
    async function naloziModul(imeModula) {
        if (config.moduli[imeModula]) {
            return config.moduli[imeModula];
        }
        
        info(`Nalagam modul: ${imeModula}`);
        
        const odziv = await posljiAPI('modul_podatki', { modul: imeModula });
        
        if (odziv.status === 'uspeh') {
            config.moduli[imeModula] = odziv.vsebina;
            window.dispatchEvent(new CustomEvent('sistem:modul_nalozen', { detail: { ime: imeModula, podatki: odziv.vsebina } }));
            return odziv.vsebina;
        }
        
        error(`Napaka pri nalaganju modula ${imeModula}:`, odziv.sporocilo);
        return null;
    }
    
    function init(options = {}) {
        config = { ...config, ...options };
        
        if (config.debug) {
            debug('Sistem inicializiran', { config });
        }
        
        const savedToken = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        if (savedToken) {
            config.token = savedToken;
        }
        
        window.dispatchEvent(new CustomEvent('sistem:inicializiran', { detail: config }));
        
        info('Sistem inicializiran');
    }
    
    function trenutniUporabnik() {
        return JSON.parse(localStorage.getItem('trenutni_uporabnik') || 'null');
    }
    
    function nastaviUporabnika(uporabnik) {
        if (uporabnik) {
            localStorage.setItem('trenutni_uporabnik', JSON.stringify(uporabnik));
        } else {
            localStorage.removeItem('trenutni_uporabnik');
        }
    }
    
    return {
        init: init,
        debug: debug,
        info: info,
        warn: warn,
        error: error,
        getToken: getToken,
        setToken: setToken,
        removeToken: removeToken,
        posljiAPI: posljiAPI,
        naloziModul: naloziModul,
        trenutniUporabnik: trenutniUporabnik,
        nastaviUporabnika: nastaviUporabnika
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    Sistem.init({
        debug: window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    });
});
