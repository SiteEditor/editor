(function( $ ) {

if ( !$.cssHooks ) {
  throw( new Error( "jQuery 1.4.3+ is needed for this plugin to work" ) );
}

function styleSupport( prop ) {
  var vendorProp, supportedProp,
    capProp = prop.charAt( 0 ).toUpperCase() + prop.slice( 1 ),
    prefixes = [ "Moz", "Webkit", "O", "ms" ],
    div = document.createElement( "div" );

  if ( prop in div.style ) {
    supportedProp = prop;
  } else {
    for ( var i = 0; i < prefixes.length; i++ ) {
      vendorProp = prefixes[ i ] + capProp;
      if ( vendorProp in div.style ) {
        supportedProp = vendorProp;
        break;
      }
    }
  }

  div = null;
  $.support[ prop ] = supportedProp;
  return supportedProp;
}

var borderRadius = styleSupport( "borderRadius" );

// Set cssHooks only for browsers that support a vendor-prefixed border radius
if ( borderRadius && borderRadius !== "borderRadius" ) {
  $.cssHooks.borderRadius = {
    get: function( elem, computed, extra ) {
      return $.css( elem, borderRadius );
    },
    set: function( elem, value) {
      elem.style[ borderRadius ] = value;
    }
  };
}
alert(borderRadius );

})( jQuery );