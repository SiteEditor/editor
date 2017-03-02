<div class="sed-custom-fonts-control" id="<?php echo $control_id ;?>">

    <div class="sed-custom-fonts-accordion">

        <?php

        foreach ( $custom_fonts AS $font ){

            echo $this->custom_font_template( $font );

        }

        ?>

    </div>

    <button class="btn button-primary sed-new-custom-font-btn"><?php echo __("Add New Font","site-editor");?></button>

</div>