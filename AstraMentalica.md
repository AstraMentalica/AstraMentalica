🌌 AstraMentalica — Pregled projekta
AstraMentalica je modularna mistično-duhovna platforma, zgrajena v PHP-ju, namenjena astrologiji, numerologiji, tarotu, meditaciji in sorodnim temam.

🏗️ Arhitektura
Sistem je zasnovan kot strogo modularna večplastna arhitektura:
UPORABNIK → GLOBALNO (frontend) → ADAPTER → SISTEM (jedro) → BAZA
                                     ↕
                              MODULI (50+ samostojnih app)
Vsak modul komunicira z ostalim sistemom izključno prek centralnega mosta (Modul_Bridge) in ne ve za druge module — čista modularnost.

📁 Ključne mape
MapaNamenADAPTER/Prevajalnik med zunanjim svetom in jedrom (middleware, CORS, auth, rate-limit)AI/AI pot + varnostno sidroASTRA/Auth + upravljanjeGLOBALNO/Frontend (layout, JS, CSS, zvoki, vmesnik)MODULI/50+ samostojnih modulovPODATKI/JSON/SQLite/MySQL baze, cache, logi, registriSISTEM/Backend jedro (varnost, baza, avtentikacija, kodiranje)UPORABNIKI/Prijava, registracija, profil, VIP passportVSEBINA/FAQ, gradiva, pravila, ponudba

🧩 Moduli (50+)
Nekateri izmed modulov: Tarot, Stelaris (astrologija), Numyra (numerologija), Orakleum, Codex, Chakrarium, Kabbaloria, Runaris, Somnaris (sanje), Mystaia (mistična trgovina), Synera, VibraMystica, NASA/SenzornasaNasa, Transmutaria in mnogi drugi.

⚙️ Tehnični sklop

Backend: PHP 8+ z strict_types, PDO, singleton pattern, RBAC vloge (gost → S0–S5 → admin)
Varnost: CSRF zaščita, AES šifriranje, JWT žetoni, IP blacklist, prepared statements
AI: Integracija z OpenAI, Anthropic, Google (API Hub), DNA samooptimizirajoči sistem
Frontend: Dinamične teme (glede na čas + lunine faze), glasovni vnos, AI asistent (AstraGPT), audio sistem s frekvenčnim generatorjem
Verzija: v117 (18.6.2026)