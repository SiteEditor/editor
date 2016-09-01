<?php
$order     = wc_get_order( $order_id );
?>
<div class="woocommerce-info-box">
    <h3 class="title-box"><?php _e( 'Order Details', 'woocommerce' ); ?></h3>
    <table class="table sed-simple-table  table-order-details">
        <thead>
            <tr>
                <th class="product-thumbnail"><?php _e( 'Thumbnail', 'woocommerce' ); ?></th>
                <th class="product-name"><?php _e( 'Name', 'woocommerce' ); ?></th>
                <th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
                <th class="product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ( sizeof( $order->get_items() ) > 0 ) {

                foreach( $order->get_items() as $item ) {
                    $_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
                    $item_meta    = new WC_Order_Item_Meta( $item['item_meta'], $_product );

                    ?>
                    <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
                        <td>
                            <?php
                            $thumb_id = get_post_thumbnail_id( $item['product_id'] );
                            $image    = sed_get_attachment( $thumb_id );
                            $src = wp_get_attachment_image_src( $image->ID , 'shop_thumbnail');
                           ?>

                           <div>
                            <img src="<?php echo $src[0] ?>" height="<?php echo $src[2] ?>" width="<?php echo $src[1] ?>" alt="<?php echo $image->alt ?>" title="<?php echo $image->post_title ?>" >
                           </div>

                        </td>
                        <td class="product-name">
                            <?php
                                if ( $_product && ! $_product->is_visible() )
                                    echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item );
                                else
                                    echo apply_filters( 'woocommerce_order_item_name', sprintf( '<a href="%s">%s</a>', get_permalink( $item['product_id'] ), $item['name'] ), $item );


                                $item_meta->display();

                                if ( $_product && $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted() ) {

                                    $download_files = $order->get_item_downloads( $item );
                                    $i              = 0;
                                    $links          = array();

                                    foreach ( $download_files as $download_id => $file ) {
                                        $i++;

                                        $links[] = '<small><a href="' . esc_url( $file['download_url'] ) . '">' . sprintf( __( 'Download file%s', 'woocommerce' ), ( count( $download_files ) > 1 ? ' ' . $i . ': ' : ': ' ) ) . esc_html( $file['name'] ) . '</a></small>';
                                    }

                                    echo '<br/>' . implode( '<br/>', $links );
                                }
                            ?>
                        </td>
                        <td class="product-quantity">
                            <?php echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong>' . sprintf( '&times; %s', $item['qty'] ) . '</strong>', $item );  ?>
                        </td>
                        <td class="product-total">
                            <?php echo $order->get_formatted_line_subtotal( $item ); ?>
                        </td>
                    </tr>
                    <?php

                    if ( $order->has_status( array( 'completed', 'processing' ) ) && ( $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) ) ) {
                        ?>
                        <tr class="product-purchase-note">
                            <td colspan="3"><?php echo wpautop( do_shortcode( $purchase_note ) ); ?></td>
                        </tr>
                        <?php
                    }
                }
            }

            do_action( 'woocommerce_order_items_table', $order );
            ?>
            <?php
                if ( $totals = $order->get_order_item_totals() ) foreach ( $totals as $total ) :
                    ?>
                    <tr>
                        <th colspan="3" scope="row"><?php echo $total['label']; ?></th>
                        <td class="product-total"><?php echo $total['value']; ?></td>
                    </tr>
                    <?php
                endforeach;
            ?>
        </tbody>
    </table>

    <?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

</div><!-- /.col2-set -->
<div class="woocommerce-info-box">
    <h3 class="title-box"><?php _e( 'Customer details', 'woocommerce' ); ?></h3>
    <table class="table sed-simple-table table-customer-details">
        <?php if ( $order->billing_email  ): ?>
        <tr>
            <th><?php _e( 'Email:', 'woocommerce' ); ?></th>
            <td><?php echo $order->billing_email ?></td>
        </tr>
        <?php endif ?>
        <?php if ( $order->billing_phone  ): ?>
        <tr>
            <th><?php _e( 'Telephone:', 'woocommerce' ); ?></th>
            <td><?php echo $order->billing_phone; ?></td>
        </tr>
        <?php endif ;
            // Additional customer details hook
            do_action( 'woocommerce_order_details_after_customer_details', $order );
        ?>
    </table>
        <?php
        $Shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && get_option( 'woocommerce_calc_shipping' ) !== 'no'  ;
        ?>
        <div class="row">
            <div class="col-lg-<?php echo ( $Shipping ? 6 : 12 ) ?> woo-order-address">
                <h4><?php _e( 'Billing Address', 'woocommerce' ); ?></h4>
                <?php
                        if ( ! $order->get_formatted_billing_address() ) 
                            _e( 'N/A', 'woocommerce' );
                        else 
                            echo $order->get_formatted_billing_address();
                    ?>
            </div>
    <?php if ( $Shipping ) : ?>
            <div class="col-lg-6 woo-order-address">
                <h4><?php _e( 'Shipping Address', 'woocommerce' ); ?></h4>
                    <?php
                        if ( ! $order->get_formatted_shipping_address() ) 
                            _e( 'N/A', 'woocommerce' ); 
                        else echo $order->get_formatted_shipping_address();
                    ?>
            </div>
    <?php endif;?>
        </div>


   
</div>