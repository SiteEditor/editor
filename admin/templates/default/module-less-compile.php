<div class="wrap">
        <hr>
        <div id="sed_admin_settings_head">
        	<H1><?php _e('Module Less Compile' , 'site-editor')?></H1>
        </div>
        <div class="sed_admin_settings_warp">
            <ul id="install-process">
                <li class="install-process-title"><span><?php _e('Compiling Less Files ...' , 'site-editor')?></span>
                    <ul id="install-process-module">
                        <?php
                            if( !class_exists( 'SEDPageBuilderModules' ) )
                                require_once SED_INC_DIR . DS . 'app_pb_modules.class.php';

                        SEDPageBuilderModules::all_less_compile();
                        ?>
                    </ul>
                </li>
            </ul>
        </div>
        <div id="sed_admin_settings_footer">
            <a href="<?php echo self_admin_url('admin.php?page=site_editor_index') ?>" class="button button-primary"><?php _e('Back' , 'site-editor')?></a>
        </div>
</div>