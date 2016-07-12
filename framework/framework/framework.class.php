<?php
class ThemeSync{
    var $main_rows_echo = array();
    var $main_cols_echo = array();

    function __construct( ) {
        add_action( 'before_sed_row', array(&$this, 'before_sed_row') , 10 , 2 );
        add_action( 'sed_row_inner', array(&$this, 'sed_row_inner') , 10 , 1 );
        add_action( 'sed_row_sync', array(&$this, 'sed_row_sync') , 10 , 1 );
        add_action( 'sed_row_area', array(&$this, 'sed_row_area') , 10 , 1 );
        add_action( 'before_sed_col', array(&$this, 'before_sed_col') , 10 , 1 );
        add_action( 'sed_col_inner', array(&$this, 'sed_col_inner') , 10 , 1 );
        add_action( 'sed_col_sync', array(&$this, 'sed_col_sync') , 10 , 1 );
        add_action( 'sed_print_columns', array(&$this, 'sed_print_columns') , 10 );
    }

    function before_sed_row( $row_id , $area ){
        global $sed_data;
        if(empty($sed_data))
            return false;

        $page_main_rows = $sed_data['page_main_row'];
        if(!isset( $page_main_rows[$row_id] ))
            return false;

        $current_row_index = $page_main_rows[$row_id]['index'];
        $area_rows = array();
        if(!empty($page_main_rows)){
            foreach($page_main_rows AS $row_id => $row){
               if($row_id != "default"){
                  if($row['drop_area'] == $area){
                      $row['id'] = $row_id;
                      $area_rows[$row['index']] = $row;
                  }
               }
            }
        }

        if(!empty($area_rows)){
            ksort($area_rows);
            foreach($area_rows AS $row_index => $row){
                if( empty($this->main_rows_echo) || (!empty($this->main_rows_echo) && !in_array($row['id'] , $this->main_rows_echo) ) ){
                  if($row_index < $current_row_index && $row['type'] != "default" ){
                      array_push($this->main_rows_echo , $row['id']);
                      echo $row['start'].$row['content'].$row['end'];
                  }
                }
            }
        }
    }

    function before_sed_col( $col_id ){
        global $sed_data;
        if(empty($sed_data))
            return false;

        $page_main_cols = $sed_data['page_main_col'];
        if(!isset( $page_main_cols[$col_id] ))
            return false;

        $current_col_index = $page_main_cols[$col_id]['index'];
        $main_cols = array();
        if(!empty($page_main_cols)){
            foreach($page_main_cols AS $col_id => $col){
               if($col_id != "default"){
                  $col['id'] = $col_id;
                  $main_cols[$col['index']] = $col;
               }
            }
        }

        if(!empty($main_cols)){
            ksort($main_cols);
            foreach($main_cols AS $col_index => $col){
                if( empty($this->main_cols_echo) || (!empty($this->main_cols_echo) && !in_array($col['id'] , $this->main_cols_echo) ) ){
                  if($col_index < $current_col_index && $col['type'] != "default" ){
                      array_push($this->main_cols_echo , $col['id']);
                      echo $col['start'].$col['content'].$col['end'];
                  }
                }
            }
        }

    }

    function sed_row_sync( $row_id ){
        $this->sed_layout_sync( $row_id , 'row');
    }

    function sed_col_sync( $col_id ){
        $this->sed_layout_sync( $col_id , 'col');
    }

    function sed_layout_sync( $layout_id , $layout = 'row' ){
        global $sed_data;
        if(empty($sed_data))
            return false;
        $page_main_layouts = $sed_data['page_main_'.$layout];
        $sync = $page_main_layouts[$layout_id]['sync'];
        if($sync)
            echo 'sed-sync="true"';
        else
            echo 'sed-sync="false"';
    }


    function sed_row_inner( $row_id ){

    }

    function sed_col_inner( $col_id ){

    }

    function sed_print_columns(){
        global $sed_data;
        if(empty($sed_data))
            return false;

        $page_main_cols = $sed_data['page_main_col'];
        $main_cols = array();
        if(!empty($page_main_cols)){
            foreach($page_main_cols AS $col_id => $col){
               if($col_id != "default"){
                      $col['id'] = $col_id;
                      $main_cols[$col['index']] = $col;
               }
            }
        }

        if(!empty($main_cols)){
            ksort($main_cols);
            foreach($main_cols AS $col_index => $col){
                if( empty($this->main_cols_echo) || (!empty($this->main_cols_echo) && !in_array($col['id'] , $this->main_cols_echo) ) ){
                  if($col['type'] != "default" ){
                      array_push($this->main_cols_echo , $col['id']);
                      echo $col['start'].$col['content'].$col['end'];
                  }
                }
            }
        }
    }

    function sed_row_area( $area ){
        global $sed_data;
        if(empty($sed_data))
            return false;

        $page_main_rows = $sed_data['page_main_row'];
        $area_rows = array();
        if(!empty($page_main_rows)){
            foreach($page_main_rows AS $row_id => $row){
               if($row_id != "default"){
                  if($row['drop_area'] == $area){
                      $row['id'] = $row_id;
                      $area_rows[$row['index']] = $row;
                  }
               }
            }
        }

        if(!empty($area_rows)){
            ksort($area_rows);
            foreach($area_rows AS $row_index => $row){
                if( empty($this->main_rows_echo) || (!empty($this->main_rows_echo) && !in_array($row['id'] , $this->main_rows_echo) ) ){
                  if($row['type'] != "default" ){
                      array_push($this->main_rows_echo , $row['id']);
                      echo $row['start'].$row['content'].$row['end'];
                  }
                }
            }
        }
    }

}