<?php

/**
 * SilverWare Contact module configuration file
 *
 * @author Colin Tucker <colin@praxis.net.au>
 * @package silverware-contact
 */

// Define Module Directory:

if (!defined('SILVERWARE_CONTACT_DIR')) {
    define('SILVERWARE_CONTACT_DIR', basename(__DIR__));
}

// Define Shortcode Handlers:

ShortcodeParser::get('default')->register(
    'contact_link',
    array(
        'ContactPage',
        'link_shortcode_handler'
    )
);
