<?php
global $sed_data;
 /*
 @-- data-contextmenu-post-id is required for all modules when have one post edit button in dialog settings
 @-- all data-contextmenu-{%s} send to contextmenus
 @-- all data-contextmenu-{%s} just only defined in html tag when html tag is module container and have module id ($id)
 @-- files ----
    siteeditor-contextmenu.min.js line 115 ::: add data-contextmenu-{%s} to any contextmenu item like
    settings
    for data-contextmenu-post-id :: in line 166 plugins/contextmenu/plugin.js
    add data-post-id to any post edit buttons
    using data-post-id in in line 166 plugins/posts/plugin.js  in line 109 when click post edit button
 */

if(have_posts()): the_post();
?>
<div <?php echo $sed_attrs; ?> class="page-wrapper <?php echo $class; ?>">

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <section class="content-post">
            <?php the_content(); ?>
            <?php sed_link_pages(); ?>
        </section>

    </article>

</div>
<?php endif;?>