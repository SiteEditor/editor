<?php
the_post(); 
$tags = wp_get_post_tags( get_the_ID() ); //edit parsaatef
$cats = get_the_terms( get_the_ID() , 'category' );
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
?>
<div <?php echo $sed_attrs; ?> class="posts-wrapper <?php echo $class; ?>">
    <div class="row">
        <div class="col-md-12">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php if( ! post_password_required()  ) : ?>
                <header class="entry-header">
                    <h1 class="sed-title-posts <?php if( !$sed_data['single_post_title_show']  ) echo 'hide'; ?>"><?php the_title();?></h1>
                    <div class="entry-meta item-meta <?php if( !$sed_data['single_post_meta_show'] ) echo 'hide'; ?>">
						<div class="item post-author <?php if( !$sed_data['single_post_author_show'] ) echo 'hide'; ?>" >
                            <i class="fa fa-user"></i>
							<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php printf( __("By %s" , "site-editor" ) , get_the_author() ) ?></a>
						</div>
						<div class="item post-date <?php if( !$sed_data['single_post_date_show'] ) echo 'hide'; ?>" >
                            <i class="fa fa-calendar"></i>
							<span><?php the_time( $sed_data['single_post_data_format'] ) ?></span>
						</div>
    	                <div class="item post-cats <?php if( !$sed_data['single_post_cat_show'] ) echo 'hide'; ?>" >
                            <?php if(!empty($cats)) : ?>
                                <i class="fa fa-folder-open-o"></i>
        						<span class="label"><?php _e("Categories: ", "site-editor") ?></span>
        						<?php
                                foreach ( $cats as $cat ):
                                $category_link = get_category_link( $cat->term_id );
                                ?>
        							<span class="post-cat"><a href="<?php echo esc_url( $category_link ); ?>"><?php echo $cat->name ?></a></span>
        						<?php
                                endforeach;
                            endif;
                            ?>
    					</div>
						<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :?>
						<div class="item post-comments <?php if( !$sed_data['single_post_comment_count_show'] ) echo 'hide'; ?>" >
                            <i class="fa fa-comments-o"></i>
							<span class="comments-link">
								<?php comments_popup_link(
									__( 'Leave a comment', 'site-editor' ),
									__( '1 Comment', 'site-editor' ),
									__( '% Comments', 'site-editor' )
								); ?>
							</span>
						</div>
						<?php
							endif;
							edit_post_link( __( 'Edit', 'site-editor' ), '<span class="edit-link"><i class="fa fa-edit"></i>', '</span>' );
						?>
					</div><!-- .entry-meta -->
                    <?php
					if ( has_post_thumbnail() && ( $sed_data['single_post_show_featured_image'] || site_editor_app_on() ) ): ?>
						<?php
						// GET THUMBNAIL INFO
						$thumb_id   = get_post_thumbnail_id();
						$thumb_alt  = get_post_meta( $thumb_id , '_wp_attachment_image_alt', true );
                        $attachment_image = wp_get_attachment_image_src( $thumb_id , $sed_data['single_post_featured_using_size']);
                        $thumb_info = get_post( $thumb_id );
                        //$attachment_data = wp_get_attachment_metadata($thumb_id);

						if( $thumb_id > 0 ):
						?>
						<div class="thumb single-post-featured-image <?php echo $sed_data['single_post_featured_image_align'];?> <?php if( !$sed_data['single_post_show_featured_image'] ) echo "hide";?>">
                            <img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo $thumb_alt; ?>" title="<?php echo $thumb_info->post_title ?>" class="sed-img"/>
						</div>
				        <?php endif;
			                   endif; ?>
                </header>
                <?php endif; ?>
                <section class="content-post">
                    <?php the_content(); ?>
                </section>

                <?php sed_link_pages(); ?>

                <?php if( ! post_password_required()  ) : ?>
                <footer class="entry-footer item-meta <?php if( !$sed_data['single_post_meta_show'] ) echo 'hide'; ?>">
					<div class="item post-tags <?php if( !$sed_data['single_post_tags_show'] ) echo 'hide'; ?>" >
                        <?php if(!empty($tags)): ?>
						<span class="label"><i class="fa fa-tags"></i><span><?php _e("Tags: ", "site-editor") ?></span></span>
						 <?php foreach ( $tags as $tag ):  ?>
							<h4 class="post-tag"><a href="<?php echo get_tag_link($tag->term_id); ?>"><?php echo $tag->name ?></a></h4>
						 <?php endforeach;
                        endif; ?>
					</div>
                </footer>
                <?php endif; ?>
            </article>
        </div>
    </div>
</div>
