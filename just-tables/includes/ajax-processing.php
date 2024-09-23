<?php
/**
 * JustTables ajax processing.
 *
 * All functions of ajax processing of the plugin.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Ajax generate table body rows.
 *
 * @since 1.0.0
 */
function jtpt_ajax_generate_table_body_rows() {
    check_ajax_referer('jtpt-ajax-nonce', 'nonce');

    $request = ( isset( $_POST ) && is_array( $_POST ) ) ? just_tables_clean( $_POST ) : array();

    $query_args                  = isset( $request['query_args'] ) ? just_tables_clean( $request['query_args'] ) : array();
    $taxonomy_query_include_args = isset( $request['taxonomy_query_include_args'] ) ? just_tables_clean( $request['taxonomy_query_include_args'] ) : array();
    $taxonomy_query_exclude_args = isset( $request['taxonomy_query_exclude_args'] ) ? just_tables_clean( $request['taxonomy_query_exclude_args'] ) : array();
    $active_columns              = isset( $request['active_columns'] ) ? just_tables_clean( $request['active_columns'] ) : array();
    $active_columns_id           = isset( $request['active_columns_id'] ) ? just_tables_clean( $request['active_columns_id'] ) : array();
    $element_configuration       = isset( $request['element_configuration'] ) ? just_tables_clean( $request['element_configuration'] ) : array();
    $woocommerce_settings        = isset( $request['woocommerce_settings'] ) ? just_tables_clean( $request['woocommerce_settings'] ) : array();

    $draw           = isset( $request['draw'] ) ? absint( $request['draw'] ) : 0;
    $offset         = isset( $request['start'] ) ? absint( $request['start'] ) : 0;
    $search         = isset( $request['search'] ) ? just_tables_clean( $request['search'] ) : array();
    $search_keyword = ( is_array( $search ) && isset( $search['value'] ) ) ? sanitize_text_field( $search['value'] ) : '';

    if ( is_array( $query_args ) && is_array( $active_columns ) && is_array( $active_columns_id ) && is_array( $element_configuration ) && is_array( $woocommerce_settings ) ) {
        if ( ! empty( $offset ) ) {
            $query_args['offset'] = $offset;
        }

        if ( ! empty( $search_keyword ) ) {
            $query_args['s'] = $search_keyword;
        }

        $taxonomy_query_include_args = is_array( $taxonomy_query_include_args ) ? array_values( $taxonomy_query_include_args ) : array();
        $taxonomy_query_exclude_args = is_array( $taxonomy_query_exclude_args ) ? array_values( $taxonomy_query_exclude_args ) : array();

        if ( ! empty( $taxonomy_query_include_args ) || ! empty( $taxonomy_query_exclude_args ) ) {
            $query_args['tax_query'] = array_merge( $taxonomy_query_include_args, $taxonomy_query_exclude_args );
        }

        $queried_data = just_tables_get_queried_data( $query_args, $active_columns, $active_columns_id, $element_configuration, $woocommerce_settings );
    } else {
        $queried_data = array();
    }

    $total_products = 0;
    $table_body_rows = array();

    if ( is_array( $queried_data ) && ! empty( $queried_data ) ) {
        $total_products = isset( $queried_data['total_products'] ) ? absint( $queried_data['total_products'] ) : 0;
        $table_body_rows = ( isset( $queried_data['table_body_rows'] ) && is_array( $queried_data['table_body_rows'] ) ) ? $queried_data['table_body_rows'] : array();
    }

    $response = array(
        'draw'            => $draw,
        'recordsTotal'    => $total_products,
        'recordsFiltered' => $total_products,
        'data'            => $table_body_rows,
    );

    wp_send_json( $response );
}
add_action( 'wp_ajax_jtpt_ajax_generate_table_body_rows', 'jtpt_ajax_generate_table_body_rows' );
add_action( 'wp_ajax_nopriv_jtpt_ajax_generate_table_body_rows', 'jtpt_ajax_generate_table_body_rows' );