<?php
/**
 * ============================================================
 * POT: MODULI/ORAKLEUM/Orakleum/podatki/vrste_branj.php
 * 📅 VERZIJA: v1.0.0 (19.6.2026)
 * ============================================================
 *
 * 🏛️ NIVO: MODUL / PODATKI
 *
 * 📰 NAMEN:
 *     Statični podatki — seznam vseh vrst intuitivnih branj,
 *     njihovi opisi in priporočena donacija.
 *
 * 🔧 JAVNE FUNKCIJE:
 *     - orakleum_vrste_branj(): array
 *     - orakleum_vrsta_branja(string $oznaka): ?array
 *
 * 📌 STATUS:
 *     Stabilno
 *
 * 👤 AVTOR:
 *     AstraMentalica Mojster
 *
 * 🌐 JEZIK:
 *     sl
 *
 * 🏷️ OZNAKE:
 *     modul, orakleum, podatki, branja, cenik
 * ============================================================
 */

declare(strict_types=1);

function orakleum_vrste_branj(): array {
    static $branja = null;

    if ($branja !== null) {
        return $branja;
    }

    $branja = [
        [
            'oznaka'   => 'hitro',
            'ikona'    => '⚡',
            'naslov'   => 'Hitro vprašanje',
            'opis'     => 'Kratek in jasen odgovor na eno ključno vprašanje, ki te trenutno zaposluje. Branje je hitro, osredotočeno in intuitivno naravnano na trenutno energijo tvojega vprašanja. Uporablja se simbolni tarot in orakelj za jasno sliko zdajšnjega toka.',
            'donacija' => 9,
        ],
        [
            'oznaka'   => 'ljubezen',
            'ikona'    => '💌',
            'naslov'   => 'Ljubezensko branje',
            'opis'     => 'Poglobljeno čutenje in analiza partnerske dinamike. Razkrivajo se čustvene blokade, karmične vezi, nezavedni vzorci in skupna prihodnost. Uporablja se ljubezenski orakelj, senca, arhetipi in energijska povezava med partnerjema.',
            'donacija' => 18,
        ],
        [
            'oznaka'   => 'kariera',
            'ikona'    => '💼',
            'naslov'   => 'Kariera / Finance',
            'opis'     => 'Vpogled v tvojo karierno pot, finančne odločitve in energetsko stanje obilja. Uporabljeni sistemi vključujejo numerologijo, denarni tok, poslovni tarot in manifestacijske energije.',
            'donacija' => 18,
        ],
        [
            'oznaka'   => 'senca',
            'ikona'    => '🌑',
            'naslov'   => 'Senca & notranje blokade',
            'opis'     => 'Branje, ki razkrije tvoje notranje sence – potlačene dele, prepričanja iz otroštva, karmične ponovitve in blokade, ki te držijo nazaj. Uporablja se Shadow Work orakelj, energijsko zdravljenje, osebni vzorci in svetlobni ključi.',
            'donacija' => 18,
        ],
        [
            'oznaka'   => 'boginje',
            'ikona'    => '🌿',
            'naslov'   => 'Zmaji, boginje, vile',
            'opis'     => 'Povezava z višjimi energijami zaščitnikov in naravnih sil. Zmaji prinašajo moč, vile subtilne spremembe, boginje pa notranjo preobrazbo. Branje temelji na večih orakljih in arhetipskem branju tvoje trenutne podpore.',
            'donacija' => 18,
        ],
        [
            'oznaka'   => 'karma',
            'ikona'    => '🌌',
            'naslov'   => 'Dušna pot & karma',
            'opis'     => 'Odkrij, zakaj si tukaj, kaj te vodi in katere karmične niti te še zadržujejo. V branju se odprejo tvoji dušni darovi, nevidni vzorci in karmične preizkušnje. Oraklji duše, arhetipi in karmični tok se povežejo v tvojo življenjsko matriko.',
            'donacija' => 27,
        ],
        [
            'oznaka'   => 'pretekla',
            'ikona'    => '🔁',
            'naslov'   => 'Pretekla življenja',
            'opis'     => 'Branje razkriva pretekle inkarnacije, odnose, ki jih že poznaš, in lekcije, ki se ponavljajo. S pomočjo orakljev preteklih življenj in podzavestnih simbolov odpremo dušni spomin in odvežemo karmične vozle.',
            'donacija' => 27,
        ],
        [
            'oznaka'   => 'energetsko',
            'ikona'    => '🔋',
            'naslov'   => 'Energetska diagnostika',
            'opis'     => 'Vpogled v tvoje energetsko polje, blokade v čakrah, tok življenjske energije in notranjo pretočnost. Uporabljeni so energijski vpogledi, intuitivna telesna diagnostika in oraklji za zdravljenje in pretočnost.',
            'donacija' => 27,
        ],
        [
            'oznaka'   => 'pdf',
            'ikona'    => '📜',
            'naslov'   => 'Mesečni PDF vodnik',
            'opis'     => 'Obsežno vodstvo za ves mesec: ljubezen, duhovnost, zdravje in prehodi. Branje vključuje 12 kart, simbolno runo meseca, podporne afirmacije, časovna obdobja in PDF poročilo s celostno razlago tednov.',
            'donacija' => 33,
        ],
        [
            'oznaka'   => 'dušni_par',
            'ikona'    => '💞',
            'naslov'   => 'Dušni odnosi',
            'opis'     => 'Branje za vse vrste odnosov – sorodne duše, karmični partnerji, dušni pari. Razkrije se vez med osebama, lekcije, skupna rast, časovna dinamika in energijska prihodnost. Posebna pozornost je namenjena povezavam dušnih poti.',
            'donacija' => 33,
        ],
        [
            'oznaka'   => 'astro',
            'ikona'    => '🪐',
            'naslov'   => 'Astro-numerološki vpogled',
            'opis'     => 'Poglobljeno branje tvoje rojstne kode: astrološki znaki, numerološke vibracije, karmične lekcije, dušni arhetipi. Prejmeš izračune, razlage in globlje razumevanje svojih ciklov in potencialov. Idealno za tiste, ki želijo celosten vpogled vase.',
            'donacija' => 42,
        ],
        [
            'oznaka'   => 'svetovanje',
            'ikona'    => '🗣️',
            'naslov'   => 'Zoom svetovanje',
            'opis'     => 'Osebno intuitivno svetovanje v živo preko Zooma. Branje v realnem času, z vodenim vpogledom, pogovorom, kartami in odgovori na več vprašanj. Priporočljivo za tiste, ki želijo direktno podporo in skupen prostor z razlago.',
            'donacija' => 42,
        ],
        [
            'oznaka'   => 'celostno',
            'ikona'    => '🌀',
            'naslov'   => 'Celostno intuitivno branje',
            'opis'     => 'Najobsežnejše branje. Uporabimo vse oraklje, energijska orodja, intuitivne vzorce, karte preteklih življenj, senc, arhetipov in simbolov. Prejmeš PDF poročilo, glasovno razlago in dušni kompas za trenutni preboj.',
            'donacija' => 54,
        ],
    ];

    return $branja;
}

function orakleum_vrsta_branja(string $oznaka): ?array {
    foreach (orakleum_vrste_branj() as $branje) {
        if ($branje['oznaka'] === $oznaka) {
            return $branje;
        }
    }
    return null;
}
