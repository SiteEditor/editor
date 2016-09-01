<div {{sed_attrs}} class="sed-alert s-tb-sm module module-alert alert-skin4  {{className}} {{type}}">
  <div class="alert alert-variant-style " role="alert">
		<button type="button" class="close" data-dismiss="alert">
		  <span aria-hidden="true">&times;</span>
		  <span class="sr-only">{{I18n.close}}</span>
		</button>
		<#
			var api = sedApp.editor ;
		    var sedImageHtml = api.fn.getSedAttachmentImageHtml( image_source , attachment_id , image_url , default_image_size , external_image_size );
		#>
        <div class="alert-icons">{{{sedImageHtml}}}</div>
        {{{content}}}
  </div>
</div>