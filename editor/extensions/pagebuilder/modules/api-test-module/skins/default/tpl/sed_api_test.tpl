<#
    var api = sedApp.editor ;

    var imageHtml = api.fn.getAttachmentImageHtml( attribute20 , attribute21 );

    var videoAttachment , videoUrl;

    if( attribute22 > 0 ){
        videoAttachment = _.findWhere( api.attachmentsSettings , { id : attribute22}  );
    }

    if( !_.isUndefined( videoAttachment ) && videoAttachment && !_.isUndefined( videoAttachment.url ) ){
        videoUrl = videoAttachment.url;
    }else{
        videoUrl = "No Video";
    }

    var audioAttachment , audioUrl;

    if( attribute23 > 0 ){
        audioAttachment = _.findWhere( api.attachmentsSettings , { id : attribute23}  );
    }

    if( !_.isUndefined( audioAttachment ) && audioAttachment && !_.isUndefined( audioAttachment.url ) ){
        audioUrl = audioAttachment.url;
    }else{
        audioUrl = "No audio";
    }

    var fileAttachment , fileUrl;

    if( attribute24 > 0 ){
        fileAttachment = _.findWhere( api.attachmentsSettings , { id : attribute24}  );
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
        <li><span class="attr">Text Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute1}}</span></li>
        <li><span class="attr">Tel Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute2}}</span></li>
        <li><span class="attr">Password Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute3}}</span></li>
        <li><span class="attr">Search Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute4}}</span></li>
        <li><span class="attr">Url Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute5}}</span></li>
        <li><span class="attr">Email Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute6}}</span></li>
        <li><span class="attr">Date Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute7}}</span></li>
        <li><span class="attr">Time Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute8}}</span></li>
        <li><span class="attr">Textarea Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{{attribute9}}}</span></li>
        <li><span class="attr">Range Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute10}}</span></li>
        <li><span class="attr">Single Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute11}}</span></li>
        <li><span class="attr">Multiple Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute12}}</span></li>
        <li><span class="attr">optgroup Single Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute13}}</span></li>
        <li><span class="attr">optgroup multi Select Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute14}}</span></li>
        <li><span class="attr">Spinner Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute18}}</span></li>
        <li><span class="attr">Spinner1 with lock</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute25}}</span></li>
        <li><span class="attr">Spinner2 with lock</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute26}}</span></li>
        <li><span class="attr">Spinner3 with lock</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute27}}</span></li>
        <li><span class="attr">Spinner Lock Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute28}}</span></li>
        <li><span class="attr">Icon Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><span class="my-icon-single {{attribute29}}"></span></span></li>
        <li>
            <span class="attr">Select Icons Field</span><br>
            <ul class="icons-group">
                <#
                    if( !_.isEmpty( attribute30 ) && _.isString( attribute30 ) ){
                        var iconsGroup = attribute30.split(",");
                        _.each( iconsGroup , function( gIcon ){
                #>
                        <li><span class="icon-group-single {{gIcon}}"></span></li>
                <#
                        });
                    }
                #>
            </ul>
        </li>
        <li>
            <span class="attr">Select Images Field</span><br>
            <ul class="images-group">
                <#
                    if( !_.isEmpty( attribute32 ) && _.isString( attribute32 ) ){
                        var imagesGroup = attribute32.split(","); 
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
        <li><span class="attr">Checkbox Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute15}}</span></li>
        <li><span class="attr">Multi Checkbox Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute16}}</span></li>
        <li><span class="attr">Radio Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute17}}</span></li>
        <li><span class="attr">Color Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute19}}</span></li>
        <li><span class="attr">Single Image Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{{imageHtml}}}</span></li>
        <li><span class="attr">Video Field (MP4)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : {{attribute22}} </span>&nbsp;&nbsp;&nbsp;<span class="value">Url : {{videoUrl}}</span></li>
        <li><span class="attr">Audio Field (MP3)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : {{attribute23}} </span>&nbsp;&nbsp;&nbsp;<span class="value">Url : {{audioUrl}}</span></li>
        <li><span class="attr">File Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : {{attribute23}} </span>&nbsp;&nbsp;&nbsp;<span class="value">Url : {{fileUrl}}</span></li>
        <li><span class="attr">Style Editor Color Box</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style-color-test">this is style editor settings</span></li>

        <#
            var sedImageHtml = api.fn.getSedAttachmentImageHtml( image_source , attachment_id , image_url , default_image_size , external_image_size );
        #>  
        <li><span class="attr">SED Image Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{{sedImageHtml}}}</span></li>

        <li><span class="attr">Custom Dropdown Field</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">{{attribute31}}</span></li>
    </ul>

    </div>
      {{{content}}}
</div>