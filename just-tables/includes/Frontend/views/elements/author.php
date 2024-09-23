<?php
// Column element html.
$column_element_html = '';

// Author meta.
$author_id = get_post_field( 'post_author', $product_id );
$author_display_name = get_the_author_meta( 'display_name', $author_id );

// Author name.
if ( 'view' === $author_name_on_click ) {
	$author_posts_url = get_author_posts_url( $author_id );

	if ( 'blank' === $view_author_target ) {
		$author_name = '<a target="_blank" href="' . esc_url( $author_posts_url ) . '" rel="noopener">' . wp_kses_data( $author_display_name ) . '</a>';
	} else {
		$author_name = '<a href="' . esc_url( $author_posts_url ) . '">' . wp_kses_data( $author_display_name ) . '</a>';
	}
} else {
	$author_name = wp_kses_data( $author_display_name );
}

if ( ! empty( $author_name ) ) {
	$column_element_html .= '<div class="jtpt-author-name jtpt-author-name-' . esc_attr( $product_id ) . ' jtpt-align-wrap">' . $author_name . '</div>';
}

// Author description.
if ( in_array( 'description', $author_add_elements ) ) {
	$author_description = get_the_author_meta( 'description', $author_id );

	if ( ! empty( $author_description ) ) {
		$column_element_html .= '<div class="jtpt-author-description jtpt-author-description-' . esc_attr( $product_id ) . ' jtpt-align-wrap">' . wp_kses_data( $author_description ) . '</div>';
	}
}

$column_element = $column_element_html;