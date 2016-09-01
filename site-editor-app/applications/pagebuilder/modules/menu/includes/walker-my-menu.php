<?php

class SED_Walker_Nav_Menus extends Walker_Nav_Menu {
    private $prev_item;

    public $menu_atts;

    function __construct(  $atts = array() , $content ) {
        $this->menu_atts = $atts;
        $this->menu_content = $content;
    }

    function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output )
    {
        $id_field = $this->db_fields['id'];
        if ( is_object( $args[0] ) ) {
            $args[0]->has_children   = isset( $children_elements[$element->$id_field] );
            $args[0]->count_children = $args[0]->has_children ? count( $children_elements[$element->$id_field] ) : 0;
            $parent_id = get_menu_parent_id( $element->$id_field );
            if( $parent_id > 0){
                $args[0]->parent_megamenu_children = isset( $children_elements[$parent_id] ) ? count( $children_elements[$parent_id] ) : 0;
            }
        }
        return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

// add classes to ul sub-menus
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        global $current_module;
        // depth dependent classes
        $indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent
        $display_depth = ( $depth + 1); // because it counts the first submenu as 0
        $classes = array();
        $class_names = implode( ' ', $classes );



        $tpl = $current_module['skin-path'] . DS . 'walker-tpl' . DS . 'start_lvl.php';
        if( is_file( $tpl ) ){
            ob_start();
            include ( $tpl );
            $content = ob_get_contents();
            ob_end_clean();
            $output .= $content ;
        }else{
            // build html
            $output .= "\n" . $indent . '<ul class="' . $class_names . '1">' . "\n";
        }

    }

    // add main/sub classes to li's and links
    function start_el(  &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $wp_query,$current_module;
        

        $indent    = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
        $item_title = apply_filters( 'the_title', $item->title, $item->ID );
        // depth dependent classes
        $depth_classes = array();
        $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );
        $this->prev_item = $item;



        // passed classes
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

        $tpl = $current_module['skin-path'] . DS . 'walker-tpl' . DS . 'start_el.php';
        if( is_file( $tpl ) ){
            ob_start();
            include ( $tpl );
            $content = ob_get_contents();
            ob_end_clean();
            $item_output = $args->before . $content . $args->after;
        }else{
            
            // build html
            $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class=" '. $depth_class_names . ' ' . $class_names . ' '. $dropdown_class .' '.$submenu_class.'">';

            // link attributes
            $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
            $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
            $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
            $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
            $attributes .= ' class=" ' . ( $depth > 0 ? 'megamenu-target' : 'megamenu-target icon' ) . '"';
            
            $item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
                $args->before,
                $attributes,
                $args->link_before,
                '<span class="'.( $depth > 0 ? 'megamneu-target-title' : '' ).'">'.$item_title.'</span> ' ,
                $args->link_after,
                $args->after
            );
        }

        // build html
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        global $current_module;
        $tpl = $current_module['skin-path'] . DS . 'walker-tpl' . DS . 'end_lvl.php';

        if( is_file( $tpl ) ){
            ob_start();
            include ( $tpl );
            $content = ob_get_contents();
            ob_end_clean();
            $output .= $content ;
        }else{
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }
    }

}
