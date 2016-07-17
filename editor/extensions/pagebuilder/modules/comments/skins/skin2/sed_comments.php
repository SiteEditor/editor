<?php
global $sed_data;

if( is_page() )
    $show_comment = 'single_page_show_comments';
else
    $show_comment = 'single_post_show_comments';

if( ( $sed_data[$show_comment] && !post_password_required() ) || site_editor_app_on() ) :
?>
<div <?php echo $sed_attrs; ?>   class="module module-comments comments-skin2  <?php echo $class;?> <?php if( !$sed_data[$show_comment] || post_password_required() ) echo "hide";?>">

<?php comments_template(); ?>
</div>
<?php endif; ?>