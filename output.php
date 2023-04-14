<?php

	// get original input
	$copy = $_POST["original"];
	$errorLog = "";

	// which brand are we dealing with?
	//$detectedBrand = substr($campaignCode, 4, 3); 

	$brand = $_POST["clientSelect"];
	$numberOfSegments = $_POST["numberOfSegments"];

	function writeLog($data) {
		$output = $data;
		echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
	}

	function checkReplace($toFind, $replacement, $description) {
		global $copy;
		global $errorLog;
		if(stripos($copy, $toFind) === false){
			errorLog .= $toFind . "not found";
		}else{
			$copy = str_replace($toFind, $replacement, $copy);
			errorLog .= $toFind . "found and replaced";
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

		


		
		if($brand == 'HSBC Expat'){
			// EXPAT RULES


			$copy = str_replace($campaignCode, "HSBC Expat", $copy);

			
		}else if($brand == 'MLT'){

			// MALTA RULES

			checkReplace($campaignCode, "HSBC Malta", "Campaign Code");
			
			
		}else if($brand == 'BMU' || $brand == 'UK_'){

			// BERMUDA RULES

			checkReplace($campaignCode, "HSBC Bermuda", "Title Tag");

			// replace webversion
			checkReplace("%%=v(@vawpURL)=%%", "<%@ include view='Bermuda_Mirror' %>", "Mirror Link URL");

			// replace salutation
			checkReplace("%%Indv_Titl_Txt%% %%Indv_Last_Name%%", "<%= recipient.CustomerName %>", "Salutation");
			// add KD tag
			checkReplace("</body>", "<%@ include view='KickDynamicTag' %></body>", "KD Tracking");

			// replace pref link
			checkReplace("https://email.marketing.hsbc.bm/nms/jsp/webForm.jsp?fo=bermudaUnsubscribe&id=%40rh35qpBpkp7VpjekOBD81g%3D%3D", "<%@ include view='Bermuda_Unsubscribe' %>", "Unsubscribe Link");
		}
		else if( $brand == 'CI'){
			// CIIOM RULES

			// replace title tag
			checkReplace($campaignCode, "HSBC CIIOM", "Campaign Code");

			// replace webversion
			checkReplace("%%=v(@vawpURL)=%%", "<%@ include view='HSBC_CIIOM_Mirror' %>", "Mirror Link URL");

			// replace salutation
			checkReplace("%%indv_titl_txt%% %%indv_last_name%%", "<%= recipient.salutation %> <%= recipient.lastName %>", "Salutation");

			// replace pref link
			checkReplace("%%=v(@unsubURL)=%%", "<%@ include view='HSBC_CIIOM_Unsubscribe' %>", "Unsubscribe Link");

			// replace year
			checkReplace('%%=Format(Now(), "yyyy")=%%', "<%= formatDate(new Date(), '%4Y') %> ", "Current Year");

			// replace tracking
			checkReplace("</body>", '<%@ include view="HSBC_CIIOM_Tracking_Tag" %><div class="h" style="white-space:nowrap;font:20px courier;color:#ffffff;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</div></body>', "Tracking");	

		}

		
		// GENERIC hsbc RULES
		

		// add date code
		//$copy = str_replace("2021", "<%= formatDate(new Date(), '%4Y') %>", $copy);

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


		
		

		

		

		// fix malformed URL params
		if(stripos($copy, "&?eid=") === false){

		}else{
			$copy = str_replace("&?eid=", "&eid=", $copy);
			$errorLog .= "<div style='padding:10px; border-bottom:1px solid white;'><strong>Malformed Query String (&?eid=)</strong> has been fixed.</div>";
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

		
		
		



		// replace footer phone number 313
		$copy = str_replace(" +44&nbsp;1534&nbsp;616313", " <a href='tel:+441534616313' style='color: inherit; text-decoration:none;'>+44&nbsp;1534&nbsp;616313</a>", $copy);

		// replace footer phone number 212
		$copy = str_replace(" +44&nbsp;1534&nbsp;616212", " <a href='tel:+441534616212' style='color: inherit; text-decoration:none;'>+44&nbsp;1534&nbsp;616212</a>", $copy);

		

		// replace preheader if it is garbage
		$copy = str_replace('<span style="display: none; line-height: 0; max-height: 0; font-size: 0; overflow: hidden; mso-hide: all;">HSBC Expat Logo</span>', '<span style="display: none; line-height: 0; max-height: 0; font-size: 0; overflow: hidden; mso-hide: all;">HSBC Expat</span>', $copy);

		// Insert HK Dynamic Code - Contact Us
		$copy = str_replace('<a title="Contact us Opens in new window" href="https://www.expat.hsbc.com/contact/" style="color: #333; text-decoration: underline; font-weight: 400; font-size: 12px; display: inline-block; margin: 0 2px 10px 6px;">Contact us</a>', '<%if(hsbcResidency!="HONG KONG" && hsbcResidency!="HK"){%><a href="https://www.expat.hsbc.com/contact/" style="color: #333; text-decoration: underline; font-weight: 400; font-size: 12px; display: inline-block; margin: 0 2px 10px 6px;">Contact us</a><%}%>', $copy);

		// Insert HK Dynamic Code - Security Center
		$copy = str_replace('<a title="Security centre Opens in new window" href="https://www.expat.hsbc.com/help/security/" style="color: #333; text-decoration: underline; font-weight: 400; font-size: 12px; display: inline-block; margin: 0 2px 10px 6px;">Security centre</a>', '<%if(hsbcResidency!="HONG KONG" && hsbcResidency!="HK"){%><a href="https://www.expat.hsbc.com/help/security/" style="color: #333; text-decoration: underline; font-weight: 400; font-size: 12px; display: inline-block; margin: 0 2px 10px 6px;">Security centre</a><%}%>', $copy);

		// Insert HK Dynamic Code - Security Center 2
		$copy = str_replace('<a href="https://www.expat.hsbc.com/help/security/" style="color: #333; text-decoration: underline;">Security centre</a>.', '<% if ((recipient.hsbcResidency != "HONG KONG") && (recipient.hsbcResidency != "HK") && (recipient.hsbcResidency)) { %><a href="https://www.expat.hsbc.com/help/security/" style="color: #333; text-decoration: underline;">Security Centre</a>.<%}else{%>Security Centre at w&#8205;w<span>w.&#8205;e</span>xp&#8205;a<span>t.&#8205;h</span>sb<span>c.&#8205;c</span>om/help/security/<%}%>', $copy);

		// Footer - Terms Link
		$copy = str_replace('http://www.expat.hsbc.com/1/2/hsbc-expat/terms', '<% if ((recipient.hsbcResidency != "HONG KONG") && (recipient.hsbcResidency != "HK") && (recipient.hsbcResidency)) { %><a href="http://www.expat.hsbc.com/1/2/hsbc-expat/terms" style="color:#333; text-decoration:none;">http://www.expat.hsbc.com/1/2/hsbc-expat/terms</a><%}else{%>w&#8205;w<span>w.&#8205;e</span>xpa<span>t.&#8205;h</span>sb<span>c.&#8205;c</span>om/1/2/hsbc-expat/terms<%}%>', $copy);

		// RM Signoff
		if($RM == '1')
		{
			//checkReplace("Your HSBC Expat Banking Team", "<% if(recipient.rmName){%><%=recipient.rmName%><%}else{%>Your HSBC Expat Banking Team<%}%>", "RM Sign Off");
			//checkReplace("Your HSBC Expat Team", "<% if(recipient.rmName){%><%=recipient.rmName%><%}else{%>Your HSBC Expat Team<%}%>", "RM Sign Off");


			checkReplace("%%RM_NAME%%", "<%=recipient.rmName%>", "RM Name");
			checkReplace("%%RM_EMAIL%%", "<%=recipient.rmEmail%>", "RM Email");
			checkReplace("%%RM_PHONE%%", "<%=recipient.rmPhoneNumber%>", "RM Phone Number");
		}




	}else if($brand == 'TP'){
	// --------------------------- TRADEPOINT ---------------------------


		// PREHEADER

		checkReplace("Pre-header goes here", "<%= message.delivery.preHeader %>", "Preheader 1 (Hidden)");
		checkReplace("INSERT PRE-HEADER HERE", "<%= message.delivery.preHeader %>", "Preheader 2 (Visible)");



		// WEBVERSION
		checkReplace('<a href="####" target="_blank" data-ed-action="mirror link" style="color:#108096">View online</a>', '<a href="<%@ include view=\'tradepoint_mirror_link_only\' %>" target="_blank" data-ed-action="mirror link" style="color:#108096">View online</a>', "Mirror Link 1 (Header)");

		checkReplace('<a class="wr" href="#" style="font-family:\'Kingfisher\', Arial, Helvetica, sans-serif !important; color:#3c3c3c; text-decoration:underline;" target="_blank">View in browser</a>', '<a class="wr" href="<%@ include view=\'tradepoint_mirror_link_only\' %>" style="font-family:\'Kingfisher\', Arial, Helvetica, sans-serif !important; color:#3c3c3c; text-decoration:underline;" target="_blank">View in browser</a>', "Mirror Link 2 (Footer)");
		
	
	

		//LOYALTY BANNER
		$loyaltyBanner = "<%@ include view='Current_Loyalty_Tier_15' %>";
		$pos6 = stripos($copy, '<div class="pad5" align="center" style=""><div style="width:500px; border: 2px dashed black; padding:20px; margin: 10px;" align="center">CURRENT LOYALTY DISCOUNT BANNER</div></div>');
		$pos6a = stripos($copy, '<div class="pad5" align="center" style="z-index: 110;"><div style="width: 500px; border: 2px dashed black; padding: 20px; margin: 10px; z-index: 111;" align="center">CURRENT LOYALTY DISCOUNT BANNER</div></div>');
		$pos6b = stripos($copy, '<div class="pad5" align="center" style="z-index: 134;"><div style="width: 500px; border: 2px dashed black; padding: 20px; margin: 10px; z-index: 135;" align="center">CURRENT LOYALTY DISCOUNT BANNER</div></div>');


		
		if ($pos6 !== false ) {
	    	$copy = str_replace('<div class="pad5" align="center" style=""><div style="width:500px; border: 2px dashed black; padding:20px; margin: 10px;" align="center">CURRENT LOYALTY DISCOUNT BANNER</div></div>', $loyaltyBanner, $copy);
	    	$errorLog .= "<div style='padding:10px; border-bottom:1px solid white;'><strong>Loyalty Banner</strong> has been replaced!</div>";
		}elseif ($pos6a !== false) {
	    	$copy = str_replace('<div class="pad5" align="center" style="z-index: 110;"><div style="width: 500px; border: 2px dashed black; padding: 20px; margin: 10px; z-index: 111;" align="center">CURRENT LOYALTY DISCOUNT BANNER</div></div>', $loyaltyBanner, $copy);
	    	$errorLog .= "<div style='padding:10px; border-bottom:1px solid white;'><strong>Loyalty Banner</strong> has been replaced!</div>";

		}elseif ($pos6b !== false) {
	    	$copy = str_replace('<div class="pad5" align="center" style="z-index: 134;"><div style="width: 500px; border: 2px dashed black; padding: 20px; margin: 10px; z-index: 135;" align="center">CURRENT LOYALTY DISCOUNT BANNER</div></div>', $loyaltyBanner, $copy);
	    	$errorLog .= "<div style='padding:10px 30px; border-bottom:1px solid white;'><strong>Loyalty Banner</strong> has been replaced!</div>";

		}else{
			$errorLog .= "<div style='padding:10px 30px; background: #d14343; border-bottom:1px solid white;'><strong>Loyalty Banner</strong> has not been replaced!</div>";
		}

		//MEMBERSHIP
		checkReplace("0123456789", "<%= recipient.customerID %>", "Membership Number");

		
		//YEAR
		$copy = str_replace('B&amp;Q 2024', 'B&amp;Q <%= formatDate(new Date(), "%4Y") %>', $copy);
		$copy = str_replace('B&amp;Q 2021', 'B&amp;Q <%= formatDate(new Date(), "%4Y") %>', $copy);
		$copy = str_replace('B&amp;Q 2022', 'B&amp;Q <%= formatDate(new Date(), "%4Y") %>', $copy);
		$copy = str_replace('B&amp;Q 2023', 'B&amp;Q <%= formatDate(new Date(), "%4Y") %>', $copy);

		//UNSUB
		checkReplace('<a href="#" style="color:#ffffff; text-decoration:underline;" target="_blank">Unsubscribe</a>', '<a href="<%@ include view=\'tp_unsubscribe_link\' %>" style="color:#ffffff; text-decoration:underline;" target="_blank">Unsubscribe</a>', "Unsubscribe Link");

		
		// FOOTER STUFF (GMAIL APP FIX AND TRACKING)
		checkReplace("</body>", '<div class="gmail-app-fix" style="background-color: #ffffff; color: #ffffff; font-family: monospace; font-size: 15px; line-height: 0;">
	&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div><%@ include view="TradePointTracking" %><%@ include view="MEC_Tags" %></body>', "Gmail App Fix & Tracking");


		// --------------------------- END TRADEPOINT --------------------------- //





	}else if($brand == 'SFXIE'){
	// --------------------------- SCREWFIX IRELAND --------------------------- //


		checkReplace("<%@ include option='NmsTracking_OpenFormulaTop' %>", '', "Adobe Include");

		checkReplace('<th align="left" class="nav-item" style="font-family: sf, sans-serif; font-size: 13px; padding: 10px 20px;"><a href="https://www.screwfix.com/c/outdoor-gardening/cat840458?utm_source=email&utm_medium=email&utm_campaign=<%= bauContentSheetObject.variationRef %>&utm_content=navigation&utm_term=outdoor" style="color:#FFFFFE; text-decoration: none;"><strong style="font-weight: 700;">Outdoor &amp; <br class="h">Gardening</strong></a></th>', '<th align="left" class="nav-item" style="font-family: sf, sans-serif; font-size: 13px; padding: 10px 20px;"><a href="https://www.screwfix.ie/c/electrical-lighting/lighting/cat840782?utm_source=email&utm_medium=email&utm_campaign=<%= bauContentSheetObject.variationRef %>&utm_content=navigation&utm_term=lighting" style="color:#FFFFFE; text-decoration: none;"><strong style="font-weight: 700;">Lighting</strong></a></th>', "Nav Bar (Lighting/Outdoors");

		checkReplace("<%@ include view='MirrorPageUrl' %>", '{?$view_online?}', "Webversion Link");

		checkReplace("<%@ include view='SDL_unsubscribe_link_only' %>", '{?$optout_link?}', "Unsubscribe Link");

		checkReplace('You have registered to receive these offers with the following email address: <a href="mailto:<%= recipient.email %>" style="color: #ffffff; text-decoration: underline;" target="_blank"><%= recipient.email %></a>', 'You have registered to receive offers from Screwfix Ireland with the following email address: <a href="mailto:({Attribute;3:IE_email_address})" title="" style="color: #ffffff; text-decoration: underline;" target="_blank">({Attribute;3:IE_email_address})</a>.', "You have registered...");

		checkReplace('<img src="http://assets.fwdto.net/sf/<% if ( bauContentSheetObject.brand == \'ELECTRICFIX\' ) { %>e<% } else if ( bauContentSheetObject.brand == \'PLUMBFIX\' ) { %>p<% } else { %>s<% } %>fx-champions-logo.png" style="display: block; border: 0;" width="320" height="131" alt="<%= recipient.brandCode %>">', '<img src="http://assets.fwdto.net/screwfix/images/sfx-champions-logo.png" style="display: block; border: 0;" width="320" height="131" alt="Screwfix">', "Logo Image");

		checkReplace('https://www.screwfix.com', 'https://www.screwfix.ie', ".COM Domain");

		checkReplace('<td align="center" valign="top" width="107" class="footerIcon"><a href="https://www.screwfix.com/help/sprint/?utm_source=emailutm_medium=emailutm_campaign=<%= message.delivery.label %>&utm_content=footer&utm_term=Sprint&utm_block=footer" title="SCREWFIX SPRINT" target="_blank" _label="footer - screwfix sprint"><img src="https://assets.fwdto.net/sf/footer_sfx-sprint.png" style="display: block; border: 0; width: 100%; max-width: 107px; height: auto;" width="107" alt="SCREWFIX SPRINT" class="footerIcon" /></a></td>', '', "Sprint Logo");

		



		checkReplace('<table width="336" border="0" cellspacing="0" cellpadding="0" style="width: 336px;" class="w320"><tr><td align="center" width="60" valign="top"><a href="https://twitter.com/screwfix" title="Twitter" target="_blank" style="display: inline-block;" _label="footer - Twitter"><img src="https://assets.fwdto.net/sf/Image_754.png" style="display: block; border: 0; width: 60px; max-width: 60px;" width="60" alt="Twitter" class="w60" /></a></td><td width="32" style="width: 32px;">&nbsp;</td><td align="center" width="60" valign="top"><a href="https://www.facebook.com/Screwfix" title="Facebook" target="_blank" style="display: inline-block;" _label="footer - Facebook"><img src="https://assets.fwdto.net/sf/Image_755.png" style="display: block; border: 0; width: 60px max-width: 60px;" width="60" alt="Facebook" class="w60"/></a></td><td width="32" style="width: 32px;">&nbsp;</td><td align="center" width="60" valign="top"><a href="https://www.youtube.com/user/Screwfix" title="YouTube" target="_blank" style="display: inline-block;" _label="footer - YouTube"><img src="https://assets.fwdto.net/sf/Image_756.png" style="display: block; border: 0; width: 60px; max-width: 60px;" width="60" alt="YouTube" class="w60"/></a></td><td width="32" style="width: 32px;">&nbsp;</td><td align="center" width="60" valign="top"><a href="https://community.screwfix.com/?utm_source=email" title="Forum" target="_blank" style="display: inline-block;" _label="footer - Forum"><img src="https://assets.fwdto.net/sf/Image_757.png" style="display: block; border: 0; width: 60px; max-width: 60px;" width="60" alt="Forum" class="w60"/></a></td><td width="32" style="width: 32px;">&nbsp;</td><td align="center" width="60" valign="top"><a href="https://www.instagram.com/accounts/login/" title="Instagram" target="_blank" style="display: inline-block;" _label="footer - Instagram"><img src="https://assets.fwdto.net/sf/Image_758.png" style="display: block; border: 0; width: 60px; max-width: 60px;" width="60" alt="Instagram" class="w60"/></a></td></tr></table>', '<table width="270" border="0" cellspacing="0" cellpadding="0" style="width: 270px;" class="w320"><tr><td align="center" width="60" valign="top"><a href="https://twitter.com/ScrewfixIreland" title="Twitter" target="_blank" style="display: inline-block;" _label="footer - Twitter"><img src="https://assets.fwdto.net/MandS/202112_Abandoned_Basket/Image_754.png" style="display: block; border: 0; width: 60px; max-width: 60px;" width="60" alt="Twitter" class="w60" /></a></td><td width="10" style="width: 10px;">&nbsp;</td><td align="center" width="60" valign="top"><a href="https://www.facebook.com/ScrewfixIreland/" title="Facebook" target="_blank" style="display: inline-block;" _label="footer - Facebook"><img src="https://assets.fwdto.net/MandS/202112_Abandoned_Basket/Image_755.png" style="display: block; border: 0; width: 60px max-width: 60px;" width="60" alt="Facebook" class="w60"/></a></td><td width="10" style="width: 10px;">&nbsp;</td><td align="center" width="60" valign="top"><a href="https://www.youtube.com/user/Screwfix" title="YouTube" target="_blank" style="display: inline-block;" _label="footer - YouTube"><img src="https://assets.fwdto.net/MandS/202112_Abandoned_Basket/Image_756.png" style="display: block; border: 0; width: 60px; max-width: 60px;" width="60" alt="YouTube" class="w60"/></a></td><td width="10" style="width: 10px;">&nbsp;</td><td align="center" width="60" valign="top"><a href="https://www.instagram.com/accounts/login/?next=/screwfix_ireland/" title="Instagram" target="_blank" style="display: inline-block;" _label="footer - Instagram"><img src="https://assets.fwdto.net/MandS/202112_Abandoned_Basket/Image_758.png" style="display: block; border: 0; width: 60px; max-width: 60px;" width="60" alt="Instagram" class="w60"/></a></td></tr></table>', "Socials Footer");

		checkReplace('https://twitter.com/screwfix', 'https://twitter.com/ScrewfixIreland', "Twitter Link");

		checkReplace('https://www.facebook.com/Screwfix', 'https://www.facebook.com/ScrewfixIreland', "Facebook Link");

		checkReplace('<td width="32" style="width: 32px;">&nbsp;</td><td align="center" width="60" valign="top"><a href="https://community.screwfix.com/?utm_source=email" title="Forum" target="_blank" style="display: inline-block;" _label="footer - Forum"><img src="https://assets.fwdto.net/sf/Image_757.png" style="display: block; border: 0; width: 60px; max-width: 60px;" width="60" alt="Forum" class="w60"/></a></td>', '', "Remove Forum Logo");








	}else{


	}

	// replace copy symbol
	//checkReplace("©", "&copy;", "Copyright Symbol");


	// CUSTOM RULES
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



<?php echo trim( $copy ) ?>