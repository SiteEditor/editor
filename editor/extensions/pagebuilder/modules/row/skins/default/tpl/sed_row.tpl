<#
var lengthClass;
if(length == "boxed")
    lengthClass = "sed-row-boxed";
else
    lengthClass = "sed-row-wide";


var _rowDropEmpty = '<div class="empty-row"><span class="drop-module-icon"></span><span class="drop-module-txt">Drop A Module Here</span></div>';

 #>

<div sed-layout-role="pb-module" class="<# if( is_sticky ) { #>sed-pb-row-sticky<# } #> sed-row-pb sed-bp-element sed-stb-sm {{className}} {{lengthClass}}" {{sed_attrs}} data-type-row="{{type}}" length_element sed-role="row-pb">

	<# if(content) { #>
		{{{content}}}
	<# }else{ #>
		{{_rowDropEmpty}}
	<# } #>

	<style type="text/css"> 

	    @media (max-width: 768px){
	        [sed_model_id="{{sed_model_id}}"] {
	            <# if( rps_spacing_top || rps_spacing_top === 0 ){ #>     padding-top:    {{rps_spacing_top}}px !important;    <# } #>
	            <# if( rps_spacing_right || rps_spacing_right === 0 ){ #>   padding-right:  {{rps_spacing_right}}px !important;  <# } #>
	            <# if( rps_spacing_bottom || rps_spacing_bottom === 0 ){ #>  padding-bottom: {{rps_spacing_bottom}}px !important; <# } #>
	            <# if( rps_spacing_left || rps_spacing_left === 0 ){ #>    padding-left:   {{rps_spacing_left}}px !important;   <# } #>
	            <# if(!_.isEmpty(rps_align)){ #>           text-align:     {{rps_align}} !important;            <# } #>
	        }         
	    } 

	</style> 

</div>
