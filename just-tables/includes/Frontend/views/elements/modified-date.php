<?php
$modified_date = get_the_modified_date();
$unix_modified_date = get_the_modified_date( 'U' );
$column_element = '<div class="jtpt-modified-date jtpt-modified-date-' . esc_attr( $product_id ) . ' jtpt-align-wrap" data-sort="' . esc_attr( $unix_modified_date ) . '">' . $modified_date . '</div>';