<?php

if(!class_exists('SedLayoutContentSetting')){
    /**
     * Class SedLayoutContentSetting
     * Not Support $this->id_data[ 'keys' ]
     */
    class SedLayoutContentSetting extends SedAppSettings{

        public function preview() {
            add_filter( 'option_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
            add_filter( 'default_option_' . $this->id_data[ 'base' ], array( $this, '_preview_filter' ) );
        }

        /*public function _preview_filter( $original ){

            $value = $this->post_value();
            return $this->get_content_layout( $original , $value );

        }*/

        protected function _update_option( $value ) {
            // Handle array-based options.
            $options = get_option( $this->id_data[ 'base' ] );
            $options = $this->get_content_layout( $options , $value );
            if ( isset( $options ) )
                return update_option( $this->id_data[ 'base' ], $options );
        }

        /**
         * Sanitize the setting's value for use in JavaScript.
         *
         * @return mixed The requested escaped value.
         */
        /*public function js_value() {

            $value = apply_filters( "sed_app_sanitize_js_{$this->id}", $this->value(), $this );

            if( !empty( $value ) && is_array( $value ) ){
                foreach ($value AS $theme_id => $content ) {
                    $shortcodes_models = PageBuilderApplication::get_pattern_shortcodes( $content );
                    $value[$theme_id] = $shortcodes_models["shortcodes"];
                }
            }

            return $value;
        }*/

        public function get_content_layout( $original , $value ){

            if ( ! isset( $value ) )
                return $original;
            else {

                if( !empty( $value ) && is_array( $value ) ){
                    foreach ($value AS $theme_id => $content ) {
                        $value[$theme_id] = $this->_filter_row_content( $content );
                    }
                }

                return $value;
            }
        }

        public function _filter_row_content( $content_shortcodes ){

            global $sed_apps;
            $tree_shortcodes = $sed_apps->editor->save->build_tree_shortcode( $content_shortcodes , $content_shortcodes[0]['parent_id'] );
            $content = $sed_apps->editor->save->create_shortcode_content( $tree_shortcodes , array() );

            return $content;
        }

    }

}