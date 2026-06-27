import { useState, useEffect, useRef, useCallback } from "react";

const ARHETIPI = {
  modrec: { ime: "Modrec", ikona: "📜", barva: "#4f46e5", opis: "Iščeš znanje in modrost." },
  zdravilec: { ime: "Zdravilec", ikona: "💚", barva: "#10b981", opis: "Zdravljenje sebe in drugih." },
  carovnik: { ime: "Čarovnik", ikona: "🔮", barva: "#8b5cf6", opis: "Obvladuješ skrite sile." },
  umetnik: { ime: "Umetnik", ikona: "🎨", barva: "#ec4899", opis: "Ustvarjaš lepoto." },
  bojevnik: { ime: "Bojevnik", ikona: "⚔️", barva: "#ef4444", opis: "Premaguješ ovire." },
  raziskovalec: { ime: "Raziskovalec", ikona: "🔍", barva: "#f59e0b", opis: "Raziskuješ neznano." },
};

const VARUHI = {
  prijazen: { ime: "Svetlobnik", ikona: "🕯️", barva: "#fbbf24", osebnost: "Prijazen in topel. Pomaga z radostjo.", pozdrav: "Pozdravljen, popotnik! Sem tu, da ti pomagam na tvoji čarobni poti. 🌟" },
  nagajiv: { ime: "Vetrnik", ikona: "💨", barva: "#6ee7b7", osebnost: "Nagajiv in zvit. Preizkuša te z ugankami.", pozdrav: "Hehe, prišel si? No, poglejva skupaj... morda te naučim kaj zanimivega. 😏" },
  strog: { ime: "Varuh Meja", ikona: "⚔️", barva: "#ef4444", osebnost: "Strog in neposreden. Zahteva disciplino.", pozdrav: "Stoj. Izkazati se moraš. Si res pripravljen na to pot? Povej mi resnico." },
  moder: { ime: "Zvezdogled", ikona: "🌟", barva: "#c084fc", osebnost: "Star in moder. Govori v metaforah.", pozdrav: "Kakor zvezde vodijo mornarje, tvoja duša ve kam gre. Kaj te vleče danes?" },
  sramezljiv: { ime: "Senca", ikona: "🌑", barva: "#9ca3af", osebnost: "Tih in umaknjen. Šepeta resnice.", pozdrav: "... (tiho) ... dobrodošel ... tukaj je varno ..." },
};

const VERZI = {
  ognjeni: { ime: "Ognjeni", simbol: "🔥", barvaOzadje: "#1a0505", barvaPoud: "#ef4444", barvaBes: "#fca5a5", glasba: "dinamična, hitra", prag: { nervoza: 70, hitrost: 3 } },
  vodni: { ime: "Vodni", simbol: "💧", barvaOzadje: "#020b18", barvaPoud: "#3b82f6", barvaBes: "#bfdbfe", glasba: "nežna, umirjena", prag: { nervoza: 30, hitrost: 1 } },
  srcni: { ime: "Srčni", simbol: "💖", barvaOzadje: "#1a0510", barvaPoud: "#ec4899", barvaBes: "#fbcfe8", glasba: "harfa, zvončki", prag: { nervoza: 50, hitrost: 0.5 } },
  zemeljski: { ime: "Zemeljski", simbol: "🌿", barvaOzadje: "#030f05", barvaPoud: "#22c55e", barvaBes: "#bbf7d0", glasba: "počasna, organična", prag: { nervoza: 20, hitrost: 0.5 } },
  zracni: { ime: "Zračni", simbol: "🍃", barvaOzadje: "#0a0520", barvaPoud: "#8b5cf6", barvaBes: "#ddd6fe", glasba: "lahkotna, piščali", prag: { nervoza: 40, hitrost: 2 } },
};

const STOPNJE = [
  { stopnja: 0, ime: "Meglica", ikona: "🌫️", tocke: 0 },
  { stopnja: 1, ime: "Iskrica", ikona: "✨", tocke: 100 },
  { stopnja: 2, ime: "Kalček", ikona: "🌱", tocke: 300 },
  { stopnja: 3, ime: "Rastlina", ikona: "🌿", tocke: 600 },
  { stopnja: 4, ime: "Cvet", ikona: "🌸", tocke: 1000 },
  { stopnja: 5, ime: "Drevo", ikona: "🌳", tocke: 1500 },
  { stopnja: 6, ime: "Zvezda", ikona: "⭐", tocke: 2200 },
  { stopnja: 7, ime: "Sonce", ikona: "☀️", tocke: 3000 },
];

