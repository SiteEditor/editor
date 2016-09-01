<?php
/**
* Filter tabs and allow third parties to add their own
*
* Each tab is an array containing title, callback and priority.
* @see woocommerce_default_product_tabs()
*/
$counter = 0 ;
$tabs = apply_filters( 'woocommerce_product_tabs', array() );
if ( ! empty( $tabs ) ) : ?>
<div class="single-product-tabs" role="tabpanel">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ( $tabs as $key => $tab ) :
            if( $counter == 0 )
                $class = "active";
            else
                $class  = "";
            $counter++;
        ?>

            <li role="presentation" class="<?php echo $class . ' ' . $key ?>_tab">
                <a href="#tab-<?php echo $key ?>" aria-controls="<?php echo $tab['title'] ?>" role="tab" data-toggle="tab"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></a>
            </li>

        <?php endforeach; ?>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <?php 
$counter = 0 ;
        foreach ( $tabs as $key => $tab ) : 
            if( $counter == 0 ) 
                $class = "active";
            else 
                $class  = "";
            $counter++;
            ?>

            <div role="tabpanel" class="tab-pane <?php echo $class ?> entry-content" id="tab-<?php echo $key ?>">
                <?php call_user_func( $tab['callback'], $key, $tab ) ?>
            </div>

        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>