<?php

global $sed_apps , $sed_data;

?>

<!--<style type="text/css">
.item{
   padding: <?php echo $sed_data['archive_masonry_spacing']  ?>px  ;
}
.item .inner{
   border-width: <?php echo $sed_data['archive_border_width']  ?>px  ;
}
</style> -->

<div <?php echo $sed_attrs; ?> class="module module-portfolio portfolio-default portfolio-main <?php echo $class; ?> ">
	<section id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
            <?php echo $content; ?>
		</div>
	</section>
</div>