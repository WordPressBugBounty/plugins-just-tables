<?php
/**
 * JustTables ajax actions.
 *
 * All functions of ajax action of the plugin.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Ajax WooCommerce notices.
 *
 * @since 1.0.0
 */
function jtpt_ajax_woocommerce_notices() {
    check_ajax_referer('jtpt-ajax-nonce', 'nonce');

    wc_print_notices();
    wp_die();
}
add_action( 'wp_ajax_jtpt_ajax_woocommerce_notices', 'jtpt_ajax_woocommerce_notices' );
add_action( 'wp_ajax_nopriv_jtpt_ajax_woocommerce_notices', 'jtpt_ajax_woocommerce_notices' );

/**
 * Ajax wrong quantity notices.
 *
 * @since 1.0.0
 */
function jtpt_ajax_wrong_quantity_notice() {
    check_ajax_referer('jtpt-ajax-nonce', 'nonce');

    if ( ! isset( $_POST['product_id'] ) ) {
        return;
    }

    $product_id    = absint( $_POST['product_id'] );
    $product_title = get_the_title( $product_id );
    $variation_id  = ! empty( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
    $variation     = ! empty( $_POST['variation'] ) ? just_tables_clean( $_POST['variation'] ) : array();
    $quantity      = absint( $_POST['quantity'] );
    $max_quantity  = intval( $_POST['max_quantity'] );

    if ( ! empty( $variation_id ) && is_array( $variation ) && ! empty( $variation ) ) {
        $product_title .= ' - ' . ucwords( implode( ', ', $variation ) );
    }

    if ( 0 >= $quantity ) {
        $notice = sprintf(
            /* translators: %s: Product title */
            esc_html__( 'You cannot add that amount of "%s" to the cart.', 'just-tables' ),
            esc_html( $product_title )
        );
    } elseif ( 0 === $max_quantity ) {
        $notice = sprintf(
            /* translators: %s: Product title */
            esc_html__( 'You cannot add "%s" to the cart because the product is out of stock.', 'just-tables' ),
            esc_html( $product_title )
        );
    } else {
        $notice = sprintf(
            /* translators: 1: Product title 2: Product stock amount */
            esc_html__( 'You cannot add that amount of "%1$s" to the cart because there is not enough stock (%2$s remaining).', 'just-tables' ),
            esc_html( $product_title ),
            esc_html( $max_quantity )
        );
    }

    printf( '<div class="woocommerce-error" role="alert">%s</div>', wp_kses_post($notice) );

    wp_die();
}
add_action( 'wp_ajax_jtpt_ajax_wrong_quantity_notice', 'jtpt_ajax_wrong_quantity_notice' );
add_action( 'wp_ajax_nopriv_jtpt_ajax_wrong_quantity_notice', 'jtpt_ajax_wrong_quantity_notice' );

/**
 * Ajax variation selection needed notices.
 *
 * @since 1.0.0
 */
function jtpt_ajax_variation_selection_needed_notice() {
    check_ajax_referer('jtpt-ajax-nonce', 'nonce');

    if ( ! isset( $_POST['product_id'] ) ) {
        return;
    }

    $product_id    = absint( $_POST['product_id'] );
    $product_title = get_the_title( $product_id );

    $notice = sprintf(
        /* translators: %s: Product title */
        esc_html__( 'Please choose a right combination of "%s".', 'just-tables' ),
        esc_html( $product_title )
    );

    printf( '<div class="woocommerce-error" role="alert">%s</div>', wp_kses_post($notice) );

    wp_die();
}
add_action( 'wp_ajax_jtpt_ajax_variation_selection_needed_notice', 'jtpt_ajax_variation_selection_needed_notice' );
add_action( 'wp_ajax_nopriv_jtpt_ajax_variation_selection_needed_notice', 'jtpt_ajax_variation_selection_needed_notice' );

/**
 * Ajax disable checkbox notices.
 *
 * @since 1.0.0
 */
function jtpt_ajax_disable_checkbox_notice() {
    check_ajax_referer('jtpt-ajax-nonce', 'nonce');

    if ( ! isset( $_POST['product_id'] ) ) {
        return;
    }

    $product_id    = absint( $_POST['product_id'] );
    $product_title = get_the_title( $product_id );
    $product_type  = sanitize_key( $_POST['product_type'] );

    if ( 'external' === $product_type ) {
        $notice = sprintf(
            /* translators: 1: Product title 2: Product type */
            esc_html__( 'You cannot add "%1$s" to the cart because it is an %2$s product.', 'just-tables' ),
            esc_html( $product_title ),
            esc_html( $product_type )
        );
    } else {
        $notice = sprintf(
            /* translators: 1: Product title 2: Product type */
            esc_html__( 'You cannot add "%1$s" to the list because it is a %2$s product.', 'just-tables' ),
            esc_html( $product_title ),
            esc_html( $product_type )
        );
    }

    printf( '<div class="woocommerce-error" role="alert">%s</div>', wp_kses_post($notice) );

    wp_die();
}
add_action( 'wp_ajax_jtpt_ajax_disable_checkbox_notice', 'jtpt_ajax_disable_checkbox_notice' );
add_action( 'wp_ajax_nopriv_jtpt_ajax_disable_checkbox_notice', 'jtpt_ajax_disable_checkbox_notice' );

/**
 * Ajax product selection needed notices.
 *
 * @since 1.0.0
 */
function jtpt_ajax_products_selection_needed_notice() {
    check_ajax_referer('jtpt-ajax-nonce', 'nonce');

    if ( ! isset( $_POST['product_count'] ) ) {
        return;
    }

    $notice = esc_html__( 'Please select one or more product using checkbox to add to the cart.', 'just-tables' );

    printf( '<div class="woocommerce-error" role="alert">%s</div>', wp_kses_post($notice) );

    wp_die();
}
add_action( 'wp_ajax_jtpt_ajax_products_selection_needed_notice', 'jtpt_ajax_products_selection_needed_notice' );
add_action( 'wp_ajax_nopriv_jtpt_ajax_products_selection_needed_notice', 'jtpt_ajax_products_selection_needed_notice' );

/**
 * Ajax add to cart.
 *
 * @since 1.0.0
 */
function jtpt_ajax_woocommerce_add_to_cart() {
    check_ajax_referer('jtpt-ajax-nonce', 'nonce');

    if ( ! isset( $_POST['product_id'] ) || ! isset( $_POST['table_id'] ) ) {
        return;
    }

    $table_id          = absint( $_POST['table_id'] );
    $product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
    $product_title     = get_the_title( $product_id );
    $quantity          = ! empty( $_POST['quantity'] ) ? wc_stock_amount( absint( $_POST['quantity'] ) ) : 1;
    $product_status    = get_post_status( $product_id );
    $variation_id      = ! empty( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
    $variation         = ! empty( $_POST['variation'] ) ? just_tables_clean( $_POST['variation'] ) : array();
    $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation );
    $cart_page_url     = wc_get_cart_url();

    if ( ! empty( $variation_id ) && is_array( $variation ) && ! empty( $variation ) ) {
        $product_title .= ' - ' . ucwords( implode( ', ', $variation ) );
    }

    $table_options = get_post_meta( $table_id, '_jt_product_table_options', true );
    $table_options = ( is_array( $table_options ) ? $table_options : array() );

    $added_to_cart_notice = true;
    $added_to_cart_notice_text = sprintf( 
        /* translators: %1$s: Product Title, %2$s: View Cart Link */
        esc_html__( '"%1$s" has been added to your cart. %2$s', 'just-tables' ), '_PRODUCT_TITLE_', '_VIEW_CART_LINK_' );

    if ( isset( $table_options['added-to-cart-notice'] ) ) {
        $added_to_cart_notice = rest_sanitize_boolean( $table_options['added-to-cart-notice'] );
    }

    if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {
        do_action( 'woocommerce_ajax_added_to_cart', $product_id );

        if ( $added_to_cart_notice ) {
            $added_to_cart_notice_text = str_replace(
                array(
                    '_PRODUCT_TITLE_',
                    '_VIEW_CART_LINK_',
                ),
                array(
                    esc_html( $product_title ),
                    '<a href="' . esc_url( $cart_page_url ) . '">' . esc_html__( 'View Cart', 'just-tables' ) . '</a>',
                ),
                $added_to_cart_notice_text
            );

            wc_add_notice( $added_to_cart_notice_text );
        }
    }

    WC_AJAX::get_refreshed_fragments();

    wp_die();
}
add_action( 'wp_ajax_jtpt_ajax_woocommerce_add_to_cart', 'jtpt_ajax_woocommerce_add_to_cart' );
add_action( 'wp_ajax_nopriv_jtpt_ajax_woocommerce_add_to_cart', 'jtpt_ajax_woocommerce_add_to_cart' );