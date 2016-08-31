/**
 *
 */
define([
    'jquery',
    'underscore',
    'backbone'
], function ($, _, Backbone) {

    var RoutesEventsManager = {};
    _.extend(RoutesEventsManager,Backbone.Events);

    RoutesEventsManager.on('route:showintro',function(){

        var self = this;

        require(['views/intro/mobile.intro'],function(IntroView){
            var introView = new IntroView();
            self.appView.showView(introView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(introView.$el);
            //introView.show();
        });
    }),

        RoutesEventsManager.on('route:showtype',function(){

            var self = this;

            require(['views/intro/mobile.type'],function(TypeView){
                var view = new TypeView();
                self.appView.showView(view);
                app.layout.menuview.showLogoGroup();
                $('#maincontent').prepend(view.$el);
                //introView.show();
            });
        }),




        RoutesEventsManager.on('route:showprofileintro',function(options){
            var self = this;
            require(['views/artists/mobile.professionalLayout'],function(ProfessionalLayoutView){
                options.isprofile = true;
                var layoutView = new ProfessionalLayoutView(options);
                self.appView.showView(layoutView);
                app.layout.menuview.showLogoGroup();
                $('#maincontent').prepend(layoutView.$el);
                //layoutView.show();
            });
        }),



        RoutesEventsManager.on('route:showportfolio',function(options){

            var self = this;
            require(['views/artists/mobile.professionalLayout'],function(ProfessionalLayoutView){
                var layoutView = new ProfessionalLayoutView(options);
                self.appView.showView(layoutView);
                app.layout.menuview.showLogoGroup();
                $('#maincontent').prepend(layoutView.$el);
                //layoutView.show();
            });


        });


    RoutesEventsManager.on('route:showprofileprofessional',function(options){
        var self = this;

        require(['views/artists/mobile.professionalLayout'],function(ProfessionalLayoutView){
            var layoutView = new ProfessionalLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(layoutView.$el);
            //layoutView.show();
        });
    })

    RoutesEventsManager.on('route:showmodels',function(options){
        var self=this;

        require(['views/models/mobile.categoryLayout'],function(CategoryLayoutView){
            var layoutView = new CategoryLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoModels();
            $('#maincontent').prepend(layoutView.$el);
            //layoutView.show();
        });

    });

    RoutesEventsManager.on('route:showmodelgallery',function(options){
        var self=this;

        require(['views/models/mobile.modelLayout'],function(ModelLayoutView){
            var layoutView = new ModelLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoModels();
            $('#maincontent').prepend(layoutView.$el);
            layoutView.show();
        });

    });

    RoutesEventsManager.on('route:showcontact',function(options){
        var self=this;

        require(['views/pages/mobile.contact'],function(PageView){
            var pageView = new PageView(options);
            self.appView.showView(pageView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(pageView.$el);
            //pageView.show();
        });
    });

    RoutesEventsManager.on('route:showinternational',function(options){
        var self=this;

        require(['views/pages/mobile.international'],function(PageView){
            var pageView = new PageView(options);
            self.appView.showView(pageView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(pageView.$el);
            //pageView.show();
        });
    });

    RoutesEventsManager.on('route:showspecialoccasion',function(options){
        var self=this;

        require(['views/pages/mobile.specialOccasion'],function(PageView){
            var pageView = new PageView(options);
            self.appView.showView(pageView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(pageView.$el);
            //pageView.show();
        });
    });


    RoutesEventsManager.on('route:showportfolioFD',function(options){
        var self = this;
        require(['views/fooddrink/mobile.fooddrinkLayout'],function(FDLayoutView){
            var layoutView = new FDLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(layoutView.$el);
            //layoutView.show();
        });


    });

    RoutesEventsManager.on('route:showpressFD',function(options){
        var self = this;
        require(['views/fooddrink/mobile.fooddrinkLayout'],function(FDLayoutView){
            var layoutView = new FDLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(layoutView.$el);
            //layoutView.show();
        });


    });

    RoutesEventsManager.on('route:showprofileintroFD',function(options){
        var self = this;
        require(['views/fooddrink/mobile.fooddrinkLayout'],function(FDLayoutView){
            options.isprofile = true;
            var layoutView = new FDLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(layoutView.$el);
            //layoutView.show();
        });
    });



    return RoutesEventsManager;
});
