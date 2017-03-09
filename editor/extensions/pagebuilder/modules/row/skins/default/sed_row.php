<?php

if(!empty($content)){
$wp_class = ( $from_wp_editor ) ? " sed-wp-editor-content" : "";
?>
       <div sed-layout-role="pb-module" class="sed-row-pb sed-bp-element <?php echo $class.$wp_class;?> <?php echo $length_class;?> <?php echo $sed_contextmenu_class;?>" <?php echo $sed_attrs; ?> data-type-row="<?php echo $type; ?>" length_element sed-role="row-pb">
       <?php         
       if( $from_wp_editor )
          echo wpautop( $content );
       else
          echo $content;
       ?>
       </div>
<?php }else{  ?>
      <div sed-layout-role="pb-module" class="sed-row-pb sed-bp-element <?php echo $class;?> <?php echo $length_class;?> <?php echo $sed_contextmenu_class;?>" <?php echo $sed_attrs; ?> data-type-row="<?php echo $type; ?>" length_element sed-role="row-pb">
      <div class="empty-row"><span class="drop-module-icon"></span><span class="drop-module-txt"><?php echo __('Drop A Module Here','site-editor'); ?></span></div>
      </div>
<?php } ?>
