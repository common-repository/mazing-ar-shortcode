
<?php
global $wpdb;
$tablename = $wpdb->prefix . "mazing_ar_shortcode";

// Delete mazing project record
if (isset($_GET['delid'])) {
    $delid = sanitize_key($_GET['delid']);
    $specget = $wpdb->prepare("DELETE FROM " . $tablename . " WHERE id=" . $delid);
    $entriesList = $wpdb->get_results($specget);
}

// Update mazing project record
if (isset($_GET['update_record'])) {
    $update_record = sanitize_key($_GET['update_record']);
    $aspectLeft = intval($_GET['aspectLeft']);
    $aspectRight = intval($_GET['aspectRight']);
    $maxWidth = intval($_GET['maxWidth']);
    $maxHeight = intval($_GET['maxHeight']);

    $updateQuery = "UPDATE " . $tablename . " SET ratio_left = " . $aspectLeft . ", ratio_right = " . $aspectRight . ", max_width = " . $maxWidth . ", max_height = " . $maxHeight . " WHERE id = " . $update_record;
    $specget = $wpdb->prepare($updateQuery);
    $entriesList = $wpdb->get_results($specget);
}

// Add mazing project record
if (isset($_POST['but_submit'])) {

    $link = esc_url_raw($_POST['txt_link']);

    $pieces = explode("/", $link);
    $pieces2 = explode("pr=", end($pieces));
    $projectID = end($pieces2);

    // We are relying on our own 3rd party Mazing software. Have look at the readme to find further informations.
    $request = wp_remote_get("https://dwkpx86rtc.execute-api.eu-central-1.amazonaws.com/pro/get-project/?projectUID=" . $projectID);
    if (is_wp_error($request)) {
        return false; // Bail early
    }

    $body = wp_remote_retrieve_body($request);
    $data = json_decode($body);

    if (!empty($data)) {
        $s3 = $data->project->s3;
        require_once( ABSPATH . WPINC . '/class-json.php' );
        $jsonService = new Services_JSON();
        $requestConfig = wp_remote_get($s3 . "config.maz");
        $bodyConfig = wp_remote_retrieve_body($requestConfig);
        $jsonConfig = $jsonService->decode($bodyConfig);;
        $projectName = $jsonConfig->project . " " . $jsonConfig->customer;
        $requestModel = wp_remote_get($s3 . "models.maz");
        $bodyModel = wp_remote_retrieve_body($requestModel);
        $jsonModel = $jsonService->decode($bodyModel);
        $posterLink = $s3 . "models/" . $jsonModel[0]->poster;
        if ($projectName != '' && $posterLink != '' && $link != '') {
            $check_data_spec = $wpdb->prepare("SELECT * FROM " . $tablename . " WHERE url='" . $link . "' ");
            $check_data = $wpdb->get_results($check_data_spec);
            if (count($check_data) == 0) {
                $insert_sql = "INSERT INTO " . $tablename . "(name,url,image) values('" . $projectName . "','" . $link . "','" . $posterLink . "') ";
                $specget = $wpdb->prepare($insert_sql);
                $entriesList = $wpdb->get_results($specget);
                $new_inserted_spec = $wpdb->prepare("SELECT * FROM " . $tablename . " WHERE url='" . $link . "' ");
                $new_inserted = $wpdb->get_results($new_inserted_spec);
                foreach ($new_inserted as $entry) {
                    $startID = $entry->id;
                }
            }
        }
    }
}
?>

    <hr>
    <!-- Main navigation -->
    <div class="container" style="max-width: unset">
    <div class="row">
        <div class="col-md-3">

            <div class="card" style="max-width: 100%">

                <div>
                    <div
                            style=" overflow: hidden; position: relative; padding-top: min(450px ,100%); max-width: 650px; margin-left: auto; ">
                        <iframe style=" border: 0; height: 100%; left: 0; position: absolute; max-height: 450px; top: 0; width: 100%; "
                                src="https://pro.mazing-ar.com/bizb6c2psg" allowusermedia="" allowfullscreen=""
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; camera *; xr-spatial-tracking;"></iframe>
                    </div>
                </div>
            </div>
            <div class="card" style="max-width: 100%; padding-bottom: 0;">
                <div class="card-body">
                    <h5 class="card-title">ADD NEW MAZING 3D/AR URL </h5>
                    <div class="input-group mb-3">

                        <form method='post' action='' style="display: flex;">
                            <input type="text" name='txt_link' class="form-control"
                                   placeholder="Link (https://pro.mazing-ar.com/....)" aria-label="Link">
                            <input class="btn btn-primary" type='submit' name='but_submit' value='ADD PROJECT'
                                   style="margin-left: 16px">
                        </form>

                        <figcaption class="blockquote-footer" style="margin-top: 6px">
                            No Mazing link yet? <a href="https://mazingxr.com/" target="_blank">CONTACT US</a> for more
                            info. Or try to add this demo link first: <span style="font-weight: bold">https://pro.mazing-ar.com/4lxSMU7UB5</span>
                        </figcaption>

                    </div>
                </div>
            </div>


        </div>

        <div class="col-md-9">
            <div class="card" style="max-width: 100%">
                <div class="row">
                    <div class="col-md-3" style="border-right: 1px solid #101777;">
                        <p class="lead">About MazingAR</p>
                        <p style="font-size: 12pt; margin-top: 20px;">Instantly grow your revenue with our futuristic
                            form of
                            product presentation. Let your visitors experience your products in 3D and let them place it
                            in
                            their
                            own homes, before buying, with Augmented Reality.</p>
                        <br>
                        <p style="font-size: 12pt" class="text-center">Your own products in 3D/AR?</p>
                        <a href="https://meetings.hubspot.com/manuel159" target="_blank">
                            <div class="text-center">
                                <button type="button" class="btn btn-primary btn-lg"><img width="28px" src="<?php echo plugins_url('/img/help.png', __FILE__) ?>"/>
                                    GET A QUOTE
                                </button>
                            </div>
                        </a>
                        <br>
                        <br>

                        <p style="font-size: 12pt" class="text-center">Interested? Feel free to contact us!</p>
                        <a href="https://mazingxr.com" target="_blank">
                            <div class="text-center">
                                <button type="button" class="btn btn-lg btn-outline-dark"><img width="28px" src="<?php echo plugins_url('/img/globe.png', __FILE__) ?>"/>
                                    VISIT WEBSITE
                                </button>
                            </div>
                        </a>
                        <a href="mailto:office@mazingxr.com" target="_blank">
                            <div class="text-center" style="margin-top: 8px">
                                <button type="button" class="btn btn-lg btn-outline-dark"><img width="28px" src="<?php echo plugins_url('/img/email.png', __FILE__) ?>"/>
                                    SEND MAIL
                                </button>
                            </div>

                        </a>
                        <br>
                    </div>

                    <div class="col-md-9">
                        <div class="row">
                            <p class="lead">What's the process?</p>
                            <div class="col-md-6">
                                <h5>1.) Imagine your 3D/AR models on your website?</h5>
                                <div class="card">

                                    <img class="img-fluid" style="max-width: 300px"
                                         src="<?php echo plugins_url('/img/description/chair.jpg', __FILE__)  ?>"/>
                                </div>

                                <h5>2.) Copy and paste Link</h5>
                                <div class="card">

                                    <img class="img-fluid" style="max-width: 250px"
                                         src="<?php echo plugins_url('/img/description/addlink.jpg', __FILE__) ?>"/>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <h5>3.) Adjust the Appearance?</h5>
                                <div class="card">

                                    <img class="img-fluid" style="max-width: 300px"
                                         src="<?php echo plugins_url('/img/description/configure.jpg', __FILE__) ?>"/>
                                </div>

                                <h5>4.) Copy and pase the Shortcode into any textbox</h5>
                                <div class="card">

                                    <img class="img-fluid" style="max-width: 200px"
                                         src="<?php echo  plugins_url('/img/description/embed.jpg', __FILE__) ?>"/>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

    <br>
    <table id="projects" width='100%' border='1' style='border-collapse: collapse;'>
        <tr>
            <th>#</th>
            <th>Preview</th>
            <th>Name</th>
            <th>Shortcode</th>
            <th>Frame (Settings)</th>
            <th>Link</th>
            <th>&nbsp;</th>
        </tr>
        <?php
        // Select records
        $entriesList = $wpdb->get_results("SELECT * FROM " . $tablename . " order by id desc");
        if (count($entriesList) > 0) {
            $count = 1;
            foreach ($entriesList as $entry) {

                $id = $entry->id;
                $name = $entry->name;
                $shortcode = "[mazing id=" . $id . "]";
                $url = $entry->url;
                $image = $entry->image;
                $ratioLeft = $entry->ratio_left;
                $ratioRight = $entry->ratio_right;
                $maxWidth = $entry->max_width;
                $maxHeight = $entry->max_height;

                $maxWidthString = $maxWidth == 0 ? 'auto ' : $maxWidth . "px";
                $maxHeightString = $maxHeight == 0 ? 'auto ' : $maxHeight . "px";

                echo "<tr>
		    	<td>" . intval($id) . "</td>
				<td ><img class=\"product-image\" src=\"" . esc_url_raw($image) . "\"></td>
		    	<td>" . esc_textarea($name) . "</td>
				<td>" . esc_textarea($shortcode) . "<img tabIndex='0' role='button' onClick='copyToClipboard(\"" . esc_textarea($shortcode) . "\")' style='margin-left: 16px' src=\"" . plugins_url('/img/copy.png', __FILE__) . "\"/></td>
				<td><div style='display: flex'><span>Ratio " . intval($ratioLeft) . ":" . intval($ratioRight) . "<br>max-width " . esc_attr($maxWidthString) . "<br>max-height " . esc_attr($maxHeightString) . "</span><img id='frame-setting-" . intval($id) . "' onClick='setProductDetails(\"" . esc_url_raw($url) . "\",\"" . intval($ratioLeft) . "\",\"" . intval($ratioRight) . "\",\"" . intval($maxWidth) . "\",\"" . intval($maxHeight) . "\",\"" . intval($id) . "\")' tabIndex='1' role='button' data-bs-toggle=\"modal\" data-bs-target=\"#staticBackdrop\" style='margin-left: 16px; margin-top: 12px; width: 40px; height: 40px;' src=\"" . plugins_url('/img/settings.png', __FILE__)  . "\"/></div></td>
				<td>" . esc_url_raw($url) . "</td>
		    	<td><a href='?page=mazgar_displayList&delid=" . intval($id) . "'><img src=\"" . plugins_url('/img/delete.png', __FILE__) . "\"/></a></td>
		    </tr>
		    ";
                $count++;
            }
        } else {
            echo "<tr><td colspan='7'><figcaption class='blockquote-footer' style='margin-top: -6px'>No Mazing link yet? <a href='https://mazingxr.com/' target='_blank'>CONTACT US</a> for more info.<br><br>Or try to add this demo link first: https://pro.mazing-ar.com/4lxSMU7UB5
		  </figcaption></td></tr>";
        }
        ?>
    </table>


    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="false" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" style="background: #d4d4d4b0; z-index: 10000;">
        <div class="modal-dialog" style="max-width: unset; padding: 5%;">
            <div class="modal-content" style="height: fit-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">iFrame Styler</h5>


                    <div class="header-save-cancel">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="saveBtnHeader" class="btn btn-primary">Save</button>
                    </div>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 offset-md-3">

                            <h5>Easy Settings</h5>

                            <label for="maxWidth" class="form-label">Select a preset depending on your product type for an easy setup.</label>
                            <br>
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">

                                <button class="btn btn-outline-primary" onclick="setPreset('standard')">
                                    <img class="img-fluid" style="max-width: 80px" src="<?php echo plugins_url('/img/presets/standard.jpg', __FILE__)  ?>"/>
                                    Standard Product
                                </button>

                                <button class="btn btn-outline-primary" onclick="setPreset('wide')">
                                    <img class="img-fluid" style="max-width: 80px" src="<?php echo plugins_url('/img/presets/wide.jpg', __FILE__)  ?>"/>
                                    Wide Product
                                </button>

                                <button class="btn btn-outline-primary" onclick="setPreset('high')">
                                    <img class="img-fluid" style="max-width: 80px" src="<?php echo plugins_url('/img/presets/high.jpg', __FILE__)  ?>"/>
                                    High Product
                                </button>

                            </div>
                            <br>

                            <hr>

                            <h5>Advanced Settings</h5>

                            <label for="maxWidth" class="form-label">Ratio</label>
                            <br>
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="btnradio" id="btnradio1" value="1x1"
                                       prefill="600x600"
                                       autocomplete="off" checked>
                                <label class="btn btn-outline-primary" for="btnradio1">1:1</label>

                                <input type="radio" class="btn-check" name="btnradio" value="4x3" id="btnradio2"
                                       prefill="750x550"
                                       autocomplete="off">
                                <label class="btn btn-outline-primary" for="btnradio2">4:3</label>

                                <input type="radio" class="btn-check" name="btnradio" id="btnradio3" value="16x9"
                                       prefill="650x350"
                                       autocomplete="off">
                                <label class="btn btn-outline-primary" for="btnradio3">16:9</label>

                                <input type="radio" class="btn-check" name="btnradio" id="btnradio4" value="3x1"
                                       prefill="600x200"
                                       autocomplete="off">
                                <label class="btn btn-outline-primary" for="btnradio4">3:1</label>

                                <input type="radio" class="btn-check" name="btnradio" id="btnradio5" value="2x1"
                                       prefill="600x400"
                                       autocomplete="off">
                                <label class="btn btn-outline-primary" for="btnradio5">2:1</label>

                                <input type="radio" class="btn-check" name="btnradio" id="btnradio6" value="1x2"
                                       prefill="300x600"
                                       autocomplete="off">
                                <label class="btn btn-outline-primary" for="btnradio6">1:2</label>

                                <input type="radio" class="btn-check" name="btnradio" id="btnradio7" value="3x4"
                                       prefill="450x600"
                                       autocomplete="off">
                                <label class="btn btn-outline-primary" for="btnradio7">3:4</label>
                            </div>
                            <br>

                            <hr>
                            <label for="max-width-range" class="form-label"><span id="max-width-slider-value"
                                                                                  style="font-weight:bold;">120</span> -
                                Max Width in pixels (0=automatic) </label>
                            <input type="range" class="form-range" min="0" max="1000" step="50" id="max-width-range">

                            <hr>
                            <label for="max-height-range" class="form-label"><span id="max-height-slider-value"
                                                                                   style="font-weight:bold;">120</span>
                                - Max Height in pixels (0=automatic)</label>
                            <input type="range" class="form-range" min="0" max="1000" step="50" id="max-height-range">

                            <label for="maxWidth" class="form-label">Preview</label>
                            <br>
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="btnpreview" id="btnpreview1" value="pc"
                                       autocomplete="off" checked>
                                <label class="btn btn-outline-primary" for="btnpreview1">PC</label>

                                <input type="radio" class="btn-check" name="btnpreview" id="btnpreview2" value="mobile"
                                       autocomplete="off">
                                <label class="btn btn-outline-primary" for="btnpreview2">MOBILE</label>

                            </div>

                        </div>


                    </div>


                    <br>
                    <br>
                    <div id="outPreview" style="width: 100%; margin-left: auto; margin-right: auto;">
                        <div id="preview" style="border: 1px solid;"></div>
                    </div>
                    <br>
                    <br>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button id="saveBtnFooter" type="button" class="btn btn-primary">Save</button>
                    </div>

                </div>
            </div>
        </div>


    </div>

    <style id="style"></style>

    <script>

        let productUrl = 'https://mazing-ar.com/living/';
        let aspectLeft = 1;
        let aspectRight = 1;

        let maxWidth = null;
        let maxHeight = null;

        let id = -1;

        const PRESETS = {
            'standard': {
                aLeft: 1,
                aRight: 1,
                rawMaxWidth: 600,
                rawMaxHeight: 600,
            },
            'wide': {
                aLeft: 4,
                aRight: 3,
                rawMaxWidth: 750,
                rawMaxHeight: 550,
            },
            'high': {
                aLeft: 1,
                aRight: 2,
                rawMaxWidth: 400,
                rawMaxHeight: 600,
            }
        }

        function setPreset(key){
            const preset = PRESETS[key];

            aspectLeft = preset.aLeft;
            aspectRight = preset.aRight;
            maxHeight = preset.rawMaxHeight;
            maxWidth = preset.rawMaxWidth;

            let $sliderMaxHeight = jQuery('#max-height-range'),
                $sliderMaxHeightVal = jQuery('#max-height-slider-value'),
                $sliderMaxWidth = jQuery('#max-width-range'),
                $sliderMaxWidthVal = jQuery('#max-width-slider-value');

            $sliderMaxHeight.val(maxHeight);
            $sliderMaxHeightVal.text(maxHeight);

            $sliderMaxWidth.val(maxWidth);
            $sliderMaxWidthVal.text(maxWidth);

            jQuery("input:radio[value='" + aspectLeft + 'x' + aspectRight + "']").prop('checked', true).trigger('change');
        }

        function setProductDetails(url, aLeft, aRight, rawMaxWidth, rawMaxHeight, rawId) {
            productUrl = url;
            aspectLeft = aLeft;
            aspectRight = aRight;
            maxHeight = rawMaxHeight;
            maxWidth = rawMaxWidth;

            id = rawId;

            let $sliderMaxHeight = jQuery('#max-height-range'),
                $sliderMaxHeightVal = jQuery('#max-height-slider-value'),
                $sliderMaxWidth = jQuery('#max-width-range'),
                $sliderMaxWidthVal = jQuery('#max-width-slider-value');

            $sliderMaxHeight.val(rawMaxHeight);
            $sliderMaxHeightVal.text(rawMaxHeight);

            $sliderMaxWidth.val(rawMaxWidth);
            $sliderMaxWidthVal.text(rawMaxWidth);

            jQuery("input:radio[value='" + aspectLeft + 'x' + aspectRight + "']").prop('checked', true).trigger('change');
        }

        function copyToClipboard(message) {
            var textArea = document.createElement("textarea");
            textArea.value = message;
            textArea.style.opacity = "0";

            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                const msg = successful ? 'successful' : 'unsuccessful';
                alert('Copied Shortcode to clipboard: ' + message);
                console.log('Copy State: ', msg);
            } catch (err) {
                alert('Error in copying text to clipboard: ' + err.message);
            }

            document.body.removeChild(textArea);
        }

        function save() {
            console.log('Saving Item: ', {
                    id: id,
                    aspectLeft: aspectLeft,
                    aspectRight: aspectRight,
                    maxWidth: maxWidth,
                    maxHeight: maxHeight,
                }
            );

            const a = document.createElement('a');
            a.href = '?page=mazgar_displayList&update_record=' + id + '&aspectLeft=' + aspectLeft + '&aspectRight=' + aspectRight + '&maxWidth=' + maxWidth + '&maxHeight=' + maxHeight;
            a.click();
        }


        function update() {
            let $preview = jQuery("#preview"),
                $saveBtnHeader = jQuery("#saveBtnHeader"),
                $saveBtnFooter = jQuery("#saveBtnFooter"),
                $outPreview = jQuery("#outPreview"),
                $css = jQuery("#css"),
                $html = jQuery("#html"),
                $combined = jQuery("#combined"),
                $aspect = jQuery('[type="radio"][name="btnradio"]:checked'),
                $previewRatio = jQuery('[type="radio"][name="btnpreview"]:checked'),
                $container = jQuery('<div class="iframe-container" />'),
                $style = jQuery("#style"),
                $iframe = jQuery('<iframe />'),
                $sliderMaxHeight = jQuery('#max-height-range'),
                $sliderMaxHeightVal = jQuery('#max-height-slider-value'),
                $sliderMaxWidth = jQuery('#max-width-range'),
                $sliderMaxWidthVal = jQuery('#max-width-slider-value');


            $saveBtnHeader.click(() => {
                save();
            });

            $saveBtnFooter.click(() => {
                save();
            });


            const sliderMaxHeightVal = $sliderMaxHeight.val();
            $sliderMaxHeight.change(function () {
                $sliderMaxHeightVal.text($sliderMaxHeight.val());
            });

            $sliderMaxHeight.on("input change", (e) => {
                    $sliderMaxHeightVal.text(e.target.value);
                    maxHeight = e.target.value;
                }
            );

            const sliderMaxWidthVal = $sliderMaxWidth.val();
            $sliderMaxWidth.change(function () {
                $sliderMaxWidthVal.text($sliderMaxWidth.val());
            });

            $sliderMaxWidth.on("input change", (e) => {
                    $sliderMaxWidthVal.text(e.target.value);
                    maxWidth = e.target.value;
                }
            );

            let aspect = $aspect.val().split("x");

            if (aspectLeft != aspect[0] || aspectRight != aspect[1]) {
                const prefills = $aspect.attr("prefill").split("x");

                maxHeight = prefills[1];
                $sliderMaxHeight.val(maxHeight);
                $sliderMaxHeightVal.text(maxHeight);

                maxWidth = prefills[0];
                $sliderMaxWidth.val(maxWidth);
                $sliderMaxWidthVal.text(maxWidth);
            }

            aspectLeft = aspect[0];
            aspectRight = aspect[1];


            aspect = 100 * aspect[1] / aspect[0];

            if (productUrl.includes('?')) {
                productUrl = productUrl + '&disableImpressionTracking=true';
            } else {
                productUrl = productUrl + '/?disableImpressionTracking=true';
            }

            if (!productUrl.includes('http')) {
                productUrl = 'https://' + productUrl;
            }

            $iframe.attr("src", productUrl + '/?disableImpressionTracking=true');
            $iframe.attr("allowusermedia", "");
            $iframe.attr("allowfullscreen", "");
            $iframe.attr("allow", "accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; camera *; xr-spatial-tracking;");

            $container.html($iframe);
            $preview.html($container);

            let previewRatio = $previewRatio.val();
            if (previewRatio === 'pc') {
                $outPreview.css({"max-width": "1200px"});
            } else {
                $outPreview.css({"max-width": "400px"});
            }

            $html.text($container.prop("outerHTML"));

            let containerRule1 = ".iframe-container {\n";
            containerRule1 += "&nbsp;&nbsp;overflow: hidden;\n";
            containerRule1 += "&nbsp;&nbsp;position: relative;\n";

            let containerRule2 = ".iframe-container iframe {\n";
            containerRule2 += "&nbsp;&nbsp;border: 0;\n";
            containerRule2 += "&nbsp;&nbsp;height: 100%;\n";
            containerRule2 += "&nbsp;&nbsp;left: 0;\n";
            containerRule2 += "&nbsp;&nbsp;position: absolute;\n";
            if (sliderMaxHeightVal && sliderMaxHeightVal != 0) {
                containerRule2 += "&nbsp;&nbsp;max-height: " + sliderMaxHeightVal + "px;\n";
                $preview.css({"maxHeight": sliderMaxHeightVal + "px"});
                containerRule1 += "&nbsp;&nbsp;padding-top: min(" + sliderMaxHeightVal + "px ," + aspect + "%);\n";
            } else {
                $preview.css({"maxHeight": "none"});
                containerRule1 += "&nbsp;&nbsp;padding-top: " + aspect + "%;\n";
            }

            if (sliderMaxWidthVal && sliderMaxWidthVal != 0) {
                containerRule1 += "&nbsp;&nbsp;max-width: " + sliderMaxWidthVal + "px; margin-left: auto; margin-right: auto;\n";
                $preview.css({"maxWidth": sliderMaxWidthVal + "px"});
                $preview.css({"marginLeft": "auto"});
                $preview.css({"marginRight": "auto"});
            } else {
                $preview.css({"maxWidth": "none"});
                $preview.css({"marginLeft": "unset"});
                $preview.css({"marginRight": "unset"});
            }


            containerRule1 += "}\n";
            containerRule1 += "\n";

            containerRule2 += "&nbsp;&nbsp;top: 0;\n";
            containerRule2 += "&nbsp;&nbsp;width: 100%;\n";
            containerRule2 += "}";


            let css = containerRule1 + containerRule2;
            $css.html(css.replace(/(?:\r\n|\r|\n)/g, "<br />"));

            const iFrameContainerStyle = containerRule1.replace('.iframe-container {', '').replace('}', '').replace(/&nbsp;/gi, "").replace(/(?:\r\n|\r|\n)/g, ' ');
            const iFrameElementStyle = containerRule2.replace('.iframe-container iframe {', '').replace('}', '').replace(/&nbsp;/gi, "").replace(/(?:\r\n|\r|\n)/g, ' ');
            const iFrameInlineComplete = $container.prop("outerHTML").replace('class="iframe-container"', 'style="' + iFrameContainerStyle + '"').replace('<iframe', '<iframe style="' + iFrameElementStyle + '"');

            $combined.text(iFrameInlineComplete);

            $style.text(css.replace(/&nbsp;/gi, ""));

        }

        function clickElementById(id) {
            const frameSetting = document.getElementById('frame-setting-' + id);
            if (frameSetting) {
                frameSetting.click();
            }
        }

        jQuery("input").change(function () {
            update();
        });
    </script>



<?php
echo "<script> clickElementById('" . esc_js($startID) . "') </script>";
?>
