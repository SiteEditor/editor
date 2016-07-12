<?php
    $taxonomy_cat = 'product_cat';
    $taxonomy_tag = 'product_tag';
    $cats     = get_the_terms( $post->ID , $taxonomy_cat );
    $tags     = get_the_terms( $post->ID , $taxonomy_tag );
    $sku      = ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) );
    
    $counter = 0;
    $cat_count = sizeof( $cats );
    $tag_count = sizeof( $tags );
?>
    <?php if ( !empty( $cats ) || !empty( $tags ) || $sku ): ?>
        <div class="product-meta">
        <?php do_action( 'woocommerce_product_meta_start' ); ?>
        <?php if ( $sku ) : ?>
            <span class="sku_wrapper"><?php _e( 'SKU:', 'woocommerce' ); ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'woocommerce' ); ?></span>.</span>
        <?php endif; ?>

        <?php if ( !empty( $cats )): ?>
            <div class="product-cat">
                <span class="item"><?php _e("Categories:","site-editor")?></span>
                <?php foreach ( $cats as $cat ):
                    $link = get_term_link( $cat , $taxonomy_cat );
                    $title = $cat->description;
                    if( empty( $title ) )
                        $title = sprintf( __("Category %s","site-editor") , $cat->name );

                    if ( is_wp_error( $link ) )
                        continue;
                    if( $counter > 0 )
                        echo '<span class="item spr">/</span>';
                ?>
                <a href="<?php echo  esc_url( $link ) ?>" class="item" title="<?php echo $title ?>"><?php echo $cat->name ?></a>
                <?php $counter++;endforeach;$counter = 0; ?>
            </div>
        <?php endif ?>
        <?php if ( !empty( $tags )): ?>
            <div class="product-tag">
                <span class="item"><?php _e("Tags:","site-editor")?></span>
                <?php foreach ( $tags as $tag ):
                    $link = get_term_link( $tag , $taxonomy_cat );
                    $title = $tag->description;
                    if( empty( $title ) )
                        $title = sprintf( __("Tag %s","site-editor") , $tag->name );
                    if ( is_wp_error( $link ) )
                        continue;
                    if( $counter > 0 )
                        echo '<span class="item spr">/</span>';
                ?>
                <a href="<?php echo  esc_url( $link ) ?>" class="item" title="<?php echo $title ?>"><?php echo $tag->name ?></a>
                <?php $counter++;endforeach;$counter = 0; ?>
            </div>
        <?php endif ?>
        <?php do_action( 'woocommerce_product_meta_end' ); ?>
        </div>
    <?php endif ?>
</div>
