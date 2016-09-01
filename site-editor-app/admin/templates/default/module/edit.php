<div class="wrap">
	<h2><?php echo $title ?></h2>
    <?php
        global $sed_pb_modules;
        //after array_reverse( $files )
        $current_less_file = isset( $_REQUEST['file'] ) ? $sed_pb_modules->module_basename( $_REQUEST['file'] ) : $files[0];
    ?>
	<div class="fileedit-sub">
		<div class="alignleft">
			<big><?php printf( __('Editing <strong>%s</strong>' , 'site-editor' ) , $current_less_file ) ?> <?php echo ( $is_active ? __('(active)' , 'site-editor') : '' ) ?></big>
			<?php if ( isset( $massage ) && $massage != ""):?>
		<?php echo ($massage);?>
	<?php endif;?>
		</div>
		<div class="alignright">
			<form method="post" action="<?php echo self_admin_url('admin.php?page=site_editor_edit_module') ?>">
				<strong>
                    <label>
                    <?php _e('Select module to edit:' , 'site-editor' ) ; ?>
                    </label>
                </strong>

  				<select name="module">
    				<?php foreach ( $modules as $module_file => $module_data ) {
    					$selected = ( $module_file == $module ) ? 'selected="selected"' : ''; ?>
    					<option value="<?php echo $module_file;?>" <?php echo $selected ?>><?php echo $module_data["Name"];?></option>
    				<?php
    				}	?>
				</select>
                <br />

				<strong>
                    <label>
                        <?php _e('Select skin to edit:' , 'site-editor' ) ; ?>
                    </label>
                </strong>

    			<select name="skin">
                    <option value=""><?php _e('All Skins And Other' , 'site-editor' );?></option>
    				<?php
                    if( !empty( $module_skins ) ){
                      foreach ( $module_skins as $skin_name ) {
      					$selected = ( $skin_name == $skin ) ? 'selected="selected"' : '';
                    ?>
    					<option value="<?php echo $skin_name;?>" <?php echo $selected ?>><?php echo $skin_name;?></option>
    				<?php
                      }
    				}
                    ?>
				</select>  <br>

				<input name="submit" class="button" value="<?php _e('Select' , 'site-editor' ) ?>" type="submit">
			</form>
		</div>
		<br class="clear">
	</div>

	<div id="templateside">
		<h3><?php _e('Files' , 'site-editor' ) ?></h3>

		<ul>
		    <?php


            foreach ( $files as $less_file ) {
    			$class = ( $less_file == $current_less_file ) ? 'class="highlight"' : '' ;

                $href = wp_nonce_url('admin.php?page=site_editor_edit_module&amp;module=' . $module . '&amp;file=' . $less_file . '&amp;skin=' . $skin   , 'sed-edit-module_' . $module_file);

			?>

    			<li <?php echo $class;?>>
    				<a href="<?php echo $href; ?>"><?php echo $less_file; ?></a>
    			</li>

			<?php }?>
		</ul>
	</div>
	<form name="template" id="template" method="post">

		<div>
			<textarea cols="70" rows="25" name="newcontent" id="newcontent"><?php echo $content; ?></textarea>
			<input name="action" value="update" type="hidden">
			<input name="file" value="<?php echo $current_less_file; ?>" type="hidden">
		</div>


		<p class="submit">
			<?php
				$submit_text = substr( $current_file , -5 ) == ".less" && SEDAppLess::is_main_less( $current_file ) ? __('Update And Compile' , 'site-editor' ) : __('Update File' , 'site-editor' );
			?>
			<input name="submit" id="submit" class="button button-primary" value="<?php echo $submit_text; ?>" type="submit">
		</p>
	</form>
	<br class="clear">
</div>