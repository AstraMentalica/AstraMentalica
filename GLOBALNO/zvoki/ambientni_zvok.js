5. runtime/ambientni_zvok.js
javascript
window.AmbientniZvok = (function() {
  let virZvoka = null;
  let aktiven = false;

  function začni() {
    if (!virZvoka && !aktiven) {
      virZvoka = new Audio('/zvoki/ambient/meditacija.mp3'); 
      virZvoka.loop = true;
      virZvoka.volume = 0.2;
      virZvoka.play().catch(e => console.log("Avtoplay blokiran"));
      aktiven = true;
    }
  }

  function ustavi() {
    if (virZvoka && aktiven) {
      virZvoka.pause();
      aktiven = false;
    }
  }

  function nastaviGlasnost(vol) {
    if (virZvoka) virZvoka.volume = Math.min(1, Math.max(0, vol));
  }

  return { začni, ustavi, nastaviGlasnost };
})();