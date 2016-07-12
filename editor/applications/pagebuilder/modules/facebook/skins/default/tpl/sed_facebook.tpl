<#
var height      = 65 ,
    is_ssl      = window.IS_SSL ,
    protocol    = is_ssl ? 'https' : 'http';

if( show_faces ) {
	height = 240;
}

if( show_stream ) {
	height = 515;
}

if( show_stream && show_faces && show_header ) {
	height = 540;
}

if( show_stream && show_faces && !show_header ) {
	height = 540;
}

if( show_header ) {
	height = height + 30;
}

page_url = encodeURIComponent( page_url );

#>                                                                                         
<div {{sed_attrs}} class="{{className}} s-tb-sm module facebook-module facebook-module-default" {{has_cover}}>
    <iframe src="{{protocol}}://www.facebook.com/plugins/likebox.php?href={{page_url}}&amp;show_faces={{show_faces}}&amp;stream={{show_stream}}&amp;header={{show_header}}&amp;height={{height}}&amp;force_wall=true<# if( show_faces ){ #>&amp;connections=<# } #>" style="border:none; overflow:hidden; width:100%; height: {{height}}px;"></iframe>
</div>
