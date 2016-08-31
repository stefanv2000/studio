define([
    'jquery',
    'underscore',
    'backbone',
    'crossroads',
    'models/artists/profileCollection',
    'views/artists/mobile.professionalLayout',

],function($,_,Backbone,crossroads,ProfileIntroMain,ProfessionalLayoutView){

    var FDLayoutView = ProfessionalLayoutView.extend({

        getPath : function(){
            return Backbone.history.getFragment().split('/').slice(0,2).join('/');
        },

        getTypeFromUrl:function(){
            return Backbone.history.getFragment().split('/').slice(2,3).join('');
        },

        initialize : function(options){
            this.options = options;
            this.setupEvents();
            $(window).on('resize',this.resize);

            this.template = Templates.getTemplate("template-artists-professional");

            var profilemodel = new ProfileIntroMain({path:Backbone.history.getFragment().split('/').slice(0,2).join('/')});


            var self = this;
            profilemodel.fetch({
                success : function(model){
                    self.collection = model.get('content');
                    self.background = model.get('background');
                    self.moreCollection = model.get('more');
                    self.path = model.get('path');
                    self.type = model.get('type');
                    self.firstpage = model.get('firstpage');
                    self.mainpath = Backbone.history.getFragment().split('/').slice(0,1).join('/');

                    self.render();
                    var optionsS={};

                    if (self.firstpage!=null){
                        var optionsS={};
                        //optionsS.sectiontype = 'profileintro';

                        self.trigger("showview:main",optionsS);
                        return;
                    }

                    if ((typeof self.options.isprofile != 'undefined')&&(self.options.isprofile!=null)&&(self.options.isprofile)) {
                        var firstelem = self.collection.at(1);
                        var path = '/'+firstelem.get('slug');
                        if (firstelem.get('subsections')){
                            var subsections=firstelem.get('subsections');
                            var secondelem = subsections[0];
                            path+='/'+secondelem['slug'];
                        }
                        router.navigate(Backbone.history.getFragment()+path,{trigger:false});
                    }





                     self.trigger("showview:main",{path : Backbone.history.getFragment()});
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
                var r = otherRouter.addRoute('/{type}/{name}', function(type,name){


                    if (self.firstpage!=null){
                        self.trigger('showview:profileintro',{});

                    } else {


                    }


                });
                r.rules = {
                    'type': ['food-_-drink','food_drink','influencers'],
                };


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



                if (( options.sectiontype == "gallery")||( options.sectiontype == "press_gallery")||( options.sectiontype == "portfolio_old")||( options.sectiontype == "press_old")){
                    require(['views/artists/mobile.gallery'],function(GalleryView){
                        var galleryView = new GalleryView(options);
                        self.innerView = galleryView;
                        self.$('.portfoliosectincontainer').html(galleryView.$el);
                    });
                }
            });

            self.on('showview:profile',function(options){
                require(['views/artists/mobile.profile'],function(ProfileView){
                    var profileView = new ProfileView(options);
                    self.innerView = profileView;
                    self.$('.portfoliosectincontainer').html(profileView.$el);
                });
            });


            self.on('showview:press',function(options){
                require(['views/fooddrink/mobile.press'],function(PressView){
                    options.parent = self;
                    var pressView = new PressView(options);
                    self.innerView = pressView;
                    self.$('.portfoliosectincontainer').html(pressView.$el);
                });
            });

            self.on('showview:profileintro',function(options){
                require(['views/artists/mobile.profileIntro'],function(ProfileView){
                    var profileView = new ProfileView(options);
                    self.innerView = profileView;
                    self.$('.portfoliosectincontainer').html(profileView.$el);
                });
            });

            self.on('showview:presspost',function(options){
                if (options.sectiontype == 'gallery') {
                    require(['views/fooddrink/mobile.pressgallery'], function (PressView) {
                        var pressView = new PressView(options);
                        self.innerView = pressView;
                        self.$('.portfoliosectincontainer').html(pressView.$el);
                    });
                }

                if (options.sectiontype == 'text') {
                    require(['views/fooddrink/mobile.presstext'], function (PressView) {
                        var pressView = new PressView(options);
                        self.innerView = pressView;
                        self.$('.portfoliosectincontainer').html(pressView.$el);
                    });
                }
            });
        },
        events:{
            "click .profilemainnamelink" : "goToProfile",
            "click .portfoliomenuitem" : "menuItemClick",
            "click #seemoreprofessionals" : "showMoreProfessionals",
            "click .otherprofessionallink" : "goToProfessional",
        },
        menuItemClick : function(event){
            event.preventDefault();
            if ($(event.currentTarget).data('type') == 'social'){
                window.open($(event.currentTarget).data('slug'));
                return;
            }

            router.navigate($(event.currentTarget).attr('href'),{'trigger':false});



            var options = {'sectiontype' : $(event.currentTarget).data('type'),'selectedId':$(event.currentTarget).data('id')};
            this.trigger("showview:main",{path : $(event.currentTarget).attr('href')});
        },

        render : function(){
            var self = this;
            self.$el.html(self.template({
                'con' : self.collection.toJSON(),
                'path' : self.path,
                'more' : self.moreCollection,
                'type' : self.type,
                'backslug' : self.mainpath,
            }));


            self.$el.fadeIn();
            self.resize();
            return this.$el;
        },
        resize:function(event){
            var totalWidth = $(window).innerWidth()-40;
            this.$('.portfoliomaintitle').children().css({
                'display' : 'inline-block',
                'visibility' : 'visible'
            })

            var arrayelem = this.$('.portfoliomaintitle').children();


            var compWidth = 0;

            for (var i=0;i<arrayelem.length;i++){
                var currentElem = $(arrayelem[i]);
                var precElem=null;
                if (i>=1) precElem = $(arrayelem[i-1]);


                var cWidth = currentElem.outerWidth(true)

                if (compWidth+cWidth>totalWidth){
                    if (currentElem.hasClass('menuseparator')) {
                        currentElem.css('display','none');
                        compWidth=0;
                    }

                    if ((precElem!=null)&&(precElem.hasClass('menuseparator'))){
                        precElem.css('visibility','hidden');
                        compWidth=cWidth;

                    }
                } else compWidth+=cWidth;


            }
        },
        close1 : function(){
            if (this.innerView) this.innerView.close();
            $(window).off('resize',this.resize())
            this.$el.remove();
        },

        show1 : function() {

        }
    })

    return FDLayoutView;

});
