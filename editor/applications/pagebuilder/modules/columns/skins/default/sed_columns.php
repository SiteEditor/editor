<table class="s-tb-sm sed-cols-table">
  <tr <?php echo $sed_attrs; ?> class="sed-columns-pb <?php echo  $class.' '.$responsive_option; ?>"  sed-role="column-pb">
      <?php echo $content; ?>
  </tr>
</table>

<?php
if(!empty($responsive_spacing)){
	global $sed_dynamic_css_string;
	$selector = ( site_editor_app_on() ) ? '[sed_model_id="' . $sed_model_id . '"]' : '.'.$sed_custom_css_class;
	ob_start();
	?>
	  @media (max-width: 768px){

	   <?php echo $selector; ?> > td >.sed-column-contents-pb > .sed-row-pb > .sed-pb-module-container{
	      padding : <?php echo $responsive_spacing; ?> !important;
	  }

	  }
	<?php
	$css = ob_get_clean();
	$sed_dynamic_css_string .= $css;
}


