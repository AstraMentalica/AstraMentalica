<?php
/**
 * Orakleum JSON Baza
 * Datoteka: modul_oracle_jsonbaza.php
 * Namen: JSON podatkovna baza z vsemi kartami, pozicijami in interpretacijami
 * Struktura: Vrne array z vsemi podatki za Orakel sistema
 */

return [
    // KARTICE - Glavne tarot karte in orakeljske kartice
    'kartice' => [
        'l_popolnost' => [
            'id' => 'l_popolnost',
            'ime' => 'Popolnost',
            'simbol' => '🌟',
            'opis' => 'Popolnost, harmonija in dosežek vseh ciljev',
            'kratek_opis' => 'Popolnost in harmonija',
            'element' => 'zrak',
            'znamenje' => 'vsem',
            'energija' => 'pozitivna',
            'energija_vrednost' => 8,
            'barva' => '#FFD700',
            'frekvenca' => 528,
            'tema' => 'dosežek',
            'interpretacije' => [
                'preteklost' => 'Uspešno zaključena pomembna faza življenja',
                'sedanjost' => 'Trenutno doseženo popolno ravnovesje',
                'prihodnost' => 'Priložnost za popolnost se približuje',
                'notranjost' => 'Vaša notranja modrost je dosegla vrhunec',
                'zunanjost' => 'Zunanji svet podpira vaše cilje',
                'pot' => 'Vaša pot vodi k popolnosti in harmoniji'
            ],
            'kljucne_besede' => ['popolnost', 'harmonija', 'dosežek', 'ravnovesje', 'uspeh'],
            'simboli' => ['zvezda', 'krog', 'križ'],
            'napotki_za_akcijo' => [
                'Bodite zadovoljni z doseženim',
                'Delite svojo modrost z drugimi',
                'Ohranite ravnovesje v vseh vidikih življenja'
            ]
        ],
        
        'l_ljubezen' => [
            'id' => 'l_ljubezen',
            'ime' => 'Ljubezen',
            'simbol' => '💖',
            'opis' => 'Ljubezen, strast in globoka povezanost',
            'kratek_opis' => 'Ljubezen in strast',
            'element' => 'voda',
            'znamenje' => 'ljubezen',
            'energija' => 'pozitivna',
            'energija_vrednost' => 9,
            'barva' => '#FF69B4',
            'frekvenca' => 432,
            'tema' => 'ljubezen',
            'interpretacije' => [
                'preteklost' => 'Pomembna ljubezenska izkušnja je oblikovala vas',
                'sedanjost' => 'Globoka ljubezenska povezanost v vašem življenju',
                'prihodnost' => 'Nova ljubezenska priložnost se bliža',
                'notranjost' => 'Vaše srce je pripravljeno na ljubezen',
                'zunanjost' => 'Ljubezen prihaja iz nepričakovanih virov',
                'pot' => 'Vaša pot je povezana z ljubeznijo in sočutjem'
            ],
            'kljucne_besede' => ['ljubezen', 'strast', 'povezanost', 'sočutje', 'zaupanje'],
            'simboli' => ['srce', 'rozeta', 'dvojni krog'],
            'napotki_za_akcijo' => [
                'Odprite svoje srce ljubezni',
                'Pokažite sočutje do sebe in drugih',
                'Zaupajte v moč ljubezni'
            ]
        ],
        
        'l_moc' => [
            'id' => 'l_moc',
            'ime' => 'Moč',
            'simbol' => '💪',
            'opis' => 'Moč, notranja energija in samokontrola',
            'kratek_opis' => 'Notranja moč',
            'element' => 'ogenj',
            'znamenje' => 'moč',
            'energija' => 'pozitivna',
            'energija_vrednost' => 7,
            'barva' => '#FF4500',
            'frekvenca' => 741,
            'tema' => 'moč',
            'interpretacije' => [
                'preteklost' => 'Uspešno ste premagali izziv z notranjo močjo',
                'sedanjost' => 'Vaša notranja moč je na vrhuncu',
                'prihodnost' => 'Nova priložnost za prikaz moči se približuje',
                'notranjost' => 'Vaša notranja moč je neomejena',
                'zunanjost' => 'Drugi čutijo vašo notranjo moč',
                'pot' => 'Vaša pot zahteva prikaz notranje moči'
            ],
            'kljucne_besede' => ['moč', 'samokontrola', ' disciplina', 'energija', 'odločnost'],
            'simboli' => ['lev', 'krona', 'sijaj'],
            'napotki_za_akcijo' => [
                'Uporabite svojo notranjo moč za dobre namene',
                'Ohranite samokontrolo v izzivnih situacijah',
                'Verujte v svojo sposobnost'
            ]
        ],
        
        'l_svoboda' => [
            'id' => 'l_svoboda',
            'ime' => 'Svoboda',
            'simbol' => '🕊️',
            'opis' => 'Svoboda, neodvisnost in nova perspektiva',
            'kratek_opis' => 'Svoboda in neodvisnost',
            'element' => 'zrak',
            'znamenje' => 'svoboda',
            'energija' => 'pozitivna',
            'energija_vrednost' => 6,
            'barva' => '#87CEEB',
            'frekvenca' => 963,
            'tema' => 'svoboda',
            'interpretacije' => [
                'preteklost' => 'Oslobodili ste se omejitev iz preteklosti',
                'sedanjost' => 'Trenutno uživate popolno svobodo',
                'prihodnost' => 'Nova stopnja svobode vas čaka',
                'notranjost' => 'Vaša notranja svoboda je ključna',
                'zunanjost' => 'Zunanji svet ponuja nove možnosti',
                'pot' => 'Vaša pot vodi k večji svobodi'
            ],
            'kljucne_besede' => ['svoboda', 'neodvisnost', 'prilagodljivost', 'odprtost', 'gibanje'],
            'simboli' => ['ptica', 'ključ', 'vrata'],
            'napotki_za_akcijo' => [
                'Prevzemite odgovornost za svojo svobodo',
                'Ne dovolite, da vas druge omejujejo',
                'Uživajte v trenutku svobode'
            ]
        ],
        
        'l_mudrost' => [
            'id' => 'l_mudrost',
            'ime' => 'Modrost',
            'simbol' => '🦉',
            'opis' => 'Modrost, znanje in globoko razumevanje',
            'kratek_opis' => 'Modrost in znanje',
            'element' => 'zrak',
            'znamenje' => 'mudrost',
            'energija' => 'pozitivna',
            'energija_vrednost' => 8,
            'barva' => '#8B4513',
            'frekvenca' => 639,
            'tema' => 'mudrost',
            'interpretacije' => [
                'preteklost' => 'Izkušnje so vam prinesle modrost',
                'sedanjost' => 'Vaša modrost je na voljo drugim',
                'prihodnost' => 'Nove priložnosti za učenje se bližajo',
                'notranjost' => 'Vaša notranja modrost je globoka',
                'zunanjost' => 'Modrost pride iz nepričakovanih virov',
                'pot' => 'Vaša pot zahteva uporabo modrosti'
            ],
            'kljucne_besede' => ['modrost', 'znanje', 'razumevanje', 'učenje', 'vizija'],
            'simboli' => ['sova', 'knjiga', 'svetilka'],
            'napotki_za_akcijo' => [
                'Delite svojo modrost z drugimi',
                'Še naprej se učite in raziskujte',
                'Zaupajte svoji notranji modrosti'
            ]
        ],
        
        'l_sprememba' => [
            'id' => 'l_sprememba',
            'ime' => 'Sprememba',
            'simbol' => '🌊',
            'opis' => 'Sprememba, preobrazba in nova smer',
            'kratek_opis' => 'Sprememba in preobrazba',
            'element' => 'voda',
            'znamenje' => 'sprememba',
            'energija' => 'nevtralna',
            'energija_vrednost' => 5,
            'barva' => '#20B2AA',
            'frekvenca' => 528,
            'tema' => 'sprememba',
            'interpretacije' => [
                'preteklost' => 'Velika sprememba je vplivala na vašo pot',
                'sedanjost' => 'Sprememba je na pragu vašega življenja',
                'prihodnost' => 'Pripravite se na pomembno preobrazbo',
                'notranjost' => 'Notranja sprememba je potrebna',
                'zunanjost' => 'Zunanji svet se spreminja okoli vas',
                'pot' => 'Vaša pot zahteva sprejetje sprememb'
            ],
            'kljucne_besede' => ['sprememba', 'preobrazba', 'tok', 'prilagajanje', 'nova smer'],
            'simboli' => ['val', 'spirala', 'puščica'],
            'napotki_za_akcijo' => [
                'Sprejmite spremembo kot priložnost',
                'Prilagajte se toku življenja',
                'Bodite odprti za novo smer'
            ]
        ]
    ],
    
    // POZICIJE - Mesta za različne vrste vlečenja
    'pozicije' => [
        'nakljucno' => [
            'ime' => 'Naključno',
            'opis' => 'Naključna karta za splošen vpogled',
            'energija' => 'nevtralna',
            'casovna_komponenta' => 'trenutno',
            'prioriteta' => 1
        ],
        'preteklost' => [
            'ime' => 'Preteklost',
            'opis' => 'Kar je bilo in oblikovalo sedanje stanje',
            'energija' => 'minula',
            'casovna_komponenta' => 'minulost',
            'prioriteta' => 3
        ],
        'sedanjost' => [
            'ime' => 'Sedanjost',
            'opis' => 'Trenutno stanje in vpliv na prihodnost',
            'energija' => 'aktivna',
            'casovna_komponenta' => 'sedanjost',
            'prioriteta' => 5
        ],
        'prihodnost' => [
            'ime' => 'Prihodnost',
            'opis' => 'Možnosti in priložnosti, ki prihajajo',
            'energija' => 'potencialna',
            'casovna_komponenta' => 'prihodnost',
            'prioriteta' => 4
        ],
        'notranjost' => [
            'ime' => 'Notranjost',
            'opis' => 'Vaš notranji svet, čustva in motivacije',
            'energija' => 'intimna',
            'casovna_komponenta' => 'večen trenutek',
            'prioriteta' => 5
        ],
        'zunanjost' => [
            'ime' => 'Zunanjost',
            'opis' => 'Zunanji vplivi, ljudje in okolje',
            'energija' => 'družbena',
            'casovna_komponenta' => 'zunanji vplivi',
            'prioriteta' => 4
        ],
        'pot' => [
            'ime' => 'Pot',
            'opis' => 'Priporočena smer gibanja in akcija',
            'energija' => 'vodilna',
            'casovna_komponenta' => 'prihodnja pot',
            'prioriteta' => 5
        ]
    ],
    
    // MESTA - Za različne vrste orakljev
    'mesta' => [
        'nakljucno' => [
            'ime' => 'Naključno',
            'kartice' => [], // Prazno pomeni naključen izbor
            'opis' => 'Naključen izbor kartice'
        ],
        'tri_karte' => [
            'ime' => 'Tri Karte',
            'kartice' => ['l_popolnost', 'l_ljubezen', 'l_moc'],
            'opis' => 'Osnovni orakelj s tremi kartami'
        ],
        'ljubezen' => [
            'ime' => 'Ljubezenski Orakelj',
            'kartice' => ['l_ljubezen', 'l_popolnost', 'l_mudrost'],
            'opis' => 'Specializiran orakelj za ljubezenske zadeve'
        ],
        'kariera' => [
            'ime' => 'Karierni Orakelj',
            'kartice' => ['l_moc', 'l_svoboda', 'l_mudrost'],
            'opis' => 'Orakelj za poklicne in karierne odločitve'
        ]
    ],
    
    // ORAKELJI - Prednastavljene kombinacije
    'orakelji' => [
        'tri_karte' => [
            'ime' => 'Tri Karte',
            'opis' => 'Preteklost - Sedanjost - Prihodnost',
            'pozicije' => ['preteklost', 'sedanjost', 'prihodnost'],
            'tip' => 'osnovni',
            'teza' => 0.8
        ],
        'ljubezen' => [
            'ime' => 'Ljubezenski Orakelj',
            'opis' => 'Ljubezen in odnosi',
            'pozicije' => ['ti', 'partner', 'relacija'],
            'tip' => 'specializiran',
            'teza' => 0.9
        ],
        'kariera' => [
            'ime' => 'Karierni Orakelj',
            'opis' => 'Poklicna pot in izzivi',
            'pozicije' => ['sedanjost', 'izziv', 'priložnost'],
            'tip' => 'specializiran',
            'teza' => 0.9
        ],
        'sest_kart' => [
            'ime' => 'Šest Kart',
            'opis' => 'Popolna analiza situacije',
            'pozicije' => ['notranjost', 'zunanjost', 'preteklost', 'sedanjost', 'pot', 'prihodnost'],
            'tip' => 'napredni',
            'teza' => 1.0
        ]
    ],
    
    // STATISTIKE - Sledenje uporabi
    'statistike' => [
        'skupno_vlecenj' => 0,
        'karta_stevilo' => [],
        'vlecenja' => [],
        'zadnja_posodobitev' => date('Y-m-d H:i:s')
    ],
    
    // KONFIGURACIJA - Nastavitve modula
    'konfiguracija' => [
        'ime_modula' => 'Orakleum',
        'verzija' => '1.0.0',
        'aktiviran' => true,
        'debug_mode' => false,
        'cache_enabled' => true,
        'max_vprasanj_na_dan' => 50,
        'permit_anonymous' => true,
        'shrani_statistike' => true,
        'frekvenca_ciscenja' => 30, // dni
        'tip_vlecenja' => 'nakljucno',
        'podpirani_orakelji' => ['tri_karte', 'ljubezen', 'kariera', 'sest_kart'],
        'varnostne_nastavitve' => [
            'rate_limit' => 10, // vlecenj na minuto
            'max_dolzina_vprasanja' => 500,
            'preveri_sql_injection' => true,
            'sanitiziraj_vhode' => true
        ]
    ],
    
    // SPOROCILA - Sporočila in citati
    'sporocila' => [
        'dobrodoslica' => [
            'Dobrodošli v Orakleum - mistični svet kart',
            'Karte čakajo na vaša vprašanja',
            'Modrost kart se bo razkrila'
        ],
        'napotki' => [
            'Bodite odprti za sporočila kart',
            'Zaupajte svoji intuiciji',
            'Karte govorijo resnico če jim verjamete'
        ],
        'zakljucek' => [
            'Hvala, ker ste obiskali Orakleum',
            'Karte vas bodo spremljale na poti',
            'Modrost kart ostaja z vami'
        ],
        'citati' => [
            'Karte so ogledalo duše',
            'V vsaki karti je skrita modrost',
            'Tarot odkriva to, kar že vemo'
        ]
    ],
    
    // ELEMENTI - Dodatne komponente
    'elementi' => [
        'energija' => [
            'pozitivna' => 'Dobra energija, pozitivni vplivi',
            'negativna' => 'Izzivi in ovire, ki jih je treba premagati',
            'nevtralna' => 'Običajna energija, normalen tok'
        ],
        'elementi' => [
            'ogenj' => 'Akcija, strast, energija, moč',
            'voda' => 'Čustva, intuicija, povezanost, tok',
            'zrak' => 'Misli, komunikacija, ideje, svoboda',
            'zemlja' => 'Praktičnost, stabilnost, materialno, varnost'
        ],
        'znamenja' => [
            'moč' => 'Karta vpliva na moč in avtoriteto',
            'ljubezen' => 'Karta vpliva na ljubezen in odnose',
            'kariera' => 'Karta vpliva na poklic in dosežke',
            'zdravje' => 'Karta vpliva na zdravje in vitalnost',
            'svoboda' => 'Karta vpliva na neodvisnost in gibanje',
            'mudrost' => 'Karta vpliva na znanje in razumevanje',
            'vsem' => 'Karta vpliva na vse vidike življenja'
        ]
    ]
];

?>