<?php

return [
    'plugin' => [
        'name' => 'Translate',
        'description' => 'Настройки мультиязычности сайта.',
        'tab' => 'Перевод',
        'manage_locales' => 'Управление языками',
        'manage_messages' => 'Управление сообщениями',
    ],
    'locale_picker' => [
        'component_name' => 'Выбор языка',
        'component_description' => 'Просмотр списка языков интерфейса.',
    ],
    'alternate_hreflang' => [
        'component_name' => 'Альтернативные элементы hrefLang',
        'component_description' => 'Внедряет языковые альтернативы для страницы в качестве элементов hreflang',
    ],
    'locale' => [
        'title' => 'Управление языками',
        'update_title' => 'Обновить язык',
        'create_title' => 'Создать язык',
        'select_label' => 'Выбрать язык',
        'default_suffix' => 'По умолчанию',
        'unset_default' => '":locale" уже установлен как язык по умолчанию.',
        'delete_default' => '":locale" используется по умолчанию и не может быть удален.',
        'disabled_default' => '":locale" отключен и не может быть использован как язык по умолчанию.',
        'name' => 'Название',
        'code' => 'Код',
        'is_default' => 'По умолчанию',
        'is_default_help' => 'Использовать этот язык, как язык по умолчанию.',
        'is_enabled' => 'Включено',
        'is_enabled_help' => 'Сделать язык доступным в интерфейсе сайта.',
        'not_available_help' => 'Нет настроек других языков.',
        'hint_locales' => 'Создание новых переводов содержимого интерфейса сайта.',
        'reorder_title' => 'Изменить порядок языков',
        'sort_order' => 'Порядок сортировки',
    ],
    'messages' => [
        'title' => 'Перевод сообщений',
        'description' => 'Перевод статических сообщений в шаблоне',
        'clear_cache_link' => 'Очистить кэш',
        'clear_cache_loading' => 'Очистка кэша приложения...',
        'clear_cache_success' => 'Очистка кэша завершена успешно!',
        'clear_cache_hint' => 'Используйте кнопку <strong>Очистить кэш</strong>, чтобы увидеть изменения в интерфейсе сайта.',
        'scan_messages_link' => 'Сканирование сообщений',
        'scan_messages_begin_scan' => 'Начать сканирование',
        'scan_messages_loading' => 'Сканирование наличия новых сообщений...',
        'scan_messages_success' => 'Сканирование файлов шаблона темы успешно завершено!',
        'scan_messages_hint' => 'Используйте кнопку <strong>Сканирование сообщений</strong> для поиска новых ключей перевода активной темы интерфейса сайта.',
        'scan_messages_process' => 'Этот процесс попытается отсканировать активную тему для сообщений, которые можно перевести.',
        'scan_messages_process_limitations' => 'Некоторые сообщения могут не быть отсканированы и появлятся только после первого использования.',
        'scan_messages_purge_label' => 'Сначала очистить все сообщения',
        'scan_messages_purge_help' => 'Если этот флажок установлен, это приведет к удалению всех сообщений перед выполнением сканирования.',
        'scan_messages_purge_confirm' => 'Вы действительно хотите удалить все сообщения? Операция не может быть отменена!',
        'hint_translate' => 'Здесь вы можете переводить сообщения, которые используются в интерфейсе сайта.',
        'hide_translated' => 'Скрыть перевод',
        'export_messages_link' => 'Экспорт сообщений',
        'import_messages_link' => 'Импорт сообщений',
    ],
];
