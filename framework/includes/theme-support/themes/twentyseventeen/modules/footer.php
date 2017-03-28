<?php

/**
 * SiteEditor Static Module Class
 *
 * Handles add static module in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class TwentyseventeenFooterStaticModule
 * @description : Footer Static Module
 */
class TwentyseventeenFooterStaticModule extends SiteEditorStaticModule {

    /**
     * Main Element Selector
     *
     * @var string
     * @access public
     */
    public $selector = '#colophon';

    /**
     * Header & Main Navigation Dynamic Css Options
     *
     * @var string
     * @access public
     */
    public $dynamic_css_options = array();

    /**
     * is added dynamic css options ?
     *
     * @var string
     * @access public
     */
    public $is_added_dynamic_css_options = false;

    /**
     * Initialize Class after Initialize parent class
     */
    public function init(){

        add_filter( "sed_twentyseventeen_css_vars" , array( $this , "get_dynamic_css_vars" ) , 10 , 1 );

    }

    /**
     * Footer Dynamic Css Variables
     * Add New variable to dynamic css
     *
     * @param $vars
     * @return array
     */
    public function get_dynamic_css_vars( $vars ){

        if( $this->is_added_dynamic_css_options === false ){

            $this->register_dynamic_css_options();

            $this->is_added_dynamic_css_options = true;

        }

        $new_vars = array();

        foreach ( $this->dynamic_css_options As $field_id => $option ){

            if( ! isset( $option['setting_id'] ) )
                continue;

            $new_vars[$field_id] = array(
                'settingId'             =>  $option['setting_id'] ,
                'default'               =>  ! isset( $option['default'] ) ? '' : $option['default']
            );

        }

        return array_merge( $vars , $new_vars );

    }


