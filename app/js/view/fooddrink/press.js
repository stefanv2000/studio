define([
    'jquery',
    'underscore',
    'backbone',
    'models/fooddrink/pressContentModel'
], function ($, _, Backbone, PressContentModel) {

    var PressView = Backbone.View.extend({
        className: 'presscontainer',
        id: "presscontainer",
        initialize: function (options) {
            var self = this;
            this.template = Templates.getTemplate("template-fooddrink-press");

            this.parentView = options.parent;

            $(window).on('resize', this.resize);
            app.layout.hideCredit();
            self.model = new PressContentModel({path: Backbone.history.getFragment()});

            NProgress.inc();
            var self = this;
            self.model.fetch({
                success: function (model) {
                    NProgress.inc();
                    self.render();
                }
            });
        },
        events: {
            "click .mainpressimagecontainer": 'showPressPost'
        },

        render: function () {

            var self = this;
            self.$el.html(self.template({'con': self.model.get('content').toJSON()}));
            NProgress.inc();
            backgroundview.clearBackground();

            $('.portfoliomaingalleryscrollable').mCustomScrollbar({
                axis: "x",
                scrollInertia: 0,
                theme: 'my-theme',
                autoDraggerLength: false,
                setLeft: "0px",
                mouseWheel: {
                    enable: true
                },
                advanced: {
                    updateOnBrowserResize: true,
                    updateOnContentResize: true,
                    autoExpandHorizontalScroll: true
                }

            });
            self.resize();
            self.showThumbnails();
            NProgress.done();
            self.$el.fadeIn();
            return this.$el;
        },

        showThumbnails: function () {
            this.$('.pressgalleryimage').each(function () {
                var elemImage = $(this);
                $('<img>').load(function () {
                    elemImage.fadeIn();
                }).attr('src', elemImage.attr('src'));
            });
        },

        resize: function (event) {


            var self = this;
            var height = Utils.getWindowHeight() - $('#menucontainer').height() - $('.portfoliotop').height() - 11;

            this.$('.portfoliomaingalleryscrollable,.portfoliomaingallerycontainer').height(height);
            height -= 45;
            this.$('.portfoliomaingallerycontainer').height(height).css('margin', '0px');


            //height = Math.min(850,height);//maximum gallery height is 850px


            var margin = 4;//margin to the left and to the bottom


            var rows = Math.floor((height + margin) / (200 + margin));
            var cols = Math.ceil(this.$('.mainpressimagecontainer').length / rows);

            if (this.$('.mainpressimagecontainer').length <= -1) {
                var widthrest = Math.round((Utils.getWindowWidth() - cols * (200 + margin)) / 2);
                if (widthrest > 0) this.$('.portfoliomaingallerycontainer').css('margin-left', widthrest + 'px');

                var heightrest = Math.round((height + 45 - rows * (200 + margin)) / 2);
                if (heightrest > 0) this.$('.portfoliomaingallerycontainer').css('margin-top', heightrest + 'px');
            }


            this.$('.mainpressimagecontainer').each(function (index, elem) {
                var elemCon = $(this);

                var cIndex = index + 1;


                var left = Math.floor(index / rows) * (200 + margin) + 35;
                var top = Math.floor(index % rows) * (200 + margin);

                $(this).css({
                    'top': top + 'px',
                    'left': left + 'px'
                })


            });


            widthtotal = cols * (200 + margin) - margin + 35;


            this.$('.portfoliomaingallerycontainer').width(widthtotal);

            $('.portfoliomaingalleryscrollable').mCustomScrollbar('update');
        },

        showPressPost: function (event) {
            event.preventDefault();
            var element = $(event.currentTarget);
            router.navigate(element.data('slug'), {trigger: false});
            this.parentView.trigger('showview:main');

        },
        close: function () {
            app.layout.showCredit();
            $(window).off('resize', this.resize);
            this.$el.remove();
        },


        show: function () {

        }
    })

    return PressView;

});
