<?php


if(!class_exists('SiteEditorPresetInit'))
{
	class SiteEditorPresetInit
	{
        function __construct( ) {

            add_filter('admin_init', array($this,'set_preset_caps') );

            // Register Custom Post Type
            add_action( 'init', array($this,'custom_preset_post_type'), 0 );
    	}

        function set_preset_caps() {
            // gets the author role
            $role = get_role( 'administrator' );

            // This only works, because it accesses the class instance.
            // would allow the author to edit others' posts for current theme only
            $role->add_cap( 'manage_site_editor_preset' );

        }


        function custom_preset_post_type() {

            $labels = array();

            $args = array(
                'label'                 => __( 'Preset Settings', 'text_domain' ),
                'description'           => __( 'Site Editor Presets', 'text_domain' ),
                'labels'                => $labels,
                'supports'              => array( ),
                'hierarchical'          => false,
                'public'                => false,
                'show_ui'               => false,
                'show_in_menu'          => false,
                'show_in_admin_bar'     => false,
                'show_in_nav_menus'     => false,
                'can_export'            => true,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'capability_type'       => 'page',
            );

            register_post_type( 'sed_preset_settings', $args );

        }


    }

}

