<?php
// Nastavi spremenljivke za glavo
$stran_naslov = 'Codex - AstraMentalica';
$aktivni_modul = 'codex';
$dodatni_stili = ['/main/moduli/codex/slog.css'];
$dodatni_skripti = ['/main/moduli/codex/uporaba.js'];

// Vključi config in glavo
include '../../config.php';
include '../../datoteke/skupno/glava.php';
?>

<div class="codex-container">
    <div class="codex-header">
        <h2 class="codex-title">Codex AstraMentalica</h2>
        <p class="codex-subtitle">Živa knjiga modrosti, ki se nenehno razvija</p>
        
        <div class="codex-search">
            <i class="fas fa-search codex-search-icon"></i>
            <input type="text" class="codex-search-input" placeholder="Išči po Codexu...">
        </div>
    </div>
    
    <div class="codex-content">
        <nav class="codex-nav">
            <ul class="codex-nav-list">
                <li class="codex-nav-item"><a href="#" class="codex-nav-link active" data-chapter="uvod"><i class="fas fa-book-open"></i> Uvod</a></li>
                <li class="codex-nav-item"><a href="#" class="codex-nav-link" data-chapter="poglavje1"><i class="fas fa-fire"></i> Ognjena poglavja</a></li>
                <li class="codex-nav-item"><a href="#" class="codex-nav-link" data-chapter="poglavje2"><i class="fas fa-heart"></i> Ljubezenska poglavja</a></li>
                <li class="codex-nav-item"><a href="#" class="codex-nav-link" data-chapter="poglavje3"><i class="fas fa-mountain"></i> Mistična poglavja</a></li>
                <li class="codex-nav-item"><a href="#" class="codex-nav-link" data-chapter="poglavje4"><i class="fas fa-dragon"></i> Modrosti starodavnih</a></li>
            </ul>
        </nav>
        
        <main class="codex-main">
            <div class="codex-chapter" id="uvod">
                <h3 class="codex-chapter-title"><i class="fas fa-book-open"></i> Uvod v Codex</h3>
                <div class="codex-chapter-content">
                    <p>Dobrodošli v Codexu AstraMentalica, živi knjigi modrosti, ki se nenehno razvija in prilagaja potrebam iskalcev resnice. Codex je zbirka znanja, ki zajema starodavno modrost in moderne spoznaje o delovanju vesolja in človeške zavesti.</p>
                    <p>Ta knjiga ni statična - vsak dan dodajamo nova poglavja, razširjamo obstoječa in ustvarjamo povezave med različnimi področji znanja. Codex je organiziran v več tematskih sklopov, vsak osredotočen na drugo področje modrosti.</p>
                    <p>Kot bralec si ne le ogledovalec te modrosti, temveč tudi sodelavec pri njenem razvoju. Svoje vpogledove in izkušnje lahko delite z drugimi člani skupnosti AstraMentalica.</p>
                    <blockquote style="border-left: 4px solid var(--highlight); padding-left: 1rem; margin: 1.5rem 0; font-style: italic;">
                        "Modrost ni v tem, da imaš vse odgovore, temveč v tem, da si pripravljen sproščati vprašanja in se odpirati novim možnostim."
                    </blockquote>
                </div>
            </div>
            
            <div class="codex-chapter" id="poglavje1" style="display: none;">
                <h3 class="codex-chapter-title"><i class="fas fa-fire"></i> Ognjena poglavja</h3>
                <div class="codex-chapter-content">
                    <p>Ognjena poglavja se osredotočajo na energijo, transformacijo in moč volje. Tukaj raziskujemo, kako ogenj kot element vpliva na našo zavest in kako ga lahko uporabimo za osebno rast.</p>
                    <p>Ogenj je element spremembe. Kot tak nas uči, kako se spoprijeti z izgubami, kako spremeniti svojo naravo in kako izkoristiti notranjo moč za doseganje svojih ciljev.</p>
                    <p>V tem sklopu boste našli razprave o:</p>
                    <ul style="list-style: none; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-chevron-right" style="color: var(--highlight); margin-right: 0.5rem;"></i> Transformaciji zavesti skozi osebne izzive</li>
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-chevron-right" style="color: var(--highlight); margin-right: 0.5rem;"></i> Razvijanju notranje moči in volje</li>
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-chevron-right" style="color: var(--highlight); margin-right: 0.5rem;"></i> Uporabi ognja v različnih spiritualnih praksah</li>
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-chevron-right" style="color: var(--highlight); margin-right: 0.5rem;"></i> Povezavi med ognjem in kreativnostjo</li>
                    </ul>
                    <blockquote style="border-left: 4px solid var(--highlight); padding-left: 1rem; margin: 1.5rem 0; font-style: italic;">
                        "Ogenj uničuje le to, kar je za transformacijo pripravljeno. Vse drugo preoblikuje v močnejšo obliko."
                    </blockquote>
                </div>
            </div>
            
            <div class="codex-chapter" id="poglavje2" style="display: none;">
                <h3 class="codex-chapter-title"><i class="fas fa-heart"></i> Ljubezenska poglavja</h3>
                <div class="codex-chapter-content">
                    <p>Ljubezenska poglavja raziskujejo najmočnejšo silo v vesolju - ljubezen. Tukaj preučujemo različne oblike ljubezni, od romantične do nesebične ljubezni do vsega živega.</p>
                    <p>Ljubezen je več kot le čustvo; je stanje bitja in način obstoja. V tem sklopu raziskujemo, kako ljubezen vpliva na našo zavest, odnose in celo na materialno stvarnost.</p>
                    <p>Teme, ki jih obravnavamo v ljubezenskih poglavjih, vključujejo:</p>
                    <ul style="list-style: none; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-chevron-right" style="color: var(--highlight); margin-right: 0.5rem;"></i> Razlike in podobnosti med različnimi vrstami ljubezni</li>
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-chevron-right" style="color: var(--highlight); margin-right: 0.5rem;"></i> Ljubezen kot zdravilna sila</li>
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-chevron-right" style="color: var(--highlight); margin-right: 0.5rem;"></i> Vloga ljubezni v karmičnih odnosih</li>
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-chevron-right" style="color: var(--highlight); margin-right: 0.5rem;"></i> Razvoj nesebične ljubezni (agape)</li>
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-chevron-right" style="color: var(--highlight); margin-right: 0.5rem;"></i> Povezava med ljubeznijo in svetlobo</li>
                    </ul>
                    <blockquote style="border-left: 4px solid var(--highlight); padding-left: 1rem; margin: 1.5rem 0; font-style: italic;">
                        "Ljubezen je jezik, ki ga vesolje govori. Ko se ga nauščiš, razumeš vse."
                    </blockquote>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
// Vključi nogo
include '../../datoteke/skupno/noga.php';