function izracunajStopnjo(tocke) {
  for (let i = STOPNJE.length - 1; i >= 0; i--) {
    if (tocke >= STOPNJE[i].tocke) return STOPNJE[i];
  }
  return STOPNJE[0];
}

function dolociVerz(nervoza, hitrost, energija) {
  if (nervoza > 72 && hitrost > 3) return "ognjeni";
  if (nervoza < 25 && hitrost < 0.8) return "zemeljski";
  if (nervoza < 35 && energija > 60) return "vodni";
  if (hitrost < 0.6 && nervoza < 50) return "srcni";
  if (energija > 65 && nervoza < 55) return "zracni";
  return "vodni";
}

export default function DuhovniVaruh() {
  const [zaslonCas, setZaslonCas] = useState("pozdrav"); // pozdrav | pogovor | profil | portali
  const [izbraniVaruh, setIzbraniVaruh] = useState(null);
  const [izbraniArhetip, setIzbraniArhetip] = useState(null);
  const [sporocila, setSporocila] = useState([]);
  const [vnos, setVnos] = useState("");
  const [nalaganje, setNalaganje] = useState(false);
  const [tocke, setTocke] = useState(120);
  const [nervoza, setNervoza] = useState(30);
  const [hitrostKlikov, setHitrostKlikov] = useState(1);
  const [energija, setEnergija] = useState(55);
  const [trenutniVerz, setTrenutniVerz] = useState("vodni");
  const [prehodVerza, setPrehodVerza] = useState(false);
  const [delci, setDelci] = useState([]);
  const [zadnjiKliki, setZadnjiKliki] = useState([]);
  const [prikazDelcev, setPrikazDelcev] = useState(false);

  const spodnjRef = useRef(null);
  const intervaliRef = useRef([]);

  // Merilci klikanja
  const beleziKlik = useCallback(() => {
    const zdaj = Date.now();
    setZadnjiKliki(prev => {
      const novi = [...prev, zdaj].filter(t => zdaj - t < 3000).slice(-15);
      const hitrost = novi.length / 3;
      setHitrostKlikov(hitrost);
      if (hitrost > 3) setNervoza(n => Math.min(100, n + 4));
      return novi;
    });
    setEnergija(e => Math.min(100, e + 1.5));
  }, []);

  // Dinamični verzi – preverjaj vsakih 20s
  useEffect(() => {
    const i = setInterval(() => {
      if (Math.random() > 0.55) return; // ~45% možnost
      const nov = dolociVerz(nervoza, hitrostKlikov, energija);
      if (nov !== trenutniVerz) {
        setPrehodVerza(true);
        setTimeout(() => {
          setTrenutniVerz(nov);
          sproziDelce(VERZI[nov].simbol, 20);
          setPrehodVerza(false);
        }, 800);
      }
    }, 20000);
    intervaliRef.current.push(i);
    return () => clearInterval(i);
  }, [nervoza, hitrostKlikov, energija, trenutniVerz]);

  // Zmanjšuj nervozo
  useEffect(() => {
    const i = setInterval(() => {
      setNervoza(n => Math.max(0, n - 0.4));
      setEnergija(e => Math.max(0, e - 0.2));
    }, 2000);
    return () => clearInterval(i);
  }, []);

  function sproziDelce(simbol, kolicina) {
    const novi = Array.from({ length: kolicina }, (_, k) => ({
      id: Date.now() + k,
      simbol,
      x: Math.random() * 100,
      y: Math.random() * 100,
      velikost: Math.random() * 24 + 12,
      zamik: Math.random() * 800,
    }));
    setDelci(novi);
    setTimeout(() => setDelci([]), 1200);
  }

  useEffect(() => {
    spodnjRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [sporocila]);

  async function posljiSporocilo() {
    if (!vnos.trim() || nalaganje) return;
    const besedilo = vnos.trim();
    setVnos("");
    beleziKlik();
    setNervoza(n => Math.min(100, n + 2));

    const novoSporocilo = { vloga: "user", vsebina: besedilo };
    setSporocila(prev => [...prev, novoSporocilo]);
    setNalaganje(true);

    const varuh = VARUHI[izbraniVaruh];
    const arhetip = ARHETIPI[izbraniArhetip];
    const stopnja = izracunajStopnjo(tocke);
    const verz = VERZI[trenutniVerz];

    const sistemskiPrompt = `Si ${varuh?.ime || "Duhovni Varuh"} – čarobni pomočnik v slovenskem mističnem portalu.

Tvoja osebnost: ${varuh?.osebnost || "Moder in skrivnosten."}
Arhetip sogovornika: ${arhetip?.ime || "Neznani popotnik"} – ${arhetip?.opis || "na svoji poti."}
Njihova stopnja zavesti: ${stopnja.ime} ${stopnja.ikona} (${tocke} točk)
Trenutni energijski verz: ${verz.ime} ${verz.simbol}
Nervoza sogovornika: ${Math.round(nervoza)}% | Energija: ${Math.round(energija)}%

Navodila:
- Govori SLOVENSKO vedno
- Nagovarjaj kot "popotnik" ali z imenom arhetipa
- Bodi ${varuh?.osebnost?.split(".")[0]?.toLowerCase() || "skrivnosten"}
- Odzovi se na energijski verz: ${verz.ime === "Ognjeni" ? "sogovornik je razburjen, bodi umirjevalen a direkten" : verz.ime === "Vodni" ? "sogovornik je miren, bodi poetičen in globok" : verz.ime === "Srčni" ? "sogovornik je nežen, bodi topel in ljubeč" : verz.ime === "Zemeljski" ? "sogovornik je utrujen, bodi zemeljski in stabilen" : "sogovornik je radoveden, bodi dinamičen"}
- Odgovori v 2-4 stavkih, poetično ampak jasno
- Občasno omeni njihovo stopnjo ali arhetip na subtilen način`;

    try {
      const odg = await fetch("https://api.anthropic.com/v1/messages", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          model: "claude-sonnet-4-6",
          max_tokens: 1000,
          system: sistemskiPrompt,
          messages: [
            ...sporocila.map(s => ({ role: s.vloga, content: s.vsebina })),
            { role: "user", content: besedilo }
          ],
        }),
      });
      const podatki = await odg.json();
      const odgovor = podatki.content?.[0]?.text || "... (tišina) ...";
      setSporocila(prev => [...prev, { vloga: "assistant", vsebina: odgovor }]);
      setTocke(t => t + 15);
      sproziDelce("✨", 8);
    } catch {
      setSporocila(prev => [...prev, { vloga: "assistant", vsebina: "Portali so trenutno zaprti... Poskusi znova." }]);
    }
    setNalaganje(false);
  }

  function izberiVaruhaInArhetip(varuhId, arhetipId) {
    setIzbraniVaruh(varuhId);
    setIzbraniArhetip(arhetipId);
    const varuh = VARUHI[varuhId];
    setSporocila([{ vloga: "assistant", vsebina: varuh.pozdrav }]);
    setZaslonCas("pogovor");
    sproziDelce(varuh.ikona, 15);
    setTocke(t => t + 30);
  }

  const verz = VERZI[trenutniVerz];
  const stopnja = izracunajStopnjo(tocke);
  const naslednjaSt = STOPNJE.find(s => s.tocke > tocke);
  const napredek = naslednjaSt ? Math.round(((tocke - stopnja.tocke) / (naslednjaSt.tocke - stopnja.tocke)) * 100) : 100;

  return (
    <div
      onClick={beleziKlik}
      style={{
        minHeight: "600px",
        background: verz.barvaOzadje,
        transition: "background 1.5s ease",
        fontFamily: "'Inter', sans-serif",
        position: "relative",
        overflow: "hidden",
        borderRadius: 16,
      }}
    >
      {/* Zvezdno ozadje */}
      <div style={{ position: "absolute", inset: 0, overflow: "hidden", pointerEvents: "none" }}>
        {Array.from({ length: 40 }, (_, i) => (
          <div key={i} style={{
            position: "absolute",
            left: `${(i * 137.5) % 100}%`,
            top: `${(i * 83.7) % 100}%`,
            width: i % 3 === 0 ? 3 : 2,
            height: i % 3 === 0 ? 3 : 2,
            borderRadius: "50%",
            background: verz.barvaBes,
            opacity: 0.15 + (i % 5) * 0.06,
            animation: `pulz ${2 + (i % 4)}s ease-in-out infinite ${(i * 0.3) % 3}s`,
          }} />
        ))}
      </div>

      {/* Delci */}
      {delci.map(d => (
        <div key={d.id} style={{
          position: "absolute",
          left: `${d.x}%`,
          top: `${d.y}%`,
          fontSize: d.velikost,
          pointerEvents: "none",
          zIndex: 50,
          animation: `delecPojav 1s ease-out forwards`,
          animationDelay: `${d.zamik}ms`,
          opacity: 0,
        }}>{d.simbol}</div>
      ))}

      {/* Prehod verza */}
      {prehodVerza && (
        <div style={{
          position: "absolute", inset: 0, zIndex: 40,
          background: verz.barvaPoud + "30",
          display: "flex", alignItems: "center", justifyContent: "center",
          fontSize: 80, animation: "portalEfekt 0.8s ease-out forwards",
        }}>{verz.simbol}</div>
      )}

      <style>{`
        @keyframes pulz { 0%,100%{opacity:0.1} 50%{opacity:0.4} }
        @keyframes delecPojav { 0%{opacity:0;transform:scale(0) translateY(0)} 50%{opacity:1;transform:scale(1.2) translateY(-20px)} 100%{opacity:0;transform:scale(0.5) translateY(-40px)} }
        @keyframes portalEfekt { 0%{transform:scale(0);opacity:0} 50%{transform:scale(1.5);opacity:0.8} 100%{transform:scale(5);opacity:0} }
        @keyframes valovanje { 0%,100%{transform:translateX(0)} 50%{transform:translateX(3px)} }
        @keyframes trepetanje { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-3px)} }
        @keyframes utrip { 0%,100%{opacity:0.6} 50%{opacity:1} }
      `}</style>

      <div style={{ position: "relative", zIndex: 10, padding: "1rem" }}>

        {/* Glava */}
        <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", marginBottom: "1rem" }}>
          <div style={{ display: "flex", alignItems: "center", gap: 8 }}>
            <div style={{ fontSize: 20, animation: "utrip 2s infinite" }}>{verz.simbol}</div>
            <div>
              <div style={{ fontSize: 13, fontWeight: 500, color: verz.barvaPoud }}>Duhovni Varuh</div>
              <div style={{ fontSize: 11, color: verz.barvaBes + "99" }}>{verz.ime} verz</div>
            </div>
          </div>

          {/* Stopnja */}
          <div style={{ textAlign: "right" }}>
            <div style={{ fontSize: 12, color: verz.barvaBes + "99" }}>{stopnja.ikona} {stopnja.ime}</div>
            <div style={{ fontSize: 11, color: verz.barvaBes + "66" }}>{tocke} točk</div>
          </div>
        </div>

        {/* Merilci */}
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr 1fr", gap: 6, marginBottom: "1rem" }}>
          {[
            { nal: "🌊", ime: "Nervoza", val: nervoza, barva: nervoza > 60 ? "#ef4444" : "#22c55e" },
            { nal: "⚡", ime: "Energija", val: energija, barva: "#f59e0b" },
            { nal: "🖱", ime: "Aktivnost", val: Math.min(100, hitrostKlikov * 25), barva: verz.barvaPoud },
          ].map(m => (
            <div key={m.ime} style={{ background: "rgba(255,255,255,0.04)", borderRadius: 8, padding: "6px 8px" }}>
              <div style={{ fontSize: 10, color: verz.barvaBes + "88", marginBottom: 3 }}>{m.nal} {m.ime}</div>
              <div style={{ height: 3, background: "rgba(255,255,255,0.1)", borderRadius: 2 }}>
                <div style={{ height: "100%", width: `${m.val}%`, background: m.barva, borderRadius: 2, transition: "width 0.5s" }} />
              </div>
            </div>
          ))}
        </div>

        {/* Napredek stopnje */}
        {naslednjaSt && (
          <div style={{ marginBottom: "1rem", background: "rgba(255,255,255,0.03)", borderRadius: 8, padding: "6px 10px" }}>
            <div style={{ display: "flex", justifyContent: "space-between", marginBottom: 4 }}>
              <span style={{ fontSize: 10, color: verz.barvaBes + "88" }}>Pot do: {naslednjaSt.ikona} {naslednjaSt.ime}</span>
              <span style={{ fontSize: 10, color: verz.barvaPoud }}>{napredek}%</span>
            </div>
            <div style={{ height: 3, background: "rgba(255,255,255,0.1)", borderRadius: 2 }}>
              <div style={{ height: "100%", width: `${napredek}%`, background: `linear-gradient(90deg, ${verz.barvaPoud}, ${verz.barvaBes})`, borderRadius: 2, transition: "width 0.5s" }} />
            </div>
          </div>
        )}

        {/* Navigacija */}
        <div style={{ display: "flex", gap: 6, marginBottom: "1rem" }}>
          {[
            { id: "pozdrav", lab: "🌀 Portal" },
            { id: "pogovor", lab: "💬 Varuh", onemog: !izbraniVaruh },
            { id: "profil", lab: "👤 Arhetip" },
          ].map(g => (
            <button key={g.id} disabled={g.onemog} onClick={() => !g.onemog && setZaslonCas(g.id)} style={{
              flex: 1, padding: "6px 0", fontSize: 11, fontWeight: 500,
              background: zaslonCas === g.id ? verz.barvaPoud : "rgba(255,255,255,0.06)",
              color: zaslonCas === g.id ? "#fff" : verz.barvaBes + "cc",
              border: "none", borderRadius: 8, cursor: g.onemog ? "not-allowed" : "pointer",
              opacity: g.onemog ? 0.4 : 1, transition: "all 0.2s",
            }}>{g.lab}</button>
          ))}
        </div>

        {/* ZASLON: POZDRAV / IZBIRA */}
        {zaslonCas === "pozdrav" && (
          <div>
            <div style={{ textAlign: "center", marginBottom: "1.5rem" }}>
              <div style={{ fontSize: 36, marginBottom: 8, animation: "utrip 3s infinite" }}>🌀</div>
              <div style={{ fontSize: 16, fontWeight: 500, color: verz.barvaBes, marginBottom: 4 }}>Izberi svojega Varuha</div>
              <div style={{ fontSize: 12, color: verz.barvaBes + "88" }}>Vsak varuh ima svojo osebnost in pristop</div>
            </div>

            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 8, marginBottom: "1.5rem" }}>
              {Object.entries(VARUHI).map(([id, v]) => (
                <button key={id} onClick={() => setIzbraniVaruh(id === izbraniVaruh ? null : id)} style={{
                  background: izbraniVaruh === id ? v.barva + "33" : "rgba(255,255,255,0.04)",
                  border: `1px solid ${izbraniVaruh === id ? v.barva : "rgba(255,255,255,0.1)"}`,
                  borderRadius: 10, padding: "10px 8px", cursor: "pointer", textAlign: "left", transition: "all 0.2s",
                }}>
                  <div style={{ fontSize: 18, marginBottom: 3 }}>{v.ikona}</div>
                  <div style={{ fontSize: 12, fontWeight: 500, color: v.barva }}>{v.ime}</div>
                  <div style={{ fontSize: 10, color: verz.barvaBes + "77", marginTop: 2, lineHeight: 1.3 }}>{v.osebnost.split(".")[0]}</div>
                </button>
              ))}
            </div>

            {izbraniVaruh && (
              <div>
                <div style={{ marginBottom: "0.75rem" }}>
                  <div style={{ fontSize: 12, color: verz.barvaBes + "88", marginBottom: 6 }}>Izberi arhetip:</div>
                  <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr 1fr", gap: 6 }}>
                    {Object.entries(ARHETIPI).map(([id, a]) => (
                      <button key={id} onClick={() => setIzbraniArhetip(id === izbraniArhetip ? null : id)} style={{
                        background: izbraniArhetip === id ? a.barva + "33" : "rgba(255,255,255,0.04)",
                        border: `1px solid ${izbraniArhetip === id ? a.barva : "rgba(255,255,255,0.08)"}`,
                        borderRadius: 8, padding: "6px 4px", cursor: "pointer", textAlign: "center",
                      }}>
                        <div style={{ fontSize: 16 }}>{a.ikona}</div>
                        <div style={{ fontSize: 9, color: a.barva, marginTop: 2 }}>{a.ime}</div>
                      </button>
                    ))}
                  </div>
                </div>

                {izbraniArhetip && (
                  <button onClick={() => izberiVaruhaInArhetip(izbraniVaruh, izbraniArhetip)} style={{
                    width: "100%", padding: "10px", fontSize: 13, fontWeight: 500,
                    background: `linear-gradient(135deg, ${VARUHI[izbraniVaruh].barva}, ${ARHETIPI[izbraniArhetip].barva})`,
                    color: "#fff", border: "none", borderRadius: 10, cursor: "pointer",
                  }}>
                    {VARUHI[izbraniVaruh].ikona} Vstopi v portal
                  </button>
                )}
              </div>
            )}
          </div>
        )}

        {/* ZASLON: POGOVOR */}
        {zaslonCas === "pogovor" && (
          <div>
            {izbraniVaruh && (
              <div style={{ display: "flex", alignItems: "center", gap: 8, marginBottom: "0.75rem", padding: "8px 10px", background: "rgba(255,255,255,0.04)", borderRadius: 10 }}>
                <div style={{ fontSize: 20 }}>{VARUHI[izbraniVaruh].ikona}</div>
                <div>
                  <div style={{ fontSize: 12, fontWeight: 500, color: VARUHI[izbraniVaruh].barva }}>{VARUHI[izbraniVaruh].ime}</div>
                  {izbraniArhetip && <div style={{ fontSize: 10, color: verz.barvaBes + "77" }}>{ARHETIPI[izbraniArhetip].ikona} {ARHETIPI[izbraniArhetip].ime}</div>}
                </div>
                <div style={{ marginLeft: "auto", fontSize: 10, color: verz.barvaBes + "66" }}>+15 točk / sporočilo</div>
              </div>
            )}

            <div style={{
              height: 280, overflowY: "auto", marginBottom: "0.75rem",
              display: "flex", flexDirection: "column", gap: 10,
            }}>
              {sporocila.map((s, i) => (
                <div key={i} style={{ display: "flex", justifyContent: s.vloga === "user" ? "flex-end" : "flex-start" }}>
                  {s.vloga === "assistant" && (
                    <div style={{ fontSize: 16, marginRight: 6, alignSelf: "flex-end" }}>
                      {izbraniVaruh ? VARUHI[izbraniVaruh].ikona : "🌀"}
                    </div>
                  )}
                  <div style={{
                    maxWidth: "80%", padding: "8px 12px", borderRadius: s.vloga === "user" ? "14px 14px 4px 14px" : "14px 14px 14px 4px",
                    background: s.vloga === "user" ? verz.barvaPoud + "cc" : "rgba(255,255,255,0.08)",
                    color: s.vloga === "user" ? "#fff" : verz.barvaBes,
                    fontSize: 13, lineHeight: 1.5,
                    animation: i === sporocila.length - 1 ? "valovanje 0s" : "none",
                  }}>
                    {s.vsebina}
                  </div>
                </div>
              ))}
              {nalaganje && (
                <div style={{ display: "flex", alignItems: "center", gap: 6 }}>
                  <div style={{ fontSize: 16 }}>{izbraniVaruh ? VARUHI[izbraniVaruh].ikona : "🌀"}</div>
                  <div style={{ padding: "8px 12px", background: "rgba(255,255,255,0.06)", borderRadius: "14px 14px 14px 4px", color: verz.barvaBes + "88", fontSize: 13 }}>
                    <span style={{ animation: "utrip 1s infinite" }}>✦ ✦ ✦</span>
                  </div>
                </div>
              )}
              <div ref={spodnjRef} />
            </div>

            <div style={{ display: "flex", gap: 8 }}>
              <input
                value={vnos}
                onChange={e => setVnos(e.target.value)}
                onKeyDown={e => e.key === "Enter" && !e.shiftKey && posljiSporocilo()}
                placeholder="Govori s svojih varuhom..."
                style={{
                  flex: 1, padding: "9px 12px", fontSize: 13, borderRadius: 10,
                  background: "rgba(255,255,255,0.07)", border: `1px solid ${verz.barvaPoud}44`,
                  color: verz.barvaBes, outline: "none",
                }}
              />
              <button onClick={posljiSporocilo} disabled={!vnos.trim() || nalaganje} style={{
                padding: "9px 14px", fontSize: 13, fontWeight: 500,
                background: verz.barvaPoud, color: "#fff", border: "none", borderRadius: 10,
                cursor: vnos.trim() && !nalaganje ? "pointer" : "not-allowed",
                opacity: vnos.trim() && !nalaganje ? 1 : 0.5,
              }}>→</button>
            </div>
          </div>
        )}

        {/* ZASLON: PROFIL / ARHETIP */}
        {zaslonCas === "profil" && (
          <div>
            <div style={{ textAlign: "center", marginBottom: "1.5rem" }}>
              <div style={{ fontSize: 48, marginBottom: 8 }}>{stopnja.ikona}</div>
              <div style={{ fontSize: 18, fontWeight: 500, color: verz.barvaBes }}>{stopnja.ime}</div>
              <div style={{ fontSize: 12, color: verz.barvaBes + "88" }}>{tocke} točk skupaj</div>
            </div>

            {izbraniArhetip && (
              <div style={{ background: "rgba(255,255,255,0.05)", borderRadius: 12, padding: "12px", marginBottom: "1rem", border: `1px solid ${ARHETIPI[izbraniArhetip].barva}44` }}>
                <div style={{ display: "flex", alignItems: "center", gap: 8, marginBottom: 8 }}>
                  <div style={{ fontSize: 24 }}>{ARHETIPI[izbraniArhetip].ikona}</div>
                  <div>
                    <div style={{ fontSize: 14, fontWeight: 500, color: ARHETIPI[izbraniArhetip].barva }}>{ARHETIPI[izbraniArhetip].ime}</div>
                    <div style={{ fontSize: 11, color: verz.barvaBes + "88" }}>{ARHETIPI[izbraniArhetip].opis}</div>
                  </div>
                </div>
              </div>
            )}

            <div style={{ marginBottom: "1rem" }}>
              <div style={{ fontSize: 11, color: verz.barvaBes + "77", marginBottom: 8 }}>Pot zavesti:</div>
              <div style={{ display: "flex", flexWrap: "wrap", gap: 6 }}>
                {STOPNJE.map(s => (
                  <div key={s.stopnja} style={{
                    padding: "4px 8px", borderRadius: 20, fontSize: 11,
                    background: tocke >= s.tocke ? verz.barvaPoud + "44" : "rgba(255,255,255,0.04)",
                    color: tocke >= s.tocke ? verz.barvaBes : verz.barvaBes + "44",
                    border: `1px solid ${tocke >= s.tocke ? verz.barvaPoud + "66" : "rgba(255,255,255,0.08)"}`,
                  }}>
                    {s.ikona} {s.ime}
                  </div>
                ))}
              </div>
            </div>

            <div style={{ background: "rgba(255,255,255,0.04)", borderRadius: 10, padding: "10px" }}>
              <div style={{ fontSize: 11, color: verz.barvaBes + "88", marginBottom: 6 }}>Zakladnica simbolov:</div>
              <div style={{ display: "flex", gap: 6, flexWrap: "wrap" }}>
                {tocke >= 0 && <span title="Kristal" style={{ fontSize: 20 }}>💎</span>}
                {tocke >= 100 && <span title="Zvezda" style={{ fontSize: 20 }}>⭐</span>}
                {tocke >= 300 && <span title="Runa" style={{ fontSize: 20 }}>🌀</span>}
                {tocke >= 600 && <span title="Portal" style={{ fontSize: 20 }}>🔮</span>}
                {tocke >= 1000 && <span title="Plamen" style={{ fontSize: 20 }}>🔥</span>}
                {tocke < 100 && <span style={{ fontSize: 11, color: verz.barvaBes + "55" }}>Zberi več točk za simbole...</span>}
              </div>
            </div>

            <button onClick={() => { setTocke(t => t + 50); sproziDelce("💎", 10); }} style={{
              marginTop: "0.75rem", width: "100%", padding: "8px", fontSize: 12,
              background: "rgba(255,255,255,0.06)", color: verz.barvaBes + "cc",
              border: `1px solid rgba(255,255,255,0.1)`, borderRadius: 8, cursor: "pointer",
            }}>
              ✨ Meditacija (+50 točk)
            </button>
          </div>
        )}
      </div>
    </div>
  );
}
