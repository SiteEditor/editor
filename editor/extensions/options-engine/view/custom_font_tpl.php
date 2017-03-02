<div class="sed-accordion-header" data-font-id="<?php echo $id;?>">

    <span class="sed-font-heading">
    <?php

    $heading = ( $font_title == "{{ data.font_title }}" || empty( $font_title ) ) ? __("Custom Font","site-editor") : $font_title;

    echo $heading;

    ?>
    </span>
    <div class="sed-custom-font-accordion-delete"><span class="icon-delete"></span></div> 
  
</div>

<div class="sed-accordion-content sed-custom-font-fields" data-font-id="<?php echo $id;?>">

    <div class="row_settings">

        <div class="row_setting_inner row_setting_box">

            <div class="clearfix">

                <label><?php echo __("Custom Font Title","site-editor");?></label>

                <input type="text" class="sed-bp-form-text sed-bp-input sed_custom_font_title" value="<?php echo $font_title;?>" />

            </div>

        </div>

    </div>

    <div class="row_settings">

        <div class="row_setting_inner row_setting_box">

            <div class="clearfix">

                <label><?php echo __("Custom Font Family","site-editor");?></label>

                <input type="text" class="sed-bp-form-text sed-bp-input sed_custom_font_family" value="<?php echo $font_family;?>" />

            </div>

        </div>

    </div>

    <div class="row_settings">

        <div class="row_setting_inner row_setting_box">

            <div class="clearfix">

                <label><?php echo __("Custom Font .woff","site-editor");?></label>

                <input type="text" class="sed-bp-form-text sed-bp-input media-url-field sed_custom_font_woff" value="<?php echo $font_woff;?>" disabled="disabled" />

                <button class="btn button-primary upload-btn" data-font-type="woff"><?php echo __("Upload","site-editor");?></button>

            </div>

        </div>

    </div>

    <div class="row_settings">

        <div class="row_setting_inner row_setting_box">

            <div class="clearfix">

                <label><?php echo __("Custom Font .ttf","site-editor");?></label>

                <input type="text" class="sed-bp-form-text sed-bp-input media-url-field sed_custom_font_ttf" value="<?php echo $font_ttf;?>" disabled="disabled" />

                <button class="btn button-primary upload-btn" data-font-type="ttf"><?php echo __("Upload","site-editor");?></button>

            </div>

        </div>

    </div>

    <div class="row_settings">

        <div class="row_setting_inner row_setting_box">

            <div class="clearfix">

                <label><?php echo __("Custom Font .svg","site-editor");?></label>

                <input type="text" class="sed-bp-form-text sed-bp-input media-url-field sed_custom_font_svg" value="<?php echo $font_svg;?>" disabled="disabled" />

                <button class="btn button-primary upload-btn" data-font-type="svg"><?php echo __("Upload","site-editor");?></button>

            </div>

        </div>

    </div>

    <div class="row_settings">

        <div class="row_setting_inner row_setting_box">

            <div class="clearfix">

                <label><?php echo __("Custom Font .eot","site-editor");?></label>

                <input type="text" class="sed-bp-form-text sed-bp-input media-url-field sed_custom_font_eot" value="<?php echo $font_eot;?>" disabled="disabled" />

                <button class="btn button-primary upload-btn" data-font-type="eot"><?php echo __("Upload","site-editor");?></button>

            </div>

        </div>

    </div>

</div>