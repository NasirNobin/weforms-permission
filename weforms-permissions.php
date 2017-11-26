<?php

/**
 * WeForms Permissions
 */
class WeForms_Permissions extends WeForms_Abstract_Integration {

	/**
	 * Construct
	 *
	 * @return void
	 */
	function __construct() {

 		$this->id                = 'permission';
		$this->title             = __( 'Permission', 'weforms' );
		$this->template          = dirname( __FILE__ ) . '/components/template.php';

    	add_action( 'wpuf_contact_form_settings_tab', array( $this, 'add_permission_tab' ));
    	add_action( 'wpuf_contact_form_settings_tab_content', array( $this, 'show_permission_tab_content' ));
    	add_action( 'wp_ajax_weforms_permission_fetch_users', array( $this, 'fetch_users' ) );
    	add_filter( 'weforms_builder_scripts', array( $this, 'enqueue_mixin' ) );
    	add_filter( 'admin_footer', array( $this, 'load_template' ) );
	}

    /**
     * Enqueue the mixin
     *
     * @param $scritps
     *
     * @return array
     */
    public function enqueue_mixin( $scripts ) {

        $scripts['weforms-permission'] = array(
            'src' => plugins_url( 'components/index.js', __FILE__ ),
            'deps' => array( 'wpuf-form-builder-components' )
        );

        return $scripts;
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
               {{ settings }}

               <wpuf-integration-permission></wpuf-integration-permission>
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
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'     => '_wesocial_api_key',
                    'compare' => 'NOT EXISTS'
                ),
            )
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
}
