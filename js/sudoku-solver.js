
//
// Utility functions
//

function assert(condition, message) {
    if (!condition) {
        message = message || "Assertion failed";
        if (typeof Error !== "undefined") {
            throw new Error(message);
        }
        throw message;
    }
}

//
// Table edit
//


function filterInputNumbers(event) {
    // 0 not allowed either
    return (event.charCode >= 49 && event.charCode <= 57) || 
            event.keyCode == 8;
}

$("table tr td").on('blur',"input[type='number']", function( e ){
    $(this).closest('td').text($(this).val());
});

$("table").on('click','td', function( e ) {

    if ( !$(this).find('input').length ) { // If it is not already an input element
        var value = $(this).text();
        var fsize = $(this).css('font-size');
        var input = $("<input type='number' style=\"font-size:" + fsize + "\" \
                       onkeypress='return filterInputNumbers(event)' \
                       maxlength='1' min='1' max='9'/>");

        var twidth = $(this).width();
        var theight = $(this).height();

        $(this).empty().append( input );

        input.val(value);
        input.width(twidth);
        input.height(theight);
        input.focus();
    }
});


function getSudokuValues() {
    var table = document.getElementById("sudoku-table");
    var nrows = table.rows.length;
    var values = Array(9);  // easy to JSON.stringify later

    assert(nrows == 9, "Sudoku grid must have 9 rows");

    for (var r = 0; r < nrows; ++r) {
        var cells = table.rows[r].cells;
        var ncols = cells.length;
        values[r] = Array(9);  // easy to JSON.stringify later

        assert(ncols == 9, "Sudoku grid must have 9 columns");

        for (var c = 0; c < ncols; ++c) {
            values[r][c] = cells[c].innerHTML;
        }
    }

    return values;
}

function setSudokuValues(values) {
    assert(values.length == 9, "setSudokuValues(): wrong number of rows");

    var table = document.getElementById("sudoku-table");

    for (var i = 0; i < values.length; ++i) {
        var cells = table.rows[i].cells;
        var row = values[i];

        assert(row.length == 9, "setSudokuValues(), row " + 
                                (i + 1) + ": wrong number of columns");

        for (var j = 0; j < row.length; ++j) {
            cells[j].innerHTML = row[j];
        }
    }
}

function clearSudokuValues() {
    var table = document.getElementById("sudoku-table");
    
    for (var r = 0; r < table.rows.length; ++r) {
        var cells = table.rows[r].cells;
        for (var c = 0; c < cells.length; ++c) {
            cells[c].innerHTML = "";
        }
    }
}
