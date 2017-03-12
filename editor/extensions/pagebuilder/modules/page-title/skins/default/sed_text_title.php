<<?php echo $tag; ?>   class="page-title-continer sed-title module module-title <?php echo $class; ?>" <?php echo $sed_attrs; ?>>
  <?php

   if( $content == "@@@" ){
      echo "<h3>" . PBPageTitleShortcode::get_title() . "</h3>";
   }else
      echo $content;

	?>

</<?php echo $tag; ?>>