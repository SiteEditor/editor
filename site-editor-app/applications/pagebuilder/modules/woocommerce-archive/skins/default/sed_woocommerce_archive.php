<?php

 global $sed_data;

 $a = $sed_data['woo_number_columns'];
 $type = $sed_data['woo_archive_type'];

  if($type == "masonry"){
      $type_class = "sed-products-masonry";
      $data_attr = 'data-sed-role="masonry" data-item-selector=".sed-products-masonry .sed-item-product"';
  }else if($type == "grid"){
      $type_class = "sed-products-grid";
      $data_attr = '';
  }

 ?>

<style type="text/css">
    [sed_model_id="<?php echo $sed_model_id; ?>"] .<?php echo $type_class;?> .sed-item-product {
       padding: <?php echo $sed_data['woo_product_spacing']  ?>px  ;
    }
    [sed_model_id="<?php echo $sed_model_id; ?>"] .<?php echo $type_class;?>.sed-products-list {
       margin: -<?php echo $sed_data['woo_product_spacing'] ?>px  ;
    }
</style>
                                 
<?php
if($type == "grid"){
?>
    <style id="sed-products-grid-clear" type="text/css">
    [sed_model_id="<?php echo $sed_model_id; ?>"] .sed-products-grid .sed-item-product:nth-of-type(<?php echo $sed_data['woo_number_columns']; ?>n+1){
      clear: both;
    }
    </style>
<?php
}
?>

<div <?php echo $sed_attrs; ?> class="module woocommerce-archive woocommerce-archive-layout-masonry woocommerce-archive-default <?php echo $class; ?>">
    <?php echo $content; ?>
</div>