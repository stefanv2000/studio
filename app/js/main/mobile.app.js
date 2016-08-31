/**
 *
 */

define([
    'jquery',
    'underscore',
    'backbone',
    'views/layouts/mobile.mainLayout',
    'main/mobile.router',
    'main/appview',
    'main/mobile.eventsRoutes',
    'views/menu/mobile.menu'
], function($, _, Backbone, MainLayout,Router,AppView,RoutesEventsManager,MenuView){
    var App = function(){
        this.initialize =  function(){


            //setup layout
            var layout = new MainLayout();
            $('body').prepend(layout.$el);
            layout.render();
            this.layout=layout;

            this.appView=appview;
            var appview = new AppView();
            routesEventsManager = RoutesEventsManager;
            routesEventsManager.appView = appview;
            //start router

            Router.initialize({
                appView:appview,
            });


        }
    }

    return App;
});
