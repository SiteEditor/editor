<?php
/*
Module Name: Icon Library
Module URI: http://www.siteeditor.org/modules/icon-library
Description: Module Icon Library For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/                            

if(!class_exists('SiteEditorIconManager'))
{
	class SiteEditorIconManager
	{
        public $fonts;
        var $paths = array();
		var $sed_fonts_dir;
		var $sed_default_fonts_dir;
		var $font_name = 'unknown';
		var $unicode = '';
		var $svg_config = array();
        static $iconlist = array();
		var $json_config = array();
        var $styles_added = array(); //for add style to ajax load fon in to library

		function __construct(  )
		{
            $this->module_base_dir = SED_EXT_PATH . DS . "icon-library";
			$this->paths = wp_upload_dir();
			$this->paths['fonts'] 	= 'sed_icons_fonts';
			$this->paths['temp']  	= trailingslashit($this->paths['fonts']).'sed_icons_fonts_temp';
			$this->paths['fontdir'] = trailingslashit($this->paths['basedir']).$this->paths['fonts'];
			$this->paths['tempdir'] = trailingslashit($this->paths['basedir']).$this->paths['temp'];
			$this->paths['fonturl'] = trailingslashit($this->paths['baseurl']).$this->paths['fonts'];
			$this->paths['tempurl'] = trailingslashit($this->paths['baseurl']).trailingslashit($this->paths['temp']);
			$this->paths['config']	= 'config.php';
			$this->sed_fonts_dir = trailingslashit($this->paths['basedir']).$this->paths['fonts'];
			$this->sed_default_fonts_dir =$this->module_base_dir . DS . 'fonts' . DS;

		    $this->fonts = get_option('sed_icons_fonts');

            add_action( 'sed_init' , array($this,'check_add_default_font') );

            add_action( 'sed_footer' , array($this,'add_tmpls_icon_library') );

            add_action( 'sed_enqueue_scripts' , array($this,'load_styles') );

            add_action("site_editor_ajax_add_zipped_font", array($this,"add_zipped_font") );

            add_action('site_editor_ajax_remove_icons_font', array($this, 'remove_icons_font'));

            add_filter('sed_enqueue_scripts', array($this,'add_icons_library_js_plugin') );

            add_filter( "sed_js_I18n", array($this,'js_I18n'));

            add_filter( "sed_addon_settings", array($this,'icon_settings'));
		}

        function icon_settings( $sed_addon_settings ){
            global $site_editor_app;
            $sed_addon_settings['iconLibrary'] = array(
                'nonce'  => array(
                    'load'  =>  wp_create_nonce( 'sed_app_icon_font_load_' . $site_editor_app->get_stylesheet() ) ,
                    'remove'  =>  wp_create_nonce( 'sed_app_icon_font_remove_' . $site_editor_app->get_stylesheet() )
                )
            );
            return $sed_addon_settings;
        }

        function js_I18n( $I18n ){
            $I18n['invalid_data']            =  __('Sent Data, Invalid' , "site-editor");
            $I18n['insert_icon']             =  __("Insert Icon" , "site-editor");
            $I18n['cancel']                  =  __("Cancel" , "site-editor");
            $I18n['font_archive']            =  __("Font Archive" , "site-editor");
            $I18n['ajax_remove_font_error']  =  __("Couldn't remove the font because the server didn�t respond." , "site-editor");
            $I18n['remove_font_success']     =  __("Icon set deleted successfully!" , "site-editor");
            $I18n['remove_font_error']       =  __("Couldn't remove the font.The script returned the following error:" , "site-editor");
            return $I18n;
        }

        function check_add_default_font(){
                
            if( is_dir( $this->sed_default_fonts_dir ) ){
    			// Make destination directory
    			if (!is_dir($this->sed_fonts_dir)) {
    				wp_mkdir_p($this->sed_fonts_dir);
    			}
    			@chmod($this->sed_fonts_dir,0777);

            	$fonts_dir = @ opendir( $this->sed_default_fonts_dir );
            	$fonts = array();
            	if ( $fonts_dir ) {
            		foreach (glob($this->sed_default_fonts_dir.'*') as $font_folder ) {
                        $folder_name = basename($font_folder);
                        $new_dir = $this->sed_fonts_dir. DS . $folder_name;
                        wp_mkdir_p( $new_dir );
                        @chmod( $new_dir ,0777);

            			foreach(glob($font_folder.'/*') as $file)
            			{
            				$new_file = basename($file);
            				@copy($file, $new_dir . DS .$new_file);
            			}

            			$fonts[$folder_name] = array(
                            'icon_prefix'       => "fa-" ,
                            'class_selector'    => ".fa" ,
            				'include'           => trailingslashit($this->paths['fonts']).$folder_name,
            				'folder' 	        => trailingslashit($this->paths['fonts']).$folder_name,
            				'style'	            => trailingslashit($this->paths['fonturl']).$folder_name. '/' .$folder_name.'.css',
            				'config' 	        => $this->paths['config']
            			);

                    }
                }

    			if(!$this->fonts){
    				update_option('sed_icons_fonts',$fonts);
                    $this->fonts = $fonts;
    			}

                @rmdir( $this->sed_default_fonts_dir );
            }
        }

        function add_tmpls_icon_library(){
			$max_upload_size = SiteEditorMediaManager::sed_max_upload_size();
			require $this->module_base_dir . DS . "view" . DS . "icon-library.php";
        }

		public function get_icons_fonts(){

			$fonts =  $this->fonts;
            $fonts = array_reverse( $fonts );
            $output = "";

			foreach($fonts as $font => $info)
			{
				$icon_set = array();
				$icons = array();
				$upload_dir = wp_upload_dir();
				$path		= trailingslashit($upload_dir['basedir']);
				$file = $path.$info['include'].'/'.$info['config'];

				include($file);
				if(!empty($icons))
				{
					$icon_set = array_merge($icon_set,$icons);
				}
				if($font == "FontAwesome")
					$set_name = __('Font Awesome' , 'site-editor');
				else
					$set_name = ucfirst($font);
				if(!empty($icon_set))
				{

                    $selector = str_replace( "." , "" , $info['class_selector'] );
                    $prefix = str_replace( "-" , "" , $info['icon_prefix'] );

				    $output .= '<div class="sed-font-icon-box">';
					$output .= '<h4 class="sed-font-icon-title"><span>'.$set_name.'</span>';

                    $output .= '<span class="sed-icon-prefix">'. __("prefix" , "site-editor"). " : " . $prefix .'</span>';

                    $output .= '<span class="sed-icon-class-selector">'. __("class Selector" , "site-editor"). " : " . $selector .'</span>';

                    if( $font != "FontAwesome" )
                        $output .= '<span data-font-name="'.$font.'" class="FontAwesome-remove remove-font-icon"></span>';

                    $output .= '</h4><ul>';
					foreach($icon_set as $icons)
					{
						foreach($icons as $icon_name => $icon)
						{

						    if( empty($icon['tags']) )
                                $icon_tag = stripslashes($icon['class']) . "," . $prefix .'-'. stripslashes($icon['class']);
						    else
                                $icon_tag = "," . stripslashes($icon['class']) . "," . $prefix .'-'. stripslashes($icon['class']);

							$output .= '<li class="sed-icon-item" title="'.stripslashes($icon['class']).'" data-icon="'. $selector . ' ' .$prefix.'-'.stripslashes($icon_name).'" data-icon-tag="'.stripslashes($icon['tags']).'">';
							$output .= '<i class="icon '. $selector . ' ' . $prefix.'-'.stripslashes($icon_name).'"></i><label class="icon">'.stripslashes($icon['class']).'</label></li>';
						}
					}
                    $output .= '</ul></div>';
				}
			}

			return $output;
		}

        public function load_styles(){

			if(is_array($this->fonts))
			{
				foreach($this->fonts as $font => $info)
				{
                    wp_register_style( 'sed-'.$font, $info['style'] , array());
                    wp_enqueue_style( 'sed-'.$font );
				}
			}
        }

		public function add_icons_library_js_plugin() {
			wp_register_script("sed-icons-library", SED_EXT_URL . 'icon-library/js/iconsLibraryPlugin.min.js' , array(  ) , "1.0.0",1 );
			wp_enqueue_script( 'sed-icons-library' );
		}

        public function sed_ico_die($success = true , $output = '' , $styles = array()){
            die( wp_json_encode( array(
              'success' => $success,
              'data'    => array(
                    'output' => $output ,
                    'styles' => $styles
              ),
            ) ) );
        }

        public function add_zipped_font(){
			//check if referer is ok
            global $sed_apps;
            $sed_apps->editor->manager->check_ajax_handler('icon_font_loader' , 'sed_app_icon_font_load');

			//get the file path of the zip file
			$attachment = $_POST['filedata'];
			$path 		= realpath(get_attached_file($attachment['id']));
			$unzipped 	= $this->zip_flatten( $path , array('\.eot','\.svg','\.ttf','\.woff','\.json','\.css'));
				// if we were able to unzip the file and save it to our temp folder extract the svg file
			if($unzipped)
			{
				$this->create_config();
			}
			//if we got no name for the font dont add it and delete the temp folder
			if($this->font_name == 'unknown')
			{
				$this->delete_folder($this->paths['tempdir']);
                $this->sed_ico_die(false , __('Was not able to retrieve the Font name from your Uploaded Folder' , 'site-editor'));
			}

            $output = $this->get_icons_fonts();
            $this->sed_ico_die(true , $output , $this->styles_added);

        }

		//extract the zip file to a flat folder and remove the files that are not needed
		function zip_flatten ( $zipfile , $filter)
		{
			@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', WP_MAX_MEMORY_LIMIT ) );
				$tempdir = $this->create_font_folder($this->paths['tempdir'], false);

			if(!$tempdir) $this->sed_ico_die(false ,__('Wasn\'t able to create temp folder' , 'site-editor'));
				$zip = new ZipArchive;

			if ( $zip->open( $zipfile ) )
			{
				for ( $i=0; $i < $zip->numFiles; $i++ )
				{
					$entry = $zip->getNameIndex($i);
					if(!empty($filter))
					{
						$delete 	= true;
						$matches 	= array();
						foreach($filter as $regex)
						{
							preg_match("!".$regex."!", $entry , $matches);
							if(!empty($matches))
							{
								$delete = false;
								break;
							}
						}
					}
					if ( substr( $entry, -1 ) == '/' || !empty($delete)) continue; // skip directories and non matching files
						$fp  = $zip->getStream( $entry );
					$ofp 	= fopen( $this->paths['tempdir'].'/'.basename($entry), 'w' );
					if ( ! $fp )
						$this->sed_ico_die(false ,__('Unable to extract the file.' , 'site-editor'));
					while ( ! feof( $fp ) )
						fwrite( $ofp, fread($fp, 8192) );
					fclose($fp);
					fclose($ofp);
				}
			 $zip->close();
			}
			else
			{
				$this->sed_ico_die(false ,__('Wasn\'t able to work with Zip Archive' , 'site-editor'));
			}

			return true;
		}

		//iterate over xml file and extract the glyphs for the font
		function create_config()
		{
			$this->json_file = $this->find_json();
			$this->svg_file = $this->find_svg();
			if(empty($this->json_file) || empty($this->svg_file))
			{
				$this->delete_folder($this->paths['tempdir']);
				$this->sed_ico_die(false ,__('selection.json or SVG file not found. Was not able to create the necessary config files' , 'site-editor'));
			}
			//$response 	= wp_remote_get( $this->paths['tempurl'].$this->svg_file );
			$response   	= wp_remote_fopen(trailingslashit($this->paths['tempurl']).$this->svg_file );
			//if wordpress wasnt able to get the file which is unlikely try to fetch it old school
			$json = file_get_contents(trailingslashit($this->paths['tempdir']).$this->json_file );
			if(empty($response)) $response = file_get_contents(trailingslashit($this->paths['tempdir']).$this->svg_file );
			if (!is_wp_error($json) && !empty($json))
			{
				$xml = simplexml_load_string($response);
				$font_attr = $xml->defs->font->attributes();
				$glyphs = $xml->defs->font->children();
				$this->font_name = (string) $font_attr['id'];
				$unicodes = array();
				foreach($glyphs as $item => $glyph)
				{
					if($item == 'glyph')
					{
						$attributes = $glyph->attributes();
						$unicode	=  (string) $attributes['unicode'];
						array_push($unicodes,$unicode);
					}
				}
				$font_folder = trailingslashit($this->paths['fontdir']).$this->font_name;
				if(is_dir($font_folder))
				{
					$this->delete_folder($this->paths['tempdir']);
					$this->sed_ico_die(false ,__("It seems that the font with the same name is already exists! Please upload the font with different name." , 'site-editor'));
				}
				$file_contents = json_decode($json);
				$icons = $file_contents->icons;

                if( ! $icon_prefix = $file_contents->preferences->fontPref->prefix )
                    $icon_prefix = "icon-";

                $this->icon_prefix = $icon_prefix;

                if( ! $class_selector = $file_contents->preferences->fontPref->classSelector )
                    $class_selector = ".sed-" . $this->font_name;

                $this->class_selector = $class_selector;

				unset($unicodes[0]);
				$n = 1;
				foreach($icons as $icon)
				{
					$icon_name = $icon->properties->name;

                    if( strpos( $icon_name ,  "," ) > -1 ){
                        $icon_names = explode( "," , $icon_name );
                        $icon_name = trim($icon_names[0]);

                    }

                    if(!empty($icon->icon->tags))
					    $tags = implode(",",$icon->icon->tags);
                    else
                        $tags = "";

					$this->json_config[$this->font_name][$icon_name] = array(
							"class" => str_replace(' ', '', $icon_name),
							"tags" => $tags,
							"unicode" => $unicodes[$n]
					);
					$n++;
				}
				if(!empty($this->json_config) && $this->font_name != 'unknown')
				{
					$this->write_config();
					$this->re_write_css();
					$this->rename_files();
					$this->rename_folder();
					$this->add_font();
				}
			}
			return false;
		}

		//finds the json file we need to create the config
		function find_json()
		{
			$files = scandir($this->paths['tempdir']);
			foreach($files as $file)
			{
				if(strpos(strtolower($file), '.json')  !== false && $file[0] != '.')
				{
					return $file;
				}
			}
		}
		//finds the svg file we need to create the config
		function find_svg()
		{
			$files = scandir($this->paths['tempdir']);
			foreach($files as $file)
			{
				if(strpos(strtolower($file), '.svg')  !== false && $file[0] != '.')
				{
					return $file;
				}
			}
		}

		//delete a folder
		function delete_folder($new_name)
		{
			//delete folder and contents if they already exist
			if(is_dir($new_name))
			{
				$objects = scandir($new_name);
				 foreach ($objects as $object) {
				   if ($object != "." && $object != "..") {
					 unlink($new_name."/".$object);
				   }
				 }
				 reset($objects);
				 rmdir($new_name);
			}
		}

		//writes the php config file for the font
		function write_config()
		{
			$charmap 	= $this->paths['tempdir'].'/'.$this->paths['config'];
			$handle 	= @fopen( $charmap, 'w' );
			if ($handle)
			{
				fwrite( $handle, '<?php $icons = array();');
				foreach($this->json_config[$this->font_name] as $icon => $info)
				{
					if(!empty($info))
					{
						$delimiter = "'";
						fwrite( $handle, "\r\n".'$icons["'.addslashes($this->font_name).'"]["'.addslashes($icon).'"] = array("class"=>"'.addslashes($info["class"]).'","tags"=>"'.addslashes($info["tags"]).'","unicode"=>"'.addslashes($info["unicode"]).'");' );
					}
				}
				fclose( $handle );
			}
			else
			{
				$this->delete_folder($this->paths['tempdir']);
				$this->sed_ico_die(false ,__('Was not able to write a config file' , 'site-editor'));
			}
		}
		//re-writes the php config file for the font
		function re_write_css()
		{
			$style 	= $this->paths['tempdir'].'/style.css';
			$file = @file_get_contents($style);
			if($file) {
				$str = str_replace('fonts/', '', $file);
				//$str = str_replace('icon-', $this->icon_prefix.'-', $str);
                $str = str_replace('i {', $this->class_selector . ' {' , $str);
                $str = str_replace( '[class^="'.$this->icon_prefix.'-"], [class*=" '.$this->icon_prefix.'-"] {', $this->class_selector . ' {' ,  $str);

				@file_put_contents($style,$str);
			}
			else
			{
				$this->sed_ico_die(false ,__('Unable to write css. Upload icons downloaded only from icomoon' , 'site-editor'));
			}
		}


		function rename_files()
		{
			$extensions = array('eot','svg','ttf','woff','css');
			$folder = trailingslashit($this->paths['tempdir']);
			foreach(glob($folder.'*') as $file)
			{
				$path_parts = pathinfo($file);
				if(strpos($path_parts['filename'], '.dev') === false && in_array($path_parts['extension'], $extensions) )
				{
					if($path_parts['filename'] !== $this->font_name)
						rename($file, trailingslashit($path_parts['dirname']).$this->font_name.'.'.$path_parts['extension']);
				}
			}
		}

		//rename the temp folder and all its font files
		function rename_folder()
		{
			$new_name = trailingslashit($this->paths['fontdir']).$this->font_name;
			//delete folder and contents if they already exist
			$this->delete_folder($new_name);
			rename($this->paths['tempdir'], $new_name);
		}

		function add_font()
		{
			$fonts = get_option('sed_icons_fonts');
			if(empty($fonts)) $fonts = array();

			$fonts[$this->font_name] = array(
                'icon_prefix'       => $this->icon_prefix ,
                'class_selector'    => $this->class_selector ,
				'include'           => trailingslashit($this->paths['fonts']).$this->font_name,
				'folder' 	        => trailingslashit($this->paths['fonts']).$this->font_name,
				'style'	            => trailingslashit($this->paths['fonturl']).$this->font_name.'/'.$this->font_name.'.css',
				'config' 	        => $this->paths['config']
			);

            $this->styles_added[] = $fonts[$this->font_name]['style'];

            $this->fonts = $fonts;
			update_option('sed_icons_fonts', $fonts);
		}

    	/*
    	* creates a folder for the sed App
    	*/

  		function create_font_folder(&$folder, $addindex = true)
  		{
  			if(is_dir($folder) && $addindex == false)
  				return true;
  			$created = wp_mkdir_p( trailingslashit( $folder ) );
  			@chmod( $folder, 0777 );
  			if($addindex == false) return $created;
  			$index_file = trailingslashit( $folder ) . 'index.php';
  			if ( file_exists( $index_file ) )
  				return $created;
  			$handle = @fopen( $index_file, 'w' );
  			if ($handle)
  			{
  			    $msg = __('Sorry, browsing the directory is not allowed!' , 'site-editor');
  				fwrite( $handle, "<?php\r\necho '{$msg}';\r\n?>" );
  				fclose( $handle );
  			}
  			return $created;
  		}

		function remove_icons_font()
		{
		    global $sed_apps;
			$sed_apps->editor->manager->check_ajax_handler('icon_font_remove' , 'sed_app_icon_font_remove');
			//get the file path of the zip file
			$font 		= $_POST['font_name'];
			$list 		= self::load_iconfont_list();
			$delete		= isset($list[$font]) ? $list[$font] : false;
			if($delete)
			{
				$this->delete_folder($delete['include']);
				$this->remove_font($font);
					$this->sed_ico_die(true );
			}
			$this->sed_ico_die(false ,__('Was not able to remove Font' , 'site-editor') );
		}

		static function load_iconfont_list()
		{
			if(!empty(self::$iconlist)) return self::$iconlist;
			$extra_fonts = get_option('sed_icons_fonts');
			if(empty($extra_fonts))
                $extra_fonts = array();

		    $font_configs = $extra_fonts;
			//if we got any include the charmaps and add the chars to an array
			$upload_dir = wp_upload_dir();
			$path		= trailingslashit($upload_dir['basedir']);
			$url		= trailingslashit($upload_dir['baseurl']);
			foreach($font_configs as $key => $config)
			{
				if(empty($config['full_path']))
				{
					$font_configs[$key]['include'] = $path.$font_configs[$key]['include'];
					$font_configs[$key]['folder'] = $url.$font_configs[$key]['folder'];
				}
			}
			//cache the result
			self::$iconlist = $font_configs;
				return $font_configs;
		}

		function remove_font($font)
		{
			$fonts = get_option('sed_icons_fonts');
			if(isset($fonts[$font]))
			{
				unset($fonts[$font]);
				update_option('sed_icons_fonts', $fonts);
			}
		}

	}// End class

}
if(class_exists('SiteEditorIconManager'))
{
	new SiteEditorIconManager;
}