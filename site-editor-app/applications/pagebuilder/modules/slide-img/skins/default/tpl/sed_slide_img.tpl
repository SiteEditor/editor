<#

var api = sedApp.editor ;

var imgAttrs = {
    "class" : "sed-img" ,
    "alt"   : "alt" ,
};

var sedImageHtml = api.fn.getSedAttachmentImageHtml( image_source , attachment_id , image_url , default_image_size , external_image_size , imgAttrs );
if( image_source == "attachment" ){
    full_src = api.fn.getAttachmentImageFullSrc( attachment_id );
}
#>
<a href="{{link}}" target="{{link_target}}" {{sed_attrs}} class="module module-slide-img slide-img-default {{className}}">  
    {{{sedImageHtml}}}
</a>