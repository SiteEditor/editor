<?php

if(!class_exists('SedLayoutContentSetting')){
    /**
     * @Class SedLayoutContentSetting
     *
     */
    class SedLayoutContentSetting {

        /**
         * @var string
         */
        public $id;

        /**
         * Cached and sanitized $_POST value for the setting.
         *
         * @access private
         * @var mixed
         */
        private $_post_value;
        
        /**
         * SedLayoutContentSetting constructor.
         */
        function __construct(  ) {

            $this->id = 'sed_layouts_content';

            add_action( "sed-app-save-data"     , array( $this , "save" ) , 10 , 2 );

            add_action( "sed_app_preview_init"  , array( $this , "preview" ) , 10 , 1  );
  
        }

        public function preview( $manager ) {
            add_filter( 'option_' . $this->id, array( $this, '_preview_filter' ) );
            add_filter( 'default_option_' . $this->id, array( $this, '_preview_filter' ) );
        }

        public function save( $sed_page_customized , $all_posts_content ) {

            // Handle array-based options.
            $options = get_option( $this->id );

            $value = $this->post_value( $options );

            $this->_update_option( $value , $options );

        }

        public final function post_value( $original ) {

            if ( ! isset( $this->_post_value ) ) {

                if ( isset( $_POST['sed_layouts_content'] ) ) {

                    $this->_post_value = json_decode( wp_unslash( $_POST['sed_layouts_content'] ), true );

                }

                if ( empty( $this->_post_value ) ) { // if not isset or if JSON error

                    $this->_post_value = array();

                }

                if( !empty( $original ) && is_array( $original ) ){

                    $this->_post_value = array_merge( $original , $this->_post_value );

                }

            }

            if ( empty( $this->_post_value ) ) {

                return array();

            } else {

                return $this->_post_value;

            }

        }

        public function _preview_filter( $original ){

            $value = $this->post_value( $original );
            return $this->get_content_layout( $original , $value );

        }

        protected function _update_option( $value , $original ) {

            $options = $this->get_content_layout( $original , $value );
            if ( isset( $options ) )
                return update_option( $this->id, $options );
        }

        public function get_content_layout( $original , $value ){

            if ( ! isset( $value ) )
                return $original;
            else {

                if( !empty( $value ) && is_array( $value ) ){
                    foreach ($value AS $theme_id => $content ) {

                        if( is_array( $content ) )
                            $value[$theme_id] = $this->_filter_row_content( $content );
                        else
                            $value[$theme_id] = $content;
                    }
                }

                return $value;
            }
        }

        public function _filter_row_content( $content_shortcodes ){

            global $sed_apps;
            $tree_shortcodes = $sed_apps->editor->save->build_tree_shortcode( $content_shortcodes , $content_shortcodes[0]['parent_id'] );
            $content = $sed_apps->editor->save->create_shortcode_content( $tree_shortcodes , array() , 0 , true );

            return $content;
        }

    }

}