define([
    'jquery',
    'underscore',
    'backbone',
    'models/artists/profileCollection'
], function ($, _, Backbone, ProfileIntroMain) {

    var ProfileIntroView = Backbone.View.extend({
        className: 'profileintrocontainer',
        id: "profileintrocontainer",
        showMoreOpened: 0,
        initialize: function () {
            NProgress.inc();
            this.template = Templates.getTemplate("template-artists-profileIntro");

            NProgress.inc();
            var profilemodel = new ProfileIntroMain({path: Backbone.history.getFragment()});

            $(window).on('resize', {that :this },this.resize);

            var self = this;
            profilemodel.fetch({
                success: function (model) {
                    NProgress.inc();
                    self.collection = model.get('content');
                    self.background = model.get('background');
                    self.moreCollection = model.get('more');
                    self.path = model.get('path');
                    self.firstpage = model.get('firstpage');
                    self.render();
                },
                error: function(){
                    alert('fetch error');
                }
            });
        },
        events: {
            "mouseenter .profilemainname": "mouseentermainlink",
            "mouseleave .profilemainname": "mouseleavemainlink",

            "click .profilemainnamelink,.profileintrosubsectionslinks": "subsectionsclick",
            "click #profilemorebutton": "showMoreProfessionals",
            "click .otherprofessionallink": "goToProfessional",
        },
        render: function () {

            var self = this;
            self.$el.html(self.template({
                'con': self.collection.toJSON(),
                'path': self.path,
                'more': self.moreCollection,
                'firstpage': self.firstpage
            }));
            NProgress.inc();
            if ((self.firstpage != null) && (self.firstpage != '') && (self.firstpage.image != '')) {
                this.addMediaContent();
            }

            self.resize();
            backgroundview.reinit(self.background, self, false);


            self.$('.bioprofilecontainerscrollable').mCustomScrollbar({
                theme: 'verticalarrows',
                scrollInertia:1600,
                scrollButtons: {enable: true,
                    scrollAmount:260,
                    scrollType:"stepped",
                    },
                axis: 'y',
                callbacks:{
                    whileScrolling: function(){

                        self.$('.mCS-verticalarrows .mCSB_scrollTools .mCSB_buttonUp').css('opacity',1);
                        self.$('.mCS-verticalarrows .mCSB_scrollTools .mCSB_buttonDown').css('opacity',1);


                        if (this.mcs.topPct==0){
                            self.$('.mCS-verticalarrows .mCSB_scrollTools .mCSB_buttonUp').css('opacity',0.2);
                        }

                        if (this.mcs.topPct==100){
                            self.$('.mCS-verticalarrows .mCSB_scrollTools .mCSB_buttonDown').css('opacity',0.2);
                        }
                    }
                }

            });
            self.$('.mCS-verticalarrows .mCSB_scrollTools .mCSB_buttonUp').css('opacity',0.2);

            this.$('.profilemainnamelink span').kern();
            self.resize();
            NProgress.done();
            //self.$el.fadeIn();
            return this.$el;
        },



        addMediaContent: function () {
            var self = this;
            var content = self.firstpage.image;
            var contentLink = content.linkpath + '_images1/' + content.name;
            var contentType = content.contentType;


            var newElem = $('.bioprofileimagecontainer');

            if (contentType == 'video') {
                contentLink = content.linkpath + '_media/' + content.name;
                var videoElem = $('<video class="mejs-portfolioplayer" controls preload="none" data-width="' + content.imageWidth + '" data-height="' + content.imageHeight + '" src="' + contentLink + '" width="'
                    + content.imageWidth + '" height="' + content.imageHeight + '" id="innercontentvideo' + self.imageIndex + '" style="width: 100%; height: 100%;" ></video>');

                var coverlink = content.linkpath+'_media/'+content.originalName;
                $('<img>').load(function(){
                    videoElem.attr('poster',coverlink);
                }).attr('src',coverlink);

                /*
                var coverlink = content.linkpath+'/_media/'+content.originalName;
                $('<img>').load(function(){
                    videoElem.attr('poster',coverlink);
                }).attr('src',coverlink);
                */

                newElem.html($('<div id="innercontent" class="innercontent videoplayer2" data-width="' + content.imageWidth + '" data-height="' + content.imageHeight + '" ></div>').html(videoElem));
                videoElem.mediaelementplayer({
                    loop: false,
                    startVolume: 0.3,
                    alwaysShowControls: true,
                    features: ['playpause', 'progress', 'current', 'separator', 'duration'],
                    success: function (player, node) {
                    }
                });


            } else if (contentType == 'vimeo') {
                var videoElem = $('<iframe data-width="' + content.imageWidth + '" data-height="' + content.imageHeight + '" class="innercontent videoshareiframe" id="innercontent0" src="//player.vimeo.com/video/' + content.link + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
                newElem.html(videoElem);
            } else if (contentType == 'youtube') {
                var videoElem = $(' <iframe data-width="' + contentimageWidth + '" data-height="' + content.imageHeight + '" class="innercontent videoshareiframe" id="innercontent0" width="' + content.imageWidth + '" height="' + content.imageHeight + '" src="http://www.youtube.com/embed/' + content.link + '"></iframe>');
                newElem.html(videoElem);
            } else {
                var imageElem = $('<img src="' + contentLink + '" data-width="'
                    + content.imageWidth1 + '" data-height="' + content.imageHeight1 + '" id="innercontent0" class="imageportfoliobig innercontent"/>');
                newElem.html(imageElem);

                $('<img>').load(function () {
                    imageElem.animate({'opacity': 1});
                    self.resize();
                }).attr('src', imageElem.attr('src'));
            }
        },

        resize: function (event) {

            var self = this;
            if (event) {
                self = event.data.that;
            }



            var height = Utils.getWindowHeight() - $('#menucontainer').height() - 71;



            this.$('.profilemainlinkcontainer,.moreprofessionals').each(function (index, elem) {

                height -= $(this).outerHeight(true);
            });

            if ((self.firstpage)&&(self.firstpage.image)){

                height = Math.min(height, self.firstpage.image.imageHeight1);
            }

            this.$('.bioprofileimagecontainer').each(function (index, elem) {
                //height = Math.min(height, $(this).data('height'));
            });

            if (!((self.firstpage)&&(self.firstpage.image))) {
                this.$('.bioprofiletextcontainer').width(720);
                this.$('.bioprofiletextcontent').each(function (index, elem) {
                    if ($(this).height() > 0)
                        height = Math.min(height, $(this).height() + 70);
                });
            }


            height = Math.min(height, 850);

            var imagecont = this.$('.bioprofileimagecontainer');

            if ((self.firstpage)&&(self.firstpage.image)){
                var maxWidth = Utils.getWindowWidth()-70-470-35;
                var res = Utils.scaleToFit([maxWidth,height],[imagecont.data('width'),imagecont.data('height')]);

                height = Math.min(height,res.size.height);
            }



            this.$('.bioprofiletextcontainer').height(height - 70);

            var width = imagecont.data('width')*height/imagecont.data('height');
            this.$('.bioprofileimagecontainer').height(height).width(width);




        },
        close: function () {
            this.$el.remove();
            $(window).off('resize', this.resize);
        },

        showMoreProfessionals: function (event) {
            var self = this;
            event.preventDefault();
            var imgElem = $(event.currentTarget).find('img');
            if (imgElem.hasClass('rotateDown')) imgElem.removeClass('rotateDown'); else imgElem.addClass('rotateDown');

            if (self.showMoreOpened === 0) {
                this.$('.showmoreprofessionals').css({
                    'position': 'absolute',
                    'left': -1000 + 'px',
                    'display': 'block'
                });
                var contWidth = this.$('.showmoreprofessionals').width()+2;



                this.$('.containershowmoreprofessionals').height(this.$('.showmoreprofessionals').height()).animate({
                    'width': contWidth + 35 + 'px'
                }, function () {
                    self.$('.showmoreprofessionals').css({
                        'position': 'static',
                        'left': 0,
                        'display': 'none'
                    });
                    self.$('.showmoreprofessionals').slideDown()
                });
                self.showMoreOpened = 1;
            } else {
                self.$('.showmoreprofessionals').slideUp(function(){
                    self.$('.containershowmoreprofessionals').animate({
                        'width': 0
                    },function(){self.$('.containershowmoreprofessionals').height(0)});
                })
                self.showMoreOpened = 0;
            }
        },

        goToProfessional: function (event) {
            event.preventDefault();
            router.navigate($(event.currentTarget).attr('href'), {trigger: true});
        },

        mouseentermainlink: function (event) {
            var self=this;
            var element = $(event.currentTarget);

            $('#profilemainnametext' + element.data('id')).stop().slideDown(function(){self.resize();});
            var str = '';


        },

        mouseleavemainlink: function (event) {
            var self=this;
            var element = $(event.currentTarget);
            $('#profilemainnametext' + element.data('id')).stop().slideUp(function(){self.resize();});

        },

        subsectionsclick: function (event) {
            event.preventDefault();
            var element = $(event.currentTarget);

            if (element.data('type') == 'social'){
                window.open($(event.currentTarget).data('slug'));
                return;
            }

            var subsectioncont = this.$('#profilemainnametext'+element.data('id'));
            if (subsectioncont.length>0){
                var link = subsectioncont.find("a:first");
                router.navigate(link.data('slug'), {trigger: true});
                return;
            }

            router.navigate(element.data('slug'), {trigger: true});
        },


        show: function () {

            this.$el.fadeIn();
            this.resize();
        }
    })

    return ProfileIntroView;

});
