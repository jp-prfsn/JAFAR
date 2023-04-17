<!DOCTYPE html>
<html>
    <head>
        <title>JAFAR</title>
        <meta charset="UTF-8">
        <link href="styles/styles.css" rel="stylesheet">
        <link rel="shortcut icon" type="image/jpg" href="images/JAFAR-favicon.ico"/>
        <link rel="icon" type="image/png" href="images/JAFAR-favicon.png"/>
        <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
    </head>
    <body>
        

        <?php include 'header.php';?>

        <form action="output.php" method="post" target="_blank">
            
            <div id="panelholder">
                <span class="backButton" onclick="pageTurn('back')">BACK</span>
                <span class="nextButton" onclick="pageTurn('next')">NEXT</span>
                <div id="progressbar" align="left">
                    <div id="pb-inner"></div>
                </div>
                <div id="scroller">

                    <div class="panel">

                        <h3>1. Paste Original HTML</h3>
                        <label>Source Code:</label>
                        <textarea id="original" name="original" onchange="onHTMLchange()" placeholder="Go to 'view source' of the original template and paste that content here"></textarea>
                        <select id="clientSelect" name="clientSelect">
                            <option value="General">Which client?</option>
                            <option value="HSBC Expat">HSBC Expat</option>
                            <option value="HSBC Bermuda">HSBC Bermuda</option>
                            <option value="HSBC Malta">HSBC Malta</option>
                            <option value="HSBC CIIOM">HSBC CIIOM</option>
                            <option value="Tradepoint">Tradepoint</option>
                            <option value="Screwfix Ireland">Screwfix Ireland</option>
                        </select>

                        <!--<br>
                        or URL:
                        <br>
                        <input type="url" name="url" id="url" placeholder="https://example.com" pattern="https://.*" size="30">-->

                    </div><div class="panel">
                        <h3>Client specific options</h3>
                        
                        <!-- HSBC Expat -->
                        <div id="HSBCExpat-options" class="options">

                            <div class="formline">
                                <h3>Expat Template Options</h3>
                            </div>
                            <div class="formline">
                                <label for="segmentsYN">Tracked segments</label>

                                <input type="checkbox" name="segmentsYN" class="segmentsYN">
                            </div>
                            
                            <div class="formline" id="tracking">
                                <label for="lname">Output Campaign Tracking Tag:</label><br>
                                <div class="form-row">
                                    <table id="tracking-tag-table">
                                        <tr>
                                            <th align="left">Segment
                                            </th>
                                            <th align="left">Tracking Code
                                            </th>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="text" name="newtrackingseg00" placeholder="Segment Number (e.g. '1')">
                                            </td>
                                            <td>
                                                <input type="text" name="newtrackingtag00" placeholder="Tracking Code (e.g. 'EML_EXP_EN_29558_0)">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="text" name="newtrackingseg01" placeholder="Segment Number (e.g. '2')">
                                            </td>
                                            <td><input type="text" name="newtrackingtag01" placeholder="Tracking Code (e.g. 'EML_EXP_EN_29558_1)">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="text" name="newtrackingseg02" placeholder="Segment Number (e.g. '3')">
                                            </td>
                                            <td><input type="text" name="newtrackingtag02" placeholder="Tracking Code (e.g. 'EML_EXP_EN_29558_2)">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="text" name="newtrackingseg03" placeholder="Segment Number (e.g. '4')">
                                            </td>
                                            <td>
                                                <input type="text" name="newtrackingtag03" placeholder="Tracking Code (e.g. 'EML_EXP_EN_29558_3)">
                                            </td>
                                        </tr>
                                    </table>
                                    <div id="addSegment" title="Add another segment">+</div>
                                    <input type="hidden" name="numberOfSegments">
                                </div>
                            </div>

                            <div class="formline">
                                <label for="RM">RM signoff</label>

                                <input type="checkbox" name="RM">
                            </div>
                        </div>
                        <!-- -->

                        <!-- HSBC Bermuda -->
                        <div id="HSBCBermuda-options" class="options">

                            <div class="formline">
                                <h3>Bermuda Template Options</h3>
                            </div>
                            <div class="formline">
                                No options available
                            </div>
                        </div>
                        <!-- -->

                        <!-- HSBC Malta -->
                        <div id="HSBCMalta-options" class="options">

                            <div class="formline">
                                <h3>Malta Template Options</h3>
                            </div>
                            <div class="formline">
                                No options available
                            </div>
                        </div>
                        <!-- -->

                        <!-- HSBC Malta -->
                        <div id="HSBCCIIOM-options" class="options">

                            <div class="formline">
                                <h3>CIIOM Template Options</h3>
                            </div>
                            <div class="formline">
                                No options available
                            </div>
                        </div>
                        <!-- -->

                        <!-- Tradepoint -->
                        <div id="Tradepoint-options" class="options">

                            <div class="formline">
                                <h3>TradePoint Template Options</h3>
                            </div>
                            <div class="formline">
                                No options available
                            </div>
                        </div>
                        <!-- -->
        


                    </div><div class="panel" id="sb-section">

                        <h3 style="margin-bottom: 2px;">Strange beasts</h3>
                        <p>I detected some unusual tags. <br>How should I correct these?</p>

                        <div id="strangeBeasts">
                        <input type="hidden" name="sb-num" id="sb-num">
                        </div>
                            


                    </div><div class="panel">

                        <h3 style="margin-bottom: 2px;">Custom replace</h3>

                        <p>Use this section to add any custom find and replace rules.</p>

                        <div align="right">
                            <input type="checkbox" checked value=""><label style="padding-left: 10px;">case sensitve search</label>
                        </div>

                        <div align="center">
                            <span id="addAnother" onclick="addAnother()">Add custom replace rule</span>
                        </div>

                        <div id="customReplaceQueries">
                            <input type="hidden" name="cstm-num" id="cstm-num">
                        </div>

                    </div><div class="panel">

                        <h3>Code Doctor</h3>
                        <div id="codeDoctorSizeReport"></div>
                        <div id="codeDoctorClassReport"></div>
                        <ul id="unusedClasses">

                        </ul>


                    </div><div class="panel">
                        <h3>4. Submit it</h3>
                        <input type="submit" value="Convert">
                    </div>

                    </div>
            </div>
        </form>
    </body>
    <script type="text/javascript" src="scripts/form.js"></script>

    
</html>