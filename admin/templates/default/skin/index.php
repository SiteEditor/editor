<div class="wrap">
<div class="fileedit-sub">
		<div class="alignleft">
				<h2><?php printf( __('Skins %s' , 'site-editor' ) , $modules[$module]['Name'] )?>
			<a href="<?php echo self_admin_url( add_query_arg( array("module" => $module ) , 'admin.php?page=site_editor_skin&action=add_skin' ) ); ?>" class="hide-if-no-js add-new-h2"><?php _e('Add New' , 'site-editor' )?></a>
	</h2>
		</div>
		<div class="alignright">
			<form method="get" action="">
				<strong><label><?php _e('Select module to show skins:' , 'site-editor' ) ?> </label></strong>
				<input type="hidden" value="site_editor_skin" name="page">
				<input type="hidden" value="skins" name="action">
  				<select name="module">
    				<?php foreach ( $modules as $module_file => $module_data ) {
    					$selected = ( $module_file == $module ) ? 'selected="selected"' : ''; ?>
    					<option value="<?php echo $module_file;?>" <?php echo $selected ?>><?php echo $module_data["Name"];?></option>
    				<?php
    				}	?>
				</select>
				<input name="Submit" id="Submit" class="button" value="<?php _e('Select' , 'site-editor' ) ?>" type="submit">	
			</form>
		</div>
		<br class="clear">
	</div>
<?php if ( isset( $massage ) && $massage != "" ):?>
<?php echo ($massage);?>
<?php endif;?>

	<div class="theme-browser rendered">
		<div class="themes">
<?php foreach ( $skins as $skin ) :
$url_edit = wp_nonce_url('admin.php?page=site_editor_edit_module&amp;module=' . $module . '&amp;skin=' . basename( $skin )   , 'sed-skin-module_' . $module);
if( !in_array( basename( $skin ) , $skins_installed ) )
    $inc_action = "install";
else
    $inc_action = "reinstall";

$install_url = wp_nonce_url('admin.php?page=site_editor_skin&amp;action=' . $inc_action . '&amp;module=' . $module . '&amp;skin=' . basename( $skin )   , 'sed-skin-module_' . $module);
?>
			<div aria-describedby="stars-ideas-action stars-ideas-name" tabindex="0" class="theme">
				<div class="theme-screenshot">
				<?php if(isset( $thumb[basename( $skin )] ) ) :?>
					<img src="<?php echo $thumb[basename( $skin )]?>" >
				<?php endif;?>
				</div>
				<h3 class="theme-name" id="stars-ideas-name"><?php echo basename( $skin ) ?></h3>
				<div class="theme-actions">
					<a class="button button-secondary load-customize hide-if-no-customize" href="<?php echo self_admin_url($url_edit)?>"><?php _e('Less Edit' , 'site-editor' ) ?> </a>
					<a class="button button-secondary load-customize hide-if-no-customize" href="<?php echo self_admin_url( $install_url )?>">
                    <?php
                      if( !in_array( basename( $skin ) , $skins_installed ) )
                          _e('Install' , 'site-editor' );
                      else
                          _e('Reinstall' , 'site-editor' );
                    ?>

                    </a>
				</div>
			</div>
<?php endforeach;?>

		</div>
	</div>
</div>
