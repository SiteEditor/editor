<td class="sed-column-pb <?php echo $class;?>" <?php echo $sed_attrs; ?>" sed-role="column-pb">
<?php
    if( $sed_main_content == "no" ) {
        ?>
        <div class="sed-column-contents-pb sed-pb-component" <?php if (site_editor_app_on()) echo 'data-parent-id="' . $sed_model_id . '"'; ?> drop-placeholder="<?php echo $placeholder; ?>">
            <?php echo $content; ?>
        </div>
        <?php
    }else {
        ?>
        <div class="sed-column-contents-pb sed-main-content-column">
            <?php echo $content; ?>
        </div>
        <?php
    }
    ?>
    <?php
        $selector = ( site_editor_app_on() || sed_loading_module_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
        ob_start();
    ?>
        <!--  
        <?php echo $selector; ?> {  
          width : <?php echo  $width;?>;
        }
        -->
    <?php
        $css = ob_get_clean();
        sed_module_dynamic_css( $css );
    ?>
</td>

