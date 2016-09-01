<?php
/**
 * SiteEditorApp Error class.
 * @package SiteEditorApp
 * @since 1.0.0
 */

class SED_Error {

	private $errors = array();
	/*
$errors = array(
	"codeError" => array(
		"type"		=> "value" , $error ||
		"cat"		=> "value" ,
		"title"		=> "value" ,
		"massage" 	=> "value" ,
	),
)

	*/

	function __construct( $errors = array() ){

		$this->errors = $errors;

	}

	function set_error( $error = array() ){

		if( is_array( $error ) )
			$this->errors = array_merge( $this->errors , $error );
	}

    function get_error ( $args = array() ){
    	$cout = '';
    	$add_error = false ;

    	if ( is_array( $args ) && !empty( $args ) ){
    		extract( $args );

    		$errors = $this->errors ;
    		$out = $result = array();

			if( isset( $code ) ){

				$out[] = isset( $errors[$code] ) ? $errors[$code] : null ;

			}else{

				foreach ( $errors as $key => $value) {

					foreach ( $args as $arg => $varArg )
						if ( isset( $value[$arg] ) && $value[$arg] === $varArg )
							$result[] = true ;
						else
							$result[] = false ;

					if ( !in_array( false , $result ) )
						$out[$key] = $errors[$key] ;

					$result = array() ;

				}

			}
    	}else{

    		$out = $this->errors ; // All Errors

    	}

    	if( !is_null( $out ) && !empty( $out ) && is_array( $out ) ){

    		foreach ( $out as $codeError => $itemError) {

                $cout .= $this->get_show( $itemError , $codeError ) ;

    		}

    	}

        return $cout ;
    }

    function get_message( $codeError = null ){
        $out = array();

        if( !is_null( $codeError ) && isset( $this->errors[$codeError] ) ){
            return $this->errors[$codeError]['massage'];
        }else{
            foreach ( $this->errors as $codeError => $itemError) {
                $out[$codeError] = $itemError['massage'];
            }
        }
        return $out;
    }

    public function is_error( $code ){
        return isset( $this->errors[$code] );
    }

    function get_show( $item = array() , $code = '' ){
    	$out = '';

    	if( is_array( $item ) ){
    		extract( $item );
            $out .= '<div id="setting-error-' . $code . '" class="'.(!empty( $type ) ? $type : 'error  settings-error').'" '.(isset( $style ) ? "style='{$style}'" : '').'>' ;
            $out .= !empty( $title ) ?  "<h2> $title </h2><hr/>" : '' ;
            $out .= !empty( $massage ) ? "<p>$massage</p>" : '' ;
            $out .= !empty( $action ) ? '<p>' . implode( ' | ', $action ) . '</p>': '';
            $out .= "</div>";

    	}
    	return $out;

    }
    public function create_error( $msg ){
        $code = $this->generate_code();
        $error = array(
            $code => array(
                "type"      => "error" ,
                "cat"       => "haed" ,
                "title"     => "ERROR" ,
                "massage"   => $msg ,
            )
        );
        $this->set_error( $error );
        return $code;
    }
    private function generate_code($length = 10, $letters = '1234567890qwertyuiopasdfghjklzxcvbnm') {
        $s = '';
        $lettersLength = strlen( $letters )-1;

        for($i = 0 ; $i < $length ; $i++)
            $s .= $letters[rand(0,$lettersLength)];

        return $s;
    }

}
