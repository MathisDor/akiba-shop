<?php

return [

    // Days a listing stays online after approval (or renewal).
    'listing_lifetime_days' => 60,

    // A listing can be renewed once it is this close to expiring (or already expired).
    'renewable_within_days' => 7,

    'max_images' => 8,

    // Max upload size per photo, in kilobytes (photos are re-encoded to WebP anyway).
    'max_image_kb' => 8192,

    // Longest side of a stored photo, in pixels — keeps pages light on slow connections.
    'image_max_dimension' => 1280,

    'image_quality' => 72,

    'boost_days' => 7,

    'per_page' => 24,

    /*
     * Single site-wide currency. Prices are stored as whole units (no cents).
     * `symbol_first` puts the symbol before the amount ($ 1 200) instead of after (1 200 €).
     */
    'currency' => [
        'code' => env('MARKETPLACE_CURRENCY', 'EUR'),
        'symbol' => env('MARKETPLACE_CURRENCY_SYMBOL', '€'),
        'symbol_first' => (bool) env('MARKETPLACE_CURRENCY_SYMBOL_FIRST', false),
    ],

    // Pre-selected country in forms and the default dialling code for phone numbers.
    'default_country' => env('MARKETPLACE_DEFAULT_COUNTRY', 'FR'),

    // Shown on /contact when set.
    'contact_email' => env('CONTACT_EMAIL'),
    'contact_whatsapp' => env('CONTACT_WHATSAPP'),

    /*
     * Extra fields per root category (keyed by the root category slug). Values are
     * stored in listings.specs and shown as a key/value list on the listing page.
     * Types: text, number, select.
     */
    'category_fields' => [
        'vehicules' => [
            ['key' => 'marque', 'label' => 'Marque', 'type' => 'text', 'placeholder' => 'Toyota, Hyundai…'],
            ['key' => 'modele', 'label' => 'Modèle', 'type' => 'text', 'placeholder' => 'Corolla, Tucson…'],
            ['key' => 'annee', 'label' => 'Année', 'type' => 'number', 'min' => 1970, 'max' => 2030],
            ['key' => 'kilometrage', 'label' => 'Kilométrage', 'type' => 'number', 'min' => 0, 'max' => 2000000, 'suffix' => 'km'],
            ['key' => 'carburant', 'label' => 'Carburant', 'type' => 'select', 'options' => ['Essence', 'Diesel', 'Hybride', 'Électrique']],
            ['key' => 'boite', 'label' => 'Boîte de vitesses', 'type' => 'select', 'options' => ['Manuelle', 'Automatique']],
        ],
        'immobilier' => [
            ['key' => 'surface', 'label' => 'Surface', 'type' => 'number', 'min' => 1, 'max' => 1000000, 'suffix' => 'm²'],
            ['key' => 'pieces', 'label' => 'Nombre de pièces', 'type' => 'number', 'min' => 1, 'max' => 50],
            ['key' => 'meuble', 'label' => 'Meublé', 'type' => 'select', 'options' => ['Oui', 'Non']],
        ],
        'telephones-electronique' => [
            ['key' => 'marque', 'label' => 'Marque', 'type' => 'text', 'placeholder' => 'Samsung, Apple, Tecno…'],
            ['key' => 'modele', 'label' => 'Modèle', 'type' => 'text'],
        ],
        'mode-beaute' => [
            ['key' => 'taille', 'label' => 'Taille / pointure', 'type' => 'text', 'placeholder' => 'M, 42…'],
        ],
        'emploi-services' => [
            ['key' => 'type_contrat', 'label' => 'Type', 'type' => 'select', 'options' => ['CDI', 'CDD', 'Stage', 'Prestation / freelance', 'Journalier']],
        ],
    ],

];
