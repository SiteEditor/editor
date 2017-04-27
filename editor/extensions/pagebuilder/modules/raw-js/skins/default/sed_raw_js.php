<div <?php echo $sed_attrs; ?> class="module module-raw-js <?php echo $class;?> ">

      <script type="text/javascript">

            <?php echo rawurldecode( $content );//rawurldecode( strip_tags( $content ) ) ?>

      </script>

      <?php
      if( site_editor_app_on() || sed_loading_module_on() ) {
            echo __("This is a Raw Js Module.","site-editor");
      }
      ?>

</div>
