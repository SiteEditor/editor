(function( exports, $ ){
	var api = sedApp.editor;

    api.Synchronization = api.Class.extend({
        initialize: function( params, options ) {
            var self = this;
            $.extend( this, options || {} );
            this.mainRowSync();
            this.mainColSync();

            self.preview.bind( 'active', function() {
                self.preview.send('theme_synchronization' , true);
            });
        },

        mainRowSync : function( ) {
            var self = this , $rowIndex , $rows = [];

            this.rows = $( '[sed-layout="row"]' );
            this.rows.each(function(index , element){
                $rowId = $(this).attr("id");
                if(!$rowId)
                    return false;

                if($(this).attr("sed-sync") == "false" || !$(this).attr("sed-sync")){
                    $rowIndex = 0;
                    $rows.push({
                        id        : $rowId ,
                        dropArea  : $(this).attr("sed-row-area") || "",
                        role      : $(this).attr("sed-role") || "custom",
                        index     : $rowIndex,
                        start     : '' ,
                        end       : '' ,
                        content   : '' ,
                        sync      : true ,
                        type      : 'default',  //default || custom
                        attr      :  {}
                    });
                }

            });

            self.preview.bind( 'active', function() {
                $.each($rows , function(index , row){
                    self.preview.send( 'create_main_row' , row );
                });
            });

        },

        mainColSync : function( ) {
            var self = this , $colIndex , $cols = [];

            this.cols = $( '[sed-layout="column"]' );
            this.cols.each(function(index , element){
                $colId = $(this).attr("id");
                if(!$colId)
                    return false;
                var columnRowWidth = $('[sed-role="main-content"] > .columns-row-inner').width();
                if($(this).attr("sed-sync") == "false" || !$(this).attr("sed-sync")){
                    $colIndex = 0;
                    $cols.push({
                        id        : $colId ,
                        role      : $(this).attr("sed-role") || "custom",
                        index     : $colIndex,
                        start     : '' ,
                        end       : '' ,
                        content   : '' ,
                        sync      : true ,
                        type      : 'default',  //default || custom
                        attr      :  {},
                        settings  :  {width : $(this).width()/columnRowWidth}
                    });
                }

            });

            self.preview.bind( 'active', function() {
                $.each($cols , function(index , col){
                    self.preview.send( 'create_main_col' , col );
                });
            });

        }
    });

    $( function() {
        var synchronizer, api = sedApp.editor ;
        api.settings = window._sedAppEditorSettings;


        synchronizer = new api.Synchronization({} , {
            preview : api.preview
        });

    });
          
})( sedApp, jQuery );