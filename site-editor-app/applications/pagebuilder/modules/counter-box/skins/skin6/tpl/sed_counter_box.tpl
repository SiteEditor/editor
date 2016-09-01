<div {{sed_attrs}} class="{{className}} s-tb-sm module module-counter-box counter-box-skin6">
  <div class="counter-box-inner counter-box-container">
	<#
		var api = sedApp.editor ;
	    var sedImageHtml = api.fn.getSedAttachmentImageHtml( image_source , attachment_id , image_url , default_image_size , external_image_size );
	#>
    <div class="image-icon">{{{sedImageHtml}}}</div>
    <div class="box">
      <span class="counter-box-pr" title="new" id="{{sed_model_id}}-counter" {{{item_settings}}}></span>
      <h4 class="counter-box-title" >{{counter_box_title}}</h4>
    </div>
  </div>
</div>