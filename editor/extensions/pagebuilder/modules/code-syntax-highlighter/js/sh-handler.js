SyntaxHighlighter.config.strings.expandSource = __sedSyntaxHighlighter.expand_source;
SyntaxHighlighter.config.strings.help = __sedSyntaxHighlighter.question;
SyntaxHighlighter.config.strings.alert = __sedSyntaxHighlighter.Syntax_highlighter;
SyntaxHighlighter.config.strings.noBrush = __sedSyntaxHighlighter.find_brush;
SyntaxHighlighter.config.strings.brushNotHtmlScript = __sedSyntaxHighlighter.brush_configured;


(function( exports, $ ) {
    var api = sedApp.editor;

$( document ).ready( function (  ) {

	$('.module-code-syntax-highlighter').livequery(function(){
        SyntaxHighlighter.highlight();
    }); // END LIVEQUERY
});

}(sedApp, jQuery));


