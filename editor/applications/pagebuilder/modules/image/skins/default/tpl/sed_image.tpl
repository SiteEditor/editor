<#

if( !lightbox_id ){
    lightbox_id = sed_model_id;
}
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

<div {{sed_attrs}} class="module module-image skin-default {{className}} " >

      <div class="img">
        {{{sedImageHtml}}}
      </div>
      <div class="info">
              <# if( image_click == "link_mode" || image_click == "link_expand_mode"){ #>
                  <a class="link" href="{{link}}" target="{{link_target}}"></a>
              <# } #>
              <# if( image_click == "expand_mode" || image_click == "link_expand_mode"){ #>
                  <a class="expand" href="{{full_src}}" data-lightbox="{{lightbox_id}}" data-title="{{title}}"></a>
              <# } #>
      </div>

</div>

