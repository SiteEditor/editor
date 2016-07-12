<?php
class SEDContextmenuProvider{

    function __construct(  ) {
        if( site_editor_app_on() ){
            add_action( 'wp_footer', array( $this , 'sed_app_contextmenu' ), 20 );
            add_action( 'wp_footer', array( $this , 'sed_app_contextmenu_settings' ), 20 );
        }
    }

    public function sort_menu_items_by_priority( $items ){

        if(!empty( $items )){

            $items_priorities = array();
            foreach($items AS $name => $item){
                $items_priorities[$name] = $item->priority;
            }
            asort( $items_priorities );
            $new_items = array();
            foreach($items_priorities AS $name => $priority){
                $new_items[$name] = $items[$name];
            }

            return $new_items;
        }else{
            return $items;
        }
    }

    public function sed_app_contextmenu(){
        global $site_editor_app;

        $menus = $site_editor_app->contextmenu->menus;

        if(!empty( $menus )){
           foreach($menus AS $name => $menu){
        ?>
        <ul id="<?php echo $name; ?>_contextmenu" class="jeegoocontext cm_default sed_app_contextmenu">
        <?php
          echo $this->context_menu_output( $menu->items , $name , $name);
        ?>
        </ul>
        <?php
           }
        }
    }

    public function sed_app_contextmenu_item_settings( $items = array() , $top_menu_name , $parent_name ){
        $item_contextmenu_settings = array();
        if(!empty( $items )){
            foreach($items AS $item_name => $item){
                if($item->is_submenu === true){
                    $item_contextmenu_settings = array_merge($item_contextmenu_settings , $this->sed_app_contextmenu_item_settings( $item->items , $top_menu_name , $item_name ) );
                }else{
                    $item_contextmenu_settings[$top_menu_name . "_" . $parent_name . "_" . $item_name] = array(
                        'item_name'     =>   $item_name ,
                        'options'       =>   $item->options  ,
                    );
                }
            }
        }
        return $item_contextmenu_settings;
    }

    public function sed_app_contextmenu_settings(){
        global $site_editor_app;

        $contextmenu_settings = array();
        $item_contextmenu_settings = array();
        $menus = $site_editor_app->contextmenu->menus;
        if(!empty( $menus )){
           foreach($menus AS $name => $menu){
                $contextmenu_settings[$name] = array(
                    'type'      =>   $menu->type  ,
                    'selector'  =>   $menu->selector  ,
                    'menu_id'   =>   $menu->menu_id  ,
                    'shortcode' =>   $menu->shortcode
                );
                $item_contextmenu_settings = array_merge($item_contextmenu_settings , $this->sed_app_contextmenu_item_settings( $menu->items , $name , $name  ) );
           }
    		?>
    		<script type="text/javascript">
    		        var _sedAppEditorContextMenuSettings = <?php echo wp_json_encode( $contextmenu_settings ); ?>;
                    var _sedAppEditorItemContextMenuSettings = <?php echo wp_json_encode( $item_contextmenu_settings ); ?>;
    		</script>
    		<?php
        }
    }

    function context_menu_output($items = array() , $top_menu_name , $parent_name){
        $output = '';
        if(!empty( $items )){
            $items = $this->sort_menu_items_by_priority( $items );
            foreach($items AS $name => $item){
                if( $item->type_icon == "class" ){
                    $icon_class = $item->icon;
                    $icon_img = "";
                }else if( $item->type_icon == "src" ){
                    $icon_class = "";
                    $icon_img = '<img src="'. $item->icon .'" alt="'. $item->title .'" />';
                }

                $attr_string = '';
                $classes = '';
                if(!empty($item->attr)){
                    foreach($item->attr AS $attr => $value){
                        if(strtolower($attr) == "class"){
                            $classes .= $value;
                        }else{
                            $attr_string .= $attr . '="' . $value . '" ';
                        }
                    }
                }


                if(!empty($item->custom_html))
                    $output .= '<li class="contextmenu-item-container contextmenu-custom '. $classes .'" data-name="'.$name.'" data-action="'.$item->action.'" id="'. $top_menu_name . "_" . $parent_name . "_" . $name .'" tabindex="-1" role="menuitem" '. $attr_string .'>'.$item->custom_html;
                else
                    $output .= '<li class="contextmenu-item-container '. $classes .'" data-name="'.$name.'" data-action="'.$item->action.'" id="'. $top_menu_name . "_" . $parent_name . "_" . $name .'" tabindex="-1" role="menuitem" '. $attr_string .'><a><span class="menu_item_icon '. $icon_class .'">'.$icon_img.'</span><span class="menu_item_txt">'.$item->title.'</span></a>';

                if($item->is_submenu === true){
                    $output .= '<ul>'. $this->context_menu_output($item->items , $top_menu_name , $name) .'</ul>';
                }

                $output .= '</li>';
            }
        }
        return $output;
    }

}

new SEDContextmenuProvider();

