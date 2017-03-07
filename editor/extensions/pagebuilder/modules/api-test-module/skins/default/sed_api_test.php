<?php
    /*var api = sedApp.editor ;

    $imageHtml = api.fn.getAttachmentImageHtml( attribute20 , attribute21 );

    if( $attribute22 > 0 ){
        $videoAttachment = _.findWhere( api.attachmentsSettings , { id : attribute22}  );
    }

    if( !_.isUndefined( videoAttachment ) && videoAttachment && !_.isUndefined( videoAttachment.url ) ){
        $videoUrl = videoAttachment.url;
    }else{
        $videoUrl = "No Video";
    }

    if( $attribute23 > 0 ){
        $audioAttachment = _.findWhere( api.attachmentsSettings , { id : attribute23}  );
    }

    if( !_.isUndefined( audioAttachment ) && audioAttachment && !_.isUndefined( audioAttachment.url ) ){
        $audioUrl = audioAttachment.url;
    }else{
        $audioUrl = "No audio";
    }

    if( $attribute24 > 0 ){
        $fileAttachment = _.findWhere( api.attachmentsSettings , { id : attribute24}  );
    }

    if( !_.isUndefined( fileAttachment ) && fileAttachment && !_.isUndefined( fileAttachment.url ) ){
        $fileUrl = fileAttachment.url;
    }else{
        $fileUrl = "No file";
    }*/

?>
<div <?php echo $sed_attrs; ?> class="sed-api-test module module-api-test-module api-test-module-skin-default <?php echo $class; ?>" length_element="sed-row-wide" >
    <div>
    <h3>Attribute test</h3>
    <ul>
        <li><span class="attr">Text Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute1; ?></span></li>
        <li><span class="attr">Tel Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute2; ?></span></li>
        <li><span class="attr">Password Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute3; ?></span></li>
        <li><span class="attr">Search Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute4; ?></span></li>
        <li><span class="attr">Url Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute5; ?></span></li>
        <li><span class="attr">Email Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute6; ?></span></li>
        <li><span class="attr">Date Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute7; ?></span></li>
        <li><span class="attr">Time Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute8; ?></span></li>
        <li><span class="attr">Textarea Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute9; ?></span></li>
        <li><span class="attr">Range Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute10; ?></span></li>
        <li><span class="attr">Single Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute11; ?></span></li>
        <li><span class="attr">Multiple Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute12; ?></span></li>
        <li><span class="attr">optgroup Single Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute13; ?></span></li>
        <li><span class="attr">optgroup multi Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute14; ?></span></li>
        <li><span class="attr">Spinner Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute18; ?></span></li>
        <li><span class="attr">Spinner1 with lock</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute25; ?></span></li>
        <li><span class="attr">Spinner2 with lock</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute26; ?></span></li>
        <li><span class="attr">Spinner3 with lock</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute27; ?></span></li>
        <li><span class="attr">Spinner Lock Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute28; ?></span></li>
        <li><span class="attr">Icon Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><span class="my-icon-single <?php echo $attribute29; ?>"></span></span></li>
        <li>
            <span class="attr">Select Icons Field</span><br>
            <ul class="icons-group">
                <?php 
                    /*if( !_.isEmpty( attribute30 ) && _.isString( attribute30 ) ){
                        var iconsGroup = attribute30.split(",");
                        _.each( iconsGroup , function( gIcon ){*/
                ?>
                        <li><span class="icon-group-single <?php //echo $gIcon; ?>"></span></li>
                <?php
                        //});
                    //}
                ?>
            </ul>
        </li>
        <li>
            <span class="attr">Select Images Field</span><br>
            <ul class="images-group">
                <?php
                    /*if( !_.isEmpty( attribute32 ) && _.isString( attribute32 ) ){
                        var imagesGroup = attribute32.split(","); 
                        _.each( imagesGroup , function( img_id ){
                            var gImgHtml = api.fn.getAttachmentImageHtml( img_id , "thumbnail" );*/
                ?>
                        <li><?php //echo $gImgHtml; ?></li>
                <?php
                         //});
                    //}
                ?>
            </ul>
        </li>
        <li><span class="attr">Checkbox Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute15; ?></span></li>
        <li><span class="attr">Multi Checkbox Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute16; ?></span></li>
        <li><span class="attr">Radio Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute17; ?></span></li>
        <li><span class="attr">Color Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute19; ?></span></li>
        <li><span class="attr">Single Image Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $imageHtml; ?></span></li>
        <li><span class="attr">Video Field (MP4)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : <?php echo $attribute22; ?> </span>&nbsp;&nbsp;&nbsp;<span class="value">Url : <?php echo $attribute21; ?></span></li>
        <li><span class="attr">Audio Field (MP3)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : <?php echo $attribute23; ?> </span>&nbsp;&nbsp;&nbsp;<span class="value">Url : <?php echo $audioUrl; ?></span></li>
        <li><span class="attr">File Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : <?php echo $attribute23; ?> </span>&nbsp;&nbsp;&nbsp;<span class="value">Url : <?php echo $attribute24; ?></span></li>
        <li><span class="attr">Style Editor Color Box</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style-color-test">this is style editor settings</span></li>

        <?php
            /*var sedImageHtml = api.fn.getSedAttachmentImageHtml( image_source , attachment_id , image_url , default_image_size , external_image_size );*/
        ?>  
        <li><span class="attr">SED Image Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php //echo $sedImageHtml; ?></span></li>

        <li><span class="attr">Custom Dropdown Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $attribute31; ?></span></li>
    </ul>

    </div>
      <?php echo $content; ?>
</div>