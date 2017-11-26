<?php
/**
 * Plugin Name: weForms Permissions
 * Description: The best contact form plugin for WordPress
 * Plugin URI: https://wedevs.com/weforms/
 * Author: Nobin
 * Author URI: https://about.me/Nobin
 * Version: 1.2.2
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: weforms
 * Domain Path: /languages
 */

/**
 * WeForms_Permission_Loader
 */
class WeForms_Permission_Loader {

    function __construct() {
        add_filter( 'weforms_integrations', array( $this, 'register_integration' ) );
    }

    /**
     * Register default integrations
     *
     * @param  array $integrations
     *
     * @return array
     */
    public function register_integration( $integrations ) {

		require_once dirname( __FILE__ ) . '/weforms-permissions.php';

        $integrations = array_merge( $integrations, array( 'WeForms_Permissions' ) );

        return $integrations;
    }
}

new WeForms_Permission_Loader();