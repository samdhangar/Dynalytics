$(function () {
    // run the currently selected effect
    function runEffect() {
        // get effect type from
        var selectedEffect = $("#effectTypes").val();
        // most effect types need no options passed by default
        var options = {};
        // some effects have required parameters
        if (selectedEffect === "scale") {
            options = {percent: 0};
        } else if (selectedEffect === "size") {
            options = {to: {width: 200, height: 60}};
        }
        // run the effect
        $("#effect").toggle(selectedEffect, options, 500);
    }
    ;
    // set effect from select menu value
    $("#button").click(function () {
        runEffect();
    });
    $('#demo5').scrollbox({
        direction: 'h',
        distance: 134
    });
    $('#demo5-backward').click(function () {
        $('#demo5').trigger('backward');
    });
    $('#demo5-forward').click(function () {
        $('#demo5').trigger('forward');
    });

});
