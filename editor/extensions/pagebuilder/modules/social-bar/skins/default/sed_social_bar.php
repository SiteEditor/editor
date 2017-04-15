<div <?php echo $sed_attrs; ?>  class="<?php echo $class ?>  module spcial-bar social-bar-default">
	<ul class="social-bar-default social-bar-<?php echo $layout_mode ?>">
		<?php echo $content ?>
	</ul>
    <?php
        $selector = ( site_editor_app_on() || sed_loading_module_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
        ob_start();

    if( $layout_mode == "vertical"){
    ?> 

        <?php echo $selector; ?> li{
            padding-bottom: <?php echo $margin; ?>px;
        } 

    <?php }else{ ?> 

        <?php echo $selector; ?> li{
            padding-right: <?php echo $margin; ?>px;
        }   

    <?php 
    }     
    
        $css = ob_get_clean();
        sed_module_dynamic_css( $css );
    ?>    
</div>  
