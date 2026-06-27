8. runtime/delci_miske.js
javascript
document.addEventListener('mousemove', (e) => {
  const delec = document.createElement('div');
  delec.className = 'magični-delec';
  delec.style.left = e.clientX + 'px';
  delec.style.top = e.clientY + 'px';
  delec.style.width = Math.random() * 8 + 3 + 'px';
  delec.style.height = delec.style.width;
  document.body.appendChild(delec);
  setTimeout(() => delec.remove(), 550);
});

// Slog za delce (dodamo le enkrat)
if (!document.querySelector('#slog-delcev')) {
  const slogDelcev = document.createElement('style');
  slogDelcev.id = 'slog-delcev';
  slogDelcev.textContent = `
    .magični-delec {
      position: fixed;
      pointer-events: none;
      border-radius: 50%;
      background: radial-gradient(circle, #f5cb5c, #b87333, transparent);
      z-index: 9999;
      animation: zblediDelec 0.55s ease-out forwards;
    }
    @keyframes zblediDelec {
      0% { opacity: 0.9; transform: scale(1); }
      100% { opacity: 0; transform: scale(0.2); }
    }
  `;
  document.head.appendChild(slogDelcev);
}