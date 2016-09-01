
<?php
	// ID check
	if(empty($layer_slider_id)) {
		echo '[LayerSliderWP] '.__('Invalid shortcode', 'LayerSlider').'';
	}

	// Get slider if any
	if(!$slider_data = LS_Sliders::find($layer_slider_id)) {
		echo '[LayerSliderWP] '.__('Slider not found', 'LayerSlider').'';
	}

    //var_dump( $slider_data );
    //if($slider_data['data']['properties']['width'] == "100%")

?>

<div <?php echo $sed_attrs; ?>  class="<?php echo $class;?> ">
   

<?php layerslider($layer_slider_id); ?>
</div>
<script>


    jQuery("#<?php echo $id;?>").on("sed.moduleResizeStart" , function(){
        //jQuery("#layerslider_<?php echo $layer_slider_id;?>").layerSlider('stop');
    });

    jQuery("#<?php echo $id;?>").on("sed.moduleResize sed.moduleResizeStop sed.moduleSortableStop" , function(){
        //jQuery("#layerslider_<?php echo $layer_slider_id;?>").data("LayerSlider").restart();  //LayerSlider
        //console.log( jQuery("#layerslider_<?php echo $layer_slider_id;?>").data("LayerSlider") );
//console.log("lsdata-------" , jQuery("#layerslider_<?php echo $layer_slider_id;?>").data("LayerSlider") );
        //jQuery("#layerslider_<?php echo $layer_slider_id;?>").layerSlider('start');
    });

</script>