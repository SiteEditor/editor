<?php
    /*var api = sedApp.editor ;

    $imageHtml = api.fn.getAttachmentImageHtml( image_field_attr , image_size_field_attr );

    if( $video_field_attr > 0 ){
        $videoAttachment = _.findWhere( api.attachmentsSettings , { id : video_field_attr}  );
    }

    if( !_.isUndefined( videoAttachment ) && videoAttachment && !_.isUndefined( videoAttachment.url ) ){
        $videoUrl = videoAttachment.url;
    }else{
        $videoUrl = "No Video";
    }

    if( $audio_field_attr > 0 ){
        $audioAttachment = _.findWhere( api.attachmentsSettings , { id : audio_field_attr}  );
    }

    if( !_.isUndefined( audioAttachment ) && audioAttachment && !_.isUndefined( audioAttachment.url ) ){
        $audioUrl = audioAttachment.url;
    }else{
        $audioUrl = "No audio";
    }

    if( $file_field_attr > 0 ){
        $fileAttachment = _.findWhere( api.attachmentsSettings , { id : file_field_attr}  );
    }

    if( !_.isUndefined( fileAttachment ) && fileAttachment && !_.isUndefined( fileAttachment.url ) ){
        $fileUrl = fileAttachment.url;
    }else{
        $fileUrl = "No file";
    }*/

?>
<div <?php echo $sed_attrs; ?> class="sed-api-test module module-api-test-module api-test-module-skin-default sed-sas-md <?php echo $class; ?>" length_element="sed-row-wide" >
    <div>
    <h3>Attribute test</h3>
    <div>
        <br>
        <div><h4 class="attr">Text Box Settings</h4></div>
        <div><span class="attr">Text Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $text_field_attr; ?></span></div>
        <div><span class="attr">Tel Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $tel_field_attr; ?></span></div>
        <div><span class="attr">Password Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $pass_field_attr; ?></span></div>
        <div><span class="attr">Search Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $search_field_attr; ?></span></div>
        <div><span class="attr">Url Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $url_field_attr; ?></span></div>
        <div><span class="attr">Email Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $email_field_attr; ?></span></div>
        <div><span class="attr">Date Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $date_field_attr; ?></span></div>
        <div><span class="attr">Dimension Control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $dimension_field_attr; ?></span></div>
        <div><span class="attr">Textarea Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $textarea_field_attr; ?></span></div>

        <br>
        <div><h4 class="attr">Select Settings</h4></div>
        <div><span class="attr">Single Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $single_select_field_attr; ?></span></div>
        <div><span class="attr">Multiple Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $multi_select_field_attr; ?></span></div>
        <div><span class="attr">optgroup Single Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $og_single_select_field_attr; ?></span></div>
        <div><span class="attr">optgroup multi Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $og_multi_select_field_attr; ?></span></div>

        <br>
        <div><h4 class="attr">Check Box Settings</h4></div>
        <div><span class="attr">Checkbox Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $checkbox_field_attr; ?></span></div>
        <div><span class="attr">Multi Checkbox Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $multi_check_field_attr; ?></span></div>
        <div><span class="attr">Toggle Control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $toggle_field_id; ?></span></div>
        <div><span class="attr">Sortable Control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $sortable_field_id; ?></span></div>
        <div><span class="attr">Switch Control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $switch_field_id; ?></span></div> 

        <br>
        <div><h4 class="attr">Radio Settings</h4></div>
        <div><span class="attr">Radio Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $radio_field_attr; ?></span></div> 
        <div><span class="attr">Radio Buttonset control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $radio_buttonset_field_id; ?></span></div> 
        <div><span class="attr">Radio Image control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $radio_image_field_id; ?></span></div>

        <br>
        <div><h4 class="attr">Color Settings</h4></div>
        <div><span class="attr">Color Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $color_field_attr; ?></span></div>
        <div><span class="attr">Style Editor Color Box</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style-color-test">this is style editor settings</span></div>
        <div><span class="attr">Multicolor control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php var_dump($multi_color_field_id); ?></span></div>


        <br>
        <div><h4 class="attr">Media Settings</h4></div>
        <?php
            /*var sedImageHtml = api.fn.getSedAttachmentImageHtml( image_source , attachment_id , image_url , default_image_size , external_image_size );*/
        ?>  
        <div><span class="attr">SED Image Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php //echo $sedImageHtml; ?></span></div>
        <div>
            <span class="attr">Select Images Field</span><br>
            <div class="images-group">
                <?php
                    /*if( !_.isEmpty( multi_image_field_attr ) && _.isString( multi_image_field_attr ) ){
                        var imagesGroup = multi_image_field_attr.split(","); 
                        _.each( imagesGroup , function( img_id ){
                            var gImgHtml = api.fn.getAttachmentImageHtml( img_id , "thumbnail" );*/
                ?>
                        <div><?php //echo $gImgHtml; ?></div>
                <?php
                         //});
                    //}
                ?>
            </div>
        </div>
        <div><span class="attr">Single Image Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $imageHtml; ?></span></div>
        <div><span class="attr">Video Field (MP4)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : <?php echo $video_field_attr; ?> </span>&nbsp;&nbsp;&nbsp;<span class="value">Url : <?php echo $image_size_field_attr; ?></span></div>
        <div><span class="attr">Audio Field (MP3)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : <?php echo $audio_field_attr; ?> </span>&nbsp;&nbsp;&nbsp;<span class="value">Url : <?php echo $audioUrl; ?></span></div>
        <div><span class="attr">File Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : <?php echo $audio_field_attr; ?> </span>&nbsp;&nbsp;&nbsp;<span class="value">Url : <?php echo $file_field_attr; ?></span></div>

        <br>
        <div><h4 class="attr">Number Settings</h4></div>
        <div><span class="attr">Spinner Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $spinner_field_attr; ?></span></div>
        <div><span class="attr">Spinner1 with lock</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $spinner1_with_lock_attr; ?></span></div>
        <div><span class="attr">Spinner2 with lock</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $spinner2_with_lock_attr; ?></span></div>
        <div><span class="attr">Spinner3 with lock</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $spinner3_with_lock_attr; ?></span></div>
        <div><span class="attr">Spinner Lock Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $spinner_lock_attr; ?></span></div>
        <div><span class="attr">Range Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $range_field_attr; ?></span></div>

        <br>
        <div><h4 class="attr">Icon Settings</h4></div>
        <div><span class="attr">Icon Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><span class="my-icon-single <?php echo $icon_field_attr; ?>"></span></span></div>
        <div>
            <span class="attr">Select Icons Field</span><br>
            <div class="icons-group">
                <?php 
                    /*if( !_.isEmpty( multi_icon_field_attr ) && _.isString( multi_icon_field_attr ) ){
                        var iconsGroup = multi_icon_field_attr.split(",");
                        _.each( iconsGroup , function( gIcon ){*/
                ?>
                        <div><span class="icon-group-single <?php //echo $gIcon; ?>"></span></div>
                <?php
                        //});
                    //}
                ?>
            </div>
        </div>

        <br>
        <div><h4 class="attr">Custom Settings</h4></div>
        <div><span class="attr">Custom Dropdown Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $custom_attr; ?></span></div>
    </div>

    </div>
      <?php echo $content; ?>
</div>