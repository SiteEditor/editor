window.sedApp = window.sedApp || {};

(function( exports, $ ){

    var undoManager,
        btnUndo,
        btnRedo,
        btnClear;

    undoManager = new UndoManager();

    btnUndo = $('.undo-site-editor');
    btnRedo = $('.redo-site-editor');
    //btnClear = $();

    function updateUI() {
        btnUndo.disabled = !undoManager.hasUndo();
        btnRedo.disabled = !undoManager.hasRedo();
    }
    undoManager.setCallback(updateUI);

    btnUndo.on("click" , function () {
        undoManager.undo();
        updateUI();
    });

    btnRedo.on("click" , function () {
        undoManager.redo();
        updateUI();
    });

    /*
    btnClear.onclick = function () {
        undoManager.clear();
        updateUI();
    };*/

    updateUI();


    $(document).keydown(function (e) {
        var key = e.which;
        if (e.ctrlKey) { // ctrl
            if (key === 90) { // z
                undoManager.undo();
                updateUI();
            }
            if (key === 89) { // y
                undoManager.redo();
                updateUI();
            }
        }
    });

	// Expose the API to the world.
	exports.undoManager = undoManager;
})( sedApp, jQuery );