<div {{sed_attrs}} class="module module-text-icon text-icon-skin3 {{className}}">
	<#

		var api = sedApp.editor ;
        var CustomImgSize = ( image_source == "external" ) ? external_image_size : custom_image_size;
        var sedImageHtml = api.fn.getSedAttachmentImageHtml( image_source , attachment_id , image_url , default_image_size , CustomImgSize );
            	
	#>
    <div class="text-icon-wrapper">
	    {{{content}}}  
	    <div class="text-icon">{{{sedImageHtml}}}</div>
    </div>

    <style type="text/css">

        [sed_model_id="{{sed_model_id}}"].module-text-icon.text-icon-skin3 .text-icon img {
            min-width: {{image_width}};  
            width: {{image_width}};   
        }

    </style>

</div>