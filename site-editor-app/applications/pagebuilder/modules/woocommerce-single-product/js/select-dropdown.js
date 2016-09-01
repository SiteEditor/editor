jQuery( document ).ready( function ( $ ) {

    $('.sed-select-dropdown').livequery(function(){
        var $element =$(this),
            dropdownToggle =  $element.find(".dropdown-toggle"),
            li =  $element.find(".dropdown-menu > li") ,
            maxW = $("#product-archive-orderby").width();

        //li.width( maxW );
        dropdownToggle.width( maxW );

        $("#product-archive-orderby").addClass("hide");

        if(li.filter(".selected").length > 0){
            dropdownToggle.text(li.filter(".selected").text());
        }else{
            dropdownToggle.text(li.filter('[data-value="default"]').text());
        }

        li.click(function(){

            var text =  $(this).text();
            dropdownToggle.text(text);
            dataValue =   $(this).data("value");

            li.removeClass("selected");
            $(this).addClass("selected");
            $("#product-archive-orderby").val(dataValue);
            $element.parent(".woocommerce-ordering:first").submit();

        });

    });
});
