

<?php

	// get original input & create empty error log
	$copy = $_POST["original"];
	$errorLog = "";
	$imageArray = [];

	// which brand are we dealing with?
	// TODO: Detect brand from content
	//$detectedBrand = substr($campaignCode, 4, 3); 
	$brand = $_POST["clientSelect"];
	$numberOfSegments = $_POST["numberOfSegments"];



	function checkReplace($toFind, $replacement, $description) {
		global $copy;
		global $errorLog;
		if(stripos($copy, $toFind) === false){
			$errorLog .= "<hr>" . $description . " not found";
		}else{
			$copy = str_replace($toFind, $replacement, $copy);
			$errorLog .= "<hr>" . $description . " found";
		}
	}


	function findImageUrls(){
		global $imageArray;
		global $copy;
		if (preg_match_all('/https?:\/\/.*\.(?:png|jpg|gif)/', $copy, $match) > 0) {
			foreach ($match[0] as $img)
			{
				
				array_push($imageArray, $img);
			}
		}else{
			echo "No images";
		}
		foreach ($imageArray as $ur)
		{
			$headers=get_headers($ur);

			$size = preg_replace('/Content-Length: /', '', $headers[9]);
			$adjustedValueKB = intval($size) / 1000;
			$adjustedValueMB = intval($adjustedValueKB) / 1000;
			echo $ur . " <br> " . $adjustedValueKB . " KB | "  . $adjustedValueMB . " MB<br>";
			if($adjustedValueMB > 1){
				echo "THIS IS A BIG OLD IMAGE!";
			}
			echo "<hr>";
			
		}
	}
	




	function findOrphanedStyles(){


		$classes = array();
		$lastPos = 0;

		while (($lastPos = strpos($copy, $classPattern, $lastPos))!== false) {
			$classes[] = $lastPos;
			$lastPos = $lastPos + strlen($needle);
		}
	}




	// GET TITLE //
	if (preg_match('/\<title\>(.*?)\<\/title\>/', $copy, $match) == 1) {
	    $campaignCode = $match[1];
	}else{
		$errorLog .= "The title tag could not be found.<br>";
	}






	// load the rules file
	$url = 'rules.xml';
	$xml = simplexml_load_file($url);
	
	// for each client
	foreach ($xml->client as $c)
	{
		if($c["name"] == $brand || $c["name"] == "Generic"){
			// if brand matches (or is generic)
			
			foreach ($c->rule as $r)
			{
				$childarray = $r->children();
				//echo $childarray[1] . " " . $childarray[2] . " " . $childarray[0] . "<hr>";
				// for each rule, run checkreplace on it's children
				checkReplace((string)$childarray[1], (string)$childarray[2], $childarray[0]);
				
			}
		}
		 
	}

	

	if(strpos($brand, 'HSBC')){


		// is RM box ticked?
		$RM = (isset($_POST['RM'])) ? 1 : 0;

		// Update Title if it is ugly
		checkReplace($campaignCode, $brand, "Title Tag");

		// is Segments box ticked?
		$segmentsYN = (isset($_POST['segmentsYN'])) ? 1 : 0;

		$tracker_string = '';

		if( isset($_POST['newtrackingseg00'])){

			// fix this to accomodate any number
			for($i = 0; $i <= $numberOfSegments; $i++){
				$number = strval( $i );
				$newtrackingtag = 'newtrackingtag0' . $number;
				$newtrackingseg = 'newtrackingseg0' . $number;
				$tracker = 'tracker0' . $number;

				if( isset($_POST[$newtrackingseg]))
		    	{

		    		$this_tracking_tag = $_POST[$newtrackingtag];
		    		$this_tracking_seg = $_POST[$newtrackingseg];

		    		if(!empty($this_tracking_tag))
		    		{
		    			$tracker = "<% if(recipient.hsbcSegment == '" . $this_tracking_seg . "'){ var trackingtag = '" . $this_tracking_tag . "';}%>";
		    		}
		    		else
		    		{
		    			$tracker = '';
		    		}
		    	}
		    	else
		    	{
		    		$tracker  = '';
		    	}

				$tracker_string .= $tracker;
			}
		}

		// fix malformed URL params
		if(stripos($copy, "&?eid=") === false){
		}else{
			$copy = str_replace("&?eid=", "&eid=", $copy);
			$errorLog .= "<div style='padding:10px; border-bottom:1px solid white;'><strong>Malformed Query String (&?eid=)</strong> has been fixed.</div>";
		}

		// strange beasts
		if( isset($_POST["sb-num"])){
			$sbnum = $_POST["sb-num"];
			for ($x = 0; $x < $sbnum; $x++) {
				if( !empty($_POST["sb_" . $x . "_name"]) && !empty($_POST["sb_" . $x . "_value"]) ){
					$SBcheck = '%%' . $_POST["sb_" . $x . "_name"] . '%%';
					$SBreplace = '<%= ' . $_POST["sb_" . $x . "_value"] . ' %>';
					checkReplace($SBcheck, $SBreplace, '<span style="background:#da7e7e; padding:5px; color:black; margin-right:10px;">[STRANGE BEAST]</span> ' . $SBcheck);
				}else{
					$SBcheck = '%%' . $_POST["sb_" . $x . "_name"] . '%%';
					$errorLog .= "<div style='padding:10px 30px; background: #d14343; border-bottom:1px solid white;'><span style='background:#da7e7e; padding:5px; color:black; margin-right:10px;'>[STRANGE BEAST]</span><strong>" . $SBcheck . "</strong> has not been replaced! Did you specify something to replace it with?<span style='float:right; font-weight:bold; color:white; font-size:25px; float:left; margin-left:-21px; line-height:18px;'>!</span></div>";
				}
			} 
		}


		
		



		if($segmentsYN == '1'){

			// get title and transform to tracking code (by removing the middle)
			$campaignCA = explode("_",$campaignCode);
			$campaignCode = $campaignCA[0] . "_" . $campaignCA[1] . "_" . $campaignCA[2] . "_" . $campaignCA[7] . "_" . $campaignCA[8];


			// replace static tracking tag with dynamic tracking tag
			$copy = str_replace($campaignCode, "<%= trackingtag %>", $copy);

			// fix potential tracking tag errors
			$copy = str_replace("%>eid=", "%>&eid=", $copy);
			$copy = str_replace("%>?eid=", "%>&eid=", $copy);
			$copy = str_replace("%>&?eid=", "%>&eid=", $copy);

			//insert tracking headers
			//$trackerString = '';

			//foreach ($trackerarray as $value) {

			   //$trackerString = $trackerString . $value;

			//}

			//echo $trackerString . "<hr>";





			$bodytag = '<body style="margin: 0; padding: 0; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">';
			checkReplace($bodytag, $bodytag . $tracker_string, "Tracker script");

		}

		// footer links breaking
		$copy = str_replace('<span class="footer__bottom-links-text" style="color: #333; text-decoration: underline; font-weight: 400;">About us</span>', '<span class="footer__bottom-links-text" style="color: #333; text-decoration: underline; font-weight: 400;">About&nbsp;us</span>', $copy);
		$copy = str_replace('<span class="footer__bottom-links-text" style="color: #333; text-decoration: underline; font-weight: 400;">Contact us</span>', '<span class="footer__bottom-links-text" style="color: #333; text-decoration: underline; font-weight: 400;">Contact&nbsp;us</span>', $copy);
		$copy = str_replace('<span class="footer__bottom-links-text" style="color: #333; text-decoration: underline; font-weight: 400;">Find a branch</span>', '<span class="footer__bottom-links-text" style="color: #333; text-decoration: underline; font-weight: 400;">Find&nbsp;a&nbsp;branch</span>', $copy);
		$copy = str_replace('<span class="footer__bottom-links-text" style="color: #333; text-decoration: underline; font-weight: 400;">Update contact preferences</span>', '<span class="footer__bottom-links-text" style="color: #333; text-decoration: underline; font-weight: 400;">Update&nbsp;contact&nbsp;preferences</span>', $copy);
		$copy = str_replace('<span class="footer__bottom-links-text" style="color: #333; text-decoration: underline; font-weight: 400;">Security centre</span>', '<span class="footer__bottom-links-text" style="color: #333; text-decoration: underline; font-weight: 400;">Security&nbsp;centre</span>', $copy);


		// Insert HK Dynamic Code - Contact Us
		$copy = str_replace('<a title="Contact us Opens in new window" href="https://www.expat.hsbc.com/contact/" style="color: #333; text-decoration: underline; font-weight: 400; font-size: 12px; display: inline-block; margin: 0 2px 10px 6px;">Contact us</a>', '<%if(hsbcResidency!="HONG KONG" && hsbcResidency!="HK"){%><a href="https://www.expat.hsbc.com/contact/" style="color: #333; text-decoration: underline; font-weight: 400; font-size: 12px; display: inline-block; margin: 0 2px 10px 6px;">Contact us</a><%}%>', $copy);

		// Insert HK Dynamic Code - Security Center
		$copy = str_replace('<a title="Security centre Opens in new window" href="https://www.expat.hsbc.com/help/security/" style="color: #333; text-decoration: underline; font-weight: 400; font-size: 12px; display: inline-block; margin: 0 2px 10px 6px;">Security centre</a>', '<%if(hsbcResidency!="HONG KONG" && hsbcResidency!="HK"){%><a href="https://www.expat.hsbc.com/help/security/" style="color: #333; text-decoration: underline; font-weight: 400; font-size: 12px; display: inline-block; margin: 0 2px 10px 6px;">Security centre</a><%}%>', $copy);

		// Insert HK Dynamic Code - Security Center 2
		$copy = str_replace('<a href="https://www.expat.hsbc.com/help/security/" style="color: #333; text-decoration: underline;">Security centre</a>.', '<% if ((recipient.hsbcResidency != "HONG KONG") && (recipient.hsbcResidency != "HK") && (recipient.hsbcResidency)) { %><a href="https://www.expat.hsbc.com/help/security/" style="color: #333; text-decoration: underline;">Security Centre</a>.<%}else{%>Security Centre at w&#8205;w<span>w.&#8205;e</span>xp&#8205;a<span>t.&#8205;h</span>sb<span>c.&#8205;c</span>om/help/security/<%}%>', $copy);



	}


	// -------------------------------- CUSTOM RULES --------------------------- //
	if( isset($_POST["cstm-num"])){
		$cstmnum = $_POST["cstm-num"];
		for ($x = 0; $x < $cstmnum; $x++) {
			if( !empty($_POST["custom_replace_" . $x]) && !empty($_POST["custom_with_" . $x]) ){
				$cstmreplace = $_POST["custom_replace_" . $x];
				$cstmwith = $_POST["custom_with_" . $x];
				checkReplace($cstmreplace, $cstmwith, '<span style="background:#f7bc2f; padding:5px; color:black; margin-right:10px;">[CUSTOM]</span> ' . $cstmreplace);
			}
		}
	}
?>


<!-- build results page -->
<textarea placeholder="<?php echo trim( $copy ) ?>"></textarea>
<hr>
<div><?php echo $errorLog ?></div>
<hr>
<div><?php echo findImageUrls() ?></div>
<hr>
<!-- build results page -->