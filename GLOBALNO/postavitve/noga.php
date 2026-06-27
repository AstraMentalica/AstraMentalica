<?php
/**
 * ============================================================
 * POT: GLOBALNO/render/noga.php
 * 📅 VERZIJA: v114 (9.6.2026 18:00)
 * ============================================================
 *
 * 🏛️ NIVO: GLOBALNO (render)
 * 📰 NAMEN: Noga strani – JS inicializacija, zapiranje HTML.
 * ✅ DOVOLJENO: echo, HTML, JS
 * 🚫 PREPOVEDI: Brez business logike
 * 📌 STATUS: Stabilno
 * 📅 ZGODOVINA: - v114: implementacija
 * 👤 AVTOR: AstraMentalica Mojster
 * 🌐 JEZIK: sl
 * 🏷️ OZNAKE: globalno, render, noga
 * ============================================================
 */

declare(strict_types=1);

defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');
?>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Stisni / razširi navigacijo
    const nav      = document.getElementById('navigacija');
    const gumbStisni = document.getElementById('navStisni');
    const kljucNav = 'nav_stisnjena';

    if (nav && gumbStisni) {
        if (localStorage.getItem(kljucNav) === '1') nav.classList.add('stisnjena');

        gumbStisni.addEventListener('click', function() {
            nav.classList.toggle('stisnjena');
            localStorage.setItem(kljucNav, nav.classList.contains('stisnjena') ? '1' : '0');
        });
    }

    // Tema preklop
    const gumbTema = document.getElementById('temaPreklop');
    const html     = document.documentElement;

    if (gumbTema) {
        const tema = localStorage.getItem('tema') || 'temna';
        html.setAttribute('data-tema', tema);
        gumbTema.textContent = tema === 'temna' ? '🌙' : '☀️';

        gumbTema.addEventListener('click', function() {
            const nova = html.getAttribute('data-tema') === 'temna' ? 'svetla' : 'temna';
            html.setAttribute('data-tema', nova);
            localStorage.setItem('tema', nova);
            gumbTema.textContent = nova === 'temna' ? '🌙' : '☀️';
        });
    }

    // Globalno iskanje (Ctrl+K)
    const iskalnik = document.getElementById('globalnoIskanje');
    if (iskalnik) {
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                iskalnik.focus();
                iskalnik.select();
            }
            if (e.key === 'Escape' && document.activeElement === iskalnik) {
                iskalnik.blur();
            }
        });
    }

});
</script>
</body>
</html>
