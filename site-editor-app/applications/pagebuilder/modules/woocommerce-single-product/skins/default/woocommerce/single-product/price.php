
<span class="price"><?php echo $product->get_price_html(); ?></span>
<meta itemprop="price" content="<?php echo $product->get_price(); ?>" />
<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
