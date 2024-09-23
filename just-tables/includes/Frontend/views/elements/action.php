<?php
// Action column elements.
$action_column_elements = array( 'wishlist', 'add-to-cart', 'variations' );

// Column element html
$column_element_html = '';

// Add to cart button.
$atc_button_type = isset( $atc_button_opt['type'] ) ? $atc_button_opt['type'] : '';
$small_atc_button_icon = isset( $atc_button_opt['small-icon'] ) ? $atc_button_opt['small-icon'] : 'cart';
$large_atc_button_icon = isset( $atc_button_opt['large-icon'] ) ? $atc_button_opt['large-icon'] : 'cart';
$large_atc_button_text = isset( $atc_button_opt['large-text'] ) ? $atc_button_opt['large-text'] : '';

$atc_button_url = $product_permalink;
$atc_button_class = 'jtpt-add-to-cart jtpt-add-to-cart-button-' . $atc_button_type;
$atc_button_icon = '';
$atc_button_text = '';

if ( 'grouped' !== $product_type ) {
	if ( 'external' === $product_type ) {
		$atc_button_url = $product->add_to_cart_url();
	} elseif ( 'variable' === $product_type ) {
		$atc_button_class .= ' jtpt-variation-selection-needed';
	} else {
		$atc_button_class .= ' jtpt-ajax-add-to-cart';
	}
}

if ( 'small' === $atc_button_type ) {
	$atc_button_icon = '<i class="jtpt-icon jtpt-icon-shopping-' . esc_attr( $small_atc_button_icon ) . '"></i>';
} elseif ( 'large' === $atc_button_type && 'none' !== $large_atc_button_icon ) {
	$atc_button_icon = '<i class="jtpt-icon jtpt-icon-shopping-' . esc_attr( $large_atc_button_icon ) . '"></i>';
}

if ( 'large' === $atc_button_type ) {
	$atc_button_text = $large_atc_button_text;
}

if ( 'blank' === $view_product_target ) {
	$atc_button = '<a class="' . esc_attr( $atc_button_class ) . '" target="_blank" href="' . esc_attr( $atc_button_url ) . '" data-jtpt-product-id="' . esc_attr( $product_id ) . '" data-jtpt-product-type="' . esc_attr( $product_type ) . '" data-jtpt-quantity="' . esc_attr( $product_min_purchase_quantity ) . '" data-jtpt-variation-id="" data-jtpt-variation="" rel="noopener">' . wp_kses_post( $atc_button_icon ) . esc_html( $atc_button_text ) . '</a>';
} else {
	$atc_button = '<a class="' . esc_attr( $atc_button_class ) . '" href="' . esc_attr( $atc_button_url ) . '" data-jtpt-product-id="' . esc_attr( $product_id ) . '" data-jtpt-product-type="' . esc_attr( $product_type ) . '" data-jtpt-quantity="' . esc_attr( $product_min_purchase_quantity ) . '" data-jtpt-variation-id="" data-jtpt-variation="">' . wp_kses_post( $atc_button_icon ) . esc_html( $atc_button_text ) . '</a>';
}

// Wishlist button.
if ( in_array( 'wishlist', (array) $action_add_elements ) && ! in_array( 'wishlist', (array) $active_columns_id ) ) {
	if ( function_exists( 'WishSuite' ) ) {
		$wishlist_button = do_shortcode( '[wishsuite_button product_id="' . $product_id . '" button_text="" button_added_text="" button_exist_text=""]' );
	} elseif ( function_exists( 'YITH_WCWL' ) ) {
		$wishlist_button = do_shortcode( '[yith_wcwl_add_to_wishlist product_id="' . $product_id . '" label="" browse_wishlist_text="" already_in_wishslist_text="" product_added_text=""]' );
	} else {
		$wishlist_button = '';
	}
} else {
	$wishlist_button = '';
}

