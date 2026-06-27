================================================================================
ASTRAMENTALICA — JEZIKOVNI STANDARD v1 (ZAKLENJEN)
================================================================================

1. IDENTITETA SISTEMA
Primarni jezik: slovenščina.
Velja za: poslovno logiko, sistemske funkcije, helperje, komentarje,
CSS razrede, dokumentacijo, log sporočila, interne kontrakte, module,
konfiguracije.

================================================================================
2. IMENA FUNKCIJ
FORMAT: podrocje_akcija()
Primeri: shramba_beri(), seja_obnovi(), cache_pocisti(),
         middleware_registriraj(), odziv_preveri(), vrsta_dodaj()

================================================================================
3. IMENA DATOTEK
male_crke_z_podctajem.php
Primeri: upravljalec_baz.php, odziv.php, kanal_web.php

PREPOVEDANO: manager.php, utils.php, common.php, helper.php

================================================================================
4. IMENA RAZREDOV
PascalCase
Primeri: UpravljalecUporabnikov, SistemRuntime, CacheAdapter

SUFFIXI: *Adapter, *Runtime, *Provider, *Middleware, *Kontrakt, *DTO, *VO

================================================================================
5. IMENA MAP
ROOT: SISTEM/, ADAPTER/, MODULI/
MODULI/[PODROCJA]/ - VELIKE ČRKE (ZEMLJA, NEBO, ETER)
POSAMEZNI MODULI - PascalCase (BotanicaSacra, OrdoSolaris)
OSTALO - male črke (sredstva/, elementi/, protokoli/)

================================================================================
6. CSS STANDARD
CSS RAZREDI: kebab-case, slovensko
Primeri: .glavni-ovoj, .kartica-uporabnika, .modul-aktiven

FILES: reset.css, spremenljivke.css, sloji.css, kartica.css, navigacija.css

================================================================================
7. HEADER STANDARD (vsaka PHP datoteka)
================================================================================

<?php
    /**
     * ============================================================
     * POT: SISTEM/kernel/jedro/05_pravice.php
     * 📅 VERZIJA: v114 (9.6.2026 15:00)
     * ============================================================
     *
     * 🏛️ NIVO: KERNEL
     *
     * 📰 NAMEN:
     *     Upravljanje uporabniških pravic (RBAC).
     *
     * 🔧 JAVNE FUNKCIJE:
     *     - pravice_preveri_vlogo(int $potrebna): bool
     *     - pravice_ima_dovoljenje(string $dovoljenje): bool
     *     - pravice_registriraj_dovoljenje(string $ime, int $vloga): void
     *
     * 📡 ODVISNOSTI:
     *     - SISTEM/kernel/jedro/03_varnost.php
     *     - POT_PODATKI . '/sistem/registri/pravice.json'
     *
     * 🤝 SOODVISNOSTI:
     *     - SISTEM/kernel/jedro/04_seja.php
     *     - SISTEM/kernel/jedro/06_cache.php
     *
     * ⚡ UPORABA:
     *     - Kliče se iz middleware ali neposredno v storitvah.
     *
     * 🚫 PREPOVEDI:
     *     - Brez echo, print_r, var_dump
     *     - Brez die(), exit()
     *     - Brez direktnih poti (uporabi konstante!)
     *     - Brez direktnega branja $_SESSION
     *
     * 📌 STATUS:
     *     Stabilno
     *
     * 📅 ZGODOVINA:
     *     - v114: uskladitev s Header Standard v114
     *     - v113: dodane oznake in jezik
     *     - v112: prva implementacija
     *
     * 👤 AVTOR:
     *     AstraMentalica Mojster
     *
     * 🌐 JEZIK:
     *     sl
     *
     * 🏷️ OZNAKE:
     *     kernel, jedro, pravice, rbac
     * ============================================================
     */
    declare(strict_types=1);

    defined('SISTEM_VARNOST') or die('Direkten dostop ni dovoljen.');

================================================================================
8. PREVODI (angleško → slovensko)
================================================================================

loader → nalagalnik          request → zahteva          response → odziv
router → usmerjevalnik       session → seja             bootstrap → zagon
provider → ponudnik          hook → kavelj              event → dogodek
context → kontekst           logger → dnevnik           queue → vrsta
cache → predpomnilnik        contract → kontrakt        dispatcher → odprava
worker → izvajalec           firewall → pozarni_zid      token → zeton

================================================================================
9. ANGLEŠKO DOVOLJUJEM (brez prevoda)
================================================================================

debug, debugging, monitoring, hash, middleware, pipeline, csrf, jwt

✅ runtime: cache_pocisti(), middleware_registriraj(), pipeline_izvedi()
❌ business: predpomnilnik_pocisti(), ponudnik_nalozi()

================================================================================
10. NAMESPACE STANDARD
================================================================================

namespace AstraMentalica\Kernel;
namespace AstraMentalica\Storitve;
namespace AstraMentalica\Moduli;
namespace AstraMentalica\Adapter;

FILESYSTEM ≠ NAMESPACE
Filesystem: MODULI/ZEMLJA/BotanicaSacra
Namespace:  AstraMentalica\Moduli\Zemlja\BotanicaSacra

================================================================================
11. PREPOVEDANO
================================================================================

❌ magic helperji, doStuff(), utils_final_v2.php
❌ global side-effect bootstrapi, implicit runtime registracije
❌ skrite dependency povezave, "smart" abstrakcije brez potrebe
❌ __DIR__ izven pot.php, relativne poti (../)
❌ echo, var_dump, print_r v CELEM SISTEMU (SISTEM/kernel/, SISTEM/storitve_svetov/, SISTEM/api.php)
✅ Dovoljeno samo v GLOBALNO/render/ za prikaz HTML
❌ Business logika v GLOBALNO/
❌ GLOBALNO direktno vidi MODULI/, UPORABNIKI/, PODATKI/

================================================================================
12. KONČNO PRAVILO
================================================================================

Če odločitev: zmanjša magijo, poveča predvidljivost, izboljša debugging,
zmanjša coupling, poveča modularnost → potem je pravilna.

================================================================================
KONEC JEZIKOVNEGA STANDARDA
================================================================================