    public function register_dynamic_css_options(){

        $this->dynamic_css_options = array(

            /**
             * --------------------------------------------------------------
             * 13.6 Footer
             * --------------------------------------------------------------
             */

            'footer_border' => array(
                'setting_id'        => 'sed_footer_border',
                'type'              => 'color',
                'label'             => __('Border Color', 'site-editor'),
                "description"       => __("Footer Border Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'footer_custom_styling' ,
            ),

            'site_info_color' => array(
                'setting_id'        => 'sed_site_info_color',
                'type'              => 'color',
                'label'             => __('Site Info Color', 'site-editor'),
                "description"       => __("Site Info Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'footer_custom_styling' ,
            ),

            'social_bg' => array(
                'setting_id'        => 'sed_social_bg',
                'type'              => 'color',
                'label'             => __('Social Background Color', 'site-editor'),
                "description"       => __("Social Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'footer_custom_styling' ,
            ),

            'social_color' => array(
                'setting_id'        => 'sed_social_color',
                'type'              => 'color',
                'label'             => __('Social Text Color', 'site-editor'),
                "description"       => __("Social Text Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'footer_custom_styling' ,
            ),

            'social_active_bg' => array(
                'setting_id'        => 'sed_social_active_bg',
                'type'              => 'color',
                'label'             => __('Social Active Background Color', 'site-editor'),
                "description"       => __("Social Active Background Color", "site-editor"),
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'panel'             => 'footer_custom_styling' ,
            ),

        );

    }

    /**
     * Register Module Settings & Panels
     */
    public function register_settings(){


        if( $this->is_added_dynamic_css_options === false ){

            $this->register_dynamic_css_options();

            $this->is_added_dynamic_css_options = true;

        }

        $menus = wp_get_nav_menus();
        $menu_options = array(
            "" => __('Select Menu' , 'site-editor')
        );

        if( !empty($menus) ){
            foreach ( $menus as $menu ) {
                $menu_options[$menu->term_id] = esc_html( $menu->name );
            }
        }

        $panels = array(

            /*'footer_media_settings_panel' =>  array(
                'type'              => 'inner_box',
                'title'             => __('Footer Media', 'textdomain'),
                'btn_style'         => 'menu' ,
                'has_border_box'    => false ,
                'icon'              => 'sedico-animation' ,
                'field_spacing'     => 'sm' , 
                //'capability'        => '' ,
                //'theme_supports'    => '' ,
                'parent_id'         => "root",
                'atts'              => array()
            ) ,*/

            'footer_custom_styling' =>  array(
                'type'              => 'inner_box',
                'title'             => __('Footer Custom Edit Style', 'site-editor'),
                'btn_style'         => 'menu' ,
                'has_border_box'    => false ,
                'icon'              => 'sedico-change-style' ,
                'field_spacing'     => 'sm' ,
                'parent_id'         => "root" ,
                'priority'          => 50 ,
            ) ,

        );

        $fields = array( 

            'disable_footer' => array(
                'setting_id'        => 'sed_disable_footer',
                'label'             => __('Disable Footer', 'site-editor'),
                'type'              => 'switch',
                'default'           => false,
                'choices'           => array(
                    "on"       =>    "Yes" ,
                    "off"      =>    "No" ,
                ) ,
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
            ),

            'footer_columns' => array(
                'setting_id'        => 'sed_footer_columns',
                'label'             => __('Footer Columns', 'site-editor'),
                'type'              => 'radio-buttonset',
                'default'           => '2',
                'transport'         => 'postMessage' ,
                'choices'           => array(
                    "2"      =>    __('Two', 'site-editor'),
                    "3"      =>    __('Tree', 'site-editor'),
                    "4"      =>    __('Four', 'site-editor'),
                ) ,
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "disable_footer" ,
                            "value"     => false ,
                            "compare"   => "==="
                        )
                    )
                )
            ),

            'select_social_navigation' => array(
                'setting_id'        => 'nav_menu_locations[social]',
                'label'             => __('Select Social Navigation', 'site-editor'),
                'type'              => 'select',
                'default'           => '',
                'transport'         => 'postMessage' ,
                'option_type'       => 'theme_mod',
                'choices'           => $menu_options ,
                'partial_refresh'   => array(
                    'selector'            => '#colophon .twse-social-navigation',
                    'render_callback'     => array( $this, '_render_social_navigation' ),
                    'container_inclusive' => false
                ),
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "disable_footer" ,
                            "value"     => false ,
                            "compare"   => "==="
                        )
                    )
                )
            ),

            'copyright_text' => array(
                'setting_id'        => 'sed_copyright_text', 
                'label'             => __('Copyright Text', 'site-editor'),
                'type'              => 'text',
                'default'           => '',
                'option_type'       => 'theme_mod',
                'transport'         => 'postMessage' ,
                /*'partial_refresh'   => array(
                    'selector'            => '#colophon .site-info a',
                    'render_callback'     => 'twse_the_copyright_text' ,
                    'container_inclusive' => true
                ),*/
                'dependency' => array(
                    'queries'  =>  array(
                        array(
                            "key"       => "disable_footer" ,
                            "value"     => false ,
                            "compare"   => "==="
                        )
                    )
                )
            ),

        );

        $fields = array_merge( $fields , $this->dynamic_css_options );

        return array(
            'fields'    => $fields ,
            'panels'    => $panels
        );

    }

    public function _render_social_navigation(){

        ob_start();

        if ( has_nav_menu( 'social' ) ) : ?>
            <nav class="social-navigation" role="navigation" aria-label="<?php _e( 'Footer Social Links Menu', 'twentyseventeen' ); ?>">
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'social',
                    'menu_class'     => 'social-links-menu',
                    'depth'          => 1,
                    'link_before'    => '<span class="screen-reader-text">',
                    'link_after'     => '</span>' . twentyseventeen_get_svg( array( 'icon' => 'chain' ) ),
                ) );
                ?>
            </nav><!-- .social-navigation -->
        <?php endif;

        $content = ob_get_contents();

        ob_end_clean();

        return $content;

    }


}




