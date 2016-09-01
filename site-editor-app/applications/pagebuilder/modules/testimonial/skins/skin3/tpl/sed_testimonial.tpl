<div {{sed_attrs}} class="{{className}} module module-testimonial testimonial-skin3">
    <div>
    <#
        var sedImageHtml = api.fn.getSedAttachmentImageHtml( image_source , attachment_id , image_url , default_image_size , external_image_size );
    #>      
    <div class="author thumbnail clearfix">
    	<div class="img">{{{sedImageHtml}}}</div>	
    </div>
        {{{content}}}  
    </div>
</div>
