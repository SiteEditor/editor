<div <?php echo $sed_attrs; ?> class="s-tb-sm module module-masonery-gallery masonry-gallery-default sed-columns-<?php echo $number_columns;?> <?php echo $class  ; ?>" >
    <?php echo $content;?>                                                                                                                      
</div>

<?php
	global $sed_dynamic_css_string;
	$selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
	ob_start();
	?>
		<?php echo $selector; ?> .item{
		  padding: <?php echo $items_spacing;?>px;
		}
		<?php echo $selector; ?> .items-container{
		  margin: -<?php echo $items_spacing;?>px;
		}	  
	<?php
	$css = ob_get_clean();
	$sed_dynamic_css_string .= $css;