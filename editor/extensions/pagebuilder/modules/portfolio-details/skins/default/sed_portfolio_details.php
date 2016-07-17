<?php
global $sed_data , $post;

if( !isset( $_REQUEST['post_id'] ) && !$post )
    return ;

$post_id = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : $post->ID;

?>

<div <?php echo $sed_attrs; ?> class="module module-portfolio-details portfolio-details-default <?php echo $class ?> ">
    <?php
        //$post_cats->name , $post_cats->term_id , $post_cats->slug
        $post_cats = wp_get_post_terms( $post_id, 'portfolio_category', array("fields" => "all"));

        $post_skills = wp_get_post_terms( $post_id, 'portfolio_skill', array("fields" => "all"));

        $portfolio_project_url          = esc_url( get_post_meta( $post_id, '_portfolio_project_url', true ) );

        $portfolio_project_url_text     = get_post_meta( $post_id, '_portfolio_project_url_text', true );

        $portfolio_copyright_url        = esc_url( get_post_meta( $post_id, '_portfolio_copyright_url', true ) );

        $portfolio_copyright_url_text   = get_post_meta( $post_id, '_portfolio_copyright_url_text', true );
    ?>

    <table class="table table-portfolio-details">

        <tbody>
          <tr>
              <th scope="row"><?php _e( 'Skills Needed', 'woocommerce' ); ?></th>
              <td class="skills-name">
                  <ul>
                      <?php
                        foreach( $post_skills AS $skill ){   ?>
                            <li><?php echo $skill->name; ?></li>
                      <?php  }
                      ?>
                  </ul>
              </td>
          </tr>
          <tr>
              <th scope="row"><?php _e( 'Categoreis', 'woocommerce' ); ?></th>
              <td class="cats-name">
                  <ul>
                      <?php
                        foreach( $post_cats AS $cat ){   ?>
                            <li><?php echo $cat->name; ?></li>
                      <?php  }
                      ?>
                  </ul>
              </td>
          </tr>
          <tr>
              <th scope="row"><?php _e( 'Project URL', 'woocommerce' ); ?></th>
              <td class=""><a href="<?php echo $portfolio_project_url ?>"><?php echo $portfolio_project_url_text ?></a></td>
          </tr>
          <tr>
              <th scope="row"><?php _e( 'Copyright', 'woocommerce' ); ?></th>
              <td class=""><a href="<?php echo $portfolio_copyright_url ?>"><?php echo $portfolio_copyright_url_text ?></a></td>
          </tr>
        </tbody>
    </table>

</div>