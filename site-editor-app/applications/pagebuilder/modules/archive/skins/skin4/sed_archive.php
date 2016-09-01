<?php

global $sed_apps , $sed_data;

?>

<style type="text/css">
[sed_model_id="<?php echo $sed_model_id; ?>"] .item{
   padding: <?php echo $sed_data['archive_masonry_spacing']  ?>px  ;
}
[sed_model_id="<?php echo $sed_model_id; ?>"] .repository-posts{
    margin-top: -<?php echo $sed_data['archive_masonry_spacing']  ?>px ;
    margin-left: -<?php echo $sed_data['archive_masonry_spacing']  ?>px ;
    margin-right: -<?php echo $sed_data['archive_masonry_spacing']  ?>px ;
}

[sed_model_id="<?php echo $sed_model_id; ?>"] .item .inner{
   border-width: <?php echo $sed_data['archive_border_width']  ?>px  ;
}

</style>

<div <?php echo $sed_attrs; ?> class="<?php echo $class; ?>">
	<section id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
            <?php echo $content; ?>
		</div>
	</section>

</div>