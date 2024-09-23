<?php
$args = array(
	'input_id'     => uniqid( 'quantity_' ),
	'input_name'   => 'quantity',
	'input_value'  => $product_min_purchase_quantity,
	'classes'      => apply_filters( 'woocommerce_quantity_input_classes', array( 'input-text', 'qty', 'text' ), $product ),
	'max_value'    => $product_max_purchase_quantity,
	'min_value'    => $product_min_purchase_quantity,
	'step'         => apply_filters( 'woocommerce_quantity_input_step', 1, $product ),
	'pattern'      => apply_filters( 'woocommerce_quantity_input_pattern', has_filter( 'woocommerce_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
	'inputmode'    => apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' ),
	'product_name' => $product_title,
	'placeholder'  => apply_filters( 'woocommerce_quantity_input_placeholder', '', $product ),
	'autocomplete' => apply_filters( 'woocommerce_quantity_input_autocomplete', 'off', $product ),
);

$args = apply_filters( 'woocommerce_quantity_input_args', $args, $product );

$input_id     = $args['input_id'];
$input_name   = $args['input_name'];
$input_value  = $args['input_value'];
$classes      = $args['classes'];
$max_value    = $args['max_value'];
$min_value    = $args['min_value'];
$step         = $args['step'];
$pattern      = $args['pattern'];
$inputmode    = $args['inputmode'];
$product_name = $args['product_name'];
$placeholder  = $args['placeholder'];
$autocomplete = $args['autocomplete'];

$label = ! empty( $product_name ) ? sprintf( esc_html__( '%s quantity', 'just-tables' ), wp_strip_all_tags( $product_name ) ) : esc_html__( 'Quantity', 'just-tables' );

$class = join( ' ', (array) $classes );

$min_value = max( $min_value, 0 );

$max_value = 0 < $max_value ? $max_value : '';
$max_value = ( '' !== $max_value && $max_value < $min_value ) ? $min_value : $max_value;
$max_value = 0 < $max_value ? $max_value : '';

$autocomplete = isset( $autocomplete ) ? $autocomplete : 'on';

$quantity_input = '';

if ( $max_value && $min_value === $max_value ) {
	$quantity_input .= '<div class="quantity">';
	$quantity_input .= '<label class="screen-reader-text" for="' . esc_attr( $input_id ) . '">' . esc_html( $label ) . '</label>';
	$quantity_input .= '<input type="number" id="' . esc_attr( $input_id ) . '" class="' . esc_attr( $class ) . '" step="' . esc_attr( $step ) . '" min="' . esc_attr( $min_value ) . '" max="' . esc_attr( $max_value ) . '" name="' . esc_attr( $input_name ) . '" value="' . esc_attr( $min_value ) . '" title="' . esc_attr_x( 'Qty', 'Product quantity input tooltip', 'just-tables' ) . '" size="4" placeholder="' . esc_attr( $placeholder ) . '" inputmode="' . esc_attr( $inputmode ) . '" disabled="disabled" />';
	$quantity_input .= '</div>';
} else {
	$quantity_input .= '<div class="quantity">';
	$quantity_input .= '<div class="jtpt-qty-button decrease">-</div>';
	$quantity_input .= '<label class="screen-reader-text" for="' . esc_attr( $input_id ) . '">' . esc_html( $label ) . '</label>';
	$quantity_input .= '<input type="number" id="' . esc_attr( $input_id ) . '" class="' . esc_attr( $class ) . '" step="' . esc_attr( $step ) . '" min="' . esc_attr( $min_value ) . '" max="' . esc_attr( $max_value ) . '" name="' . esc_attr( $input_name ) . '" value="' . esc_attr( $input_value ) . '" title="' . esc_attr_x( 'Qty', 'Product quantity input tooltip', 'just-tables' ) . '" size="4" placeholder="' . esc_attr( $placeholder ) . '" inputmode="' . esc_attr( $inputmode ) . '" autocomplete="' . esc_attr( $autocomplete ) . '" />';
	$quantity_input .= '<div class="jtpt-qty-button increase">+</div>';
	$quantity_input .= '</div>';
}

$column_element = '<div class="jtpt-quantity jtpt-quantity-' . esc_attr( $product_id ) . ' jtpt-quantity-plus-minus jtpt-align-wrap" data-jtpt-product-id="' . esc_attr( $product_id ) . '"><div class="jtpt-quantity-core">' . $quantity_input . '</div></div>';