<?php
/**
 * JustTables Frontend.
 *
 * @since 1.0.0
 */

namespace JustTables;

/**
 * Frontend class.
 */
class Frontend {

	/**
     * Frontend constructor.
     *
     * @since 1.0.0
     */
    function __construct() {
        new Frontend\Shortcode();

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
    }

    /**
     * Enqueue frontend assets.
     *
     * @since 1.3.0
     */
    public function enqueue_frontend_assets() {
        global $post;

        $enqueue = false;

        if ( ( is_object( $post ) && isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'JT_Product_Table' ) || ( false !== strpos( $post->post_content, 'jtpt-product-table' ) ) ) ) || ( class_exists( '\Elementor\Plugin' ) && ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) ) ) {
            $enqueue = true;
        }

        $enqueue = apply_filters( 'just_tables_enqueue_frontend_assets', $enqueue );
        $enqueue = rest_sanitize_boolean( $enqueue );

        if ( true === $enqueue ) {
            just_tables_add_assets();
        }
    }

}