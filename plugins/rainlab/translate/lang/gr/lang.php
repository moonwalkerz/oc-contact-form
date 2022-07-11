<?php

return [
    'plugin' => [
        'name' => 'Μετέφρασε',
        'description' => 'Ενεργοποίηση πολύγλωσσων ιστότοπων.',
        'tab' => 'Μετάφραση',
        'manage_locales' => 'Διαχείριση τοπικών ρυθμίσεων',
        'manage_messages' => 'Διαχείριση μηνυμάτων',
    ],
    'locale_picker' => [
        'component_name' => 'Επιλογή τοπικών ρυθμίσεων',
        'component_description' => 'Εμφάνιση αναπτυσσόμενου μενού για επιλογή γλώσσας front-end.',
    ],
    'alternate_hreflang' => [
        'component_name' => 'Εναλλακτικά στοιχεία hrefLang',
        'component_description' => 'Εισαγωγή εναλλακτικών γλωσσών για τη σελίδα ως στοιχεία hreflang',
    ],
    'locale' => [
        'title' => 'Διαχείριση γλώσσας',
        'update_title' => 'Ενημέρωση γλώσσας',
        'create_title' => 'Δημιουργία γλώσσας',
        'select_label' => 'Επιλογή γλώσσας',
        'default_suffix' => 'Προεπιλογή',
        'unset_default' => '":locale" είναι ήδη προεπιλογή και δεν μπορεί να οριστεί ως προεπιλογή.',
        'delete_default' => '":locale" είναι ήδη προεπιλογή και δεν μπορεί να διαγραφεί.',
        'disabled_default' => '":locale" είναι απενεργοποιημένο και δεν μπορεί να οριστεί ως προεπιλογή.',
        'name' => 'Όνομα',
        'code' => 'Κωδικός',
        'is_default' => 'Προεπιλογή',
        'is_default_help' => 'Η προεπιλεγμένη γλώσσα αντιπροσωπεύει το περιεχόμενο πριν από τη μετάφραση.',
        'is_enabled' => 'Ενεργοποίηση',
        'is_enabled_help' => 'Οι απενεργοποιημένες γλώσσες δεν θα είναι διαθέσιμες στο front-end.',
        'not_available_help' => 'Δεν έχουν ρυθμιστεί άλλες γλώσσες.',
        'hint_locales' => 'Δημιουργήστε νέες γλώσσες εδώ για τη μετάφραση περιεχομένου front-end. Η προεπιλεγμένη γλώσσα αντιπροσωπεύει το περιεχόμενο προτού μεταφραστεί.',
        'reorder_title' => 'Αναδιάταξη γλωσσών',
        'sort_order' => 'Σειρά ταξινόμησης',
    ],
    'messages' => [
        'title' => 'Μετάφραση μηνυμάτων',
        'description' => 'Ενημέρωση μηνυμάτων',
        'clear_cache_link' => 'Εκκαθάριση προσωρινής μνήμης',
        'clear_cache_loading' => 'Εκκαθάριση προσωρινής μνήμης εφαρμογής ...',
        'clear_cache_success' => 'Επιτυχής εκκαθάριση της προσωρινής μνήμης της εφαρμογής!',
        'clear_cache_hint' => 'Ίσως χρειαστεί να κάνετε κλικ στην επιλογή <strong>Clear cashe</strong> για να δείτε τις αλλαγές στο front-end.',
        'scan_messages_link' => 'Σάρωση για μηνύματα',
        'scan_messages_begin_scan' => 'Έναρξη σάρωσης',
        'scan_messages_loading' => 'Σάρωση για νέα μηνύματα ...',
        'scan_messages_success' => 'Επιτυχής σάρωση θεματικών πρότυπων αρχείων!',
        'scan_messages_hint' => 'Κάνοντας κλικ στην επιλογή <strong>Scan για messages</strong> θα ελεγθούν τα ενεργά αρχεία θεμάτων για τυχόν νέα μηνύματα που χρειάζονται μετάφραση.',
        'scan_messages_process' => 'Αυτή η διαδικασία θα προσπαθήσει να σαρώσει το ενεργό θέμα για μηνύματα που μπορούν να μεταφραστούν.',
        'scan_messages_process_limitations' => 'Ορισμένα μηνύματα ενδέχεται να μην καταγράφονται και θα εμφανίζονται μόνο μετά την πρώτη φορά που χρησιμοποιούνται.',
        'scan_messages_purge_label' => 'Εκκαθάριση πρώτα όλων των μηνυμάτων',
        'scan_messages_purge_help' => 'Επιλέγοντας εδώ θα διαγραφούν όλα τα μηνύματα, συμπεριλαμβανομένων και των μεταφράσεών τους, πριν από τη σάρωση.',
        'scan_messages_purge_confirm' => 'Είστε βέβαιοι ότι θέλετε να διαγράψετε όλα τα μηνύματα; Αυτό δεν μπορεί να αναιρεθεί!',
        'scan_messages_purge_deleted_label' => 'Εκκαθάριση απολεσθέντων μηνυμάτων μετά τη σάρωση',
        'scan_messages_purge_deleted_help' => 'Επιλέγοντας εδώ, αφού ολοκληρωθεί η σάρωση, τυχόν μηνύματα που δεν βρήκε ο σαρωτής, συμπεριλαμβανομένων και των μεταφράσεών τους, θα διαγραφούν. Αυτό δεν μπορεί να αναιρεθεί!',
        'hint_translate' => 'Εδώ μπορείτε να μεταφράσετε μηνύματα που χρησιμοποιούνται στο front-end, τα πεδία θα αποθηκεύονται αυτόματα.',
        'hide_translated' => 'Απόκρυψη μετάφρασης',
        'export_messages_link' => 'Εξαγωγή μηνυμάτων',
        'import_messages_link' => 'Εισαγωγή μηνυμάτων',
        'not_found' => 'Δεν βρέθηκε',
        'found_help' => 'Εάν τυχόν σφάλματα παρουσιάστηκαν κατά τη σάρωση.',
        'found_title' => 'Σάρωση σφαλμάτων',
    ],
];
