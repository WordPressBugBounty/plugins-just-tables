<?php
$description = $product->get_description();
$column_element = '<div class="jtpt-description jtpt-description-' . esc_attr( $product_id ) . ' jtpt-align-wrap" data-jtpt-simple-description="' . esc_attr( do_shortcode( wp_kses_post( $description ) ) ) . '">' . do_shortcode( wp_kses_post( $description ) ) . '</div>';