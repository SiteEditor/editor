<div <?php echo $sed_attrs; ?> class="sed-stb-sm module module-contact-form-7 <?php echo $style;?> <?php echo $class;?> ">
      <?php
        if( $form_id > 0 )
            echo do_shortcode('[contact-form-7 id="'.$form_id.'" title="'.$form_title.'"]');
        else
            echo __("Please Select a Valid Contact Form" , "site-editor");
      ?>
</div>
