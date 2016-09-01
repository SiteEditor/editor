<div {{sed_attrs}} class="{{class}} module module-pricing-table pricing-table-default">
	<div class="row  {{type}} columns-{{number_column}}">
		{{{content}}}
	</div><!-- .row -->         
</div><!-- #pricing-table-default -->
<style type="text/css">
[sed_model_id="{{sed_model_id}}"] .row.pt_with_spacing .panel-wrapper-outer {
    padding: 0 {{column_spacing}}px;
}
[sed_model_id="{{sed_model_id}}"] .row.pt_with_spacing {
    margin: 0 -{{column_spacing}}px;
}
</style>