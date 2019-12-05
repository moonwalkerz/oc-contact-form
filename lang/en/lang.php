<?php return [
    'plugin' => [
        'name' => 'Contact',
        'description' => 'Simple contact form',
        'manage_settings' => 'Manage contact form'
    ],
    'navigation' => [
        'contacts' =>'Contacts'
    ],
    'contactform' => [
        'name' =>'First and last name',
        'name_placeholder' => 'Insert your name',
        'email' =>'Email',
        'email_placeholder' => 'Insert your email',
        'phone' =>'Phone',
        'phone_placeholder' => 'Insert you phone number',
        'message' =>'Message',
        'message_placeholder'=>'Enter your message here',
        'id' =>  'id',
        'date' => 'Date',
        'contact' => 'Contact?',
        'promotions' => 'Promo?',
        'third_parties' => 'Third parties?'
    ],
    'newsletterform' => [
        'email' =>'Email',
        'email_placeholder' => 'Insert your email'
    ],

    'settings' => [
        'txt_gdpr' =>'Generic GDPR Text',
        'txt_contact' =>'Contact privacy informations',
        'txt_promo' =>'Advertising privacy informations',
        'txt_third_parties' =>'Third parties privacy information',
        'txt_gdpr_comment' =>'Type a generic privacy information',
        'txt_contact_comment' =>'Type the privacy rule about contact',
        'txt_promo_comment' =>'Type the privacy rule about advertising',
        'txt_third_parties_comment' =>'Type the privacy rule about third parties',
        'enabled_gdpr' =>'Enable GDPR rules',
        'enabled_contact' =>'Enable the contact privacy rule',
        'enabled_promo' =>'Enable the advertising privacy rule',
        'enabled_third_parties' =>'Enable the third parties privacy rule',
        'txt_gdpr_default' =>'I have carefully read the privacy policy and expressly accept the privacy statement and all terms in each specific box.
        Attention: not all consents are mandatory! If you prefer to select each individual consent to the privacy policy.',
        'txt_contact_default' =>'I consent to the processing of my personal data to allow _______ contact me in the future with respect to my requests.',
        'txt_contact_error' =>'Without your consent, we cannot reply.',
        'txt_promo_default' =>'I would like to receive e-mail/direct marketing communications in accordance with the Privacy Policy',
        'txt_third_parties_default' =>'I consent to the transfer of my personal data to selected partners of ________ and business partners _______________ to be informed about their future product offers or promotional activities.'


    ],
    'properties' => [
        'emailto'=> 'Email To',
        'emailto_desc' => 'Where we send the email to.',
        'emailtoname' => 'Name',
        'emailtoname_desc' => 'Name of the email to.',
        'subject' =>'Subject of the email',
        'is_phone_requested' => [
            'title' => 'Phone?',
            'description' => 'is phone requested?',
        ],
        'is_phone_mandatory' => [
            'title' => 'Phone mandatory?',
            'description' => 'is phone mandatory?',
        ],
        'is_gdpr_contact_requested' => [
            'title' => 'Enable gdpr checkboxes?',
            'description' => 'enable gdpr checkboxes?',
        ],
        'is_gdpr_promo_requested' => [
            'title' => 'Promotional checkbox?',
            'description' => 'Ask permission to subscribe to a promotional newsletter?',
        ],
        'is_gdpr_third_parties_requested' => [
            'title' => 'Third parties checkbox?',
            'description' => 'Ask permission to share information to third parties?',
        ]
    ]
];