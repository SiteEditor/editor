<?php if(!empty($link)){ ?>                              

    <a href="<?php echo $link;?>" target="<?php echo $link_target;?>"  <?php echo $sed_attrs; ?> class=" sed-icons module module-single-icon single-icon-skin2 <?php echo $class;?>">

<?php }else{ ?>

    <div  <?php echo $sed_attrs; ?> class=" sed-icons module module-single-icon single-icon-skin2 <?php echo $class;?>">

<?php } ?>    

        <div class="hi-icon <?php echo $icon; ?>" sed-icon="<?php echo $icon; ?>"></div>

        <?php
            $selector = ( site_editor_app_on() || sed_loading_module_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
            ob_start();
        ?>

            <?php echo $selector; ?>.module.module-single-icon .hi-icon {
                border: <?php echo $border_size; ?>px solid <?php echo $border_color; ?>;
            }

        <?php
            $css = ob_get_clean();
            sed_module_dynamic_css( $css );
        ?>

<?php if(!empty($link)){ ?> 

    </a>

<?php }else{ ?>

    </div>

<?php } ?>

