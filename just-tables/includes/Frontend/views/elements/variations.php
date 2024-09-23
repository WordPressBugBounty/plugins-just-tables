<?php
$variations_html = '';

if ( 'variable' === $product_type ) {
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

	$variations_html = '<div class="jtpt-variations-options jtpt-variations-options-' . esc_attr( $product_id ) . '" data-jtpt-product-id="' . esc_attr( $product_id ) . '" data-jtpt-available-variations-json="' . esc_attr( wp_json_encode( $available_variations ) ) . '" data-jtpt-variations-stock-html-json="' . esc_attr( wp_json_encode( $variations_stock_html ) ) . '" data-jtpt-default-attributes-json="' . esc_attr( wp_json_encode( $default_attributes ) ) . '">' . $variations_select . '<div class="jtpt-variations-notice jtpt-variations-notice-' . esc_attr( $product_id ) . '">' . esc_html( $select_variation_text ) . '</div></div>';
}

$column_element = '<div class="jtpt-variations jtpt-variations-' . esc_attr( $product_id ) . ' jtpt-align-wrap">' . $variations_html . '</div>';