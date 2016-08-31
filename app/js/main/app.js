/**
 * 
 */

define([
  'jquery',
  'underscore',
  'backbone',
    'views/layouts/mainLayout',
  'main/router',
  'main/appview',
  'main/eventsRoutes',
  'views/background',
    'views/menu/menu'
], function($, _, Backbone, MainLayout,Router,AppView,RoutesEventsManager,BackgroundView,MenuView){
  var App = function(){
  this.initialize =  function(){


    //setup menu
    //var menuview = new ViewMenu();
    //$('body').append(menuview.render());

    //setup layout
    var layout = new MainLayout();
    $('body').prepend(layout.$el);
    layout.render();
    this.layout=layout;

    backgroundview = new BackgroundView();
    layout.$el.prepend(backgroundview.$el);

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