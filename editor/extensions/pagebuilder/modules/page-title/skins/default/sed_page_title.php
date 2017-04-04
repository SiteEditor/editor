<?php
if($length == "boxed")
    $length_class = "sed-row-boxed";
else
    $length_class = "sed-row-wide";
?>
<div <?php echo $sed_attrs; ?> sed_role="page-title-bar" class="module module-page-title page-title-default <?php echo $class; ?> " >

    <div class="page-title-inner <?php echo $length_class;?>" length_element>


        <div class="page-title-continer">

            <h3> <?php echo PBPageTitleShortcode::get_title(); ?> </h3>
            <?php if( $show_sub_title ){ ?>
                <p><?php echo esc_html( $sub_title ); ?> </p>
            <?php } ?>
        </div>

    </div>

</div>