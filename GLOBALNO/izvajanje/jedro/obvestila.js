6. runtime/obvestila.js
javascript
function magičnoObvestilo(sporočilo) {
  const obvestilo = document.createElement('div');
  obvestilo.className = 'magicno-obvestilo';
  obvestilo.innerHTML = `✨ ${sporočilo} ✨`;
  document.body.appendChild(obvestilo);
  setTimeout(() => obvestilo.classList.add('pokaži'), 10);
  setTimeout(() => {
    obvestilo.classList.remove('pokaži');
    setTimeout(() => obvestilo.remove(), 300);
  }, 3800);
}

// Slog dodamo dinamično, če ga še ni
if (!document.querySelector('#slog-obvestila')) {
  const slog = document.createElement('style');
  slog.id = 'slog-obvestila';
  slog.textContent = `
    .magicno-obvestilo {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: rgba(0,0,0,0.85);
      backdrop-filter: blur(12px);
      border-left: 4px solid #d4af37;
      padding: 14px 24px;
      border-radius: 40px;
      color: #ffefcf;
      font-weight: bold;
      transform: translateX(450px);
      transition: transform 0.3s ease;
      z-index: 2000;
      font-size: 0.9rem;
      box-shadow: 0 0 18px gold;
    }
    .magicno-obvestilo.pokaži {
      transform: translateX(0);
    }
  `;
  document.head.appendChild(slog);
}