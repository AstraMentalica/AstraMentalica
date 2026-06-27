<?php
/**
 * MagicnaKonfiguracija.php - Konfiguracija celotnega sistema Aurora Mystica
 */

return [
    'magicni_sistem' => [
        'stopnje_dostopa' => ['S0', 'S1', 'S2', 'S3', 'S4', 'S5'],
        'magicni_kljuci' => [
            'min_dolzina' => 8,
            'zahtevani_znaki' => ['velike_crke', 'male_crke', 'stevilke'],
            'veljavnost' => 3600
        ],
        'varnostni_mehanizmi' => [
            'max_poskusov_prijave' => 3,
            'dolzina_sea' => 3600,
            'encryption_method' => 'AES-256-GCM',
            'avtomatska_odjava' => 7200
        ],
        'nastavitve_portala' => [
            'ime' => 'Aurora Mystica',
            'opis' => 'Skrivni portal za magične izkušnje',
            'jezik' => 'slovenscina',
            'casovni_pas' => 'Europe/Ljubljana',
            'debug_mode' => true
        ]
    ],
    
    'ai_sistem' => [
        'modeli' => [
            'generator_vsebin' => [
                'ime' => 'MagicContentGenerator',
                'opis' => 'AI za generiranje magičnih vsebin',
                'zmogljivosti' => ['generiranje_zapisov', 'ustvarjanje_ritualov', 'pisanje_pripovedi']
            ],
            'analitik_izkusenj' => [
                'ime' => 'ExperienceAnalyst',
                'opis' => 'AI za analizo uporabniških izkušenj',
                'zmogljivosti' => ['analiza_vedenja', 'napovedovanje_potreb', 'optimizacija_izkusenj']
            ],
            'napovedovalec_dogodkov' => [
                'ime' => 'EventPredictor', 
                'opis' => 'AI za napovedovanje magičnih dogodkov',
                'zmogljivosti' => ['napovedovanje_aktivnosti', 'optimizacija_casa', 'personalizacija_vsebin']
            ]
        ],
        'komunikacija' => [
            'protokol' => 'WebSocket',
            'timeout' => 30,
            'retry_attempts' => 3,
            'encryption' => 'TLS 1.3'
        ],
        'avtonomnost' => [
            'samostojno_razmisljanje' => true,
            'avtomatsko_nadgrajevanje' => true,
            'adaptivno_ucenje' => true
        ]
    ],
    
    '3d_portal' => [
        'scene' => [
            'glavna_dvorana' => [
                'opis' => 'Osrednja dvorana z magičnimi artefakti',
                'velikost' => '100x100m',
                'elementi' => ['misticni_obelisk', 'ognjeni_krog', 'vodni_bazen']
            ],
            'skrivna_soca' => [
                'opis' => 'Skrita soča z redkimi zapisi',
                'velikost' => '50x30m', 
                'elementi' => ['staro_knjiznico', 'alhemisticno_oporocalo', 'kristalno_ogledalo']
            ],
            'nebeski_most' => [
                'opis' => 'Most med svetovi',
                'velikost' => '200x10m',
                'elementi' => ['oblakovna_vrata', 'zvezdna_mreza', 'energijski_tokovi']
            ]
        ],
        'animacije' => ['levitacija', 'transformacija', 'disperzija', 'materializacija'],
        'optimizacije' => [
            'max_polygons' => 100000,
            'texture_size' => '2048x2048', 
            'fps_target' => 60,
            'lod_enabled' => true
        ],
        'uporabniski_vmesnik' => [
            'kontrolniki' => ['tipkovnica', 'miska', 'touch', 'VR_controllers'],
            'dostopnost' => ['podpora_za_slabovidne', 'glasovno_upravljanje']
        ]
    ],
    
    'nagradni_sistem' => [
        'tipi_nagrad' => [
            'del_znanja' => [
                'opis' => 'Fragment starodavne modrosti',
                'redkost' => 'pogosta',
                'vrednost' => 10
            ],
            'povabilo_prijatelju' => [
                'opis' => 'Moc deljenja skrivnosti',
                'redkost' => 'redka', 
                'vrednost' => 25
            ],
            'dvig_razreda' => [
                'opis' => 'Trenutni napredek na poti',
                'redkost' => 'zelo_redka',
                'vrednost' => 50
            ],
            'magicni_predmet' => [
                'opis' => 'Eno uporabo posebnega artefakta',
                'redkost' => 'izjemno_redka',
                'vrednost' => 100
            ]
        ],
        'verjetnosti' => [
            'pogosta_nagrada' => 70,
            'redka_nagrada' => 25,
            'zelo_redka_nagrada' => 5,
            'izjemno_redka_nagrada' => 1
        ],
        'pogoji' => [
            'min_aktivnost' => '5_minut',
            'max_nagrad_na_dan' => 10,
            'zahteve_za_dvig' => ['magicne_tocke', 'aktivnost', 'prispevki']
        ]
    ],
    
    'websocket_sistem' => [
        'nastavitve_streznika' => [
            'port' => 8080,
            'max_povezav' => 1000,
            'timeout' => 300,
            'ssl_enabled' => true
        ],
        'tipi_sporocil' => [
            'magicni_dogodki' => ['prihodnji', 'trenutni', 'zakljuceni'],
            'uporabniske_aktivnosti' => ['prijava', 'odjava', 'interakcija'],
            'sistemska_obvestila' => ['nadgradnje', 'vzdrzevanje', 'opozorila']
        ],
        'varnost' => [
            'avtentikacija_obvezna' => true,
            'encryption' => 'WSS',
            'rate_limiting' => '100_sporocil_na_minuto'
        ]
    ],
    
    'nastavitve_zmogljivosti' => [
        'max_sočasnih_uporabnikov' => 1000,
        'cache_sistem' => 'redis',
        'baza_podatkov' => 'mysql',
        'limiti_pomnilnika' => '512MB',
        'backup_interval' => '24_ur'
    ]
];
?>