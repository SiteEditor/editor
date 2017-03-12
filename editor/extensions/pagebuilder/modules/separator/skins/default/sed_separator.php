<div <?php echo $sed_attrs; ?>  class="module module-separator separator-skin-default <?php echo $class;?> <?php echo $border_style;?>  <?php echo $type; ?>" ></div>
<?php
	global $sed_dynamic_css_string;
	$selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
	ob_start();
	?>
		<?php echo $selector; ?>.spr-vertical,
		<?php echo $selector; ?>.spr-shadow-right:after,
		<?php echo $selector; ?>.spr-shadow-left:after {
			min-height: <?php echo $vertical_height;?>px;
		}
		<?php echo $selector; ?>.spr-horizontal {
			max-width: <?php echo $max_width;?>px;
		}
	<?php
	$css = ob_get_clean();
	$sed_dynamic_css_string .= $css;