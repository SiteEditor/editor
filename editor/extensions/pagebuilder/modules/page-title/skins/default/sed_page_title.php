<?php
if($length == "boxed")
    $length_class = "sed-row-boxed";
else
    $length_class = "sed-row-wide";
?>
<div <?php echo $sed_attrs; ?> sed_role="page-title-bar" class="module module-page-title page-title-default <?php echo $class; ?> " >
    <div class="page-title-inner <?php echo $length_class;?>" length_element>
        <?php echo $content;?>
    </div>
</div>