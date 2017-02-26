<div <?php echo $sed_attrs; ?> class="sed-stb-sm module module-wp-text-editor <?php echo $class;?> ">
    <?php echo sed_js_remove_wpautop( apply_filters( 'sed_pb_builder_module_content', $content ) , true ) ; ?>
</div>
