<td class="sed-column-pb <?php echo $class;?>" <?php echo $sed_attrs; ?>" sed-role="column-pb">               
  <div class="sed-column-contents-pb bp-component" <?php if( site_editor_app_on() ) echo 'data-parent-id="' . $sed_model_id . '"'; ?> drop-placeholder="<?php echo $placeholder; ?>">
      <?php echo  $content; ?>
  </div>
</td>
<?php  
    global $sed_dynamic_css_string;
    $selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
    ob_start();
    ?>
        <!--  
        <?php echo $selector; ?> {  
          width : <?php echo  $width;?>;
        }
        -->
    <?php
    $css = ob_get_clean();
    $sed_dynamic_css_string .= $css;

