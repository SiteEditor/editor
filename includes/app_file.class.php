<?php
 class SEDFile
 {
    static function unpack_package( $package, $delete_package = true , $working_dir ) {
        global $wp_filesystem,$sed_error;
        WP_Filesystem();
        //$working_dir = wp_upload_dir()
        $pathinfo = pathinfo( $package );

        // Clean up working directory
        //if ( $pathinfo['filename'] != '' && $wp_filesystem->is_dir( $working_dir . DS . $pathinfo['filename'] ) )
          //  $wp_filesystem->delete( $working_dir . DS . $pathinfo['filename'] , true);

        // Unzip package to working directory

        $result = unzip_file( $package, $working_dir );
        // Once extracted, delete the package if required.
        if ( $delete_package ){
            unlink( $package );
        }

        if ( is_wp_error( $result ) ) {
            //$wp_filesystem->delete( $working_dir , true);
            $sed_error->set_error(
                    array(
                        "unzip_".$result->get_error_code() => array(
                            "type"          => "error",
                            "massage"       => $result->get_error_message()
                        )
                    )
                );
            
            return "unzip_".$result->get_error_code();
        }

        return $working_dir . $pathinfo['filename'];
    }

    static function get_download_package( $link_package ){
        global $sed_error;
       $package = download_url( $link_package );
        if( is_wp_error( $package ) ){
            $sed_error->set_error(
                array(
                    "download_".$package->get_error_code() => array(
                        "type"          => 'error' ,
                        "title"         => __( "Download failed" , "site-editor" ) ,
                        'cat'           => 'head' ,
                        "massage"       => $package->get_error_message() ,
                    )
                )
            );
            return "download_".$package->get_error_code();
        }
        else
            return $package;
    }

    static function upload( $file_name , $path ){

        require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
        $zip = new File_Upload_Upgrader( $file_name, 'package');      //UPLOAD FILE

        $result = self::unpack_package( $zip->package , true , $path ) ;
        
        if( !is_wp_error( $result ) )
            return $result;
        else{
            $zip->cleanup();
            return $result->get_error_message();
        }

    }

    static function list_files( $root = '.' , $exceptions = '' , $filter = '' , $editable_extensions = array() ){

        $files  = array();
        $directories  = array(); 
        $last_letter  = $root[strlen($root)-1]; 
        $root  = ( $last_letter == '\\' || $last_letter == '/') ? $root : $root. DS ; 
      
        $directories[]  = $root; 
      
        while ( @sizeof( $directories ) ) { 
            $dir  = array_pop( $directories ); 
            if ( $handle = @opendir( $dir )) { 

              while ( false !== ( $file = readdir( $handle ) ) ) { 

                if ( $file == '.' || $file == '..' ) { 
                  continue; 
                } 
                $file  = $dir.$file;
                if( !empty( $exceptions ) )
                    if( preg_match("/$exceptions/", $file ) )
                        continue;

                if (is_dir($file)) { 
                  $directory_path = $file . DS ; 
                  array_push( $directories, $directory_path ); 
                }elseif ( is_file($file) ) { 
                    $file_info = pathinfo($file);

                        if( !empty( $editable_extensions ) ){
                            if( in_array( $file_info['extension'] , $editable_extensions ) )
                                $files[]  = $filter != '' ? str_replace ( $filter , '', $file ) : $file;
                            else
                                continue;
                        }
                        else
                            $files[]  = $filter != '' ? str_replace ( $filter , '', $file ) : $file;

                } 
              } 
              @closedir($handle); 
            } 
        } 
      
        return $files; 
    }

    static function get_file_data( $file, $type = 'js_info' ) {
        // We don't need to write to the file, so just open for reading.
        $fp = fopen( $file, 'r' );
        $info = '';

        // Pull only the first 8kiB of the file in.
        $file_data = fread( $fp, 8192 );

        // PHP will close file handle, but we are good citizens.
        fclose( $fp );

        // Make sure we catch CR-only line endings.
        $file_data = str_replace( "\r", "\n", $file_data );

        if ( preg_match( '/^\/\*\s*\#'. $type .'\#(\([^\}]+\))\#\s*\*\//' , $file_data , $matches) && $matches[1] ){
                eval("\$info = array" . $matches[1].";");
                    return $info;
        }else{
                return false;
        }

    }

}