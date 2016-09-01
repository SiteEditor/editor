<?php

$height      = 65;
$is_ssl      = is_ssl();
$protocol    = $is_ssl ? 'https' : 'http';

if( $show_faces ) {
	$height = 240;
}

if( $show_stream ) {
	$height = 515;
}

if( $show_stream  && $show_faces  && $show_header ) {
	$height = 540;
}

if($show_stream && $show_faces && !$show_header) {
	$height = 540;
}

if( $show_header ) {
	$height = $height + 30;
}

?>
<div <?php echo $sed_attrs; ?> class="<?php echo $class; ?> s-tb-sm module facebook-module facebook-module-default " <?php echo $has_cover;?>>              <!-- stream=<?php echo $show_stream; ?>&amp; -->
    <iframe src="<?php echo $protocol; ?>://www.facebook.com/plugins/likebox.php?href=<?php echo urlencode($page_url); ?>&amp;show_faces=<?php echo $show_faces; ?>&amp;header=<?php echo $show_header; ?>&amp;height=<?php echo $height; ?>&amp;force_wall=true<?php if( $show_faces ){ ?>&amp;connections=<?php } ?>" style="border:none; overflow:hidden; width:100%;height: <?php echo $height; ?>px;"></iframe>
</div>