<?php
the_post();
global $sed_data;

?>
<div <?php echo $sed_attrs; ?> class="portfolios-wrapper <?php echo $class; ?>">
    <div class="row">
        <div class="col-md-12">
            <article id="portfolio-<?php the_ID(); ?>" <?php post_class(); ?>>
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
						<div class="thumb single-post-featured-image  <?php echo $sed_data['single_post_featured_image_align'];?> <?php if( !$sed_data['single_post_show_featured_image'] ) echo "hide";?>">
                            <img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo $thumb_alt; ?>" title="<?php echo $thumb_info->post_title ?>" class="sed-img"/>
						</div>
				        <?php endif;
			                   endif; ?>
                        <h4 class="sed-portfolio-title sed-title-posts <?php if( !$sed_data['single_post_title_show']  ) echo 'hide'; ?>"><?php echo __("Project Description" , "site-editor");//the_title();?></h4>
                </header>
                <?php endif; ?>
                <section class="content-portfolio">
                    <?php the_content(); ?>
                    <?php sed_link_pages(); ?> 
                </section>

            </article>
        </div>
    </div>
</div>
