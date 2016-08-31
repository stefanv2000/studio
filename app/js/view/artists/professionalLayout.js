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
            NProgress.inc();
            this.template = Templates.getTemplate("template-artists-professional");

            $(window).on('resize',this.resize);

            var profilemodel = new ProfileIntroMain({path:this.getPath()});



            var self = this;
            profilemodel.fetch({
                success : function(model){
                    self.collection = model.get('content');
                    self.background = model.get('background');
                    self.moreCollection = model.get('more');
                    self.path = model.get('path');
                    self.type = model.get('type');
                    NProgress.inc();
                    self.render();


                    var xtype = self.getTypeFromUrl();

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
                            optionsS.sectiontype = 'gallery';
                            if (xxy.description1 == '1') optionsS.sectiontype = 'gallery';
                            if (xxy.description1 == '2') optionsS.sectiontype = 'portfolio_old';
                            optionsS.selectedId = xxy.id;
                        }

                    if (self.options.index!=null) optionsS.index = self.options.index;

                    self.trigger("showview:main",optionsS);
                },
                error: function(){
                    alert('fetch error');
                }
            });
        },

        getPath : function(){
            return Backbone.history.getFragment().split('/').slice(0,3).join('/');
        },
        getTypeFromUrl:function(){
            return Backbone.history.getFragment().split('/').slice(3,4).join('');
        },
        setupEvents : function(){
            var self = this;

            self.on("showview:main",function(options){

                if (self.innerView) self.innerView.close();

                self.$('.portfoliomenuitemselected').removeClass('portfoliomenuitemselected');
                self.$('#portfoliomenuitem'+options.selectedId).addClass('portfoliomenuitemselected');

                if ((options.sectiontype == "portfolio_old")||(options.sectiontype == "gallery")||(options.sectiontype == "press_gallery")||(options.sectiontype == "press_old")){
                    self.trigger('showview:portfolio',options);
                } else
                if (options.sectiontype == "profile") {
                    self.trigger('showview:profile',options);
                }
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
        },
        events:{
            "mouseenter .profilemainname" : "mouseentermainlink",
            "mouseleave .profilemainname" : "mouseleavemainlink",
            "click .profilemainnamelink" : "goToProfile",
            "click .portfoliomenuitem" : "menuItemClick",
            "click #seemoreprofessionals" : "showMoreProfessionals",
            "click .otherprofessionallink" : "goToProfessional",
            "click .seemoreprofessionalsoverlay" : "closeMoreProfessionals",
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

        showMoreProfessionals : function(event){
            event.preventDefault();

            var elem = $(event.currentTarget);
            var seeMoreContainer = this.$('.seemoreprofessionalscontainer');

            var offset = elem.offset();

            seeMoreContainer.css(
                    {top:offset.top+23+'px',left:offset.left-10+'px'});

            seeMoreContainer.slideDown();

            this.resize();
            var overlayMoreContainer =  this.$('.seemoreprofessionalsoverlay');
            overlayMoreContainer.show();
            //this.$('.portfoliosectincontainer').toggle();
        },

        closeMoreProfessionals : function(event){
            event.preventDefault();
            this.$('.seemoreprofessionalscontainer').slideUp();
            this.$('.seemoreprofessionalsoverlay').fadeOut();
        },



        goToProfessional : function(event){
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
                        }));
            NProgress.inc();
            this.$('.portfoliomaintitle span').kern();
            backgroundview.clearBackground();
            self.$el.fadeIn();
            return this.$el;
        },

        resize: function(){
            var elementOffset =  this.$('.portfoliolayoutmenucontainer');
            var offset = elementOffset.offset();


            var overlayMoreContainer =  this.$('.seemoreprofessionalsoverlay');

            overlayMoreContainer.css(
                {
                    'height':Utils.getWindowHeight()-offset.top+'px',
                    'width' : Utils.getWindowWidth()+'px',
                    top:offset.top+'px',
                    left:0});

        },
        close : function(){
            if (this.innerView) this.innerView.close();
            $(window).off('resize',this.resize);
            this.$el.remove();
        },

        show : function() {

        }
    })

    return ProfessionalLayoutView;

});
