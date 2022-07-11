<?php

return [
    'plugin' => [
        'name' => 'Traductions',
        'description' => 'Permet de créer des sites Internet multilingues',
        'tab' => 'Traduction',
        'manage_locales' => 'Manage locales',
        'manage_messages' => 'Manage messages',
    ],
    'locale_picker' => [
        'component_name' => 'Sélection de la langue',
        'component_description' => 'Affiche un menu déroulant pour sélectionner la langue sur le site.',
    ],
    'alternate_hreflang' => [
        'component_name' => 'Éléments hrefLang alternatifs',
        'component_description' => "Injecte les alternatives linguistiques pour la page en tant qu'éléments hreflang",
    ],
    'locale' => [
        'title' => 'Gestion des langues',
        'update_title' => 'Mettre à jour la langue',
        'create_title' => 'Ajouter une langue',
        'select_label' => 'Sélectionner une langue',
        'default_suffix' => 'défaut',
        'unset_default' => '":locale" est déjà la langue par défaut et ne peut être désactivée',
        'delete_default' => '":locale" est la valeur par défaut et ne peut pas être supprimé.',
        'disabled_default' => '":locale" est désactivé et ne peut être utilisé comme paramètre par défaut.',
        'name' => 'Nom',
        'code' => 'Code',
        'is_default' => 'Défaut',
        'is_default_help' => 'La langue par défaut représente le contenu avant la traduction.',
        'is_enabled' => 'Activé',
        'is_enabled_help' => 'Les langues désactivées ne seront plus disponibles sur le site.',
        'not_available_help' => 'Aucune autre langue n\'est définie.',
        'hint_locales' => 'Vous pouvez ajouter de nouvelles langues et traduire les messages du site. La langue par défaut est celle utilisée pour les contenus avant toute traduction.',
        'reorder_title' => 'Réorganiser les langues',
        'sort_order' => 'Ordre de tri',
    ],
    'messages' => [
        'title' => 'Traduction des Messages',
        'description' => 'Mettre à jour Messages',
        'clear_cache_link' => 'Supprimer le cache',
        'clear_cache_loading' => 'Suppression du cache de l\'application...',
        'clear_cache_success' => 'Le cache de l\'application a été supprimé !',
        'clear_cache_hint' => 'Vous devez cliquer sur <strong>Supprimer le cache</strong> pour voir les modifications sur le site.',
        'scan_messages_link' => 'Rechercher des messages à traduire',
        'scan_messages_begin_scan' => 'Commencer la recherche',
        'scan_messages_loading' => 'Recherche de nouveaux messages...',
        'scan_messages_success' => 'Recherche dans les fichiers du thème effectuée !',
        'scan_messages_hint' => 'Cliquez sur <strong>Rechercher des messages à traduire</strong> pour parcourir les fichiers du thème actif à la recherche de messages à traduire.',
        'scan_messages_process' => 'Ce processus tentera d\'analyser le thème actif pour les messages qui peuvent être traduits.',
        'scan_messages_process_limitations' => 'Certains messages peuvent ne pas être capturés et n\'apparaîtront qu\'après leur première utilisation.',
        'scan_messages_purge_label' => 'Purger tous les messages en premier',
        'scan_messages_purge_help' => 'Si cette case est cochée, tous les messages seront supprimés avant d\'effectuer l\'analyse.',
        'scan_messages_purge_confirm' => 'Êtes-vous sûr de vouloir supprimer tous les messages? Cela ne peut pas être annulé!',
        'hint_translate' => 'Vous pouvez traduire les messages affichés sur le site, les champs s\'enregistrent automatiquement.',
        'hide_translated' => 'Masquer les traductions',
        'export_messages_link' => 'Exporter les messages',
        'import_messages_link' => 'Importer les messages',
    ],
];
