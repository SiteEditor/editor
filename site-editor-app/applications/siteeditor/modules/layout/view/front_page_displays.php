<div id="" class="index-blog-control home-control">
    <a href="javascript:void(0)" class="btn btn-default"  title="<?php echo __("Front page displays","site-editor");  ?>" id="front_page_displays" role="button" >
        <span class="fa f-sed icon-eye fa-lg "></span>
        <span class="el_txt"><?php echo __("Front page displays","site-editor");  ?></span>
    </a>
    <div id="sed-dialog-front_page_displays"  class="sed-dialog"  title="<?php echo __("Front page displays" ,"site-editor");  ?>">
<ul class="accordion-section-content">
    <li>
        <p class="description sed-app-section-description">
            <?php echo __("Your theme supports a static front page.","site-editor");  ?>
        </p>
    </li>
    <li id="sed-app-control-show_on_front" class="sed-app-control sed-app-control-radio">
        <span class="sed-app-control-title"><?php echo __("Front page displays","site-editor");  ?></span>
        <label>                                          <!-- data-sed-app-setting-link="show_on_front" -->
          <input type="radio" class="sed-element-control"  name="sed-app-radio-show_on_front" value="posts">
            <?php echo __("Your latest posts","site-editor");  ?>
          <br>
        </label>
        <label>
          <input type="radio" class="sed-element-control" name="sed-app-radio-show_on_front" value="page">
            <?php echo __("A static page","site-editor");  ?>
          <br>
        </label>
    </li>
    <li id="sed-app-control-page_on_front" class="sed-app-control sed-app-control-dropdown-pages">
        <label class="sed-app-control-select"><span class="sed-app-control-title"><?php echo __("Front page","site-editor");  ?></span>
        <select id="_customize-dropdown-pages-page_on_front" name="_customize-dropdown-pages-page_on_front" class="sed-element-control">
             <option value="0">
            <?php echo esc_attr( __( 'Select page' ) ); ?></option>
             <?php
              $pages = get_pages();
              foreach ( $pages as $page ) {
              	$option = '<option data-href="' . get_page_link( $page->ID ) . '" value="' . $page->ID . '">';
            	$option .= $page->post_title;
            	$option .= '</option>';
            	echo $option;
              }
             ?>
        </select>
        </label>
    </li>
    <li id="sed-app-control-page_for_posts" class="sed-app-control sed-app-control-dropdown-pages">
        <label class="sed-app-control-select"><span class="sed-app-control-title"><?php echo __("Posts page","site-editor");  ?></span>
        <select id="_customize-dropdown-pages-page_for_posts" name="_customize-dropdown-pages-page_for_posts" class="sed-element-control">
           <option value="0">
          <?php echo esc_attr( __( 'Select page' ) ); ?></option>
           <?php
            $pages = get_pages();
            foreach ( $pages as $page ) {
            	$option = '<option data-href="' . get_page_link( $page->ID ) . '" value="' . $page->ID . '">';
          	$option .= $page->post_title;
          	$option .= '</option>';
          	echo $option;
            }
           ?>
        </select>
        </label>
    </li>
</ul>
    </div>
</div>



