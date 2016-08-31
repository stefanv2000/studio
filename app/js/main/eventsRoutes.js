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
        NProgress.start();
        console.log('start');
        require(['views/intro/intro'],function(IntroView){
            NProgress.inc();
            var introView = new IntroView();
            self.appView.showView(introView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(introView.$el);
            //introView.show();
        });
    }),

        RoutesEventsManager.on('route:showprofileintro',function(options){
            var self = this;
            NProgress.start();
            require(['views/artists/profileIntro'],function(ProfileIntroView){
                NProgress.inc();
                var profileIntroView = new ProfileIntroView({'type':options.typea,'name':options.name});
                self.appView.showView(profileIntroView);
                app.layout.menuview.showLogoGroup();
                $('#maincontent').prepend(profileIntroView.$el);
                //profileIntroView.show();
            });
        }),



    RoutesEventsManager.on('route:showportfolio',function(options){
        var self = this;
        NProgress.start();
        require(['views/artists/professionalLayout'],function(ProfessionalLayoutView){
            NProgress.inc();
            var layoutView = new ProfessionalLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(layoutView.$el);
            //layoutView.show();
        });


    });

    RoutesEventsManager.on('route:showportfolioFD',function(options){
        var self = this;
        NProgress.start();
        require(['views/fooddrink/fooddrinkLayout'],function(FDLayoutView){
            NProgress.inc();
            var layoutView = new FDLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(layoutView.$el);
            //layoutView.show();
        });


    });

    RoutesEventsManager.on('route:showpressFD',function(options){
        var self = this;
        NProgress.start();
        require(['views/fooddrink/fooddrinkLayout'],function(FDLayoutView){
            NProgress.inc();
            var layoutView = new FDLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(layoutView.$el);
            //layoutView.show();
        });


    });



    RoutesEventsManager.on('route:showprofileprofessional',function(options){
        var self = this;
        NProgress.start();
        require(['views/artists/professionalLayout'],function(ProfessionalLayoutView){
            NProgress.inc();
            var layoutView = new ProfessionalLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(layoutView.$el);
            //layoutView.show();
        });
    });

    RoutesEventsManager.on('route:showprofilefood',function(options){
        var self = this;
        NProgress.start();
        require(['views/fooddrink/fooddrinkLayout'],function(ProfessionalLayoutView){
            NProgress.inc();
            var layoutView = new ProfessionalLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(layoutView.$el);
            //layoutView.show();
        });
    });



    RoutesEventsManager.on('route:showmodels',function(options){
        var self=this;
        NProgress.inc();
        require(['views/models/categoryLayout'],function(CategoryLayoutView){
            NProgress.inc();
            var layoutView = new CategoryLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoModels();
            $('#maincontent').prepend(layoutView.$el);
            //layoutView.show();
        });

    });

    RoutesEventsManager.on('route:showmodelgallery',function(options){
        var self=this;
        NProgress.start();
        require(['views/models/modelLayout'],function(ModelLayoutView){
            NProgress.inc();
            var layoutView = new ModelLayoutView(options);
            self.appView.showView(layoutView);
            app.layout.menuview.showLogoModels();
            $('#maincontent').prepend(layoutView.$el);
            layoutView.show();
        });

    });

    RoutesEventsManager.on('route:showcontact',function(options){
        var self=this;
        NProgress.start();
        require(['views/pages/contact'],function(PageView){
            NProgress.inc();
            var pageView = new PageView(options);
            self.appView.showView(pageView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(pageView.$el);
            //pageView.show();
        });
    });


    RoutesEventsManager.on('route:showbecomeamodel',function(options){
        var self=this;
        NProgress.start();
        require(['views/pages/becomeAModel'],function(PageView){
            NProgress.inc();
            var pageView = new PageView(options);
            self.appView.showView(pageView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(pageView.$el);
            //pageView.show();
        });
    });

    RoutesEventsManager.on('route:showinternational',function(options){
        var self=this;
        NProgress.start();
        require(['views/pages/international'],function(PageView){
            NProgress.inc();
            var pageView = new PageView(options);
            self.appView.showView(pageView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(pageView.$el);
            //pageView.show();
        });
    });

    RoutesEventsManager.on('route:showspecialoccasion',function(options){
        var self=this;
        NProgress.start();
        require(['views/pages/specialOccasion'],function(PageView){
            NProgress.inc();
            var pageView = new PageView(options);
            self.appView.showView(pageView);
            app.layout.menuview.showLogoGroup();
            $('#maincontent').prepend(pageView.$el);
            //pageView.show();
        });
    });


    return RoutesEventsManager;
});
