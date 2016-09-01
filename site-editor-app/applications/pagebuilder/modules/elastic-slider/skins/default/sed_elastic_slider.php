<div <?php echo $sed_attrs; ?> class="elastic-container module module-elastic-slider <?php echo $class ?> " >
	<div class="ei-slider" id="<?php echo $module_html_id; ?>-ei" <?php echo $item_settings ;?> ><?php echo $content ?></div>
</div>
<?php
	global $sed_dynamic_css_string;
	$selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
	ob_start();
	?>
		<?php echo $selector; ?>.module-elastic-slider.elastic-container .ei-slider{
		    height : <?php echo $height ?>px;  
		}  
	<?php
	$css = ob_get_clean();
	$sed_dynamic_css_string .= $css;