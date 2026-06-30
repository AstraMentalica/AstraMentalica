<?php
/**
 * Orakleum Modul - Nastavitve
 * Datoteka: modul_oracle_nastavitve.php
 * Namen: Konfiguracija in nastavitve za Orakleum modul
 */

// Varnostne nastavitve
return [
    
    // OSNOVNE NASTAVITVE
    'ime_modula' => 'Orakleum',
    'verzija' => '1.0.0',
    'opis' => 'Tarot & Orakelji - Mistične karte in interpretacije',
    'aktiviran' => true,
    'debug_mode' => false,
    
    // FUNKCIONALNOST
    'cache_enabled' => true,
    'cache_ttl' => 3600, // 1 ura
    'shrani_statistike' => true,
    'max_vprasanj_na_dan' => 50,
    'permit_anonymous' => true,
    'required_login' => false,
    
    // VARNOST
    'varnostne_nastavitve' => [
        'rate_limit' => 10, // maksimalno vlecenj na minuto
        'max_dolzina_vprasanja' => 500,
        'preveri_sql_injection' => true,
        'sanitiziraj_vhode' => true,
        'zahtevaj_csrf_token' => false, // za anon uporabnike
        'log_vse_zahteve' => true,
        'ban_ip_po_napakah' => 5 // po 5 napaknih zahtevah
    ],
    
    // KARTICE IN ORAKELJI
    'kartice_nastavitve' => [
        'default_kartice' => ['l_popolnost', 'l_ljubezen', 'l_moc', 'l_svoboda', 'l_mudrost'],
        'omogoci_nakljucno_vlecenje' => true,
        'omogoci_ponovno_vlecenje' => false, // enako vprašanje
        'shrani_vlecenja' => true,
        'max_vlecenj_na_sejo' => 20,
        'povprecna_teza_kartice' => 1.0
    ],
    
    'orakelji_nastavitve' => [
        'podpirani_orakelji' => [
            'tri_karte' => [
                'ime' => 'Tri Karte',
                'opis' => 'Preteklost - Sedanjost - Prihodnost',
                'pozicije' => ['preteklost', 'sedanjost', 'prihodnost'],
                'teza' => 1.0
            ],
            'sest_kart' => [
                'ime' => 'Šest Kart',
                'opis' => 'Popolna analiza',
                'pozicije' => ['notranjost', 'zunanjost', 'preteklost', 'sedanjost', 'pot', 'prihodnost'],
                'teza' => 1.2
            ],
            'ljubezen' => [
                'ime' => 'Ljubezen',
                'opis' => 'Ljubezenski orakelj',
                'pozicije' => ['ti', 'partner', 'relacija'],
                'teza' => 1.1
            ],
            'kariera' => [
                'ime' => 'Kariera',
                'opis' => 'Poklicni vpogled',
                'pozicije' => ['sedanjost', 'izziv', 'priložnost', 'pot'],
                'teza' => 1.1
            ]
        ],
        'default_orakelj' => 'tri_karte',
        'max_kart_v_oraklju' => 12,
        'enable_custom_positions' => false
    ],
    
    // INTERPRETACIJE
    'interpretacije_nastavitve' => [
        'generiraj_avtomatsko' => true,
        'uporabi_ai_analizo' => false,
        'shrani_vse_interpretacije' => true,
        'max_dolzina_interpretacije' => 2000,
        'min_dolzina_interpretacije' => 50,
        'dodaj_napotke' => true,
        'dodaj_energetsko_analizo' => true,
        'vkljuci_simbole' => true
    ],
    
    // UPORABNIKI IN DOSTOP
    'uporabniske_nastavitve' => [
        'anonimni_dostop' => true,
        'registracija_zahtevana' => false,
        'premium_funkcije' => [
            'neomejena_vlecenja' => false,
            'detailed_interpretations' => false,
            'historical_readings' => false,
            'custom_readings' => false
        ],
        'session_timeout' => 3600, // 1 ura
        'remember_preferences' => true
    ],
    
    // STATISTIKE IN ANALITIKA
    'statistike_nastavitve' => [
        'shrani_vlecenja' => true,
        'shrani_interpretacije' => true,
        'sledi_uporabnike' => true, // anonimno
        'shrani_ip_naslove' => false,
        'analytics_enabled' => true,
        'report_generation' => 'dnevno',
        'cleanup_old_data' => true,
        'cleanup_days' => 90
    ],
    
    // UI IN PRIKAZ
    'ui_nastavitve' => [
        'tema' => 'mystic',
        'barvna_shema' => 'default',
        'animacije' => true,
        'sound_effects' => false,
        'background_music' => false,
        'loading_animations' => true,
        'card_flip_animation' => true,
        'responsive_design' => true,
        'mobile_optimized' => true
    ],
    
    // NOTIFIKACIJE
    'notifikacije_nastavitve' => [
        'email_notifikacije' => false,
        'push_notifikacije' => false,
        'dnevne_statistike' => false,
        'tedensko_porocilo' => false,
        'nova_karta_obvestilo' => false
    ],
    
    // BACKUP IN VARNOST
    'backup_nastavitve' => [
        'avtomatski_backup' => true,
        'backup_frequency' => 'dnevno',
        'backup_retention' => 30, // dni
        'backup_location' => 'local',
        'encrypt_backups' => true
    ],
    
    // DEBUGGING IN LOGGING
    'debug_nastavitve' => [
        'log_level' => 'INFO', // DEBUG, INFO, WARNING, ERROR
        'log_file' => 'logs/oracle.log',
        'log_errors' => true,
        'log_performance' => false,
        'console_logging' => false
    ],
    
    // API NASTAVITVE
    'api_nastavitve' => [
        'enabled' => true,
        'rate_limit' => 100, // zahtev na uro
        'cors_enabled' => false,
        'authentication_required' => false,
        'api_version' => 'v1',
        'response_format' => 'json'
    ],
    
    // PERFORMANCE
    'performance_nastavitve' => [
        'lazy_loading' => true,
        'image_optimization' => true,
        'css_minification' => true,
        'js_minification' => true,
        'compression_enabled' => true,
        'cdn_usage' => false
    ],
    
    // INTEGRACIJE
    'integracije_nastavitve' => [
        'wordpress_plugin' => false,
        'joomla_extension' => false,
        'drupal_module' => false,
        'external_apis' => [
            'payment_gateway' => false,
            'email_service' => false,
            'social_sharing' => false
        ]
    ],
    
    // NAPREDNE NASTAVITVE
    'napredne_nastavitve' => [
        'ml_enhancements' => false,
        'blockchain_verification' => false,
        'quantum_random' => false,
        'astrology_integration' => false,
        'numerology_integration' => false,
        'i_ching_integration' => false
    ],
    
    // LOKALIZACIJA
    'lokalizacija' => [
        'default_language' => 'sl',
        'podprti_jeziki' => ['sl', 'en', 'de'],
        'timezone' => 'Europe/Ljubljana',
        'date_format' => 'Y-m-d H:i:s',
        'currency' => 'EUR'
    ],
    
    // DEPLOYMENT
    'deployment' => [
        'environment' => 'development', // development, staging, production
        'auto_updates' => false,
        'update_check_frequency' => 'tedensko',
        'rollback_enabled' => true,
        'maintenance_mode' => false
    ],
    
    // KOMPATIBILNOST
    'kompatibilnost' => [
        'php_version_min' => '7.4',
        'php_version_max' => '8.2',
        'mysql_version_min' => '5.7',
        'required_extensions' => ['json', 'curl', 'mbstring'],
        'memory_limit' => '128M',
        'max_execution_time' => 30
    ]
];

?>