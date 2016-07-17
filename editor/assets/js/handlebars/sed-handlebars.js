(function(  $ ) {


        Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {

            switch (operator) {
                case '!=':
                    return (v1 != v2) ? options.fn(this) : options.inverse(this);
                case '==':
                    return (v1 == v2) ? options.fn(this) : options.inverse(this);
                case '===':
                    return (v1 === v2) ? options.fn(this) : options.inverse(this);
                case '<':
                    return (v1 < v2) ? options.fn(this) : options.inverse(this);
                case '<=':
                    return (v1 <= v2) ? options.fn(this) : options.inverse(this);
                case '>':
                    return (v1 > v2) ? options.fn(this) : options.inverse(this);
                case '>=':
                    return (v1 >= v2) ? options.fn(this) : options.inverse(this);
                case '&&':
                    return (v1 && v2) ? options.fn(this) : options.inverse(this);
                case '||':
                    return (v1 || v2) ? options.fn(this) : options.inverse(this);
                default:
                    return options.inverse(this);
            }
        });

        Handlebars.registerHelper('ifSubstr', function ( pharse , start , len , value , options) {

            if( pharse != "" ){

               len = typeof( len ) === "undefined" ? pharse.length : len ;
               pharse = pharse.substr( start,len);
               return ( pharse == value ) ? options.fn(this) : options.inverse(this);

            }else{
                return options.inverse(this);
            }

        });


       Handlebars.registerHelper('replace', function (value,phrase,rep, options) {
			if(value != "")
			   value=value.replace(phrase,rep);
			   
			   
			return new Handlebars.SafeString(value);
        });
       
        Handlebars.registerHelper('substr', function ( value, start, len, options) {
            if( value != "" )
              if(len == "len" )
                value = value.substr( start );   
              else
                value = value.substr( start , len );          
                 
            return new Handlebars.SafeString(value);
        });


/*
       Handlebars.registerHelper('getAttr', function ( attr , valueAttr ) {
            out = '';
            
            if( valueAttr != "" )
                out = attr + '="' + valueAttr + '"' ;
            else
                out = '';

            return new Handlebars.SafeString(out);
       });
*/
        Handlebars.registerHelper('script', function (type , options){
        	var type = typeof( type ) != 'undefined' ? type : 'text/javascript';
            var s = '<script type="' + type + '">' + options.fn(this) +'</script>';
            return new Handlebars.SafeString(s);
        });

}( jQuery ));
