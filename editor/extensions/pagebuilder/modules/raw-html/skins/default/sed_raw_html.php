<div <?php echo $sed_attrs; ?> class="module module-raw-html <?php echo $class;?> ">
      <?php
      //$content = rawurlencode( $content );
      echo apply_filters( 'sed_pb_builder_module_content', $content );//do_shortcode( rawurldecode( strip_tags( $content ) ) ) ?>
</div>
