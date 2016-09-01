<#
profile_url = encodeURIComponent( profile_url );
height = board_height + 120;
#>
<div {{sed_attrs}} class="{{className}} s-tb-sm module pinterest-module pinterest-module-default" {{has_cover}}>
    <iframe src="{{api_url}}?profile_url={{profile_url}}&amp;image_width={{image_width}}&amp;board_height={{board_height}}"  style="border:none; overflow:hidden; width:100%; height: {{height}}px;"></iframe>
</div>
