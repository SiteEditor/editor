(function($) {

    $( "#button" ).click(function() {
        $( "#effect" ).toggle("slide");
    });


    $( "#dialog" ).dialog({
        autoOpen: false,
        modal: true,
        width: 600,
        buttons: {
          "Ok": function () {
            $(this).dialog("close");
          },
          "Cancel": function () {
            $(this).dialog("close");
          }
        }
    });

    $( "#opener" ).click(function() {
        $( "#dialog" ).dialog( "open" );
    });


    $('.dropdown-toggle').dropdown();

    $( ".iconf" ).tooltip({
      position: {
        my: "center bottom-15",
        at: "center top",
        using: function( position, feedback ) {
          $( this ).css( position );
          $( "<div>" )
          .addClass( "arrow bottom" )
          .addClass( feedback.vertical )
          .addClass( feedback.horizontal )
          .appendTo( this );
      }
      }
    });
    /**
    * Tooltip right
    */
    $( ".iconf2" ).tooltip({
        position: {
          my: "left+15 left",
          at: "right center",
          using: function( position, feedback ) {
            $( this ).css( position );
            $( "<div>" )
            .addClass( "arrow left" )
            .addClass( feedback.vertical )
            .addClass( feedback.horizontal )
            .appendTo( this );
          }
        }
    });


  //defualt active tab
  $('#myTab a').click(function (e) {
    e.preventDefault()
    $(this).tab('show');
  });

})(jQuery);