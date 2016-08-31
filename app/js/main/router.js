/**
 *
 */
define([
    'jquery',
    'underscore',
    'backbone'
], function ($, _, Backbone) {
    var AppRouter = Backbone.Router.extend({
        routes: {
            '(artists)(emerging-artists)(production)(spokespeople)' : 'intro',
            '(artists)(emerging-artists)(production)(spokespeople)/:typea/:name' : 'profile',
            '(artists)(emerging-artists)(production)(spokespeople)/:typea/:name/portfolio(/:gallery)(/:cindex)' : 'showPortfolio',
            '(artists)(emerging-artists)(production)(spokespeople)/:typea/:name/videos(/:gallery)(/:cindex)' : 'showPortfolioVideo',
            '(artists)(emerging-artists)(production)(spokespeople)/:typea/:name/press(/:cindex)' : 'showPortfolioPress',
            '(artists)(emerging-artists)(production)(spokespeople)/:typea/:name/profile' : 'showProfessionalProfile',

            '(food-_-drink)(food_drink)(influencers1)/:name' : 'profile',
            '(food-_-drink)(food_drink)(influencers1)/:name/portfolio/:gallery(/:cindex)' : 'showPortfolioFD',
            '(food-_-drink)(food_drink)(influencers1)/:name/videos(/:gallery)(/:cindex)' : 'showPortfolioVideoFD',
            '(food-_-drink)(food_drink)(influencers1)/:name/press' : 'showFoodDrinkPress',
            '(food-_-drink)(food_drink)(influencers1)/:name/press(/:postname)' : 'showFoodDrinkPressPost',
            '(food-_-drink)(food_drink)(influencers1)/:name/profile' : 'showFoodDrinkProfile',
            'models':'showModelsCategory',
            'models/:category':'showModelsCategory',
            'models/:category(/:name)(/:gallery)(/:cindex)':'showModelsGallery',
            'contact': 'showPageContact',
            'international': 'showPageInternational',
            'become-a-model': 'showPageBecome',
            'special-occasion': 'showPageSpecial',
            //'*actions': 'defaultAction',
        },
        initialize: function (options) {
            this.appView = options.appView;
        },

        intro : function(){
            routesEventsManager.trigger('route:showintro',{});
        },

        profile : function(typea,name){
            routesEventsManager.trigger('route:showprofileintro',{'type':typea,'name':name});
        },

        showPortfolio : function (typea,name,gallery,cindex){
            routesEventsManager.trigger('route:showportfolio',{'type':typea,'name':name,'gallery' : gallery,'index':cindex});
        },

        showPortfolioVideo:function (typea,name,gallery,cindex){
            var self = this;
            if (/^\d+$/.test(gallery)) {cindex = gallery;gallery = null;}
            routesEventsManager.trigger('route:showportfolio',{'type':typea,'name':name,'gallery' : gallery,'index':cindex});
        },

        showPortfolioFD : function (typea,name,gallery,cindex){
            routesEventsManager.trigger('route:showportfolioFD',{'name':name,'gallery' : gallery,'index':cindex});
        },

        showPortfolioVideoFD:function (name,gallery,cindex){
            var self = this;
            if (/^\d+$/.test(gallery)) {cindex = gallery;gallery = null;}
            routesEventsManager.trigger('route:showportfolioFD',{'name':name,'gallery' : gallery,'index':cindex});
        },

        showFoodDrinkPress : function(name){
            var self = this;
            routesEventsManager.trigger('route:showpressFD',{'name':name});
        },

        showFoodDrinkPressPost : function(name,postname){
            var self = this;
            routesEventsManager.trigger('route:showpressFD',{'name':name,'postname' : postname});
        },

        showPortfolioPress:function (typea,name,gallery,cindex){
            routesEventsManager.trigger('route:showportfolio',{'type':typea,'name':name,'index':cindex});
        },

        showProfessionalProfile :function (typea,name){
            routesEventsManager.trigger('route:showprofileprofessional',{'type':typea,'name':name});
        },

        showFoodDrinkProfile :function (typea,name){
            routesEventsManager.trigger('route:showprofilefood',{'name':name});
        },

        showModelsCategory:function(category){
            routesEventsManager.trigger('route:showmodels',{'category':category});
        },

        showModelsGallery: function (category,name,gallery,cindex){
            routesEventsManager.trigger('route:showmodelgallery',{'category':category,'name':name,'gallery' : gallery,'index':cindex});
        },

        showPageContact : function(){
            routesEventsManager.trigger('route:showcontact',{});
        },

        showPageInternational : function(){
            routesEventsManager.trigger('route:showinternational',{});
        },
        showPageBecome : function(){
            routesEventsManager.trigger('route:showbecomeamodel',{});
        },
        showPageSpecial : function(){
            routesEventsManager.trigger('route:showspecialoccasion',{});
        },


        defaultAction: function () {
            routesEventsManager.trigger('route:showintro',{});
        }
    });

    var initialize = function (options) {
        router = new AppRouter(options);

        router.on("route", function(route, params) {
            ga('send', 'pageview', location.pathname);
        });

        Backbone.history.start({pushState: true});
    };
    return {
        initialize: initialize
    };
});