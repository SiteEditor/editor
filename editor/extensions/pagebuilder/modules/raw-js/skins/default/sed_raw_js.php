<div <?php echo $sed_attrs; ?> class="module module-raw-js <?php echo $class;?> ">

      <script type="text/javascript">

            <?php echo rawurldecode( strip_tags( $content ) ); ?>

      </script>

      <?php
      if( site_editor_app_on() || ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == "load_modules" ) ) {
            echo __("This is a Raw Js Module.","site-editor");
      }
      ?>

</div>
