<?php
    global $sed_data;  
?>
<div <?php echo $sed_attrs; ?> data-contextmenu-post-id="<?php echo get_the_ID();?>" data-sed-page-role="page-module-container" class="module single-page single-page-default <?php echo $class; ?>" >
    <?php echo $content ?>
</div>
