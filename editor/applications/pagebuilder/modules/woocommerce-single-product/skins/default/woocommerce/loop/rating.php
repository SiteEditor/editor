<?php
/**
 * Loop Rating
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $product;
$starts = array();
$rating = $product->get_average_rating();
for( $i = $rating ; $i > 0 ; $i-- ){
    if( $i < 1 )
        $starts[] = "fa fa-star-half-o";
    else
        $starts[] = "fa fa-star";
}
?>
<div class="product-rating" title="<?php echo sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $rating ) ?>">
    <?php
    for( $i = 0 ; $i < 5 ; $i++ ){
        if( isset( $starts[$i] ) )
            echo '<i class="' . $starts[$i] . '"></i>';
        else
            echo '<i class="fa fa-star-o"></i>';
    }
    ?>
</div>