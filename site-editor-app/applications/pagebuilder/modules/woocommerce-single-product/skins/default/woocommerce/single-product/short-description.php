<div class="product-excerpt">
    <h3 class="product-excerpt-title"><?php echo __( "Product Brief Introduction" , "site-editor");?></h3>
    <div class="gl-spr gl-spr-side"></div>
    <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
    <div class="excerpt-show-more">
        <span><?php echo __( "Show More" , "site-editor");?></span>
    </div>
</div>
