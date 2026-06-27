<?php
/**
 * Nastavitve zunanjih API-jev za Stelaris astrologijo
 * Konfiguracija za vse zunanje storitve
 */
return [
    'api_efemerid' => [
        'url_osnova' => 'https://api.efemeride.com/v1/',
        'tajni_kljuc' => 'tvoj_api_kljuc'
    ],
    'api_openai' => [
        'url_osnova' => 'https://api.openai.com/v1/',
        'tajni_kljuc' => 'tvoj_openai_kljuc'
    ]
];