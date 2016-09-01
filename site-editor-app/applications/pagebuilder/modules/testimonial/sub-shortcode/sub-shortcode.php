<?php

// CREATE SUB SHORTCODE FOR TESTIMONIAL MODULE
// ===========================================

class PBTestimonialItemShortcode extends PBShortcodeClass{
  function __construct(){
    parent::__construct( array(
      "name"        => "sed_testimonial_item",
      "module"      =>  "testimonial",
      "is_child"    =>  true
    ));
  }
  function get_atts(){
      $atts = array(
          'parent_module'     => 'testimonial',
      );

      return $atts;
  }
}

new PBTestimonialItemShortcode;