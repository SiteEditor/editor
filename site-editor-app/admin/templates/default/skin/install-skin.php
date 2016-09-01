<div class="wrap">
        <hr>
        <div id="sed_admin_settings_head">
            <H1><?php _e('SiteEditor Installer' , 'site-editor')?></H1>
        </div>
        <div class="sed_admin_settings_warp">

            <ul id="install-process">
                <li><span><?php printf( __("Install skin for %s" , "site-editor" ) , $module_name ) ?></span>
                    <ul id="install-process-module">
                        <?php 
                        switch ( @$_REQUEST['action'] ) {
                            case 'reinstall':
                                $result = $sed_pb_modules->sed_skin->reinstall_skin( $module , $skin );
                                if( !$result || is_wp_error( $result ) ){
                                    if( is_wp_error( $result ) ){
                                        $sed_pb_modules->print_message( sprintf( __( "An error occurred in the skin installation : %s" ,"site-editor" ) , $result->get_error_message() )  , "error" );
                                    }else{
                                        $sed_pb_modules->print_message( __( "An error occurred in the skin installation  " ,"site-editor" ) , "error" );
                                    }
                                }
                            break;
                            case 'install':
                                $result = $sed_pb_modules->sed_skin->install_skin( $module , $skin );
                                if( !$result || is_wp_error( $result ) ){
                                    if( is_wp_error( $result ) ){
                                        $sed_pb_modules->print_message( sprintf( __( "An error occurred in the skin installation : %s" ,"site-editor" ) , $result->get_error_message() )  , "error" );
                                    }else{
                                        $sed_pb_modules->print_message( __( "An error occurred in the skin installation  " ,"site-editor" ) , "error" );
                                    }
                                }
                            break;
                        }
                        ?>
                    </ul>
                </li>
            </ul>

        </div>

        <div id="sed_admin_settings_footer">
            <a href="<?php echo wp_nonce_url('admin.php?page=site_editor_skin&amp;module=' . $module  , 'sed-skin-module_' . $module); ?>" class="button">
                <?php _e("Return to Skins Page","site-editor")?>
            </a>
        </div>
</div>