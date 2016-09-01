<?php
    $count   = $product->get_rating_count();
    $average = $product->get_average_rating();
    $starts  = array();

if ( $count > 0 ) : 
    
for( $i = $average ; $i > 0 ; $i-- ){
    if( $i < 1 )
        $starts[] = "fa fa-star-half-o";
    else
        $starts[] = "fa fa-star";
}
?>
    <div class="product-rating" title="<?php printf( __( 'Rated %s out of 5', 'woocommerce' ), $average ); ?>" itemprop="aggregateRating" itemscope>
        <?php
        for( $i = 0 ; $i < 5 ; $i++ ){
            if( isset( $starts[$i] ) )
                echo '<i class="' . $starts[$i] . '"></i>';
            else
                echo '<i class="fa fa-star-o"></i>';
        }
        ?>
        <a href="#reviews" itemprop="ratingValue" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s customer review', '%s customer reviews', $count, 'woocommerce' ), '<span itemprop="ratingCount" class="count">' . $count . '</span>' ); ?>)</a>
    </div>
</div>
<?php else:?>
</div>
<?php endif;?>
