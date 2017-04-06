<# 
	var inline_columns = "";

	if(full_width_columns) { 
		inline_columns = "normal-columns";   
	}else{
		inline_columns = "table-cell-columns"; 
	}
#>
<table class="sed-cols-table">
<tr {{sed_attrs}} class="sed-columns-pb {{inline_columns}} {{className}}" sed-role="column-pb">
    {{{content}}}
</tr>
</table>
