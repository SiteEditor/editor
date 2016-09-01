<?php
do_action( 'woocommerce_before_shipping_calculator' ); ?>
<form class="shipping_calculator" action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">

    <section class="sed-shipping-calculator-form">

        <div class="form-group">
            <select name="calc_shipping_country" id="calc_shipping_country" class="form-control country_to_state" rel="calc_shipping_state">
                <option value=""><?php _e( 'Select a country&hellip;', 'woocommerce' ); ?></option>
                <?php
                    foreach( WC()->countries->get_shipping_countries() as $key => $value )
                        echo '<option value="' . esc_attr( $key ) . '"' . selected( WC()->customer->get_shipping_country(), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
                ?>
            </select>
        </div>

        <div class="form-group">
            <?php
                $current_cc = WC()->customer->get_shipping_country();
                $current_r  = WC()->customer->get_shipping_state();
                $states     = WC()->countries->get_states( $current_cc );

                // Hidden Input
                if ( is_array( $states ) && empty( $states ) ) {

                    ?><input type="hidden" name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>" /><?php

                // Dropdown Input
                } elseif ( is_array( $states ) ) {

                    ?><span><select name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>">
                            <option value=""><?php _e( 'Select a state&hellip;', 'woocommerce' ); ?></option>
                            <?php
                                foreach ( $states as $ckey => $cvalue )
                                    echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $current_r, $ckey, false ) . '>' . __( esc_html( $cvalue ), 'woocommerce' ) .'</option>';
                            ?>
                        </select></span><?php

                // Standard Input
                } else {

                    ?><input type="text" class="input-text form-control" value="<?php echo esc_attr( $current_r ); ?>" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>" name="calc_shipping_state" id="calc_shipping_state" /><?php

                }
            ?>
        </div>

        <?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_city', false ) ) : ?>

            <div class="form-group">
                <input type="text" class="input-text form-control" value="<?php echo esc_attr( WC()->customer->get_shipping_city() ); ?>" placeholder="<?php _e( 'City', 'woocommerce' ); ?>" name="calc_shipping_city" id="calc_shipping_city" />
            </div>
        <?php endif; ?>

        <?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_postcode', true ) ) : ?>

            <div class="form-group">
                <input type="text" class="input-text form-control" value="<?php echo esc_attr( WC()->customer->get_shipping_postcode() ); ?>" placeholder="<?php _e( 'Postcode / Zip', 'woocommerce' ); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
            </div>

        <?php endif; ?>

        <div class="form-group update-cart">
            <button type="submit" name="calc_shipping" value="1" class="btn btn-main btn-sm"><?php _e( 'Update Totals', 'woocommerce' ); ?></button></div>
        <?php wp_nonce_field( 'woocommerce-cart' ); ?>
    </section>
</form>

<?php do_action( 'woocommerce_after_shipping_calculator' ); ?>
