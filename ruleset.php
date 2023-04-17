<!DOCTYPE html>
<html>
    <head>
        <title>JAFAR</title>
        <meta charset="UTF-8">
        <link href="styles/styles.css" rel="stylesheet">
        <link rel="shortcut icon" type="image/jpg" href="images/JAFAR-favicon.ico"/>
        <link rel="icon" type="image/png" href="images/JAFAR-favicon.png"/>
		<!--<link rel="icon" type="image/png" href="https://example.com/images/JAFAR-favicon.png"/>-->

        <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
    </head>
    <body>
		

		<?php include 'header.php';?>
    	

    	<div id="main" align="center">
    		<div class="container">
    			
	    			<div class="grid">
		    			<div class="col-1">
		    				<h2>About JAFAR rulesets</h2>

	    					<p style="font-size: 16px; margin-top:0px; margin-bottom: 30PX;">
								<STRONG>JAFAR</STRONG> uses prewritten rules in order to find and replace items in your template. <br>
								This is a list of every snippet that JAFAR will look for, ordered by client.
							</p>

	    					
	    					
		    			</div>
		    			<div class="col-2" style="">

		    				<div style="padding-bottom: 40px; padding-right: 30px;">
		    					<table class="rules-table" border="1" cellpadding="5" cellspacing="0">
			    					<tr>
			    						<th width="20%">Description
			    						</th>
			    						<th width="40%">Find
			    						</th>
			    						<th width="40%">Replace with:
			    						</th>
			    					</tr>
									


									<?php
										// load the file
										$url = 'rules.xml';
										$xml = simplexml_load_file($url);

										// for each client
										foreach ($xml->client as $c)
										{
											echo "<tr><th colspan='3' align='left' class='thRow'>" . $c['name'] . "</th></tr>";
											// for each rule
											foreach ($c->rule as $r)
											{
												echo "<tr>";
												// for each node
												foreach ($r->children() as $node)
												{
													echo "<td><code>" . htmlspecialchars($node) . "</code></td>";
												}
												echo "</tr>";
											}
										}
									?>
			    				</table>


			    				
			    				
	    					</div>

		    			</div>
		    		</div>
		    	
    		</div>
    	</div>
    	<div id="footer">
    	<span>JAFAR</span>
    	<span>PROFUSION</span>
    	<span>2021</span>
    	</div>
    </body>


</html>