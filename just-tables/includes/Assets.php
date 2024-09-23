<?php
/**
 * JustTables Assets.
 *
 * @since 1.0.0
 */

namespace JustTables;

/**
 * Assets class.
 */
class Assets {

	/**
	 * Assets constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_assets' ) );
	}

    /**
     * Asset version.
	 *
	 * Generate asset version.
	 *
	 * @since 1.0.0
	 *
	 * @return string Generated asset version.
     */
    protected function asset_version( $asset = array() ) {
        $version = JUST_TABLES_VERSION;

        if ( isset( $asset['version'] ) ) {
            $version = $asset['version'];
        } elseif ( isset( $asset['src'] ) ) {
            $file_url = $asset['src'];
            $file_path = realpath( str_replace( JUST_TABLES_ASSETS, JUST_TABLES_ASSETS_PATH, $file_url ) );

            if ( file_exists( $file_path ) ) {
                $version = $version . '-' . filemtime( $file_path );
            }
        }

        return $version;
    }

	/**
	 * Get frontend styles.
	 *
	 * Get all frontend stylesheet (style) file of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of frontend stylesheet (style) files.
	 */
	protected function get_frontend_styles() {
        $styles = array();

        $styles['datatables'] = array(
            'src' => JUST_TABLES_ASSETS . '/css/datatables.bundle.min.css',
        );

        if ( $this->debug_mode() ) {
            $styles['jtpt-icon'] = array(
                'src' => JUST_TABLES_ASSETS . '/css/jtpt-icon.css',
            );

            $styles['jtpt-frontend-base'] = array(
                'src' => JUST_TABLES_ASSETS . '/css/jtpt-frontend-base.css',
                'deps' => array( 'datatables', 'jtpt-icon' ),
            );
        } else {
            $styles['jtpt-frontend-bundle'] = array(
                'src' => JUST_TABLES_ASSETS . '/css/jtpt-frontend-bundle.min.css',
                'deps' => array( 'datatables' ),
            );
        }

        return $styles;
	}

	/**
	 * Get frontend scripts.
	 *
	 * Get all frontend JavaScript (script) file of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of frontend JavaScript (script) files.
	 */
	protected function get_frontend_scripts() {
        $scripts = array();

        $scripts['datatables'] = array(
            'src' => JUST_TABLES_ASSETS . '/js/datatables.bundle.min.js',
            'deps' => array( 'jquery' ),
        );

        if ( $this->debug_mode() ) {
            $scripts['jtpt-frontend-base'] = array(
                'src' => JUST_TABLES_ASSETS . '/js/jtpt-frontend-base.js',
                'deps' => array( 'jquery', 'datatables' ),
            );

            $scripts['jtpt-frontend-ajax'] = array(
                'src' => JUST_TABLES_ASSETS . '/js/jtpt-frontend-ajax.js',
                'deps' => array( 'jtpt-frontend-base' ),
            );
        } else {
            $scripts['jtpt-frontend-bundle'] = array(
                'src' => JUST_TABLES_ASSETS . '/js/jtpt-frontend-bundle.min.js',
                'deps' => array( 'jquery', 'datatables' ),
            );
        }

        return $scripts;
	}

    /**
     * Get frontend localize_data.
     */
    protected function get_frontend_localize_data() {
        $elementor_editor_mode = ( ( class_exists( '\Elementor\Plugin' ) && ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) ) ? true : false );

        $localize_data = array(
            'ajax_url'              => admin_url( 'admin-ajax.php' ),
            'ajax_nonce'            => wp_create_nonce( 'jtpt-ajax-nonce' ),
            'elementor_editor_mode' => $elementor_editor_mode,
        );

        return $localize_data;
    }

	/**
	 * Register frontend assets.
	 *
	 * @since 1.0.0
	 */
	public function register_frontend_assets() {
		// Styles.
		$styles = $this->get_frontend_styles();

		foreach ( $styles as $handle => $style ) {
			$style_deps = isset( $style['deps'] ) ? $style['deps'] : array();
			$style_version = $this->asset_version( $style );

			wp_register_style( $handle, $style['src'], $style_deps, $style_version );
		}

		// Scripts.
		$scripts = $this->get_frontend_scripts();

		foreach ( $scripts as $handle => $script ) {
			$script_deps = isset( $script['deps'] ) ? $script['deps'] : array();
			$script_version = $this->asset_version( $script );
			$in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : true;

			wp_register_script( $handle, $script['src'], $script_deps, $script_version, $in_footer );
		}

        // Localize script.
        if ( $this->debug_mode() ) {
            wp_localize_script( 'jtpt-frontend-base', 'jtpt_data', $this->get_frontend_localize_data() );
        } else {
            wp_localize_script( 'jtpt-frontend-bundle', 'jtpt_data', $this->get_frontend_localize_data() );
        }
	}

	/**
	 * Get admin styles.
	 *
	 * Get all admin stylesheet (style) file of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of admin stylesheet (style) files.
	 */
	protected function get_admin_styles() {
        $styles = array();

        if ( $this->debug_mode() ) {
            $styles['jtpt-admin-base'] = array(
                'src' => JUST_TABLES_ASSETS . '/css/jtpt-admin-base.css',
				'deps' => array( 'csf' ),
            );
        } else {
            $styles['jtpt-admin-bundle'] = array(
                'src' => JUST_TABLES_ASSETS . '/css/jtpt-admin-bundle.min.css',
				'deps' => array( 'csf' ),
            );
        }

        return $styles;
	}

	/**
	 * Get admin scripts.
	 *
	 * Get all admin JavaScript (script) file of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of admin JavaScript (script) files.
	 */
	protected function get_admin_scripts() {
        $scripts = array();

        if ( $this->debug_mode() ) {
            $scripts['jtpt-admin-base'] = array(
                'src' => JUST_TABLES_ASSETS . '/js/jtpt-admin-base.js',
                'deps' => array( 'jquery', 'csf' ),
            );
        } else {
            $scripts['jtpt-admin-bundle'] = array(
                'src' => JUST_TABLES_ASSETS . '/js/jtpt-admin-bundle.min.js',
                'deps' => array( 'jquery', 'csf' ),
            );
        }

        return $scripts;
	}

	/**
	 * Register admin assets.
	 *
	 * @since 1.0.0
	 */
	public function register_admin_assets() {
		// Styles.
		$styles = $this->get_admin_styles();

		foreach ( $styles as $handle => $style ) {
			$style_deps = isset( $style['deps'] ) ? $style['deps'] : array();
			$style_version = $this->asset_version( $style );

			wp_register_style( $handle, $style['src'], $style_deps, $style_version );
		}

		// Scripts.
		$scripts = $this->get_admin_scripts();

		foreach ( $scripts as $handle => $script ) {
			$script_deps = isset( $script['deps'] ) ? $script['deps'] : array();
			$script_version = $this->asset_version( $script );
			$in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : true;

			wp_register_script( $handle, $script['src'], $script_deps, $script_version, $in_footer );
		}
	}

    /**
     * Debug mode.
     */
    private function debug_mode() {
        return ( ( defined( 'SCRIPT_DEBUG' ) && ( true === rest_sanitize_boolean( SCRIPT_DEBUG ) ) ) ? true : false );
    }

}