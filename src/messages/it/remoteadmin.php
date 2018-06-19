<?php

return [
    
// 1.0.0

    'model_id' => 'ID',
    'model_site_token' => 'Token remoto',
    'model_site_url' => 'URL',
    'model_site_auth_is_enabled' => 'Login abilitato',
    'model_site_auth_user' => 'Login User',
    'model_site_auth_pass' => 'Login Password',
    'model_site_off' => 'Off',
    'model_site_on' => 'On',
    'status_index_heading' => 'Overview sui siti remoti',
    'status_index_intro' => 'Versione attuale LUYA: <a href="https://packagist.org/packages/luyadev/luya-core" target="_blank"><strong>{version}</strong></a>, rilasciata il {date}.',
    'status_index_caching_info' => 'I dati remoti saranno in cache per <strong>15 minutes</strong>. Puoi usare il bottone cache-reload per refreshare l\'intera cache della pagina.',
    'status_index_time_info' => '* Time: ritorna il tempo totale trascorso dall\'inzio della richiesta sull\'applicazione remota. Indica la velocità dell\'applicazione, non il tempo intercorso per fare la richiesta remota.',
    'status_index_error_text' => 'Se la richiesta alla pagina remota ritorna un errore, la richiesta potrebbe aver avuto i seguenti problemi:',
    'status_index_error_1' => 'Il sito web richiesto è protetto da un\'autorizzazione httpauth, puoi aggiungere le credenziali httpauth nelle configurazioni della pagina.',
    'status_index_error_2' => 'L\'url del sito web richiesto è sbagliato o non è più valido. Assicurati che l\'url è corretto e che ci sia il protocollo (https/http).',
    'status_index_error_3' => 'Il token relativo al sito web richiesto non è definito nelle configurazioni del sito, oppure potresti aver inserito il token in modo errato.',
    'status_index_table_error' => 'Impossibile recuperare i dati dalla pagina remota.',
    'status_index_column_time' => 'Time',
    'status_index_column_transferexception' => 'Eccezioni di trasferimento',
    'status_index_column_onlineadmin' => 'Amministratori online',

// 1.1.0

    'message_defaulttext' => "Hello\n\nWe made a technical update to your website {{domain}} on {{timestamp}}. Should you experience any problems in spite of our tests, please let us know.\n\nBest regards",
    'message_defaulttext_title' => 'System template',
    'message_text_label' => 'Email Message',
    'message_sent_success' => 'The message has been sent to all recipients successfully',
    'message_modal_title' => 'Send message to {{messageModalData.safeUrl}}',
    'message_modal_recipients' => 'Recipient(s): {{ messageModalData.recipient }}',
    'message_modal_submit_label' => 'Send message',
    'message_modal_history_recipients' => 'To: {{log.recipients}}',
    'package_modal_title' => 'Package details: {{packageModalData.safeUrl}}',
    'package_modal_column_package' => 'Package',
    'package_modal_column_installed' => 'Installed version',
    'package_modal_column_latest' => 'Latest version',
    'package_modal_column_released_time' => 'Released at',
    'package_modal_column_packagist_button' => 'Packagist infos',
    'composer_vendor_timestamp' => 'Composer vendor timestamp',
    'model_billing_product_name' => 'Name',
    'model_billing_product_month_cycle' => 'Month cycle',
    'model_billing_product_price' => 'Price',
    'model_message_log_site_id' => 'Site',
    'model_message_log_timestamp' => 'Timestamp',
    'model_message_log_recipients' => 'Recipients',
    'model_message_log_text' => 'Message text',
    'model_message_template_title' => 'Name',
    'model_message_template_text' => 'Message text',
    'model_message_template_subject' => 'E-Mail Subject',
    'model_message_template_is_default' => 'Default message template',
    'model_message_template_text_hint' => 'The email text which will be sent to the user, Markdown is enabled by default. You can use {{timestamp}} and {{domain}} variables to customize the message.',
    'model_site_recipient' => 'Message recipients',
    'model_site_last_message_timestamp' => 'Last message timestamp',
    'model_site_is_deleted' => 'Is deleted',
    'model_site_billing_start_timestamp' => 'Billing period start',
    'model_site_status' => 'Status',
    'model_site_adminBillingProducts' => 'Product subscriptions',
    'model_site_auto_update_message' => 'Auto send messages',
    'model_site_recipient_hint' => 'A comma-separated list of email addresses lets you send the message to multiple recipients.',
    'model_site_billing_start_timestamp_hint' => 'If you would like to use the billing overview, you should set the timestamp when the billing cycle starts.',
    'model_site_auto_update_message_hint' => 'If enabled and the cronjob for auto message is setup, the remote admin will send the default or customized message to the configured list of recipients when the website\'s composer timestamp has changed.',
    'model_site_status_1' => 'Production',
    'model_site_status_2' => 'Development',
    'model_site_status_3' => 'Pre-production',
    'model_site_status_4' => 'End-of-life',
    'model_site_billing_product_billing_product_id' => 'Product',
    'model_site_billing_product_site_id' => 'Site',
    'message_subject' => 'Website update',
    'message_subject_label' => 'E-Mail-Subject',
];
