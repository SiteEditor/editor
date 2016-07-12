<?php
the_post();
$tags = wp_get_post_tags( get_the_ID() );
$cats = get_the_terms( get_the_ID() , 'category' );

global $sed_data;

?>
<div <?php echo $sed_attrs; ?> class="posts-wrapper <?php echo $class; ?>">
    <div class="row">
        <div class="col-md-12">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php if( ! post_password_required()  ) : ?>
                <header class="entry-header">
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
						<div class="thumb single-post-featured-image <?php echo $sed_data['single_post_featured_image_align'];?> <?php echo $sed_data['single_post_featured_image_align'];?> <?php if( !$sed_data['single_post_show_featured_image'] ) echo "hide";?>">
                            <img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo $thumb_alt; ?>" title="<?php echo $thumb_info->post_title ?>" class="sed-img"/>
						</div>
				        <?php endif;
			                   endif; ?>

                    <div class="entry-meta item-meta <?php if( !$sed_data['single_post_meta_show'] ) echo 'hide'; ?>">
						<div class="item post-author <?php if( !$sed_data['single_post_author_show'] ) echo 'hide'; ?>" >
                            <i class="fa fa-user"></i>
							<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php printf( __("By %s" , "site-editor" ) , get_the_author() ) ?></a>
						</div>
						<div class="item post-date <?php if( !$sed_data['single_post_date_show']  ) echo 'hide'; ?>" >
                            <i class="fa fa-calendar-o"></i>
							<span><?php the_time( $sed_data['single_post_data_format']  ) ?></span>
						</div>

                        <?php
                        $format = get_post_format( get_the_ID() );
                        switch ( $format ) {
                            case 'aside':
                                $name_format = __("Aside" , "site-editor");
                                $icon_format = "fa fa-file-text-o";
                            break;
                            case 'audio':
                                $name_format = __("Audio" , "site-editor");
                                $icon_format = "fa fa-music";
                            break;
                            case 'chat':
                                $name_format = __("Chat" , "site-editor");
                                $icon_format = "fa fa-wechat";
                            break;
                            case 'image':
                                $name_format = __("Image" , "site-editor");
                                $icon_format = "fa fa-picture-o";
                            break;
                            case 'gallery':
                                $name_format = __("Gallery" , "site-editor");
                                $icon_format = "fa fa-camera-retro";
                            break;
                            case 'link':
                                $name_format = __("Link" , "site-editor");
                                $icon_format = "fa fa-link";
                            break;
                            case 'quote':
                                $name_format = __("Quote" , "site-editor");
                                $icon_format = "fa fa-quote-right";
                            break;
                            case 'status':
                                $name_format = __("Status" , "site-editor");
                                $icon_format = "fa fa-comment-o";
                            break;
                            case 'video':
                                $name_format = __("Video" , "site-editor");
                                $icon_format = "fa fa-film";
                            break;
                            default:
                                $name_format = __("Standard" , "site-editor");
                                $icon_format = "fa fa-thumb-tack";
                            break;
                        }
                        ?>
                        <div class="item post-format">
                            <a href="<?php echo get_post_format_link($format);?>"><i class="<?php echo $icon_format ?>"></i>
                            <span><?php echo $name_format?></span></a>
                        </div>

						<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :?>
						<div class="item post-comments <?php if( !$sed_data['single_post_comment_count_show'] ) echo 'hide'; ?>" >
                            <i class="fa fa-comments"></i>
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
                        ?>
                        <?php
							edit_post_link( __( 'Edit', 'site-editor' ), '<span class="edit-link"><i class="fa fa-edit"></i>', '</span>' );
						?>

					</div><!-- .entry-meta -->

                    <h1 class="title sed-title-posts <?php if( !$sed_data['single_post_title_show']  ) echo 'hide'; ?>" ><?php the_title();?></h1>


                </header>
                <?php endif; ?>
                <section class="content-post">
                    <?php the_content(); ?>
                </section>
                <?php sed_link_pages(); ?>
                <?php if( ! post_password_required()  ) : ?>
                <footer class="entry-footer item-meta <?php if( !$sed_data['single_post_meta_show'] ) echo 'hide'; ?>">
                    <div class="entry-inner">
					<div class="item post-tags <?php if( !$sed_data['single_post_tags_show'] ) echo 'hide'; ?>" >
                        <?php if(!empty($tags)): ?>
						<strong class="label"><i class="fa fa-tags"></i><span><?php _e("Tags: ", "site-editor") ?></span></strong>
						<?php foreach ( $tags as $tag ): ?>
							<h4 class="post-tag"><a href="<?php echo get_tag_link($tag->term_id); ?>"><?php echo $tag->name ?></a></h4>
						<?php endforeach;
                        endif;?>
					</div>

                    <div class="item post-cats <?php if( !$sed_data['single_post_cat_show'] ) echo 'hide'; ?>" >
                        <?php if( !empty($cats) ) : ?>
						<span class="label"><i class="fa fa-folder-open"></i><?php _e("Categories: ", "site-editor") ?></span>
						<?php
                            foreach ( $cats as $cat ):
                            $category_link = get_category_link( $cat->term_id );
                        ?>
							<span class="post-cat"><a href="<?php echo esc_url( $category_link ); ?>"><?php echo $cat->name ?></a></span>
						<?php
                            endforeach;
                          endif; ?>
					</div>

					</div><!-- .entry-meta -->


                </footer>
                <?php endif; ?>
            </article>
        </div>
    </div>
</div>
