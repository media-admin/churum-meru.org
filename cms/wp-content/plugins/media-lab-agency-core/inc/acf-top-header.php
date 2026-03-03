<?php
/**
 * ACF Field Group: Top Header Contact Info
 * Must be loaded in acf/init hook
 */

if (!defined('ABSPATH')) exit;
/**
 * Register ACF Options Page for Agency Core
 */
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Agency Core Settings',
        'menu_title' => 'Agency Core',
        'menu_slug' => 'agency-core-settings',
        'capability' => 'manage_options',
        'icon_url' => 'dashicons-admin-generic',
        'position' => 2,
        'redirect' => false,
    ));
}


// Register field group in ACF init hook
add_action('acf/init', function() {
    
    // Only register if ACF is active
    if (!function_exists('acf_add_local_field_group')) {
        error_log('Top Header ACF: acf_add_local_field_group not available');
        return;
    }
    
    error_log('Top Header ACF: Registering field group...');
    
    acf_add_local_field_group(array(
        'key' => 'group_top_header',
        'title' => 'Top Header - Kontaktdaten',
        'fields' => array(
            // Enable/Disable
            array(
                'key' => 'field_top_header_enable',
                'label' => 'Top Header anzeigen',
                'name' => 'top_header_enable',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 1,
            ),
            
            // Adresse
            array(
                'key' => 'field_top_header_address',
                'label' => 'Adresse',
                'name' => 'top_header_address',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_address_enable',
                        'label' => 'Adresse anzeigen',
                        'name' => 'enable',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key' => 'field_address_street',
                        'label' => 'Straße & Hausnummer',
                        'name' => 'street',
                        'type' => 'text',
                        'placeholder' => 'Musterstraße 123',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_address_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_address_city',
                        'label' => 'PLZ & Stadt',
                        'name' => 'city',
                        'type' => 'text',
                        'placeholder' => '12345 Musterstadt',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_address_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_address_country',
                        'label' => 'Land (optional)',
                        'name' => 'country',
                        'type' => 'text',
                        'placeholder' => 'Österreich',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_address_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_address_link',
                        'label' => 'Google Maps Link',
                        'name' => 'maps_link',
                        'type' => 'url',
                        'placeholder' => 'https://maps.google.com/...',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_address_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            
            // Öffnungszeiten
            array(
                'key' => 'field_top_header_hours',
                'label' => 'Öffnungszeiten',
                'name' => 'top_header_hours',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_hours_enable',
                        'label' => 'Öffnungszeiten anzeigen',
                        'name' => 'enable',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key' => 'field_hours_text',
                        'label' => 'Öffnungszeiten Text',
                        'name' => 'text',
                        'type' => 'text',
                        'placeholder' => 'Mo-Fr: 9-18 Uhr',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_hours_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            
            // Telefon
            array(
                'key' => 'field_top_header_phone',
                'label' => 'Telefon',
                'name' => 'top_header_phone',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_phone_enable',
                        'label' => 'Telefon anzeigen',
                        'name' => 'enable',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key' => 'field_phone_number',
                        'label' => 'Telefonnummer',
                        'name' => 'number',
                        'type' => 'text',
                        'placeholder' => '+43 123 456789',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_phone_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_phone_display',
                        'label' => 'Anzeige (optional)',
                        'name' => 'display',
                        'type' => 'text',
                        'placeholder' => '(0123) 456789',
                        'instructions' => 'Wenn leer, wird die Telefonnummer verwendet',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_phone_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            
            // E-Mail
            array(
                'key' => 'field_top_header_email',
                'label' => 'E-Mail',
                'name' => 'top_header_email',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_email_enable',
                        'label' => 'E-Mail anzeigen',
                        'name' => 'enable',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key' => 'field_email_address',
                        'label' => 'E-Mail Adresse',
                        'name' => 'address',
                        'type' => 'email',
                        'placeholder' => 'info@beispiel.at',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_email_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            
            // Social Media
            array(
                'key' => 'field_top_header_social',
                'label' => 'Social Media',
                'name' => 'top_header_social',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_social_enable',
                        'label' => 'Social Media anzeigen',
                        'name' => 'enable',
                        'type' => 'true_false',
                        'ui' => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key' => 'field_social_facebook',
                        'label' => 'Facebook',
                        'name' => 'facebook',
                        'type' => 'url',
                        'placeholder' => 'https://facebook.com/...',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_social_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_social_instagram',
                        'label' => 'Instagram',
                        'name' => 'instagram',
                        'type' => 'url',
                        'placeholder' => 'https://instagram.com/...',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_social_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_social_linkedin',
                        'label' => 'LinkedIn',
                        'name' => 'linkedin',
                        'type' => 'url',
                        'placeholder' => 'https://linkedin.com/...',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_social_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_social_twitter',
                        'label' => 'Twitter / X',
                        'name' => 'twitter',
                        'type' => 'url',
                        'placeholder' => 'https://twitter.com/...',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_social_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_social_youtube',
                        'label' => 'YouTube',
                        'name' => 'youtube',
                        'type' => 'url',
                        'placeholder' => 'https://youtube.com/...',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_social_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_social_xing',
                        'label' => 'Xing',
                        'name' => 'xing',
                        'type' => 'url',
                        'placeholder' => 'https://xing.com/...',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_social_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            
            // Styling Options
            array(
                'key' => 'field_top_header_style',
                'label' => 'Styling',
                'name' => 'top_header_style',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_style_background',
                        'label' => 'Hintergrundfarbe',
                        'name' => 'background',
                        'type' => 'select',
                        'choices' => array(
                            'primary' => 'Primary Color',
                            'dark' => 'Dunkel',
                            'light' => 'Hell',
                        ),
                        'default_value' => 'primary',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_style_mobile',
                        'label' => 'Mobile Verhalten',
                        'name' => 'mobile',
                        'type' => 'select',
                        'choices' => array(
                            'show' => 'Immer anzeigen',
                            'hide' => 'Auf Mobile ausblenden',
                            'toggle' => 'Mit Toggle-Button',
                        ),
                        'default_value' => 'toggle',
                        'ui' => 1,
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ));
    
    error_log('Top Header ACF: Field group registered successfully!');
    
}, 20); // Priority 20 to ensure options page is created first
