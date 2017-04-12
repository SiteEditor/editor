<?php

if(!empty($content)){
$wp_class = ( $from_wp_editor ) ? " sed-wp-editor-content" : "";
?>
       <div sed-layout-role="pb-module" class="<?php if($is_sticky) echo "sed-pb-row-sticky";?> sed-row-pb sed-bp-element sed-stb-sm <?php echo $class.$wp_class;?> <?php echo $length_class;?> <?php //echo $sed_contextmenu_class;?>" <?php echo $sed_attrs; ?> data-type-row="<?php echo $type; ?>" length_element sed-role="row-pb">
       <?php         
       if( $from_wp_editor )
          echo wpautop( $content );
       else
          echo $content;
       ?>
       </div>
<?php }else{  ?>
      <div sed-layout-role="pb-module" class="sed-row-pb sed-bp-element sed-stb-sm <?php echo $class;?> <?php echo $length_class;?> <?php //echo $sed_contextmenu_class;?>" <?php echo $sed_attrs; ?> data-type-row="<?php echo $type; ?>" length_element sed-role="row-pb">
        <div class="empty-row"><span class="drop-module-icon"></span><span class="drop-module-txt"><?php echo __('Drop A Module Here','site-editor'); ?></span></div>
      </div>
<?php } ?>
<?php
    global $sed_dynamic_css_string;
    $selector = ( site_editor_app_on() || sed_loading_module_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
    ob_start();
    ?>

        @media (max-width: 768px){
            <?php echo $selector; ?> {
                <?php if(!empty($rps_spacing_top)){ ?>     padding-top:    <?php echo $rps_spacing_top;?>px !important;    <?php } ?>
                <?php if(!empty($rps_spacing_right)){ ?>   padding-right:  <?php echo $rps_spacing_right;?>px !important;  <?php } ?>
                <?php if(!empty($rps_spacing_bottom)){ ?>  padding-bottom: <?php echo $rps_spacing_bottom;?>px !important; <?php } ?>
                <?php if(!empty($rps_spacing_left)){ ?>    padding-left:   <?php echo $rps_spacing_left;?>px !important;   <?php } ?>
                <?php if(!empty($rps_align)){ ?>           text-align:     <?php echo $rps_align;?> !important;            <?php } ?>
            }        
        }     

    <?php
    $css = ob_get_clean();
    $sed_dynamic_css_string .= $css;
    
