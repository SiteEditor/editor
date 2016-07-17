<?php
global $sed_data;
$skin = $sed_data['woo_product_cat_skin'] ? $sed_data['woo_product_cat_skin'] : "default";
include SED_BASE_PB_APP_PATH . DS . 'modules' . DS . 'woocommerce-categories' . DS . 'skins' . DS . $skin . DS . 'woocommerce' . DS . "content-product_cat.php";