// Variations button.
if ( ! in_array( 'variations', (array) $active_columns_id ) && ( 'variable' === $product_type ) ) {
	$variation_attributes = $product->get_variation_attributes();
	$default_attributes = $product->get_default_attributes();
	$available_variations = $product->get_available_variations();

	$variations_stock_html = array();

	$variations_id = wp_list_pluck( $available_variations, 'variation_id' );

	if ( is_array( $variations_id ) && ! empty( $variations_id ) ) {
		foreach ( $variations_id as $variation_id ) {
			$variation = wc_get_product( $variation_id );

			if ( empty( $variation ) ) {
				continue;
			}

			$variation_availability = $variation->get_availability();
			$variation_availability_class = isset( $variation_availability['class'] ) ? $variation_availability['class'] : '';

			$variation_stock_quantity = $variation->get_stock_quantity();
			$variation_stock_status = $variation->get_stock_status();
			$variation_backorders = $variation->get_backorders();

			if ( 'instock' === $variation_stock_status ) {
				if ( null !== $variation_stock_quantity ) {
					if ( 'notify' === $variation_backorders ) {
						$variation_stock_status_text = str_replace( '_QTY_', $variation_stock_quantity, $in_stock_with_quantity_and_backorder_status_text );
					} else {
						$variation_stock_status_text = str_replace( '_QTY_', $variation_stock_quantity, $in_stock_with_quantity_status_text );
					}
				} else {
					$variation_stock_status_text = $in_stock_status_text;
				}
			} elseif ( 'outofstock' === $variation_stock_status ) {
				$variation_stock_status_text = $out_of_stock_status_text;
			} else {
				$variation_stock_status_text = $available_for_backorder_status_text;
			}

			$variation_stock_html = '<p class="stock ' . esc_attr( $variation_availability_class ) . '">' . $variation_stock_status_text . '</p>';

			$variations_stock_html[ $variation_id ] = $variation_stock_html;
		}
	}

	$variations_select = just_tables_variations_filter_select( $product_id, $variation_attributes, $default_attributes );

	$variations_button = '<a class="jtpt-variations" href="#"><i class="jtpt-icon jtpt-icon-sliders"></i></a>';
	$variations_button .= '<div class="jtpt-variations-options jtpt-variations-options-' . esc_attr( $product_id ) . '" data-jtpt-product-id="' . esc_attr( $product_id ) . '" data-jtpt-available-variations-json="' . esc_attr( wp_json_encode( $available_variations ) ) . '" data-jtpt-variations-stock-html-json="' . esc_attr( wp_json_encode( $variations_stock_html ) ) . '" data-jtpt-default-attributes-json="' . esc_attr( wp_json_encode( $default_attributes ) ) . '">' . $variations_select . '<div class="jtpt-variations-notice jtpt-variations-notice-' . esc_attr( $product_id ) . '">' . esc_html( $select_variation_text ) . '</div></div>';
} else {
	$variations_button = '';
}

// Update column element html.
if ( is_array( $action_column_elements ) && ! empty( $action_column_elements ) ) {
	$column_element_html .= '<ul class="jtpt-action-list">';

	if ( ! empty( $wishlist_button ) ) {
		$column_element_html .= '<li class="jtpt-action-item jtpt-wishlist-button jtpt-wishlist-button-' . esc_attr( $product_id ) . '">' . $wishlist_button . '</li>';
	}

	if ( ! empty( $atc_button ) ) {
		$column_element_html .= '<li class="jtpt-action-item jtpt-atc-button jtpt-atc-button-' . esc_attr( $product_id ) . '">' . $atc_button . '</li>';
	}

	if ( ! empty( $variations_button ) ) {
		$column_element_html .= '<li class="jtpt-action-item jtpt-variations-button jtpt-variations-button-' . esc_attr( $product_id ) . '">' . $variations_button . '</li>';
	}

	$column_element_html .= '</ul>';
}

$column_element = '<div class="jtpt-action jtpt-action-' . esc_attr( $product_id ) . ' jtpt-align-wrap">' . $column_element_html . '</div>';