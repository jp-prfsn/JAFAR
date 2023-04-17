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

    
    function onHTMLchange(){

        //
        $( ".sb-remove" ).remove();

        // get the HTML input
        var str = document.getElementById('original').value;

        // find classes in the STYLE section of the HTML
        codeDoctor(str);
        
        
        
        
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

    function codeDoctor(input) {

        // reset
        var totalUnusedClasses = 0;
        $('#unusedClasses').empty();

        // remember that this will encapsulate everything between the start of the first style block and the end of the last one
        // so if there's a style block below the body content, then the whole doc will be included.
        var onlyStyles = input.substring(
            input.indexOf("<style>") + 1, 
            input.lastIndexOf("</style>")
        );

        // attempt to get all classes in the style section
        let regex = /\..*\{/gi;
        let matches = [...onlyStyles.matchAll(regex)];

        console.log(`Found ${matches.length} potential classes in STYLE tag`);

        console.log(`File length is ${input.length} chars.`);

        for (m of matches) {

            var s = m[0];
            s = s.substring(1);
            s =  s.substring(0, s.length - 1);
            s = s.trim();
            s = s.replace(/ .*/,'');


            var re = new RegExp(s,"gi");
            
            let count = (input.match(re) || []).length;

            

            if(count < 2){
                //console.log(`${s} only occurs once... you can probably remove it.`);
                totalUnusedClasses++;
                $('#unusedClasses').append(`<li>${s}</li>`);
            }
            

        }
        // Report Back
        // Class Utility
        if( ((totalUnusedClasses/matches.length)*100) < 2 ){
            // very few unused classes
            document.getElementById("codeDoctorClassReport").textContent = 
            `Very few classes are unused in this document - nice.`;
        }else if( ((totalUnusedClasses/matches.length)*100) < 25 ){
            // some unused classes
            document.getElementById("codeDoctorClassReport").textContent = 
            `About ${((totalUnusedClasses/matches.length)*100).toPrecision(2)}% of the classes I found are seemingly unused.`;
        }else if( ((totalUnusedClasses/matches.length)*100) < 50 ){
            // some unused classes
            document.getElementById("codeDoctorClassReport").textContent = 
            `About ${((totalUnusedClasses/matches.length)*100).toPrecision(2)}% of the classes I found are seemingly unused. That's pretty high!`;
        }
        
        
        // File Size
        document.getElementById("codeDoctorSizeReport").textContent = 
        `This email looks to be about ${input.length / 1000}kB in size.`;

        if((input.length / 1000) >= 102){
            $("#codeDoctorSizeReport").append(" Gmail will probably try to clip this email. Try to reduce it's file size.");
        }else{
            $("#codeDoctorSizeReport").append(" This size shouldn't cause you any problems.");
        }
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