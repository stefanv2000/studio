
$.extend(mejs.MepDefaults, {
    timeAndDurationSeparator: '<span> / </span>'
});

MediaElementPlayer.prototype.buildclicktozoom = function(player, controls, layers, media) {

        var
        // create the loop button
            zoom =
                $('<div class="mejs-button mejs-clicktozoom-button">' +
                    '<span class="mejs-clicktozoom-span">click to zoom</span>' +
                    '</div>')
                // append it to the toolbar
                    .appendTo(controls)
                    // add a click toggle event
                    .click(function() {
                        var callback = $(media).data('callback');
                        callback();
                        player.pause();
                    });
        return zoom;
    }


    MediaElementPlayer.prototype.buildzoomout = function(player, controls, layers, media) {

        var
        // create the loop button
            zoomout =
                $('<div class="mejs-button mejs-zoomout-button">' +
                    '<span class="mejs-zoomout-span">zoom out</span>' +
                    '</div>')
                // append it to the toolbar
                    .appendTo(controls)
                    // add a click toggle event
                    .click(function() {
                        var callback = $(media).data('callback');
                        callback();
                        player.pause();
                    });
        return zoom;
    }


