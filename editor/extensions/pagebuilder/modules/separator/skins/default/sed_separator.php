<div <?php echo $sed_attrs; ?>  class="module module-separator separator-skin-default <?php echo $class;?>" >
	<div class="separator <?php echo $type; ?> <?php echo $border_style;?>"></div> 
</div>
<?php
	global $sed_dynamic_css_string;
	$selector = ( site_editor_app_on() || sed_loading_module_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
	ob_start();
	?>

		<?php echo $selector; ?>.module-separator .separator {
		    border-color: <?php echo $separator_color;?>; 
		}

		<?php echo $selector; ?> .spr-vertical {
			min-height: <?php echo $vertical_height;?>px;
		}
		<?php echo $selector; ?> .spr-horizontal {
			max-width: <?php echo $max_width;?>px;
		}

		<?php echo $selector; ?> .separator.spr-horizontal  {
		    border-width: <?php echo $separator_width;?>px 0 0 0; 
		}

		<?php echo $selector; ?> .separator.spr-horizontal.spr-double {
		    border-width: <?php echo $separator_width;?>px 0 <?php echo $separator_width;?>px 0 ;
		}

		<?php echo $selector; ?> .separator.spr-vertical  { 
		    border-width: 0 0 0 <?php echo $separator_width;?>px;
		}
		
		<?php echo $selector; ?> .separator.spr-vertical.spr-double {
		    border-width: 0 <?php echo $separator_width;?>px 0 <?php echo $separator_width;?>px ;
		}  


	<?php
	$css = ob_get_clean();
	$sed_dynamic_css_string .= $css;