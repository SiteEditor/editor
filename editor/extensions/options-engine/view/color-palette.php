<div class="sed-color-palettes" id="<?php echo $control_id ;?>">

    <ul class="sed-dropdown">

        <?php
        $selected_class = ( "default" == $color_scheme ) ? "selected-palette" : "";
        ?>

        <li class="sed-palette-item <?php echo $selected_class;?>" data-value="default">
            <label for="<?php echo "palette_default";?>">
                <div class="sed-palette-text">
                    <span><?php echo __("Default" , "site-editor");?></span>
                </div>
                <div class="sed_color_palette">
                    <ul id="sed_custom_palette">
                        <li class="sed-palette1"></li>
                        <li class="sed-palette2"></li>
                        <li class="sed-palette3"></li>
                        <li class="sed-palette4"></li>
                        <li class="sed-palette5"></li>
                    </ul>
                </div>
            </label>
        </li>

        <?php
        foreach ( $color_schemes AS $id => $options ) {

            $selected_class = ( $id == $color_scheme ) ? "selected-palette" : "";

            ?>
            <li class="sed-palette-item <?php echo $selected_class;?>" data-value="<?php echo $id;?>">
                <label for="<?php echo "palette_{$id}"; ?>">
                    <div class="sed-palette-text">
                        <span><?php echo $options['label'];?></span>
                    </div>
                    <div class="sed_color_palette">
                        <ul id="sed_color_palette1">
                            <li class="sed-palette1" style="background-color:<?php echo $options['colors'][0];?>;"></li>
                            <li class="sed-palette2" style="background-color:<?php echo $options['colors'][1];?>;"></li>
                            <li class="sed-palette3" style="background-color:<?php echo $options['colors'][2];?>;"></li>
                            <li class="sed-palette4" style="background-color:<?php echo $options['colors'][3];?>;"></li>
                            <li class="sed-palette5" style="background-color:<?php echo $options['colors'][4];?>;"></li>
                        </ul>
                    </div>
                </label>
            </li>
            <?php

        }
        ?>

    </ul>

</div>