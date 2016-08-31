define([
    'jquery',
    'underscore',
    'backbone',
    'crossroads',
    'models/artists/profileCollection',
    'views/artists/professionalLayout',

],function($,_,Backbone,crossroads,ProfileIntroMain,ProfessionalLayoutView){

    var FDLayoutView = ProfessionalLayoutView.extend({

        getPath : function(){
            return Backbone.history.getFragment().split('/').slice(0,2).join('/');
        },

        getTypeFromUrl:function(){
            return Backbone.history.getFragment().split('/').slice(2,3).join('');
        },

        className1:'professionalcontainer',
        id1:"professionalcontainer",
        innerView1 : null,
        initialize : function(options){
            this.options = options;
            this.setupEvents();

            this.template = Templates.getTemplate("template-artists-professional");
            NProgress.inc();

            var profilemodel = new ProfileIntroMain({path:Backbone.history.getFragment().split('/').slice(0,2).join('/')});


            var self = this;
            profilemodel.fetch({
                success : function(model){
                    NProgress.inc();
                    self.collection = model.get('content');
                    self.background = model.get('background');
                    self.moreCollection = model.get('more');
                    self.path = model.get('path');
                    self.type = model.get('type');
                    self.render();


                    /*
                    var xtype = Backbone.history.getFragment().split('/').slice(2,3).join('');

                    var xxx = self.collection.find(function(item){
                        if (item.get('slug') == xtype) return true;
                        return false;
                    });



                    var xxy = xxx.toJSON();

                    if ((typeof self.options.gallery != 'undefined')&&(self.options.gallery!=null)&&(xxx.get('subsections'))) {
                        xxy = xxx.get('subsections').find(function(item){
                            if (item.slug == self.options.gallery) return true;
                            return false;
                        });
                        //xxy = xxy.toJSON();

                    }

                    var optionsS = {};
                    if (xxx.get('slug') == 'profile')  {
                        optionsS.sectiontype = 'profile';
                        optionsS.selectedId =xxx.get('id');
                    } else
                    if ((xxx.get('slug') == 'portfolio') || (xxx.get('slug') == 'videos') || (xxx.get('slug') == 'press')){
                        optionsS.sectiontype = 'portfolio_old';
                        if (xxy.description1 == '1') optionsS.sectiontype = 'gallery';
                        if (xxy.description1 == '2') optionsS.sectiontype = 'portfolio_old';
                        optionsS.selectedId = xxy.id;
                    }

                    if (self.options.index!=null) optionsS.index = self.options.index;
                    //*/


                    //self.trigger("showview:main",optionsS);

                    self.trigger("showview:main",{path : Backbone.history.getFragment()});
                },
                error: function(){
                    alert('fetch error');
                }
            });
        },
        setSelected:function(linkname){
            var self = this;
            self.$('.portfoliomenuitemselected').removeClass('portfoliomenuitemselected');
            self.$('.portfoliomenuitem').each(function(){
                if ($(this).text().toLowerCase() == linkname.toLowerCase()) $(this).addClass('portfoliomenuitemselected')
            });
        },
        setupEvents : function(){
            var self = this;

            self.on("showview:main",function(options){

                if (self.innerView) self.innerView.close();


                self.$('.portfoliomenuitemselected').removeClass('portfoliomenuitemselected');


                if (!options) options={};
                if (!options.path) options.path = Backbone.history.getFragment();


                var otherRouter = crossroads.create();
                var r = otherRouter.addRoute('/{type}/{name}/press', function(type,name){
                    self.setSelected('press');
                    self.trigger('showview:press',{});
                });
                r.rules = {
                    'type': ['food-_-drink','food_drink','influencers'],
                };

                r = otherRouter.addRoute('/{type}/{name}/press/{postname}', function(type,name,postname){
                    var pressItem = self.collection.find(function(item){
                        if (item.get('slug') == 'press') return true;
                    });

                    var postItem = pressItem.get('subsectionspress').find(function(item){
                        if (item.slug == postname) return true;
                    });
                    self.setSelected('press');
                    self.trigger('showview:presspost',{sectiontype:postItem.presstype});
                });

                r.rules = {
                    'type': ['food-_-drink','food_drink','influencers'],
                };

                r = otherRouter.addRoute(/([\-_a-z0-9]+)\/([\-_a-z0-9]+)\/([a-z]+)(\/[\-_a-z0-9]+)?(\/([0-9]+))?/, function(type,name,contenttype,gallery,index){
                    if (gallery) self.setSelected(gallery); else self.setSelected(contenttype);
                    self.trigger('showview:portfolio',{sectiontype : 'gallery'});
                });

                r.rules = {
                    0 : ['food-_-drink','food_drink','influencers'],
                    2: ['portfolio','videos']
                };

                var r = otherRouter.addRoute('/{type}/{name}/profile', function(type,name){
                    self.setSelected('profile');
                    self.trigger('showview:profile',{});
                });

                otherRouter.parse(options.path);


            });

            self.on('showview:portfolio',function(options){



                if (( options.sectiontype == "portfolio_old")||( options.sectiontype == "press_old")){
                    require(['views/artists/oldportfolio'],function(OldPortfolioView){
                        NProgress.inc();
                        var portfolioView = new OldPortfolioView(options);
                        self.innerView = portfolioView;
                        self.$('.portfoliosectincontainer').html(portfolioView.$el);
                    });
                }

                if (( options.sectiontype == "gallery")||( options.sectiontype == "press_gallery")){
                    require(['views/artists/gallery'],function(GalleryView){
                        NProgress.inc();
                        var galleryView = new GalleryView(options);
                        self.innerView = galleryView;
                        self.$('.portfoliosectincontainer').html(galleryView.$el);
                    });
                }
            });

            self.on('showview:profile',function(options){
                self.$('.portfoliolayoutmenucontainer').hide();
                require(['views/artists/profile'],function(ProfileView){
                    NProgress.inc();
                    var profileView = new ProfileView(options);
                    self.innerView = profileView;
                    self.$('.portfoliosectincontainer').html(profileView.$el);
                });
            });


            self.on('showview:press',function(options){
                require(['views/fooddrink/press'],function(PressView){
                    NProgress.inc();
                    options.parent = self;
                    var pressView = new PressView(options);
                    self.innerView = pressView;
                    self.$('.portfoliosectincontainer').html(pressView.$el);
                });
            });

            self.on('showview:presspost',function(options){

                if (options.sectiontype == 'gallery') {
                    require(['views/fooddrink/pressgallery'], function (PressView) {
                        NProgress.inc();
                        var pressView = new PressView(options);
                        self.innerView = pressView;
                        self.$('.portfoliosectincontainer').html(pressView.$el);
                    });
                }

                if (options.sectiontype == 'text') {
                    require(['views/fooddrink/presstext'], function (PressView) {
                        NProgress.inc();
                        var pressView = new PressView(options);
                        self.innerView = pressView;
                        self.$('.portfoliosectincontainer').html(pressView.$el);
                    });
                }
            });
        },

        menuItemClick : function(event){
            event.preventDefault();
            if ($(event.currentTarget).data('type') == 'social'){
                window.open($(event.currentTarget).data('slug'));
                return;
            }
            this.$('.seemoreprofessionalscontainer').slideUp();
            this.$('.portfoliosectincontainer').show();
            router.navigate($(event.currentTarget).attr('href'),{'trigger':false});

            var options = {'sectiontype' : $(event.currentTarget).data('type'),'selectedId':$(event.currentTarget).data('id')};
            this.trigger("showview:main",{path : $(event.currentTarget).attr('href')});
        },


        render : function(){
            ProfessionalLayoutView.prototype.render.apply(this);
            var self=this;
            this.$('#backbutton').click(function(event){
                event.preventDefault();
                var str=Backbone.history.getFragment();
                str = str.substring(0, str.lastIndexOf("/"));
                var oc = (str.match(new RegExp("/", "g")) || []).length;
                if (oc>=2) {
                    router.navigate(str, {trigger: false});
                    self.trigger("showview:main", {path: str});
                } else {
                    router.navigate(str, {trigger: true});
                }

            });
            this.$('.showonfd').css('display','inline-block');
            return this.$el;
        },

    })

    return FDLayoutView;

});
