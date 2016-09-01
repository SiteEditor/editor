<div <?php echo $sed_attrs; ?> class="s-tb-sm module module-image-content-box image-content-box-default <?php echo $class;?>" >
    <div class="item">
        <div class="inner <?php echo $arrow; ?> <?php if(!$show_button){ ?> hide-button <?php } if($item_bodered > 0){ ?> item-bodered <?php } ?>">
           <?php echo $content; ?>
        </div><!-- .inner -->
    </div><!-- .item -->
</div>
<?php
	global $sed_dynamic_css_string;
	$selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
	ob_start();
	?> 
		<?php echo $selector; ?> .inner{
		    border-width: <?php echo $item_bodered; ?>px;
		}
		<?php echo $selector; ?> .img-item{
		    padding: <?php echo $item_img; ?>px <?php echo $item_img; ?>px 0;
		}     
	<?php     
	$css = ob_get_clean();
	$sed_dynamic_css_string .= $css; 