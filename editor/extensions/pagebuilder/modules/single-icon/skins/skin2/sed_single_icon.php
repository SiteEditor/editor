<?php if(!empty($link)){ ?>                              

    <a href="<?php echo $link;?>" target="<?php echo $link_target;?>"  <?php echo $sed_attrs; ?> class="s-tb-sm ta-c sed-icons module module-single-icon single-icon-skin2 <?php if( $style ){ echo $hover_effect; } ?> <?php echo $class;?>" style="font-size:<?php echo $font_size; ?>px;" >
      <span class="hi-icon <?php echo $icon; ?> <?php echo $type;?>  <?php echo $style;?>" sed-icon="<?php echo $icon; ?>" style="font-size:<?php echo $font_size; ?>px;color:<?php echo $color; ?>">
      </span>
    </a>

<?php }else{ ?>

    <div  <?php echo $sed_attrs; ?> class="s-tb-sm ta-c sed-icons module module-single-icon single-icon-skin2 <?php if( $style ){ echo $hover_effect; } ?> <?php echo $class;?>" style="font-size:<?php echo $font_size; ?>px;" >
      <span class="hi-icon <?php echo $icon; ?> <?php echo $type;?>  <?php echo $style;?>" sed-icon="<?php echo $icon; ?>" style="font-size:<?php echo $font_size; ?>px;color:<?php echo $color; ?>">
      </span>
    </div>

<?php } ?>
<?php
    global $sed_dynamic_css_string;
    $selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
    ob_start();
    ?>
        <?php if( $background_color && !$style ){ ?>
        <?php echo $selector; ?> .hex-icon:before,
        <?php echo $selector; ?> .icon-ring,
        <?php echo $selector; ?> .icon-default {
            background-color: <?php echo $background_color; ?>;
        }

        <?php } ?>
        <?php if( $border_color && !$style ){ ?>
            <?php echo $selector; ?> .icon-default,
            <?php echo $selector; ?> .icon-flat,
            <?php echo $selector; ?> .icon-ring:after {
                box-shadow:0 0 0 0.07em <?php echo $border_color; ?>;
            }
        <?php } ?>
    <?php
    $css = ob_get_clean();
    $sed_dynamic_css_string .= $css;

