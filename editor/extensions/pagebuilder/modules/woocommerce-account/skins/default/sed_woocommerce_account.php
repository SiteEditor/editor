<?php global $current_user;
if( is_user_logged_in() ){
    $class_col = "col-xs-12 col-sm-9";
}else{
    $class_col = "col-xs-12";
}?>
<div <?php echo $sed_attrs; ?> class="<?php echo $class; ?>  modules-woocommerce module module-woocommerce-account woocommerce-account-default">
    <div class="row">
    <?php if( is_user_logged_in() ):?>
        <div class="col-xs-12 col-sm-3">
            <div class="woocommerce-sidebar-account">
                <div class="widget-side woocommerce-user-info">
                    <div class="woocommerce-my-avatar">
                        <?php echo get_avatar( $current_user->user_email , 150 , $current_user->display_name ); ?>
                    </div>
                    <ul class="woocommerce-user-meta">
                        <li><i class="fa fa-user"></i><?php echo $current_user->display_name ?></li>
                        <li><i class="fa fa-envelope-o"></i> <?php echo $current_user->user_email ?></li>
                    </ul>
                </div>
                <div class="widget-side woocommerce-user-action">
                    <ul>
                    <?php $edit_address = get_query_var( 'edit-address' );  ?>
                        <li class=" <?php if( empty( $edit_address ) && get_query_var( 'edit-account' , false ) === false ) echo "active";?>"><a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ); ?>"><i class="fa fa-home"></i> <?php echo __( 'Overview Account', 'woocommerce' ); ?></a></li>
                        <li class=" <?php if($edit_address == 'billing' ) echo "active";?>"><a href="<?php echo wc_get_endpoint_url( 'edit-address', 'billing' ); ?>"><i class="fa fa-money"></i> <?php printf( __( 'Edit %s', 'woocommerce' ) , __( 'Billing Address', 'woocommerce' ) ); ?></a></li>
                        <li class=" <?php if($edit_address == 'shipping' ) echo "active";?>"><a href="<?php echo wc_get_endpoint_url( 'edit-address', 'shipping' ); ?>"><i class="fa fa-map-marker"></i> <?php printf( __( 'Edit %s', 'woocommerce' ) , __( 'Shipping Address', 'woocommerce' ) ); ?></a></li>
                        <li class=" <?php if(get_query_var( 'edit-account' , false ) !== false ) echo "active";?>"><a href="<?php echo wc_customer_edit_account_url() ?>"><i class="fa fa-user"></i> <?php _e( 'Edit My Account', 'site-editor' )?></a></li>
                        <li><a href="<?php echo wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ) ?>"><i class="fa fa-sign-out"></i> <?php _e( 'Sign out', 'site-editor' )?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
        <div class="<?php echo $class_col ?> woo-account-page ">
            <div class="woocommerce-account-page">
            <?php if( is_user_logged_in() ):?>
                <!--<h3><?php printf( __("Welcome %s","site-editor") , $current_user->display_name )?>
                    <span><?php
                                printf(
                        __( '(not %1$s? <a href="%2$s">Sign out</a>).', 'woocommerce' ) . ' ',
                        $current_user->display_name,
                        wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) )
                    );?>
                    </span>
                </h3>-->
            <?php endif; ?>
                <?php if( have_posts() ):
                the_post();?>
                    <?php the_content();?>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>

</div>