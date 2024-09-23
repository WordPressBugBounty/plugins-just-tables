<?php
/**
 * JustTables Admin.
 *
 * @since 1.0.0
 */

namespace JustTables;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Admin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		new Admin\Post_Types();
		new Admin\Metabox();
		new Admin\Posts_Columns();
		new Admin\JustTables_Trial();

		// Admin assets hook into action.
		add_action( 'admin_head', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 201 );
        add_action( 'admin_footer', [ $this, 'enqueue_admin_head_scripts'], 11 );
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_assets() {
		$current_screen = get_current_screen();

		if ( ( 'post' === $current_screen->base ) && ( 'jt-product-table' === $current_screen->post_type ) ) {
            wp_enqueue_style( 'wp-jquery-ui-dialog' );
            wp_enqueue_script( 'jquery-ui-dialog' );

            if ( defined( 'SCRIPT_DEBUG' ) && ( true === rest_sanitize_boolean( SCRIPT_DEBUG ) ) ) {
                wp_enqueue_style( 'jtpt-admin-base' );
			    wp_enqueue_script( 'jtpt-admin-base' );
            } else {
                wp_enqueue_style( 'jtpt-admin-bundle' );
			    wp_enqueue_script( 'jtpt-admin-bundle' );
            }
		}
	}

	/**
	 * Admin Menu.
	 *
	 * @since 1.5.6
	 */
	public function admin_menu() {
		add_submenu_page(
            'edit.php?post_type=jt-product-table',
            __('Upgrade to Pro', 'just-tables'),
            __('Upgrade to Pro', 'just-tables'),
            'manage_options',
            'https://hasthemes.com/plugins/justtables-woocommerce-product-table/?utm_source=admin&utm_medium=mainmenu&utm_campaign=free#jt-pricing'
        );
	}
    function enqueue_admin_head_scripts() {
		printf( '<style>%s</style>', '#adminmenu #menu-posts-jt-product-table a.justtables-upgrade-pro { font-weight: 600; background-color: #ff6e30; color: #ffffff; text-align: left; margin-top: 5px;}' );
		printf( '<script>%s</script>', '(function ($) {
            $("#menu-posts-jt-product-table .wp-submenu a").each(function() {
                if($(this)[0].href === "https://hasthemes.com/plugins/justtables-woocommerce-product-table/?utm_source=admin&utm_medium=mainmenu&utm_campaign=free#jt-pricing") {
                    $(this).addClass("justtables-upgrade-pro").attr("target", "_blank");
                }
            })
        })(jQuery);' );
    }

}