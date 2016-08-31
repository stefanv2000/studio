/**
 * @author stefan valea stefanvalea@gmail.com
 */

//*

if (typeof String.prototype.endsWith !== 'function') {
    String.prototype.endsWith = function(suffix) {
        return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };
}
if (typeof String.prototype.startsWith !== 'function') {
    String.prototype.startsWith = function (str) {
        return !this.indexOf(str);
    }
}

function fillImageToContainer(imageSize,containerSize){
    var containerAspect = containerSize[0]/containerSize[1];
    var imageAspect = imageSize[0]/imageSize[1];
    var result = new Array();

    if (containerAspect < imageAspect){
        result['height'] = containerSize[1];
        result['width'] = containerSize[1]*imageAspect;
        result['top'] = 0;
        result['left'] = -(containerSize[1]*imageAspect-containerSize[0])/2
    } else {
        result['height'] = containerSize[0]/imageAspect;
        result['width'] = containerSize[0];
        result['top'] = -(containerSize[0]/imageAspect - containerSize[1])/2;
        result['left'] = 0;
    }
    return result;
}

var pathToRoot = '/';

require.config({
    paths: {
        jquery: 'js/vendor/jquery',
        underscore: 'js/vendor/underscore',
        backbone: 'js/vendor/backbone',
        text: 'js/vendor/text',
        views:'js/view/',
        models:'js/models/',
        main:'js/main/',
        crossroads: 'js/vendor/crossroads.min',
        signals: 'js/vendor/signals.min',
    },
    shim: {
        backbone: {
            deps: ["underscore", "jquery"],
            exports: "Backbone",
        },

        underscore: {
            exports: "_"
        }
    },
    baseUrl:pathToRoot+'app/',
});

require(['jquery'],'utils');
var router;
var routesEventsManager;
var app;
Utils.changeTitle(" studio GROUP");


require([
        'backbone','js/main/mobile.app'],
    function(Backbone,App){

        app = new App();
        app.initialize()

    });
//*/