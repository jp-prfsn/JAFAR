    // Global Variables
    var max_segments = 4;
    var strangeBeasts_amount = 0;
    var page = 1;
    var maxPage;
    var xpos = 0;
    var pcComplete;
    var customRulesAmount = 0;
    let darkModeOn = false;

    // resize for amount of panels
    function resizePanels(){
        maxPage = $('.panel:visible').length;
        $(".panel").css("width", 100/(maxPage) + "%");
        $("#scroller").css("width", 100 * (maxPage) + "%");
    }
    
    
    // toggle prev/next
    function hideImpossibleOptions(){
        if(page == 1){
            $(".backButton").css("display", "none");
        }
        else if(page == maxPage){
            $(".nextButton").css("display", "none");
        }
        else{
            $(".backButton").css("display", "inline-block");
            $(".nextButton").css("display", "inline-block");
        }
    }
    
    // move to next page functionality
    function pageTurn(dir){
        if(dir == "next" && page < maxPage){
            // next page
            xpos -= 100;
            page ++;
        }else if(dir == "back" && page > 1){
            // prev page
            xpos += 100;
            page --;
        }
        $("#scroller").css("left", xpos + "%");
        //console.log(page + "/" + maxPage);

        pcComplete = (page / maxPage)*100;
 
        $("#pb-inner").css("width", pcComplete + "%");
        hideImpossibleOptions();
    }

    // Toggle segments (anonymous function)
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
        `<tr>
            <td>
                <input type="text" name="newtrackingseg0${max_segments}" 
                placeholder="Segment Number (e.g. &apos;${max_segments}&apos;)">
            </td>
            <td>
                <input type="text" name="newtrackingtag0${max_segments}" 
                placeholder="Tracking Code (e.g. &apos;EML_EXP_EN_29558_${max_segments})">
            </td>
        </tr>`
        );
    });


    //set select to detected client
    function checkClient(){

        let codebox = $( "#original" ).val();
        var codeArr = [
            ['EXP_', 'HSBC Expat'],
            ['BMU_', 'HSBC Bermuda'],
            ['MLT_', 'HSBC Malta'],
            ['CI_', 'HSBC CIIOM'],
            ['TradePoint', 'Tradepoint']
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
    $( "#clientSelect" ).change(
    function revealOptions() {
        console.log("Client Changed");
        
        var client = $( "#clientSelect" ).val();
        client = client.replace(/\s/g, '');

        var fullClientName = client + "-options";
        console.log(fullClientName);
        
        var element = document.getElementById(fullClientName);
        //var addLine = document.getElementById(addLine);

        $('.options').hide(); // hides
        $(element).show(); // Shows
        //$(addLine).show(); // Shows
    });

    
    function findUnusualTags(){

        //
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
            $( "#strangeBeasts" ).append( 
                `<div class="sb-remove">
                    <span class="tag-detected">${newCodes[i]}</span>: 
                    <input type="hidden" name="sb_${i}_name" value="${newCodes[i]}">
                    <input type="text" name="sb_${i}_value" placeholder="recipient.${newCodes[i]}">
                    <br>
                </div>`
            );
            strangeBeasts_amount ++;
            document.getElementById('sb-num').value = strangeBeasts_amount;
            if(newCodes.length == 0){
                $( "#sb-section" ).hide();
                resizePanels();
            }else{
                $( "#sb-section" ).show();
                resizePanels();
            }
        }
    }



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

    // Toggle styling setting
    $( "#modetoggle" ).click(function() {
        $( "body" ).toggleClass("darkmode");
        if($("#j_logo").attr("src") == "images/JAFAR-logotype.png"){
            $("#j_logo").attr("src", "images/JAFAR-logotype-rev.png");
        }else{
            $("#j_logo").attr("src", "images/JAFAR-logotype.png");
        }
        
    });


    // run setup functions
    resizePanels();
    pageTurn("none");