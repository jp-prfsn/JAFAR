// Add another custom rule
function addAnother(){
    $( "#customReplaceQueries" ).append(
        `<div class="customReplaceLine">
            <div>
                replace: 
                <input type="text" name="custom_replace_${customRulesAmount}" placeholder="this">
            </div>
            <div>
                with: 
                <input type="text" name="custom_with_${customRulesAmount}" placeholder="that">
            </div>
            <span class="delete" onclick="removeCustomRule(this)">
                <img src="images/del.png">
            </span>
        </div>`);

    customRulesAmount ++;
    $('#cstm-num').val(customRulesAmount);
}

// delete a custom rule
function removeCustomRule(inst) {

    $(inst).closest('.customReplaceLine').remove();

    customRulesAmount --;
    $('#cstm-num').val(customRulesAmount);

    customs = $('.customReplaceLine');

    // rename in order
    customs.each(function(index){
        $($(this).children('div')[0]).children('input').attr("name","custom_replace_" + index);
        $($(this).children('div')[1]).children('input').attr("name","custom_with_" + index);
    });
}





// Toggle tracking tag inputs
$( "input[name='RM']" ).click(function() {
    if ($('input.RM').is(':checked')) {
        $( "#tracking" ).css( "display", "block" );
    }else{
        $( "#tracking" ).css( "display", "none" );
    }
});