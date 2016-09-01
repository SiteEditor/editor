<div <?php echo $sed_attrs; ?> class="<?php echo $class ?> module module-pricing-table pricing-table-default ">
	<div class="row  <?php echo $type ?> columns-<?php echo $number_column ?>">
		<?php echo $content ?>
	</div><!-- .row -->
</div><!-- #pricing-table-default -->
<?php
	global $sed_dynamic_css_string;
	$selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
	ob_start();
	?>
		<?php echo $selector; ?> .row.pt_with_spacing .panel-wrapper-outer {
		    padding: 0 <?php echo $column_spacing ?>px; 
		} 
	<?php
	$css = ob_get_clean();
	$sed_dynamic_css_string .= $css;