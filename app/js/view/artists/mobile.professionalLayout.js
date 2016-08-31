define([
    'jquery',
    'underscore',
    'backbone',
    'models/artists/profileCollection'
],function($,_,Backbone,ProfileIntroMain){

    var ProfessionalLayoutView = Backbone.View.extend({
        className:'professionalcontainer',
        id:"professionalcontainer",
        innerView : null,
        initialize : function(options){
            this.options = options;
            this.setupEvents();

            this.template = Templates.getTemplate("template-artists-professional");

            var lpath = Backbone.history.getFragment().split('/').slice(0,3).join('/');
            //if (options.isprofile) lpath+='/p/g'; //hack
            var profilemodel = new ProfileIntroMain({path:lpath});

            $(window).on('resize',this.resize);


            //spli path

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
                    var optionsS = {};



                    if ((typeof self.options.isprofile != 'undefined')&&(self.options.isprofile!=null)&&(self.options.isprofile)) {
                        if (self.firstpage!=null){
                            optionsS.sectiontype = 'profileintro';
                            self.trigger("showview:main",optionsS);
                            return;
                        }

                        var firstelem = self.collection.at(1);
                        var path = '/'+firstelem.get('slug');
                        if (firstelem.get('subsections')){
                            var subsections=firstelem.get('subsections');
                            var secondelem = subsections[0];
                            path+='/'+secondelem['slug'];
                            self.options.gallery = secondelem['slug']
                        }
                        router.navigate(Backbone.history.getFragment()+path,{trigger:false});
                    }


                    var xtype = Backbone.history.getFragment().split('/').slice(3,4).join('');

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



                    if (xxx.get('slug') == 'profile')  {
                        optionsS.sectiontype = 'profile';
                        optionsS.selectedId =xxx.get('id');
                    } else
                    if ((xxx.get('slug') == 'portfolio') || (xxx.get('slug') == 'videos') || (xxx.get('slug') == 'press')){
                        optionsS.sectiontype = 'gallery';
                        optionsS.selectedId = xxy.id;
                    }

                    self.trigger("showview:main",optionsS);
                }
            });
        },
        setupEvents : function(){
            var self = this;

            self.on("showview:main",function(options){

                if (self.innerView) self.innerView.close();

                self.$('.portfoliomenuitemselected').removeClass('portfoliomenuitemselected');
                self.$('#portfoliomenuitem'+options.selectedId).addClass('portfoliomenuitemselected');


                if (options.sectiontype == "profileintro") {
                    self.trigger('showview:profileintro',options);
                }
                if ((options.sectiontype == "gallery")||(options.sectiontype == "press_gallery")){
                    self.trigger('showview:portfolio',options);
                } else
                if (options.sectiontype == "profile") {
                    self.trigger('showview:profile',options);
                }
            });

            self.on('showview:portfolio',function(options){
              if (( options.sectiontype == "gallery")||( options.sectiontype == "press_gallery")){
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

            self.on('showview:profileintro',function(options){
                require(['views/artists/mobile.profileIntro'],function(ProfileView){
                    var profileView = new ProfileView(options);
                    self.innerView = profileView;
                    self.$('.portfoliosectincontainer').html(profileView.$el);
                });
            });
        },
        events:{
            "click .seemoreprofessionals,.profilemainnamelink" : "goToLink",
            "click .portfoliomenuitem" : "menuItemClick",
        },
        goToProfile : function(event){
            event.preventDefault();
            router.navigate(this.path,{'trigger':true});
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
            this.trigger("showview:main",options);
        },


        showPortfolioView : function(options){

        },


        goToLink : function(event){
            event.preventDefault();
            router.navigate($(event.currentTarget).attr('href'),{trigger:true});
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
        close : function(){
            if (this.innerView) this.innerView.close();
            $(window).off('resize',this.resize())
            this.$el.remove();
        },

        show : function() {

        }
    })

    return ProfessionalLayoutView;

});
