<?php

class SED_Admin_Options{
	/*
	$tabs = array(
		"id_tabs"	=> "title tabs"
	);

	$items['tabs'] = array(
		"id_item" 	=> array(
			"type"		=> "text",
			"label"		=> "item 1",
			"desc"		=> "description",
			"std"		=> "default value",
		)
	)

	$options = array( "id_options" => "value" );
	*/
	public $tabs = array();
	public $container_options = array();
	public $items = array();
	public $options = array();

	function __construct( $tabs , $items){

		$this->tabs = $tabs;
		$this->items = $items;


	}

	function get_default_values( ){
	    $defaults = array();
        foreach( $this->items AS $tab => $options ){
            foreach( $options AS $id => $value ){

    			//create array of defaults
    			if ($value['type'] == 'multicheck'){
    				if (is_array($value['std'])){
    					foreach($value['std'] as $i=>$key){
    						$defaults[$id][$key] = true;
    					}
    				} else {
    						$defaults[$id][$value['std']] = true;
    				}
    			} else {
    				if (isset($value['std'])) $defaults[$id] = $value['std'];
    			}
            }
        }

        return $defaults;
	}

	private function set_content_tabs( $id , $title , $content ){

		$this->container_options[] = "<li id='{$id}'>
				<h1>". $title ."</h1>".
				implode('', $this->create_item_field( $content ) ) ."</li>";
	}

	private function create_item_field(  $content  ){
		$items = array();

		foreach ( $content as $id => $options ) {
			$func = 'create_' . $options['type'] . '_option';
			$items[] = $this->$func( $id , $options );
			
		}
		return $items;

	}
	private function create_text_option( $id , $attrs ){
 		$options = $this->options;
		$value = isset( $options[$id] ) ? $options[$id] : ( isset( $attrs['std'] ) ? $attrs['std']  : '' ) ;

        $value = stripslashes( $value );

		$desc = isset( $attrs['desc'] )? '<div class="sed_admin_desc_item"><p>'.$attrs['desc'] .'</p></div>': '';

		$item = '<div id="sed_admin_item'. $id .'" class="sed_admin_item_setting">
			        					<div class="sed_admin_label_item"><label for="'.$id.'">'.$attrs['label'].'</label></div>
				        				<div class="sed_admin_box_field_item">
				        					<div class="sed_admin_field_item">
				        					<input id="'.$id.'" name="'.$id.'" type="text" class="sed_admin_text_field" value="' . $value . '"></div>'.
				        					$desc.'
				        				</div>
			        				</div>';
		return $item;

	}

	private function create_color_option( $id , $attrs ){
 		$options = $this->options;
		$value = isset( $options[$id] ) ? $options[$id] : ( isset( $attrs['std'] ) ? $attrs['std']  : '' ) ;

		$desc = isset( $attrs['desc'] )? '<div class="sed_admin_desc_item"><p>'.$attrs['desc'] .'</p></div>': '';

		$item = '<div id="sed_admin_item'. $id .'" class="sed_admin_item_setting">
			        					<div class="sed_admin_label_item"><label for="'.$id.'">'.$attrs['label'].'</label></div>
				        				<div class="sed_admin_box_field_item">
				        					<div class="sed_admin_field_item">
				        					<input id="'.$id.'" name="'.$id.'" type="text" class="color-field sed_admin_text_field" value="' . $value . '"></div>'.
				        					$desc.'
				        				</div>
			        				</div>';
		return $item;

	}

	private function create_html_option( $id , $attrs ){
 		$options = $this->options;
		$value = isset( $options[$id] ) ? $options[$id] : ( isset( $attrs['std'] ) ? $attrs['std']  : '' ) ;

		$desc = isset( $attrs['desc'] )? '<div class="sed_admin_desc_item"><p>'.$attrs['desc'] .'</p></div>': '';

		$item = '<div id="sed_admin_item'. $id .'" class="sed_admin_item_setting">
			        					<div class="sed_admin_label_item"><label for="'.$id.'">'.$attrs['label'].'</label></div>
				        				<div class="sed_admin_box_field_item">
				        					<div class="sed_admin_field_item">
                                            '.$attrs['html'].'
                                            </div>'.
				        					$desc.'
				        				</div>
			        				</div>';
		return $item;

	}

	private function create_textarea_option( $id , $attrs ){
 		$options = $this->options;
		$value = isset( $options[$id] ) ? $options[$id] : ( isset( $attrs['std'] ) ? $attrs['std']  : '' ) ;

		$desc = isset( $attrs['desc'] )? '<div class="sed_admin_desc_item"><p>'.$attrs['desc'] .'</p></div>': '';

        $value = stripslashes( $value );

		$item = '<div id="sed_admin_item'. $id .'" class="sed_admin_item_setting">
			        					<div class="sed_admin_label_item"><label for="'.$id.'">'.$attrs['label'].'</label></div>
				        				<div class="sed_admin_box_field_item">
				        					<div class="sed_admin_field_item">
                                            <textarea name="'.$id.'" id="'.$id.'" class="sed_admin_textarea_field" cols="80" rows="10">' . $value . '</textarea>'.
				        					$desc.'
				        				</div>
			        				</div>';
		return $item;

	}

