window.sedApp = window.sedApp || {};

(function( exports, $ ){
	var api = {};
    api.fn = {};

    api.fn.ucfirst = function(str) {
        //  discuss at: http://phpjs.org/functions/ucfirst/
        str += '';
        var f = str.charAt(0).toUpperCase();

        return f + str.substr(1);
    };

	// Expose the API to the world.
	exports.editor = api;
})( sedApp, jQuery );