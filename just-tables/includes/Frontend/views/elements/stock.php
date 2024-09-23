<?php
$availability = $product->get_availability();
$availability_class = isset( $availability['class'] ) ? $availability['class'] : '';

$stock_quantity = $product->get_stock_quantity();
$stock_status = $product->get_stock_status();
$backorders = $product->get_backorders();

if ( 'instock' === $stock_status ) {
	if ( null !== $stock_quantity ) {
		if ( 'notify' === $backorders ) {
			$stock_status_text = str_replace( '_QTY_', $stock_quantity, $in_stock_with_quantity_and_backorder_status_text );
		} else {
			$stock_status_text = str_replace( '_QTY_', $stock_quantity, $in_stock_with_quantity_status_text );
		}
	} else {
		$stock_status_text = $in_stock_status_text;
	}
} elseif ( 'outofstock' === $stock_status ) {
	$stock_status_text = $out_of_stock_status_text;
} else {
	$stock_status_text = $available_for_backorder_status_text;
}

$stock_html = '<p class="stock ' . esc_attr( $availability_class ) . '">' . $stock_status_text . '</p>';

$column_element = '<div class="jtpt-stock jtpt-stock-' . esc_attr( $product_id ) . ' jtpt-align-wrap" data-jtpt-simple-stock-html="' . esc_attr( $stock_html )  . '">' . $stock_html . '</div>';