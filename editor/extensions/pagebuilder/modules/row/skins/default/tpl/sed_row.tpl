<#
var lengthClass;
if(length == "boxed")
    lengthClass = "sed-row-boxed";
else
    lengthClass = "sed-row-wide";


var _rowDropEmpty = '<div class="empty-row"><span class="drop-module-icon"></span><span class="drop-module-txt">Drop A Module Here</span></div>';

 #>

<div sed-layout-role="pb-module" class="sed-row-pb sed-bp-element sed-stb-sm {{className}} {{lengthClass}}" {{sed_attrs}} data-type-row="{{type}}" length_element sed-role="row-pb">

	<# if(content) { #>
		{{{content}}}
	<# }else{ #>
		{{_rowDropEmpty}}
	<# } #>

	<style type="text/css"> 

	    @media (max-width: 768px){
	        [sed_model_id="{{sed_model_id}}"] {
	            <# alert(rps_spacing_top); if(!_.isEmpty(rps_spacing_top)){ #>     padding-top:    {{rps_spacing_top}}px !important;    <# } #>
	            <# if(!_.isEmpty(rps_spacing_right)){ #>   padding-right:  {{rps_spacing_right}}px !important;  <# } #>
	            <# if(!_.isEmpty(rps_spacing_bottom)){ #>  padding-bottom: {{rps_spacing_bottom}}px !important; <# } #>
	            <# if(!_.isEmpty(rps_spacing_left)){ #>    padding-left:   {{rps_spacing_left}}px !important;   <# } #>
	            <# if(!_.isEmpty(rps_align)){ #>           text-align:     {{rps_align}} !important;            <# } #>
	        }         
	    } 

	</style> 

</div>
