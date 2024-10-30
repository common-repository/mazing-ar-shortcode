function generateMazingFrame() {
    const divs = document.querySelectorAll('div[mazing-id]');
    for (let i = 0; i < divs.length; i++) {
        const mazingContainer = divs[i];

        const mazingId = mazingContainer.getAttribute('mazing-id');

        let done = false;
        if(!globalThis.PROCESSED_MAZING){
            globalThis.PROCESSED_MAZING = [];
        }

        if(globalThis.PROCESSED_MAZING.includes(mazingId)){
            done = true;
        }

        globalThis.PROCESSED_MAZING.push(mazingId);

        if (!done) {

            const mazingUrl = mazingContainer.getAttribute('mazing-url');
            const mazingRatioLeft = mazingContainer.getAttribute('mazing-ratio-left');
            const mazingRatioRight = mazingContainer.getAttribute('mazing-ratio-right');
            const mazingMaxWidth = mazingContainer.getAttribute('mazing-max-width');
            const mazingMaxHeight = mazingContainer.getAttribute('mazing-max-height');


            let $css = jQuery("#css"),
                $html = jQuery("#html"),
                $combined = jQuery("#combined"),
                $container = jQuery('<div mazing-done="' + mazingId + '" class="iframe-container" />'),
                $style = jQuery("#style"),
                $iframe = jQuery('<iframe />'),

                $aspect = 100 * mazingRatioRight / mazingRatioLeft;

            $iframe.attr("src", mazingUrl);
            $iframe.attr("allowusermedia", "");
            $iframe.attr("loading", "lazy");
            $iframe.attr("allowfullscreen", "");
            $iframe.attr("allow", "accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; camera *; xr-spatial-tracking;");

            $container.html($iframe);

            $html.text($container.prop("outerHTML"));

            let containerRule1 = ".iframe-container {\n";
            containerRule1 += "&nbsp;&nbsp;overflow: hidden;\n";
            containerRule1 += "&nbsp;&nbsp;position: relative;\n";

            let containerRule2 = ".iframe-container iframe {\n";
            containerRule2 += "&nbsp;&nbsp;border: 0;\n";
            containerRule2 += "&nbsp;&nbsp;height: 100%;\n";
            containerRule2 += "&nbsp;&nbsp;left: 0;\n";
            containerRule2 += "&nbsp;&nbsp;position: absolute;\n";

            if (mazingMaxHeight && mazingMaxHeight != 0) {
                containerRule2 += "&nbsp;&nbsp;max-height: " + mazingMaxHeight + "px;\n";
                containerRule1 += "&nbsp;&nbsp;padding-top: min(" + mazingMaxHeight + "px ," + $aspect + "%);\n";
            } else {
                containerRule1 += "&nbsp;&nbsp;padding-top: " + $aspect + "%;\n";
            }

            if (mazingMaxWidth && mazingMaxWidth != 0) {
                containerRule1 += "&nbsp;&nbsp;max-width: " + mazingMaxWidth + "px; margin-left: auto; margin-right: auto;\n";
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

            mazingContainer.outerHTML = iFrameInlineComplete;
        }
    }

}
