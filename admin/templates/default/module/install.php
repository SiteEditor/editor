<div class="wrap">
        <hr>
        <div id="sed_admin_settings_head">
            <H1><?php _e('SiteEditor Module Installer' , 'site-editor')?></H1>
        </div>
        <div class="sed_admin_settings_warp">
            <ul id="install-process">
                <li><span><?php _e('Start install Module' , 'site-editor')?></span>
                    <ul id="install-process-module">
                        <?php
                            if( !empty( $modules ) ){

                                foreach ( $modules as $module ){
                                    if( $sed_pb_modules->is_install( $module ) ){
                                        $module_name = $sed_pb_modules->get_module_name( $module );
                                        $sed_pb_modules->print_message( sprintf( __('%s Module already installed' , 'site-editor') , $module_name ) , "error" );
                                    }else{
                                        $sed_pb_modules->install( $module , false );
                                    }
                                }

                            }
                        ?>

                    </ul>
                </li>
            </ul>
        </div>
        <div id="sed_admin_settings_footer">
            <a href="<?php echo admin_url('admin.php?page=site_editor_module&amp;show_modules=' . $_REQUEST['show_modules'] . '&amp;paged=' . $_REQUEST['paged'] . '&amp;s=' . $_REQUEST['s'] );?>" class="button">
                <?php _e("Return to Modules Page","site-editor")?>
            </a>
        </div>
</div>