<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Analytics (GA4 / GTM)
    |--------------------------------------------------------------------------
    |
    | Analytics csak akkor töltődik, ha enabled=true és van mérési azonosító.
    | A böngészőben Consent Mode v2 alapból denied; GA csak elfogadás után
    | kap granted állapotot (lásd cookie-consent komponens).
    |
    */

    'analytics' => [
        // Állítsd true-ra élesben, és add meg a GA4 vagy GTM azonosítót.
        // A mérőkód csak cookie-elfogadás után töltődik (Consent Mode v2).
        'enabled' => (bool) env('ANALYTICS_ENABLED', false),
        'ga4_id' => env('GOOGLE_ANALYTICS_ID'),
        'gtm_id' => env('GOOGLE_TAG_MANAGER_ID'),
    ],

    'privacy_url' => env('PRIVACY_POLICY_URL', '/oldal/adatkezelesi-tajekoztato'),
    'cookie_policy_url' => env('COOKIE_POLICY_URL', '/oldal/adatkezelesi-tajekoztato'),

];
