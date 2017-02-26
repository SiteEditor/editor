<div <?php echo $sed_attrs; ?> class="sed-stb-sm sed-sidebar module module-sidebar sidebar-skin-default <?php echo $class ;?>"  >

    <?php

    if( is_active_sidebar( $sidebar ) ){
        ?>

        <aside class="sidebar widget-area <?php esc_attr( $sidebar );?>" role="complementary">
            <?php dynamic_sidebar( $sidebar ); ?>
        </aside><!-- .sidebar .widget-area -->

        <?php
    }

    ?>

</div>