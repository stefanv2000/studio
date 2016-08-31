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
            this.template = Templates.getTemplate("template-artists-profileIntro");

            var profilemodel = new ProfileIntroMain({path: Backbone.history.getFragment()});

            $(window).on('resize', this.resize);

            var self = this;
            profilemodel.fetch({
                success: function (model) {
                    self.firstpage = model.get('firstpage');
                    self.render();
                }
            });
        },
        events: {
        },
        render: function () {

            var self = this;
            self.$el.html(self.template({
                'firstpage': self.firstpage
            }));

            if ((self.firstpage != null) && (self.firstpage != '') && (self.firstpage.image != '')) {
                this.addMediaContent();
            }
            self.resize();


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
                contentLink = content.linkpath + '/_media/' + content.name;
                var videoElem = $('<video class="mejs-portfolioplayer" controls preload="none" data-width="' + content.imageWidth + '" data-height="' + content.imageHeight + '" src="' + contentLink + '" width="'
                    + content.imageWidth + '" height="' + content.imageHeight + '" id="innercontentvideo' + self.imageIndex + '" style="width: 100%; height: 100%;" ></video>');
                var coverlink = content.linkpath+'/_media/'+content.originalName;
                $('<img>').load(function(){
                    videoElem.attr('poster',coverlink);
                }).attr('src',coverlink);
                videoElem.data('callback', function () {
                });
                newElem.html($('<div id="innercontentprofilebio" class="innercontentprofilebio videoplayer1" data-width="' + content.imageWidth + '" data-height="' + content.imageHeight + '" ></div>').html(videoElem));



            } else if (contentType == 'vimeo') {
                var videoElem = $('<iframe data-width="' + content.imageWidth + '" data-height="' + content.imageHeight + '" class="innercontentprofilebio" id="innercontentprofilebio0" src="//player.vimeo.com/video/' + content.link + '" width="' + content.imageWidth + '" height="' + content.imageHeight + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
                newElem.html(videoElem);
            } else if (contentType == 'youtube') {
                var videoElem = $(' <iframe data-width="' + contentimageWidth + '" data-height="' + content.imageHeight + '" class="innercontentprofilebio" id="innercontentprofilebio0" width="' + content.imageWidth + '" height="' + content.imageHeight + '" src="http://www.youtube.com/embed/' + content.link + '"></iframe>');
                newElem.html(videoElem);
            } else {
                var imageElem = $('<img src="' + contentLink + '" data-width="'
                    + content.imageWidth1 + '" data-height="' + content.imageHeight1 + '" id="innercontentprofilebio0" class="imageportfoliobig innercontentprofilebio"/>');
                newElem.html(imageElem);

                $('<img>').load(function () {
                    imageElem.animate({'opacity': 1});
                    self.resize();
                }).attr('src', imageElem.attr('src'));
            }
        },

        resize: function () {

            return;
            var height = $(window).height() - $('#menucontainer').height() - 28;

            this.$('.profilemainlinkcontainer,.moreprofessionals').each(function (index, elem) {

                height -= $(this).outerHeight(true);
            });
            this.$('.bioprofileimagecontainer').each(function (index, elem) {
                height = Math.min(height, $(this).data('height'));
            });

            if (this.$('.bioprofileimagecontainer').length == 0) {
                this.$('.bioprofiletextcontainer').width(720);
                this.$('.bioprofiletextcontent').each(function (index, elem) {
                    if ($(this).height() > 0)
                        height = Math.min(height, $(this).height() + 70);
                });
            }

            height = Math.min(height, 850);

            this.$('.bioprofiletextcontainer').height(height - 70);
            this.$('.bioprofileimagecontainer').height(height);




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
                var contWidth = this.$('.showmoreprofessionals').width();



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
            router.navigate(element.data('slug'), {trigger: true});
        },


        show: function () {

            this.$el.fadeIn();
            this.resize();
        }
    })

    return ProfileIntroView;

});

