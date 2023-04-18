// get image sizes
function httpGet(theUrl)
{
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", theUrl, false ); // false for synchronous request
    xmlHttp.send( null );
    return xmlHttp.responseText;
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
    if(matches.length > 0){
        if( ((totalUnusedClasses/matches.length)*100) < 2 ){
            // very few unused classes
            document.getElementById("codeDoctorClassReport").textContent = 
            `Very few classes are unused in this document - nice.`;
        }else if( ((totalUnusedClasses/matches.length)*100) < 25 ){
            // some unused classes
            document.getElementById("codeDoctorClassReport").textContent = 
            `About ${((totalUnusedClasses/matches.length)*100).toPrecision(2)}% of the classes I found are seemingly unused.`;
        }else{
            // some unused classes
            document.getElementById("codeDoctorClassReport").textContent = 
            `About ${((totalUnusedClasses/matches.length)*100).toPrecision(2)}% of the classes I found are seemingly unused. That's pretty high!`;
        }
    }else{
        // some unused classes
        document.getElementById("codeDoctorClassReport").textContent = 
        `Couldn't find any classes in this code...`;
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
