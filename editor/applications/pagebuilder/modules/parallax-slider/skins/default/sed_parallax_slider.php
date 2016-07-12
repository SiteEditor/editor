<div id="<?php echo $module_html_id; ?>" style="height:<?php echo $parallax_height;?>px" <?php echo $sed_attrs; ?> class="<?php echo $class?> module parallax-slider parallax-slider-default " <?php echo $item_settings ?>>
    <div class=" pxs_navigation">                                                                                                                                                              
    	<span style="right:<?php echo $parallax_nav_space?>%" class="pxs_next"></span>
    	<span style="left:<?php echo $parallax_nav_space?>%" class="pxs_prev"></span>
    </div>
    <div class="pxs_bg">
        <div class="pxs_bg1"></div>
        <div class="pxs_bg2"></div>
        <div class="pxs_bg3"></div>
    </div>
    <div class="pxs_loading"><?php _e("Loading images...","site-editor")?></div>
	<?php echo $content?>
</div>
<?php
    global $sed_dynamic_css_string;
    $selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
    ob_start();
    ?>
        <?php echo $selector; ?> .image-container{
            width:  <?php echo $parallax_item_width?>%;
            top:    <?php echo $parallax_item_top?>px;
            bottom: <?php echo $parallax_item_bottom?>px;
            left:   <?php echo $parallax_item_left?>%;
        }
    <?php
    $css = ob_get_clean();
    $sed_dynamic_css_string .= $css;