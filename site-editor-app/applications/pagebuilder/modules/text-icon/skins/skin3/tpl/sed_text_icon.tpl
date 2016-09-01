<div {{sed_attrs}} class="s-tb-sm module text-icon-module text-icon-module-skin3 {{className}}">
    {{{content}}}
	<#
		var api = sedApp.editor ;
	    var sedImageHtml = api.fn.getSedAttachmentImageHtml( image_source , attachment_id , image_url , default_image_size , external_image_size );
	#>
    <div class="text-icon">{{{sedImageHtml}}}</div>
</div>