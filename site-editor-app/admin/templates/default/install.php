<div class="wrap">
        <hr>
        <div id="sed_admin_settings_head">
        	<H1><?php _e('SiteEditor Installer' , 'site-editor')?></H1>
        </div>
        <div class="sed_admin_settings_warp">
            <ul id="install-process">
                <li class="install-process-title"><span><?php _e('Installing SiteEditor Plugin ...' , 'site-editor')?></span>
                    <ul id="install-process-module">
                        <?php

                            if ( is_callable( array( $this , 'install' ) ) ) {
                                $this->install();
                            }else if( is_callable( array( $site_editor_install , 'install' ) ) ){

                                $site_editor_install->install();

                            }
                        ?>
                    </ul>
                </li>
            </ul>
        </div>
        <div id="sed_admin_settings_footer">
            <a href="<?php echo site_url("?editor=siteeditor"); ?>" class="button button-primary"><?php _e('Go To Site Editor' , 'site-editor')?></a>
            <a href="<?php echo admin_url('admin.php?page=site_editor_index&install=success') ?>" class="button button-primary"><?php _e('Go To Settings Page' , 'site-editor')?></a>
        </div>
</div>