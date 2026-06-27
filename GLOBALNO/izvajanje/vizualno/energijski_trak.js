7. runtime/energijski_trak.js
javascript
let zbranaEnergija = 0;
const energijskiElement = document.getElementById('energijski-napolnjenost');

if (energijskiElement) {
  setInterval(() => {
    // Interakcija s peskovnikom dviguje energijo
    const aktivenElement = document.activeElement;
    if (aktivenElement && aktivenElement.closest('.gradnik')) {
      zbranaEnergija = Math.min(100, zbranaEnergija + 1.5);
    } else {
      zbranaEnergija = Math.max(0, zbranaEnergija - 0.3);
    }
    energijskiElement.style.width = zbranaEnergija + '%';
    
    // Čarobno obvestilo ob polni energiji
    if (zbranaEnergija >= 99.5) {
      magičnoObvestilo("Tvoja energija je polna! 🌟");
    }
  }, 800);
}