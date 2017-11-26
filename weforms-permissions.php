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
 * WeForms Permissions
 */
class WeForms_Permissions {

	/**
	 * Construct
	 *
	 * @return void
	 */
	function __construct() {
    	add_action( 'weforms_loaded', array( $this, 'plugin_init' ) );
	}

	/**
     * Initializes the WeForms_Permissions() class
     *
     * Checks for an existing WeForms_Permissions() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

	/**
	 * Plugin Init
	 *
	 * @return void
	 **/
	function plugin_init() {
		add_action( 'wpuf_contact_form_settings_tab', array( $this, 'add_permission_tab' ));
    	add_action( 'wpuf_contact_form_settings_tab_content', array( $this, 'show_permission_tab_content' ));
    	add_action( 'wp_ajax_weforms_permission_fetch_users', array( $this, 'fetch_users' ) );
    	add_filter( 'weforms_builder_scripts', array( $this, 'enqueue_mixin' ) );
    	add_filter( 'weforms_admin_styles', array( $this, 'enqueue_styles' ) );
    	add_filter( 'weforms_is_submission_open', array( $this, 'permission_check' ), 10, 3 );
	}

    /**
     * Enqueue the mixin
     *
     * @param $scritps
     *
     * @return array
     */
    public function enqueue_mixin( $scripts ) {

        $scripts['vue-multiselect'] = array(
            'src' => plugins_url( 'assets/js/vue-multiselect.min.js', __FILE__ ),
            'deps' => array( 'wpuf-form-builder-components' )
        );

        $scripts['weforms-permission'] = array(
            'src' => plugins_url( 'components/index.js', __FILE__ ),
            'deps' => array( 'wpuf-form-builder-components','vue-multiselect' )
        );

        return $scripts;
    }

    /**
     * Enqueue the styles
     *
     * @param $styles
     *
     * @return array
     */
    public function enqueue_styles( $styles ) {

        $styles['vue-multiselect-css'] = array(
            'src' => plugins_url( 'assets/css/vue-multiselect.min.css', __FILE__ ),
            'deps' => array( )
        );

        return $styles;
    }

    /**
     * Add permission to to weForms settings
     *
     * @return void
     */
    function add_permission_tab( ) {
	    ?>
	 		<a href="#" :class="['nav-tab', isActiveSettingsTab( 'permission' ) ? 'nav-tab-active' : '']" v-on:click.prevent="makeActiveSettingsTab( 'permission' )" class="nav-tab"><?php _e( 'Permission Settings', 'weforms' ); ?></a>
	    <?php
    }


    /**
     * Show permission tab contents
     *
     * @return void
     */
    function show_permission_tab_content( ) {
    	?>
 			<div id="wpuf-metabox-settings-permission" class="tab-content" v-show="isActiveSettingsTab('permission')">
 				<weforms-integration-permission :settings="settings" inline-template>
 					<?php include __DIR__ . '/components/template.php' ; ?>
 				</weforms-integration-permission>
            </div>
    	<?php
    }


    /**
     * Get all users from wordpress
     *
     * @return void
     */
    public function fetch_users() {

        $search = !empty( $_REQUEST['search'] ) ? $_REQUEST['search'] : '';

        $args = array (
            'orderby' => 'display_name',
            'order'   => 'ASC',
            'number'  => 10,
        );

        if ( $search ) {
            $args['search']         = $search . '*';
            $args['search_columns'] = array( 'user_login' );
        } else {
            $args['role__in'] = array( 'Administrator', 'Editor');
        }

        $query  = new WP_User_Query( $args );
        $users  = $query->get_results();
        $result = array();

        foreach ( $users  as $key => $user) {
            $result[] = array(
                'id'    => $user->data->ID,
                'name'  => $user->data->display_name,
                'email' => $user->data->user_email,
            );
        }

        wp_send_json_success($result);
    }

    /**
     * Permission check before form does render
     *
     * @param boolen $is_open
     * @param array $settings
     * @param object $form
     *
     * @return boolen|WP_Error
     */
    function permission_check( $is_open, $settings, $form) {

		if ( ! isset( $settings['restrict_mood'] ) || ! $settings['restrict_mood'] ) {
			return $is_open;
		}

		$allowed_users = isset( $settings['allowed_users'] ) ? $settings['allowed_users'] : array();
		$allowed_ids   = array_column( $allowed_users, 'id' );

        if ( $allowed_ids && ! in_array( get_current_user_id(), $allowed_ids)) {
            return new WP_Error( 'no-permission', __( 'You don\'t have the right permission to view this form.', 'weforms' ) );
        }

        return $is_open;
    }
}


WeForms_Permissions::init();
