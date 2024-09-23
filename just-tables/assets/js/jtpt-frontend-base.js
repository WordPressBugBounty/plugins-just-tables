/**
 * Frontend base scripts.
 */

/* global jtpt_data */
;( function ( $ ) {
    'use strict';

    function justTablesGlobal ( context = '' ) {

        /**
         * Filter toggle.
         *
         * @since 1.0.0
         */
        $( '.jtpt-ftrigger-button' ).on( 'click', function ( e ) {
            e.preventDefault();

            $( this ).closest( '.jtpt-filter' ).find( '.jtpt-filter-options-wrap' ).slideToggle();
        } );

        /**
         * Clear selection of filter select.
         *
         * @since 1.0.0
         */
        $( '.jtpt-freset-button' ).on( 'click', function ( e ) {
            e.preventDefault();

            $( this ).closest( '.jtpt-filter-options' ).find( '.jtpt-filter-select' ).val( null ).trigger('change');
        } );

        /**
         * Quantity input plus minus button.
         *
         * @since 1.0.0
         */
        $( document ).on( 'click', '.jtpt-qty-button', function () {
            var thisButton = $( this ),
                inputField = thisButton.parent().find( 'input.qty' ),
                inputFieldValue = inputField.val(),
                inputFieldStep = inputField.attr( 'step' ),
                quantityWrapper = inputField.closest( '.jtpt-quantity' ),
                productId = quantityWrapper.attr( 'data-jtpt-product-id' ),
                tableWrapper = quantityWrapper.closest( '.jtpt-product-table-wrapper' ),
                product = tableWrapper.find( '.jtpt-body-row-' + productId ),
                minQuantity = product.attr( 'data-jtpt-min-qty' ),
                maxQuantity = product.attr( 'data-jtpt-max-qty' ),
                updatedValue = inputFieldValue;

            inputFieldValue = parseFloat( inputFieldValue );
            inputFieldStep = parseFloat( inputFieldStep );
            minQuantity = parseFloat( minQuantity );
            maxQuantity = parseFloat( maxQuantity );

            if ( isNaN( inputFieldValue ) || isNaN( inputFieldStep ) ) {
                return;
            }

            if ( thisButton.hasClass( 'increase' ) ) {
                updatedValue = inputFieldValue + inputFieldStep;
                if ( ! isNaN( maxQuantity ) && maxQuantity < updatedValue ) {
                    updatedValue = inputFieldValue;
                }
            } else {
                updatedValue = inputFieldValue - inputFieldStep;
                if ( ! isNaN( minQuantity ) && minQuantity > updatedValue ) {
                    updatedValue = inputFieldValue;
                }
            }

            inputField.val( updatedValue ).trigger( 'change' );
        } );

        /**
         * Quantity input change.
         *
         * @since 1.0.0
         */
        $( document ).on( 'change keyup', '.jtpt-quantity .quantity input.qty', function () {
            var thisInputField = $( this ),
                inputFieldValue = thisInputField.val(),
                quantityWrapper = thisInputField.closest( '.jtpt-quantity' ),
                productId = quantityWrapper.attr( 'data-jtpt-product-id' ),
                tableWrapper = quantityWrapper.closest( '.jtpt-product-table-wrapper' ),
                product = tableWrapper.find( '.jtpt-body-row-' + productId ),
                productType = product.attr( 'data-jtpt-product-type' ),
                minQuantity = product.attr( 'data-jtpt-min-qty' ),
                maxQuantity = product.attr( 'data-jtpt-max-qty' ),
                addToCartButton = tableWrapper.find( '.jtpt-atc-button-' + productId + ' .jtpt-add-to-cart' ),
                checkbox = tableWrapper.find( '.jtpt-check-' + productId + ' .jtpt-check-checkbox' );

            inputFieldValue = parseFloat( inputFieldValue );
            minQuantity = parseFloat( minQuantity );
            maxQuantity = parseFloat( maxQuantity );
            productId = parseFloat( productId );

            if ( isNaN( productId ) ) {
                return;
            }

            if ( isNaN( inputFieldValue ) ) {
                inputFieldValue = 0;
            }

            if ( ! addToCartButton.hasClass( 'jtpt-variation-selection-needed' ) && ! checkbox.hasClass( 'jtpt-variation-selection-needed' ) && ! checkbox.hasClass( 'disabled' ) ) {
                if ( ( ! isNaN( minQuantity ) && minQuantity > inputFieldValue ) || ( ! isNaN( maxQuantity ) && maxQuantity < inputFieldValue ) ) {
                    addToCartButton.addClass( 'jtpt-wrong-quantity' );
                    addToCartButton.removeClass( 'jtpt-ajax-add-to-cart' );
                    checkbox.addClass( 'jtpt-wrong-quantity' );
                } else {
                    if ( ( 'simple' === productType ) || ( 'variable' === productType ) ) {
                        addToCartButton.addClass( 'jtpt-ajax-add-to-cart' );
                    }
                    addToCartButton.removeClass( 'jtpt-wrong-quantity' );
                    checkbox.removeClass( 'jtpt-wrong-quantity' );
                }
            } else {
                addToCartButton.removeClass( 'jtpt-wrong-quantity' );
                checkbox.removeClass( 'jtpt-wrong-quantity' );
            }

            addToCartButton.attr( 'data-jtpt-quantity', inputFieldValue );
            checkbox.attr( 'data-jtpt-quantity', inputFieldValue );

            // Trigger required events.
            checkbox.trigger( 'change' );
        } );
        // Trigger quantity change.
        $( document ).find( '.jtpt-quantity .quantity input.qty' ).trigger( 'change' );

        /**
         * Variation options toggle.
         *
         * @since 1.0.0
         */
        $( document ).on( 'click', '.jtpt-variations', function ( e ) {
            e.preventDefault();

            $( this ).toggleClass( 'active' ).closest( '.jtpt-variations-button' ).find( '.jtpt-variations-options' ).toggleClass( 'active' );
        } );

        /**
         * Variation select change.
         *
         * @since 1.0.0
         */
        $( document ).on( 'change', '.jtpt-variations-options', function () {
            var thisVariationOptions = $( this ),
                variationSelects = thisVariationOptions.find( '.jtpt-variation-filter-select' ),
                variationSelectsLength = variationSelects.length,
                availableVariations = thisVariationOptions.attr( 'data-jtpt-available-variations-json' ),
                variationsStockHtml = thisVariationOptions.attr( 'data-jtpt-variations-stock-html-json' ),
                defaultAttributes = thisVariationOptions.attr( 'data-jtpt-default-attributes-json' ),
                productId = thisVariationOptions.attr( 'data-jtpt-product-id' ),
                tableWrapper = thisVariationOptions.closest( '.jtpt-product-table-wrapper' ),
                product = tableWrapper.find( '.jtpt-body-row-' + productId ),
                productType = product.attr( 'data-jtpt-product-type' ),
                elementConfiguration = tableWrapper.attr( 'data-jtpt-element-configuration-json' ),
                woocommerceSettings = tableWrapper.attr( 'data-jtpt-woocommerce-settings-json' ),
                selectedAttributes = {},
                selectedAttributesLength = 0,
                matchedVariation = '';

            // Variation id and attributes.
            var variationId = '',
                variation = '';

            // Thumbnail.
            var thumbnail = tableWrapper.find( '.jtpt-thumbnail-' + productId ),
                thumbnailImage = thumbnail.find( 'img' ),
                thumbnailAnchor = thumbnail.find( 'a' ),
                simpleThumbnailImageHtml = thumbnail.attr( 'data-jtpt-simple-thumbnail-image-html' ),
                simpleThumbnailImageUrl = thumbnail.attr( 'data-jtpt-simple-thumbnail-image-url' ),
                variationThumbnail = '',
                variationThumbnailImageUrl = '';

            // Description.
            var description = tableWrapper.find( '.jtpt-description-' + productId ),
                simpleDescription = description.attr( 'data-jtpt-simple-description' ),
                variationDescription = '';

            // SKU.
            var sku = tableWrapper.find( '.jtpt-sku-' + productId ),
                simpleSKU = sku.attr( 'data-jtpt-simple-sku' ),
                variationSKU = sku.attr( 'data-jtpt-simple-sku' );

            // Weight.
            var weight = tableWrapper.find( '.jtpt-weight-' + productId ),
                simpleWeightHtml = weight.attr( 'data-jtpt-simple-weight-html' ),
                variationWeightHtml = '';

            // Dimensions.
            var dimensions = tableWrapper.find( '.jtpt-dimensions-' + productId ),
                dimensionUnit = '',
                simpleDimensionsHtml = dimensions.attr( 'data-jtpt-simple-dimensions-html' ),
                variationDimensions = '',
                variationDimensionsHtml = '';

            // Length.
            var length = tableWrapper.find( '.jtpt-length-' + productId ),
                simpleLength = length.attr( 'data-jtpt-simple-length' ),
                variationLength = '';

            // Width.
            var width = tableWrapper.find( '.jtpt-width-' + productId ),
                simpleWidth = width.attr( 'data-jtpt-simple-width' ),
                variationWidth = '';

            // Height.
            var height = tableWrapper.find( '.jtpt-height-' + productId ),
                simpleHeight = height.attr( 'data-jtpt-simple-height' ),
                variationHeight = '';

            // Stock.
            var stock = tableWrapper.find( '.jtpt-stock-' + productId ),
                simpleStockHtml = stock.attr( 'data-jtpt-simple-stock-html' ),
                variationStockStatus = '',
                variationStockHtml = '';

            // Price.
            var price = tableWrapper.find( '.jtpt-price-' + productId ),
                simplePriceHtml = price.attr( 'data-jtpt-simple-price' ),
                variationPriceHtml = '';

            // Quantity input.
            var quantity = tableWrapper.find( '.jtpt-quantity-' + productId ),
                quantityInput = quantity.find( 'input.qty' ),
                simpleQuantityInputMin = quantity.attr( 'data-jtpt-simple-min-qty' ),
                simpleQuantityInputMax = quantity.attr( 'data-jtpt-simple-max-qty' ),
                variationQuantityInputMin = quantity.attr( 'data-jtpt-simple-min-qty' ),
                variationQuantityInputMax = quantity.attr( 'data-jtpt-simple-max-qty' );

            // Add to cart button.
            var addToCartButton = tableWrapper.find( '.jtpt-atc-button-' + productId + ' .jtpt-add-to-cart' );

            // Checkbox.
            var checkbox = tableWrapper.find( '.jtpt-check-' + productId + ' .jtpt-check-checkbox' );

            // Variation notice.
            var variationsNotice = tableWrapper.find( '.jtpt-variations-notice-' + productId ),
                selectVariationText = '',
                selectVariationAllOptionsText = '',
                variationNotAvailableText = '';

            // Parse json value.
            availableVariations = JSON.parse( availableVariations );
            variationsStockHtml = JSON.parse( variationsStockHtml );
            defaultAttributes = JSON.parse( defaultAttributes );
            elementConfiguration = JSON.parse( elementConfiguration );
            woocommerceSettings = JSON.parse( woocommerceSettings );

            // Loop through variation selects.
            $.each( variationSelects, function () {
                var thisSelect = $( this ),
                    selectLabel = thisSelect.attr( 'data-jtpt-attribute' ),
                    selectValue = thisSelect.val();

                // If select input has value.
                if ( 'jtpt-select-label' !== selectValue ) {
                    selectedAttributes[ selectLabel ] = selectValue;
                    selectedAttributesLength++;
                }
            } );

            // Check if variation all selects are selected.
            if ( 0 < selectedAttributesLength && variationSelectsLength === selectedAttributesLength ) {
                // Loop trhough available variations
                $.each( availableVariations, function () {
                    var thisVariation = this,
                        variationAttributes = thisVariation.attributes,
                        notMatchedAttributes = 0;

                    // Loop trhough variation attributes.
                    $.each( variationAttributes, function ( index, element ) {
                        if ( '' !== element && selectedAttributes[ index ] !== element ) {
                            notMatchedAttributes++;
                        }
                    } );

                    // If all attribute matched.
                    if ( 0 === notMatchedAttributes ) {
                        matchedVariation = thisVariation;
                    }
                } );
            }

            // If selected variation is available.
            if ( '' !== matchedVariation ) {
                // Variation id and attributes.
                variationId = matchedVariation.variation_id;
                variation = JSON.stringify( selectedAttributes );

                // Update thumbnail.
                variationThumbnail = matchedVariation.image;
                variationThumbnailImageUrl = variationThumbnail.url;
                thumbnailImage.attr( 'src', variationThumbnail.thumb_src );
                thumbnailImage.attr( 'srcset', variationThumbnail.srcset );
                thumbnailImage.attr( 'alt', variationThumbnail.alt );
                if ( thumbnailAnchor.hasClass( 'thickbox' ) ) {
                    thumbnailAnchor.attr( 'href', variationThumbnailImageUrl );
                }

                // Update description.
                variationDescription = matchedVariation.variation_description;
                description.html( variationDescription );

                // Update SKU.
                variationSKU = matchedVariation.sku;
                sku.html( variationSKU );

                // Update weight.
                variationWeightHtml = matchedVariation.weight_html;
                if ( 'N/A' === variationWeightHtml ) {
                    variationWeightHtml = '';
                }
                weight.html( variationWeightHtml );

                // Update dimensions.
                dimensionUnit = woocommerceSettings.dimension_unit;
                variationDimensions = matchedVariation.dimensions;
                variationDimensionsHtml = matchedVariation.dimensions_html;
                if ( 'N/A' === variationDimensionsHtml ) {
                    variationDimensionsHtml = '';
                }
                dimensions.html( variationDimensionsHtml );

                // Update length.
                variationLength = variationDimensions.length;
                if ( '' !== variationLength ) {
                    variationLength = variationLength + ' ' + dimensionUnit;
                }
                length.html( variationLength );

                // Update width.
                variationWidth = variationDimensions.width;
                if ( '' !== variationWidth ) {
                    variationWidth = variationWidth + ' ' + dimensionUnit;
                }
                width.html( variationWidth );

                // Update height.
                variationHeight = variationDimensions.height;
                if ( '' !== variationHeight ) {
                    variationHeight = variationHeight + ' ' + dimensionUnit;
                }
                height.html( variationHeight );

                // Update stock.
                if ( variationsStockHtml.hasOwnProperty( variationId ) ) {
                    variationStockHtml = variationsStockHtml[ variationId ];
                }
                stock.html( variationStockHtml );

                // Update price.
                variationPriceHtml = matchedVariation.price_html;
                price.html( variationPriceHtml );

                // Update quantity.
                variationQuantityInputMin = matchedVariation.min_qty;
                variationQuantityInputMin = ( ( ! isNaN( variationQuantityInputMin ) && 0 < variationQuantityInputMin ) ? variationQuantityInputMin : 0 );

                variationQuantityInputMax = matchedVariation.max_qty;
                variationQuantityInputMax = ( ( ! isNaN( variationQuantityInputMax ) && 0 < variationQuantityInputMax ) ? variationQuantityInputMax : '' );
                variationQuantityInputMax = ( ( '' !== variationQuantityInputMax && variationQuantityInputMax < variationQuantityInputMin ) ? variationQuantityInputMin : variationQuantityInputMax );
                variationQuantityInputMax = ( ( ! isNaN( variationQuantityInputMax ) && 0 < variationQuantityInputMax ) ? variationQuantityInputMax : '' );

                quantityInput.attr( 'min', variationQuantityInputMin );
                quantityInput.attr( 'max', variationQuantityInputMax );

                // Update add to cart button.
                addToCartButton.attr( 'data-jtpt-variation-id', variationId );
                addToCartButton.attr( 'data-jtpt-variation', variation );
                addToCartButton.removeClass( 'jtpt-variation-selection-needed' );
                if ( ( 'simple' === productType ) && ( 'variable' === productType ) ) {
                    addToCartButton.addClass( 'jtpt-ajax-add-to-cart' );
                }

                // Update checkbox.
                checkbox.attr( 'data-jtpt-variation-id', variationId );
                checkbox.attr( 'data-jtpt-variation', variation );
                checkbox.removeClass( 'jtpt-variation-selection-needed' );

                // Update min and max quantity.
                product.attr( 'data-jtpt-min-qty', variationQuantityInputMin );
                product.attr( 'data-jtpt-max-qty', variationQuantityInputMax );

                // Update notice.
                variationsNotice.html( variationStockHtml );
            } else {
                // Update thumbnail.
                thumbnailImage.replaceWith( simpleThumbnailImageHtml );
                if ( thumbnailAnchor.hasClass( 'thickbox' ) ) {
                    thumbnailAnchor.attr( 'href', simpleThumbnailImageUrl );
                }

                // Update description.
                description.html( simpleDescription );

                // Update SKU.
                sku.html( simpleSKU );

                // Update weight.
                weight.html( simpleWeightHtml );

                // Update dimensions.
                dimensions.html( simpleDimensionsHtml );

                // Update length.
                length.html( simpleLength );

                // Update width.
                width.html( simpleWidth );

                // Update height.
                height.html( simpleHeight );

                // Update stock.
                stock.html( simpleStockHtml );

                // Update price.
                price.html( simplePriceHtml );

                // Update quantity
                quantityInput.attr( 'min', simpleQuantityInputMin );
                quantityInput.attr( 'max', simpleQuantityInputMax );

                // Update add to cart button.
                addToCartButton.attr( 'data-jtpt-variation-id', '' );
                addToCartButton.attr( 'data-jtpt-variation', '' );
                addToCartButton.removeClass( 'jtpt-ajax-add-to-cart' );
                addToCartButton.removeClass( 'jtpt-wrong-quantity' );
                addToCartButton.addClass( 'jtpt-variation-selection-needed' );

                // Update checkbox.
                checkbox.attr( 'data-jtpt-variation-id', '' );
                checkbox.attr( 'data-jtpt-variation', '' );
                checkbox.removeClass( 'jtpt-wrong-quantity' );
                checkbox.addClass( 'jtpt-variation-selection-needed' );

                // Update min and max quantity.
                product.attr( 'data-jtpt-min-qty', simpleQuantityInputMin );
                product.attr( 'data-jtpt-max-qty', simpleQuantityInputMax );

                // Update notice.
                selectVariationText = elementConfiguration.select_variation_text;
                selectVariationAllOptionsText = elementConfiguration.select_variation_all_options_text;
                variationNotAvailableText = elementConfiguration.variation_not_available_text;
                if ( 0 === selectedAttributesLength ) {
                    variationsNotice.html( selectVariationText );
                } else if ( variationSelectsLength === selectedAttributesLength ) {
                    variationsNotice.html( variationNotAvailableText );
                } else {
                    variationsNotice.html( selectVariationAllOptionsText );
                }
            }

            // Trigger required events.
            quantityInput.trigger( 'change' );
            checkbox.trigger( 'change' );
        } );
        // Trigger variations change.
        $( document ).find( '.jtpt-variations-options' ).trigger( 'change' );

        /**
         * Checkbox change.
         *
         * @since 1.0.0
         */
        $( document ).on( 'change', '.jtpt-check-checkbox', function () {
            var thisCheckbox = $( this ),
                productId = thisCheckbox.attr( 'data-jtpt-product-id' ),
                quantity = thisCheckbox.attr( 'data-jtpt-quantity' ),
                variationId = thisCheckbox.attr( 'data-jtpt-variation-id' ),
                variation = thisCheckbox.attr( 'data-jtpt-variation' ),
                tableWrapper = thisCheckbox.closest( '.jtpt-product-table-wrapper' ),
                multipleAddToCartButton = tableWrapper.find( '.jtpt-matc-button' ),
                selectedProducts = multipleAddToCartButton.attr( 'data-jtpt-selected-products' ),
                selectedProductsCount = multipleAddToCartButton.attr( 'data-jtpt-selected-products-count' );

            if ( thisCheckbox.hasClass( 'jtpt-wrong-quantity' ) || thisCheckbox.hasClass( 'jtpt-variation-selection-needed' ) || thisCheckbox.hasClass( 'disabled' ) ) {
                thisCheckbox.prop( 'checked', false );
            }

            if ( isNaN( selectedProductsCount ) || 0 > selectedProductsCount ) {
                selectedProductsCount = 0;
            }

            if ( '' !== variation ) {
                variation = JSON.parse( variation );
            }

            if ( '' === selectedProducts ) {
                selectedProducts = {};
            } else {
                selectedProducts = JSON.parse( selectedProducts );
            }

            if ( thisCheckbox.prop( 'checked' ) ) {
                selectedProducts[ productId ] = {
                    product_id: productId,
                    quantity: quantity,
                    variation_id: variationId,
                    variation: variation,
                };
            } else {
                delete selectedProducts[ productId ];
            }

            selectedProductsCount = Object.keys(selectedProducts).length;

            if ( 0 < selectedProductsCount ) {
                selectedProducts = JSON.stringify( selectedProducts );
                multipleAddToCartButton.addClass( 'jtpt-multiple-ajax-add-to-cart' );
                multipleAddToCartButton.removeClass( 'jtpt-products-selection-needed' );
            } else {
                selectedProducts = '';
                multipleAddToCartButton.addClass( 'jtpt-products-selection-needed' );
                multipleAddToCartButton.removeClass( 'jtpt-multiple-ajax-add-to-cart' );
            }

            multipleAddToCartButton.attr( 'data-jtpt-selected-products', selectedProducts );
            multipleAddToCartButton.attr( 'data-jtpt-selected-products-count', selectedProductsCount );
            multipleAddToCartButton.removeClass( 'added' );
            multipleAddToCartButton.next( '.wc-forward' ).remove();
        } );

        /**
         * Check all checkbox change.
         *
         * @since 1.0.0
         */
        $( document ).on( 'change', '.jtpt-mcheck-checkbox', function () {
            var thisCheckbox = $( this ),
                tableWrapper = thisCheckbox.closest( '.jtpt-product-table-wrapper' ),
                productCheckCheckbox = tableWrapper.find( '.jtpt-check-checkbox' );

            $.each( productCheckCheckbox, function () {
                var thisProductCheckbox = $( this );

                if ( thisCheckbox.prop( 'checked' ) && ! thisProductCheckbox.hasClass( 'jtpt-wrong-quantity' ) && ! thisProductCheckbox.hasClass( 'jtpt-variation-selection-needed' ) && ! thisProductCheckbox.hasClass( 'disabled' ) ) {
                    thisProductCheckbox.prop( 'checked', true ).trigger( 'change' );
                } else {
                    thisProductCheckbox.prop( 'checked', false ).trigger( 'change' );
                }
            } );
        } );

        /**
         * Handle basic changes of each products.
         *
         * @since 1.0.0
         */
        $( document ).find( '.jtpt-body-data > div' ).each( function () {
            var thisColumnContent = $( this ),
                column = thisColumnContent.parent( 'td' ),
                columnDataSortValue = thisColumnContent.attr( 'data-sort' );

            if ( 'undefined' !== typeof columnDataSortValue && '' !== columnDataSortValue ) {
                column.attr( 'data-sort', columnDataSortValue );
            }
        } );

        /**
         * Initilize DataTable to product table.
         *
         * @since 1.0.0
         */
        function initDataTableToProductTable( productTableWrapper, productTableId, recalcResponsive ) {
            var queryArgs = productTableWrapper.attr( 'data-jtpt-query-args-json' ),
                taxonomyQueryIncludeArgs = productTableWrapper.attr( 'data-jtpt-taxonomy-query-filter-include-args-json' ),
                taxonomyQueryExcludeArgs = productTableWrapper.attr( 'data-jtpt-taxonomy-query-filter-exclude-args-json' ),
                activeColumns = productTableWrapper.attr( 'data-jtpt-columns-json' ),
                activeColumnsId = productTableWrapper.attr( 'data-jtpt-columns-id-json' ),
                elementConfiguration = productTableWrapper.attr( 'data-jtpt-element-configuration-json' ),
                dataTableConfiguration = productTableWrapper.attr( 'data-jtpt-data-table-configuration-json' ),
                woocommerceSettings = productTableWrapper.attr( 'data-jtpt-woocommerce-settings-json' ),
                productTable = productTableWrapper.find( '.jtpt-product-table' ),
                paginateInfo = productTableWrapper.find( '.jtpt-paginate-info' ),
                paginateNumbers = productTableWrapper.find( '.jtpt-paginate-numbers' ),
                exportButtons = productTableWrapper.find( '.jtpt-export-buttons' ),
                tableLoading = productTableWrapper.next( '.jtpt-product-table-loading' );

            queryArgs = JSON.parse( queryArgs );
            taxonomyQueryIncludeArgs = JSON.parse( taxonomyQueryIncludeArgs );
            taxonomyQueryExcludeArgs = JSON.parse( taxonomyQueryExcludeArgs );
            activeColumns = JSON.parse( activeColumns );
            activeColumnsId = JSON.parse( activeColumnsId );
            elementConfiguration = JSON.parse( elementConfiguration );
            dataTableConfiguration = JSON.parse( dataTableConfiguration );
            woocommerceSettings = JSON.parse( woocommerceSettings );

            // DataTable options.
            var productTableDataTableOpt = {
                retrieve: true,
                dom: 'rtipB',
                serverSide: true,
                ajax: {
                    type: 'POST',
                    url: jtpt_data.ajax_url,
                    data: function ( d ) {
                        var reloadingTableWrapper = $( document ).find( '.jtpt-ajax-reloading-' + productTableId );

                        if ( 'object' === typeof reloadingTableWrapper && 0 < reloadingTableWrapper.length ) {
                            taxonomyQueryIncludeArgs = reloadingTableWrapper.attr( 'data-jtpt-taxonomy-query-filter-include-args-json' );
                            taxonomyQueryExcludeArgs = reloadingTableWrapper.attr( 'data-jtpt-taxonomy-query-filter-exclude-args-json' );

                            taxonomyQueryIncludeArgs = JSON.parse( taxonomyQueryIncludeArgs );
                            taxonomyQueryExcludeArgs = JSON.parse( taxonomyQueryExcludeArgs );

                            reloadingTableWrapper.removeClass( 'jtpt-ajax-reloading-' + productTableId );
                        } else {
                            if ( d.hasOwnProperty( 'taxonomy_query_include_args' ) ) {
                                taxonomyQueryIncludeArgs = d.taxonomy_query_include_args;
                            }

                            if ( d.hasOwnProperty( 'taxonomy_query_exclude_args' ) ) {
                                taxonomyQueryExcludeArgs = d.taxonomy_query_exclude_args;
                            }
                        }

                        d.action = 'jtpt_ajax_generate_table_body_rows';
                        d.nonce = jtpt_data.ajax_nonce;
                        d.query_args = queryArgs;
                        d.active_columns = activeColumns;
                        d.active_columns_id = activeColumnsId;
                        d.element_configuration = elementConfiguration;
                        d.woocommerce_settings = woocommerceSettings;
                        d.taxonomy_query_include_args = taxonomyQueryIncludeArgs;
                        d.taxonomy_query_exclude_args = taxonomyQueryExcludeArgs;
                    },
                    beforeSend: function () {
                        productTableWrapper.addClass( 'jtpt-loading' );
                    },
                    complete: function () {
                        productTableWrapper.removeClass( 'jtpt-loading' );
                    }
                },
                info: dataTableConfiguration.paginate_info,
                paginate: dataTableConfiguration.paginate,
                pageLength: dataTableConfiguration.products_per_page,
                deferRender: true,
                lengthChange: false,
                language: {
                    decimal: woocommerceSettings.decimal_separator,
                    thousands: woocommerceSettings.thousand_separator,
                    zeroRecords: dataTableConfiguration.products_not_found_text,
                    info: dataTableConfiguration.paginate_info_text,
                    infoEmpty: '',
                    infoFiltered: '',
                    paginate: {
                        previous: '<i class="jtpt-icon jtpt-icon-angle-left-light"></i>',
                        next: '<i class="jtpt-icon jtpt-icon-angle-right-light"></i>',
                    }
                },
                columnDefs: [
                    {
                        targets: 'jtpt-no-sort',
                        orderable: false,
                        order: []
                    }
                ],
                aaSorting: [],
                buttons: dataTableConfiguration.export_buttons,
                initComplete: function() {
                    var thisDataTableInfo = productTableWrapper.find( '.dataTables_info' ),
                        thisDataTablePaginate = productTableWrapper.find( '.dataTables_paginate' ),
                        thisDataTableButtons = productTableWrapper.find( '.dt-buttons' );

                    thisDataTableInfo.appendTo( paginateInfo );
                    thisDataTablePaginate.appendTo( paginateNumbers );

                    if ( '' !== thisDataTableButtons.html() ) {
                        thisDataTableButtons.appendTo( exportButtons );
                    } else {
                        exportButtons.closest( '.jtpt-export' ).remove();
                    }

                    // Add initialized class.
                    productTableWrapper.addClass( 'jtpt-product-table-inited' );

                    // Show table.
                    productTableWrapper.fadeIn();

                    // Hide table loading.
                    tableLoading.remove();
                }
            };

            if ( true === dataTableConfiguration.responsive_layout ) {
                if ( true === dataTableConfiguration.responsive_content_display_default ) {
                    productTableDataTableOpt.responsive = {
                        details: {
                            display: $.fn.dataTable.Responsive.display.childRowImmediate
                        }
                    }
                } else {
                    productTableDataTableOpt.responsive = true;
                }
            } else {
                productTableDataTableOpt.responsive = false;
            }

            var productTableDataTable = null;

            if ( true === recalcResponsive ) {
                // Recalc responsive.
                productTableDataTable = productTable.DataTable( productTableDataTableOpt ).columns.adjust().responsive.recalc();
            } else {
                // Initialize DataTable.
                productTableDataTable = productTable.DataTable( productTableDataTableOpt );

                // Draw td attributes and tr attributes.
                productTableDataTable.on( 'draw', function() {
                    var thisTable = $( this ),
                        tdAttributesHtml = thisTable.find( 'tr td .jtpt-td-attributes' ),
                        trAttributesHtml = thisTable.find( 'tr td:first-child .jtpt-tr-attributes' );

                    tdAttributesHtml.each( function () {
                        var thisAttributesHtml = $( this ),
                            attributes = thisAttributesHtml.attr( 'data-jtpt-td-attributes' ),
                            td = thisAttributesHtml.closest( 'td' );

                        attributes = JSON.parse( attributes );

                        if ( 'object' === typeof attributes ) {
                            $.each( attributes, function ( key, value ) {
                                if ( 'class' === key ) {
                                    td.addClass( value );
                                } else {
                                    td.attr( key, value );
                                }
                            } );
                        }
                    } );

                    trAttributesHtml.each( function () {
                        var thisAttributesHtml = $( this ),
                            attributes = thisAttributesHtml.attr( 'data-jtpt-tr-attributes' ),
                            tr = thisAttributesHtml.closest( 'tr' );

                        attributes = JSON.parse( attributes );

                        if ( 'object' === typeof attributes ) {
                            $.each( attributes, function ( key, value ) {
                                if ( 'class' === key ) {
                                    tr.addClass( value );
                                } else {
                                    tr.attr( key, value );
                                }
                            } );
                        }
                    } );

                    thisTable.find( '.jtpt-td-attributes' ).remove();
                    thisTable.find( '.jtpt-tr-attributes' ).remove();
                } );
            }

            if ( dataTableConfiguration.paginate_scroll_to_top ) {
                productTable.on( 'page.dt', function () {
                    $( 'html, body' ).animate( {
                        scrollTop: productTableWrapper.offset().top
                    }, 'slow' );
                } );
            }

            return productTableDataTable;
        }

        /**
         * Init DataTable to each product table.
         *
         * @since 1.0.0
         */
        $( document ).find( '.jtpt-product-table-wrapper' ).each( function () {
            var thisProductTableWrapper = $( this ),
                productTableId = thisProductTableWrapper.attr( 'data-jtpt-product-table-id' ),
                searchInput = thisProductTableWrapper.find( '.jtpt-search-input' ),
                tableHeadData = thisProductTableWrapper.find( '.jtpt-head-data' ),
                elementorPopup = thisProductTableWrapper.closest( '.elementor[data-elementor-type="popup"]' ),
                elementorTabContent = thisProductTableWrapper.closest( '.elementor-tab-content' );

            let getBack = false;
            let alreadyInited = false;
            let recalcResponsive = false;

            if ( thisProductTableWrapper.hasClass( 'jtpt-product-table-inited' ) ) {
                getBack = true;
                alreadyInited = true;
            } else if ( ( 'object' === typeof elementorPopup ) && elementorPopup.hasOwnProperty( 'length' ) && ( 0 < elementorPopup.length ) ) {
                if ( ( 'elementor-editor' !== context ) && ( 'elementor-popup' !== context ) ) {
                    getBack = true;
                }
            }

            if ( ( '' !== context ) && ( 'object' === typeof elementorTabContent ) && elementorTabContent.hasOwnProperty( 'length' ) && ( 0 < elementorTabContent.length ) ) {
                if ( ( 'elementor-editor' !== context ) && ( 'elementor-tab' !== context ) && ( 'elementor-popup' !== context ) ) {
                    getBack = true;
                } else {
                    if ( 'elementor-tab' === context ) {
                        getBack = false;

                        if ( thisProductTableWrapper.hasClass( 'jtpt-product-table-inited' ) ) {
                            recalcResponsive = true;
                        }

                        recalcResponsive = ( ( true === alreadyInited ) ? true : recalcResponsive );
                    }
                }
            }

            // Get back.
            if ( true === getBack ) {
                return;
            }

            // Initialize DataTable.
            var productTableDataTable = initDataTableToProductTable( thisProductTableWrapper, productTableId, recalcResponsive );

            // DataTable search input move to custom input.
            searchInput.on( 'change keyup', function ( e ) {
                let thisField = $( this ),
                    searchKeyword = thisField.val();

                if ( ( 'change' === e.type ) && ( thisField.attr( 'data-inserted-keyword' ) === searchKeyword ) ) {
                    return;
                }

                thisField.attr( 'data-inserted-keyword', searchKeyword );
                productTableDataTable.search( searchKeyword ).draw();
            } );

            // Catch the first and last column on window resize.
            $( window ).on( 'resize', function ( e ) {
                tableHeadData.removeClass( 'jtpt-head-data-first' );
                tableHeadData.removeClass( 'jtpt-head-data-last' );

                var activeHeadData = tableHeadData.filter( function () {
                    var thisHeadData = $( this );

                    if ( 'none' !== thisHeadData.css( 'display' ) ) {
                        return thisHeadData;
                    }
                } );

                activeHeadData.first().addClass( 'jtpt-head-data-first' );
                activeHeadData.last().addClass( 'jtpt-head-data-last' );
            } );
        } );

        // Trigger window resize.
        $( window ).trigger( 'resize' );

    }

    /**
     * Elementor editor init.
     */
    function justTablesElementorEditorInit() {
        $( window ).on( 'elementor/frontend/init', function () {
            elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function ( $scope, $ ) {
                justTablesGlobal( 'elementor-editor' );
            } );
        } );
    }

    /**
     * Elementor popup init.
     */
    function justTablesElementorPopupInit() {
        $( document ).on( 'elementor/popup/show', function () {
            justTablesGlobal( 'elementor-popup' );
        } );
    }

    /**
     * Elementor tab init.
     */
    function justTablesElementorTabInit() {
        $( document ).on( 'click', '.elementor-tab-title', function () {
            justTablesGlobal( 'elementor-tab' );
        } );
    }

    /**
     * Global init.
     */
    function justTablesGlobalInit() {
        justTablesGlobal();
    }

    /**
     * Run this code for frontend or under Elementor.
     */
    if ( jtpt_data.elementor_editor_mode ) {
        justTablesElementorEditorInit();
    } else {
        justTablesElementorPopupInit();
        justTablesElementorTabInit();
        justTablesGlobalInit();
    }

} )( jQuery );