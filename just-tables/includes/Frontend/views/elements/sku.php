<?php
$sku = $product->get_sku();
$column_element = '<div class="jtpt-sku jtpt-sku-' . esc_attr( $product_id ) . ' jtpt-align-wrap" data-jtpt-simple-sku="' . esc_attr( $sku ) . '">' . $sku . '</div>';