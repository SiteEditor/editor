<?php

function current_type_elemans($current_type, $array){
    global $site_editor_app;
    $array_allow = array();
    if(!empty($array) && is_array($array)){
        foreach($array As $key => $value){
            if( !is_array($value->site_editor_types) ){
                if( strtolower($value->site_editor_types) == "all") $array_allow[$key] = $value;
            }else{
                if( in_array($current_type , $value->site_editor_types) ) $array_allow[$key] = $value;
            }
        }
    }

    return $array_allow;
}

function get_modules_url(){
    return SED_EDITOR_FOLDER_URL."applications/siteeditor/modules/";
}


add_filter( "sed_js_I18n", 'ajax_js_I18n' );

function ajax_js_I18n( $I18n ){
    $I18n['disconnect']              =  __('Not connect.\n Verify Network.' , "site-editor");
    $I18n['not_found']               =  __("Requested page not found. [404]" , "site-editor");
    $I18n['internal_error']          =  __("Internal Server Error [500]." , "site-editor");
    $I18n['parser_error']            =  __("Requested JSON parse failed." , "site-editor");
    $I18n['timeout']                 =  __("Time out error." , "site-editor");
    $I18n['abort']                   =  __("Ajax request aborted." , "site-editor");
    $I18n['uncaught']                =  __("Uncaught Error.\n" , "site-editor");
    $I18n['try_again']               =  __("Try Again" , "site-editor");
    $I18n['please']                  =  __("Please" , "site-editor");
    return $I18n;
}


