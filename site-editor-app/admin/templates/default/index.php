<?php
if ( isset( $massage ) && $massage != "" ):?>
    <div id="message" class="updated notice is-dismissible">
      <p>
      <?php echo ($massage);?>
      </p>
      <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
<?php endif;?>
<div class="wrap siteeditor_admin_index_page">
<?php
    $url_editor = get_sed_url();
?>

        <div id="sed_admin_settings_head">
        	<H1><?php _e('Welcome to SiteEditor' , 'site-editor')?></H1>
        </div>
        <form method="post">

	        <div class="sed_admin_settings_warp">
	        	<?php $admin_options->show();?>
	        </div>

	        <div class="sed_admin_save_options_warp">
	        	<input type="hidden" name="action" value="save">
	        	<input type="submit" class="button button-primary" value="<?php _e('Save Options' , 'site-editor')?>">
   	        </div>

      </form>
	        <div id="sed_admin_settings_footer">

                <form method="post" style="display:inline">
                    <input type="hidden" name="action" value="compile_framework">
                    <input type="submit" class="button button-primary" value="<?php _e('Compile Framework' , 'site-editor')?>" onclick="return confirm('<?php _e( 'Are you sure you want to Compile less Framework?' , 'site-editor' ) ?>');">
                </form>
                <a href="<?php echo admin_url( 'admin.php?page=site_editor_index&action=module_less_compile' ); ?>" class="button button-primary"><?php _e('Less compile for all modules ' , 'site-editor')?></a>
                <form method="post" style="display:inline">
                    <input type="hidden" name="action" value="reset">
                    <input type="submit" class="sed_button sed_button_red" value="<?php _e('Reset Options' , 'site-editor')?>" onclick="return confirm('<?php _e( 'Are you sure you want to reset bach to the default?' , 'site-editor' ) ?>');">
                </form>
                <a href="<?php echo $url_editor ?>" class="sed_button sed_button_red"><?php _e('Go To site editor page' , 'site-editor')?></a>
	        </div>


</div>