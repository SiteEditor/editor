<?php
/**
 * SiteEditor Options Template class
 *
 * @package SiteEditor
 * @subpackage Options
 * @since 1.0.0
 */

/**
 * SiteEditor Options Template class.
 *
 * Manage template Options
 *
 * Serves as a factory for Fields and Settings and Controls, and
 * instantiates default Fields and Settings and Controls.
 *
 * @since 1.0.0
 */

class SiteEditorOptionsTemplate{

    /**
     * Registered instances of SiteEditorOptionsPanel.
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    protected $panels = array();

    /**
     * Registered instances of SiteEditorOptionsControl.
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    protected $controls = array();

    /**
     * Collection of roo panels
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    protected $root_panels = array();

    /**
     * Collection of roo controls
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    protected $root_controls = array();

    /**
     * SiteEditorOptionsTemplate constructor.
     * @param array $controls
     * @param array $panels
     */
    function __construct( $controls = array() , $panels = array() ) {

        $this->controls = $controls;

        $this->panels = $panels;

        $this->prepare_controls();

    }

    protected function get_sub_panels( $panels , $parent_id = "root" ) {
        $sub_panels = array();

        if( !empty( $panels ) ){
            foreach( $panels AS $id => $panel ){

                if( ($parent_id == "root" && !isset( $panel->parent_id ) ) || ( isset( $panel->parent_id ) && $panel->parent_id == $parent_id ) ){

                    $child_sub_panels = $this->get_sub_panels( $panels , $id );

                    uasort( $child_sub_panels , array( $this, '_cmp_priority' ) );

                    $panel->sub_panels = $child_sub_panels;

                    $this->panels[$id] = $panel;

                    $sub_panels[$id] = $panel ;
                }
            }
        }

        return $sub_panels;
    }

    /**
     * Prepare panels and controls.
     *
     * For each, check if required related components exist,
     * whether the user has the necessary capabilities,
     * and sort by priority.
     *
     * @since 3.4.0
     */
    protected function prepare_controls() {

        $controls = array();

        uasort( $this->controls, array( $this, '_cmp_priority' ) );

        foreach ( $this->controls as $id => $control ) {

            if ( ! $control->check_capabilities() ) {
                continue;
            }

            if( empty( $control->panel ) || $control->panel == "root" ){
                $this->root_controls[ $id ] = $control;
                $controls[ $id ] = $control;
                continue;
            }

            if ( ! isset( $this->panels[ $control->panel ] ) ) {
                continue;
            }

            $this->panels[ $control->panel ]->controls[] = $control;
            $controls[ $id ] = $control;

        }

        $this->controls = $controls;

        //Prepare panels.
        uasort( $this->panels, array( $this, '_cmp_priority' ) );

        $panels = array();

        //Prepare sub panels
        foreach ( $this->panels as $id => $panel ) {

            if ( ! $panel->check_capabilities() ) {//|| ! $panel->controls
                continue;
            }

            //remove panel if parent id not exist in panels ids
            if( $panel->parent_id != "root" && !isset( $this->panels[ $panel->parent_id ] ) ){
                continue;
            }

            usort( $panel->controls , array( $this, '_cmp_priority' ) );

            $panel->template = $this;

            $panels[ $id ] = $panel;

        }

        $this->root_panels = $this->get_sub_panels( $panels );

        //$this->panels = $panels;

    }

    /**
     * Create template for specify settings ( using controls and panels )
     *
     * @return string
     */
    public function render(){

        $output = $this->get_content( $this->root_controls , $this->root_panels );

        return $output;
    }

    /**
     * get panel or top-level content
     *
     * @param $controls
     * @param array $sub_panels
     * @return string
     */
    public function get_content( $controls , $sub_panels = array() ){

        $params = $this->get_settings( $controls , $sub_panels );

        $output = '';

        foreach ( $params AS $param ){

            if( $param->type == "control" ){

                $control = $param->control;

                $output .= $control->get_content();

            }else if( $param->type == "panel" ){

                $panel = $param->panel;

                $output .= $panel->get_content();

            }

        }

        return $output;

    }

    /**
     * Merge controls And sub panels in one level
     *
     * @param $controls
     * @param array $sub_panels
     * @return array
     */
    protected function get_settings( $controls , $sub_panels = array() ){

        $settings = array();
        $count = 0;

        if( !empty( $sub_panels ) ){

            foreach( $sub_panels as $panel ) {

                $param = new stdClass();

                $param->type = "panel";

                $param->priority = $panel->priority;

                $param->panel = $panel;

                $param->instance_number = $count;

                array_push( $settings , $param );

                $count ++;
            }
        }

        if( !empty( $controls ) ){

            foreach( $controls as $control ) {

                $param = new stdClass();

                $param->type = "control";

                $param->priority = $control->priority;

                $param->control = $control;

                $param->instance_number = $count;

                array_push( $settings , $param );

                $count ++;
            }
        }

        // sort by priority
        uasort( $settings , array( $this , '_cmp_priority' ) );

        return $settings;
    }

    /**
     * Helper function to compare two objects by priority, ensuring sort stability via instance_number.
     *
     * @since 3.4.0
     *
     * @param WP_Customize_Panel|WP_Customize_Section|WP_Customize_Control $a Object A.
     * @param WP_Customize_Panel|WP_Customize_Section|WP_Customize_Control $b Object B.
     * @return int
     */
    protected function _cmp_priority( $a, $b ) {
        if ( $a->priority === $b->priority ) {
            return $a->instance_number - $b->instance_number;
        } else {
            return $a->priority - $b->priority;
        }
    }
    
}