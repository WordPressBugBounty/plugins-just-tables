<?php
$short_description = $product->get_short_description();
$column_element = '<div class="jtpt-short-description jtpt-short-description-' . esc_attr( $product_id ) . ' jtpt-align-wrap">' . do_shortcode( wp_kses_post( $short_description ) ) . '</div>';