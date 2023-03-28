var max_segments = 4;

$( "input[name='segmentsYN']" ).click(function() {
    if ($('input.segmentsYN').is(':checked')) {
        $( "#tracking" ).css( "display", "block" );
    }
    else
    {
        $( "#tracking" ).css( "display", "none" );
    }
});

$( "input[name='numberOfSegments']" ).val(max_segments);

// add a segment
$( "#addSegment" ).click(function() {

    max_segments ++;

    $( "input[name='numberOfSegments']" ).val(max_segments);

    $( "#tracking-tag-table" ).append(
    '<tr><td><input type="text" name="newtrackingseg0' + max_segments + '" placeholder="Segment Number (e.g. &apos;' + max_segments + '&apos;)"></td><td><input type="text" name="newtrackingtag0' + max_segments + '" placeholder="Tracking Code (e.g. &apos;EML_EXP_EN_29558_' + max_segments + ')"></td></tr>'
    );
});


//set select to detected client
function checkClient(){

    let codebox = $( "#original" ).val();

    var codeArr = [
        ['EXP_', 'EXP'],
        ['BMU_', 'BMU'],
        ['MLT_', 'MLT'],
        ['CI_', 'CI'],
        ['TradePoint', 'TP']
    ];
    
    for(let i = 0; i < codeArr.length; i++){
        if(codebox.search(codeArr[i][0]) != -1)
        {
            document.getElementById('clientSelect').value = codeArr[i][1];
            revealOptions();
        }
    }
}


// show / hide options //
$( "#clientSelect" ).change(function revealOptions() {

    var client = $( "#clientSelect" ).val();

    var fullClientName = client + "-options";
    
    var element = document.getElementById(fullClientName);
    var addLine = document.getElementById(addLine);

    $('.options').hide(); // hides
    $(element).show(); // Shows
    $(addLine).show(); // Shows

});


var sb_num = 0;

function findUnusualTags(){

    $( ".sb-remove" ).remove();

    var str = document.getElementById('original').value;
    var newCodes = [];
    var dupe = false;

    var regex = /%%/gi, result, indices = [];
    while ( (result = regex.exec(str)) ) {
        indices.push(result.index);

    }
    

    for(let i=0; i < indices.length; i+=2){

        newCode = str.substring(indices[i]+2, indices[i+1]);

        for(let j=0; j < newCodes.length; j++){
            
            if(newCode == newCodes[j]){
                // duplicate found
                dupe = true;
                break;
            }
            else{
                dupe = false;
            }
        } 




        if(
            newCode.toLowerCase() == '=v(@vawpurl)=' || 
            newCode.toLowerCase() == 'indv_titl_txt' || 
            newCode.toLowerCase() == 'indv_last_name' ||  
            newCode.toLowerCase() == '=format(now(), "yyyy")=' ||
            newCode.toLowerCase() == 'rm_phone' || 
            newCode.toLowerCase() == 'rm_email' || 
            newCode.toLowerCase() == 'rm_name' ){
         
            // recognised tags - not added   
        
        }else if(dupe){

            // duplicated tags - not added  

        }else{
            console.log(newCode);
            newCodes.push(newCode);
        }
    }

    for(let i=0; i < newCodes.length; i++){
        $( "#strangeBeasts" ).append( '<div class="sb-remove" style="padding-bottom:10px;"><span class="tag-detected">' + newCodes[i] + '</span>: <input type="hidden" name="sb_' + i + '_name" value="' + newCodes[i] + '"><input type="text" name="sb_' + i + '_value" placeholder="recipient.' +newCodes[i]+ '"><br></div>');
        sb_num ++;
        document.getElementById('sb-num').value = sb_num;
        if(newCodes.length == 0){ $( "#sb-section" ).hide(); }else{ $( "#sb-section" ).show(); }
    }
}


var cstm_num = 0;

function addAnother(){
    $( "#customReplaceQueries" ).append( '<div style="margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid white;">replace: <input type="text" style="margin-bottom:10px;" name="custom_replace_' + cstm_num + '" placeholder="this"><br>with: <input type="text" name="custom_with_' + cstm_num + '" placeholder="that"></div>');

    cstm_num ++;
    $('#cstm-num').val(cstm_num);
}


$( "input[name='RM']" ).click(function() {
    if ($('input.RM').is(':checked')) {
        $( "#tracking" ).css( "display", "block" );
    }
    else
    {
        $( "#tracking" ).css( "display", "none" );
    }
});