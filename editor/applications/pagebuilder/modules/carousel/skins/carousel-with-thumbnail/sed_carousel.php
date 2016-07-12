<div <?php echo $sed_attrs; ?> <?php echo $item_settings;?> class="<?php echo $class ?> s-tb-sm module module-carousel" >
    <?php echo $content;?>
</div>
<?php
	global $sed_dynamic_css_string;
	$selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : $sed_custom_css_class;
	ob_start();
	?>
		<?php echo $selector; ?> .slick-slide{
		  margin-left: <?php echo $items_spacing; ?>px;
		}
		<?php echo $selector; ?> .slick-list{
		  margin-left: -<?php echo $items_spacing; ?>px;
		}
	<?php
	$css = ob_get_clean();
	$sed_dynamic_css_string .= $css;