	function show(){
		$item_tabs = array();
		foreach ( $this->tabs as $id => $title) {
			$item_tabs[] = sprintf('<li><a href="#%1$s">%2$s</a></li>' , $id , $title ) ;
			$this->set_content_tabs( $id , $title , $this->items[$id] );
		}
		$out = '';
		$out .='<div id="sed_admin_box_tabs">
	        	<ul id="sed_admin_tabs">'.implode('', $item_tabs ) . '</ul></div>';
    	$out .='<div id="sed_admin_box_content_tabs">
		        <ul id="sed_admin_content_tabs">'.implode('', $this->container_options ) . '</ul></div>';
		echo $out;
	}

	private function create_uploader_option( $id , $attrs ){
 		$options = $this->options;
		$value = isset( $options[$id] ) ? $options[$id] : ( isset( $attrs['std'] ) ? $attrs['std']  : '' ) ;

		$desc = isset( $attrs['desc'] )? '<div class="sed_admin_desc_item"><p>'.$attrs['desc'] .'</p></div>': '';

		$item = '<div id="sed_admin_item'. $id .'" class="sed_admin_item_setting">
			        					<div class="sed_admin_label_item"><label for="'.$id.'">'.$attrs['label'].'</label></div>
				        				<div class="sed_admin_box_field_item">
				        					<div class="sed_admin_field_item section">
                                            '.self::optionsframework_uploader( $id , $value , $desc , $id ).'
				        				</div>
			        				</div>';
		return $item;

	}

	/**
	 * Media Uploader Using the WordPress Media Library.
	 *
	 * Parameters:
	 *
	 * string $_id - A token to identify this field (the name).
	 * string $_value - The value of the field, if present.
	 * string $_desc - An optional description of the field.
	 *
	 */


	static function optionsframework_uploader( $_id, $_value, $_desc = '', $_name = '' ) {

		$output = '';
		$id = '';
		$class = '';
		$int = '';
		$value = '';
		$name = '';

		$id = strip_tags( strtolower( $_id ) );

		// If a value is passed and we don't have a stored value, use the value that's passed through.
		if ( $_value != '' && $value == '' ) {
			$value = $_value;
		}

		if ($_name != '') {
			$name = $_name;
		}

		if ( $value ) {
			$class = ' has-file';
		}
		$output .= '<input id="' . $id . '" class="upload' . $class . '" type="text" name="'.$name.'" value="' . $value . '" placeholder="' . __('No file chosen', 'options-framework') .'" />' . "\n";
		if ( function_exists( 'wp_enqueue_media' ) ) {
			if ( ( $value == '' ) ) {
				$output .= '<input id="upload-' . $id . '" class="upload-button button" type="button" value="' . __( 'Upload', 'options-framework' ) . '" />' . "\n";
			} else {
				$output .= '<input id="remove-' . $id . '" class="remove-file button" type="button" value="' . __( 'Remove', 'options-framework' ) . '" />' . "\n";
			}
		} else {
			$output .= '<p><i>' . __( 'Upgrade your version of WordPress for full media support.', 'options-framework' ) . '</i></p>';
		}

		if ( $_desc != '' ) {
			$output .= '<span class="of-metabox-desc">' . $_desc . '</span>' . "\n";
		}

		$output .= '<div class="screenshot" id="' . $id . '-image">' . "\n";

		if ( $value != '' ) {
			$remove = '<a class="remove-image">Remove</a>';
			$image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value );
			if ( $image ) {
				$output .= '<img src="' . $value . '" alt="" />' . $remove;
			} else {
				$parts = explode( "/", $value );
				for( $i = 0; $i < sizeof( $parts ); ++$i ) {
					$title = $parts[$i];
				}

				// No output preview if it's not an image.
				$output .= '';

				// Standard generic output if it's not an image.
				$title = __( 'View File', 'options-framework' );
				$output .= '<div class="no-image"><span class="file_link"><a href="' . $value . '" target="_blank" rel="external">'.$title.'</a></span></div>';
			}
		}
		$output .= '</div>' . "\n";
		return $output;
	}

	function save_options_admin() {

		global $sed_error , $sed_general_data ;
		$options = array();

		if (isset($_GET['page']) && $_GET['page'] == 'site_editor_index' ){

		 	if ( 'save' == $_REQUEST['action'] ){

				foreach ( $this->items as $item ){
					foreach ( $item as $id => $attrs ) {
					
						if( isset( $_REQUEST[ $id ]) )
						{ 
							$options[$id] = $_REQUEST[ $id ];
						} 
						else 
						{ 
							if( isset( $options[$id] ))
								unset( $options[$id] );
						} 
					}

				}

                save_theme_general_options( $options );

				$sed_error->set_error(array(
						"update_setting" 	=> array(
							"type"		=> "updated",
							"massage"	=>	__('settings saved.' , 'site-editor')
						),
					)
				);

		    }elseif( 'reset' == $_REQUEST['action'] ){

				save_theme_general_options( $this->get_default_values() );
				$sed_error->set_error(array(
						"update_setting" 	=> array(
							"type"		=> "updated",
							"massage"	=>	__('Settings are reset.' , 'site-editor')
						),
					)
				);

			}
		}
		$this->options = get_theme_general_options();
        $sed_general_data = $this->options;
	}


}