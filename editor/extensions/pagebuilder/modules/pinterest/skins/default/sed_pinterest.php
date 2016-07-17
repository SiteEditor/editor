<?php
$height = $board_height + 120;
?>
<div <?php echo $sed_attrs; ?> class="<?php echo $class; ?> s-tb-sm module pinterest-module pinterest-module-default  " <?php echo $has_cover;?>>
    <iframe src="<?php echo $api_url; ?>?profile_url=<?php echo $profile_url; ?>&amp;image_width=<?php echo $image_width; ?>&amp;board_height=<?php echo $board_height; ?>"  style="border:none; overflow:hidden; width:100%; height: <?php echo $height; ?>px;"></iframe>
</div>
