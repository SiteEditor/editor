<div class="wrap">
    <h2> <?php _e("Modules" , "site-editor" ) ?></h2>

    <?php
    $table->prepare_items();
    $table->views();
     ?>

    <form id="sed-search-modules" name="sed-search-modules" action="<?php echo esc_attr( esc_url( admin_url("admin.php") ) );?>" method="get">
        <?php
        $table->search_box( __( 'Search Installed modules' , 'site-editor' ), 'module' );
        ?>
        <input type="hidden" name="page" value="site_editor_module" />
    </form>

    <form id="sed-bulk-action-form" name="sed-bulk-action-form" action="<?php echo esc_attr( esc_url( admin_url("admin.php?page=site_editor_module") ) );?>" method="post">
        <input type="hidden" name="show_modules" value="<?php echo esc_attr($status) ?>" />
        <input type="hidden" name="paged" value="<?php echo esc_attr($paged) ?>" />
        <input type="hidden" name="s" value="<?php _admin_search_query(); ?>" />
        <?php
            $table->display();
        ?>
    </form>

</div>