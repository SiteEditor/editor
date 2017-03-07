<#
    var api = sedApp.editor ;

    var imageHtml = api.fn.getAttachmentImageHtml( image_field_attr , image_size_field_attr );

    var videoAttachment , videoUrl;

    if( video_field_attr > 0 ){
        videoAttachment = _.findWhere( api.attachmentsSettings , { id : video_field_attr}  );
    }

    if( !_.isUndefined( videoAttachment ) && videoAttachment && !_.isUndefined( videoAttachment.url ) ){
        videoUrl = videoAttachment.url;
    }else{
        videoUrl = "No Video";
    }

    var audioAttachment , audioUrl;

    if( audio_field_attr > 0 ){
        audioAttachment = _.findWhere( api.attachmentsSettings , { id : audio_field_attr}  );
    }

    if( !_.isUndefined( audioAttachment ) && audioAttachment && !_.isUndefined( audioAttachment.url ) ){
        audioUrl = audioAttachment.url;
    }else{
        audioUrl = "No audio";
    }

    var fileAttachment , fileUrl;

    if( file_field_attr > 0 ){
        fileAttachment = _.findWhere( api.attachmentsSettings , { id : file_field_attr}  );
    }

    if( !_.isUndefined( fileAttachment ) && fileAttachment && !_.isUndefined( fileAttachment.url ) ){
        fileUrl = fileAttachment.url;
    }else{
        fileUrl = "No file";
    }

#>
<div {{sed_attrs}} class="sed-api-test module module-api-test-module api-test-module-skin-default {{className}}" length_element="sed-row-wide" >
    <div>
    <h3>Attribute test</h3>
    <ul>
        <li><span class="attr">Text Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{text_field_attr}}</span></li>
        <li><span class="attr">Tel Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{tel_field_attr}}</span></li>
        <li><span class="attr">Password Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{pass_field_attr}}</span></li>
        <li><span class="attr">Search Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{search_field_attr}}</span></li>
        <li><span class="attr">Url Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{url_field_attr}}</span></li>
        <li><span class="attr">Email Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{email_field_attr}}</span></li>
        <li><span class="attr">Date Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{date_field_attr}}</span></li>
        <li><span class="attr">Dimension Control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{dimension_field_attr}}</span></li>
        <li><span class="attr">Textarea Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{{textarea_field_attr}}}</span></li>
        <li><span class="attr">Single Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{single_select_field_attr}}</span></li>
        <li><span class="attr">Multiple Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{multi_select_field_attr}}</span></li>
        <li><span class="attr">optgroup Single Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{og_single_select_field_attr}}</span></li>
        <li><span class="attr">optgroup multi Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{og_multi_select_field_attr}}</span></li>
        <li><span class="attr">Checkbox Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{checkbox_field_attr}}</span></li>
        <li><span class="attr">Multi Checkbox Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{multi_check_field_attr}}</span></li>
        <li><span class="attr">Toggle Control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{toggle_field_id}}</span></li>
        <li><span class="attr">Sortable Control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{sortable_field_id}}</span></li>
        <li><span class="attr">Switch Control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{switch_field_id}}</span></li> 
        <li><span class="attr">Radio Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{radio_field_attr}}</span></li>
        <li><span class="attr">Radio Buttonset control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{radio_buttonset_field_id}}</span></li>  
        <li><span class="attr">Radio Image control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{radio_image_field_id}}</span></li>
        <li><span class="attr">Color Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{color_field_attr}}</span></li>
        <li><span class="attr">Style Editor Color Box</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style-color-test">this is style editor settings</span></li>
        <li><span class="attr">Multicolor control</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{multi_color_field_id}}</span></li>

        <#
            var sedImageHtml = api.fn.getSedAttachmentImageHtml( image_source , attachment_id , image_url , default_image_size , external_image_size );
        #>  
        <li><span class="attr">SED Image Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{{sedImageHtml}}}</span></li>
        <li>
            <span class="attr">Select Images Field</span><br>
            <ul class="images-group">
                <#
                    if( !_.isEmpty( multi_image_field_attr ) && _.isString( multi_image_field_attr ) ){
                        var imagesGroup = multi_image_field_attr.split(","); 
                        _.each( imagesGroup , function( img_id ){
                            var gImgHtml = api.fn.getAttachmentImageHtml( img_id , "thumbnail" );
                #>
                        <li>{{{gImgHtml}}}</li>
                <#
                        });
                    }
                #>
            </ul>
        </li>
        <li><span class="attr">Single Image Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{{imageHtml}}}</span></li>
        <li><span class="attr">Video Field (MP4)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : {{video_field_attr}} </span>&nbsp;&nbsp;&nbsp;<span class="value">Url : {{videoUrl}}</span></li>
        <li><span class="attr">Audio Field (MP3)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : {{audio_field_attr}} </span>&nbsp;&nbsp;&nbsp;<span class="value">Url : {{audioUrl}}</span></li>
        <li><span class="attr">File Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : {{audio_field_attr}} </span>&nbsp;&nbsp;&nbsp;<span class="value">Url : {{fileUrl}}</span></li>
        <li><span class="attr">Spinner Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{spinner_field_attr}}</span></li>
        <li><span class="attr">Spinner1 with lock</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{spinner1_with_lock_attr}}</span></li>
        <li><span class="attr">Spinner2 with lock</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{spinner2_with_lock_attr}}</span></li>
        <li><span class="attr">Spinner3 with lock</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{spinner3_with_lock_attr}}</span></li>
        <li><span class="attr">Spinner Lock Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{spinner_lock_attr}}</span></li>
        <li><span class="attr">Range Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{range_field_attr}}</span></li>
        <li><span class="attr">Icon Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><span class="my-icon-single {{icon_field_attr}}"></span></span></li>
        <li>
            <span class="attr">Select Icons Field</span><br>
            <ul class="icons-group">
                <#
                    if( !_.isEmpty( multi_icon_field_attr ) && _.isString( multi_icon_field_attr ) ){
                        var iconsGroup = multi_icon_field_attr.split(",");
                        _.each( iconsGroup , function( gIcon ){
                #>
                        <li><span class="icon-group-single {{gIcon}}"></span></li>
                <#
                        });
                    }
                #>
            </ul>
        </li>

        <li><span class="attr">Custom Dropdown Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{custom_attr}}</span></li>
    </ul>

    </div>
      {{{content}}}
</div>