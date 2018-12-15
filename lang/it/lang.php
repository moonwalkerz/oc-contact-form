<?php return [
    'plugin' => [
        'name' => 'Contatto',
        'description' => 'Semplice modulo di contatto',
        'manage_settings' => 'Impostazioni del modulo di contatto'
    ],
    'navigation' => [
        'contacts' =>'Contatti'
    ],
    'contactform' => [
        'name' =>'Nome e cognome',
        'name_placeholder' => 'Inserisci il tuo nome e cognome',
        'email' =>'Email',
        'email_placeholder' => 'Inserisci la tua email',
        'phone' =>'Telefono',
        'phone_placeholder' => 'Inserisci il numero di telefono',
        'message' =>'Messaggio',
        'message_placeholder'=>'Scrivi qui il tuo messaggio',
        'id' =>  'id',
        'date' => 'Data',
        'contact' => 'Contattabile',
        'promotions' => 'Promozioni',
        'third_parties' => 'Terzi'
    ],
    'settings' => [
        'txt_gdpr' =>'Testo generico GDPR',
        'txt_contact' =>'Informativa di contatto',
        'txt_promo' =>'Informativa sulle promozioni',
        'txt_third_parties' =>'Informativa terze parti',
        'txt_gdpr_comment' =>'Inserisci un testo generico relativo al GDPR',
        'txt_contact_comment' =>'Inserisci l\'informativa per essere ricontattati',
        'txt_promo_comment' =>'Inserisci l\'informativa relativa alle promozioni',
        'txt_third_parties_comment' =>'Informativa nel caso di comunicazione a terze parti',
        'enabled_gdpr' =>'Attiva informative GDPR',
        'enabled_contact' =>'Attiva l\'informativa per essere ricontattati',
        'enabled_promo' =>'Attiva l\'informativa relativa alle promozioni',
        'enabled_third_parties' =>'Attiva l\'informativa nel caso di comunicazione a terze parti',
        'txt_gdpr_default' =>'Ho letto attentamente l\'informativa sulla privacy e accetto espressamente l\'informativa e tutte le clausole in ogni specifico box.
        Attenzione: non tutti i consensi sono obbligatori! Qualora preferisca selezionare ogni singolo consenso dell\'informativa privacy.',
        'txt_contact_default' =>'Acconsento al trattamento dei miei dati personali per consentire a _______  di contattarmi  in futuro relativamente alle mie richieste.',
        'txt_contact_error' =>'Senza il tuo consenso non possiamo risponderti.',
        'txt_promo_default' =>'Desidero ricevere e-mail/comunicazioni di marketing diretto in conformità con la Privacy Policy.',
        'txt_third_parties_default' =>'Acconsento al trasferimento dei miei dati personali a partners selezionati di ________ e ai partner commerciali di _______________ per essere informato circa loro future offerte di prodotti o attività promozionali in essere.'

    ],
    'properties' => [
        'emailto'=> 'Email a',
        'emailto_desc' => 'A chi viene spedita.',
        'emailtoname' => 'Nome',
        'emailtoname_desc' => 'Nome di chi riceve.',
        'subject' =>'Oggetto dell\'email',
        'is_phone_requested' => [
            'title' => 'Telefono?',
            'description' => 'è richiesto il n° di telefono?',
        ],
        'is_phone_mandatory' => [
            'title' => 'Telefono obbligatorio?',
            'description' => 'è obbligatorio il telefono?',
        ],
        'is_gdpr_enabled' => [
            'title' => 'GDPR?',
            'description' => 'Abilito il flag del GDPR?',
        ],
        'enable_gdpr_promo' => [
            'title' => 'Promozioni?',
            'description' => 'Chiedo la possibilità di mandare promozioni o newslette?',
        ],
        'enable_gdpr_third' => [
            'title' => 'Terzi?',
            'description' => 'Chiedo la possibilità di comunicare dati a terzi?',
        ]
    ]
];