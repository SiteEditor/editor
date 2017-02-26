<div <?php echo $sed_attrs; ?>  class="<?php echo $class ?> sed-stb-sm sed-ta-c module spcial-bar social-bar-skin2">
	<ul class="social-bar-<?php echo $layout_mode ?>">
		<?php echo $content ?>
	</ul>
</div>
<?php
    global $sed_dynamic_css_string;
    $selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
    ob_start();
    if( $layout_mode == "vertical"){?> 
            <?php echo $selector; ?> ul{
                margin-bottom: -<?php echo $margin; ?>px;
            }
            <?php echo $selector; ?> li{
                padding-bottom: <?php echo $margin; ?>px;
            } 
        <?php }else{ ?> 
            <?php echo $selector; ?> ul{
                margin-right: -<?php echo $margin; ?>px;
            }
            <?php echo $selector; ?> li{
                padding-right: <?php echo $margin; ?>px;
            }   
    <?php 
    }     
    $css = ob_get_clean();
    $sed_dynamic_css_string .= $css;  
