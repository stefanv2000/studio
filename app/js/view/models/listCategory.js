define([
    'jquery',
    'underscore',
    'backbone',
    'models/models/categoryContentCollection'
],function($,_,Backbone,CategoryContentMain){

    var ListCategoryView = Backbone.View.extend({
        className:'maincategorycontainer',
        id:"maincategorycontainer",
        initialize : function(options){
            this.options = options;
            this.template = Templates.getTemplate("template-models-listCategory");
            NProgress.inc();
            console.log('xxxx');

            var categorymodel = new CategoryContentMain({path:Backbone.history.getFragment().split('/').slice(0).join('/')});

            if (options.index != null) this.imageIndex=parseInt(options.index); else this.imageIndex=0;


            var self = this;
            categorymodel.fetch({
                success : function(model){
                    NProgress.inc();
                    self.collection = model.get('content');
                    self.path= model.get('path');
                    self.render();
                }
            });
        },
        events:{
            "mouseenter .modelimage" : "mouseenterimage",
            "mouseleave .modelimage" : "mouseleaveimage",
            "click .modelimage" : "imageModelClick",
            "click .modellink" : "imageModelClick",
        },
        render : function(){
            var self = this;
            self.$el.html(self.template({
                'con' : self.collection.toJSON(),
                'path' : this.path
            }));
            NProgress.inc();

            //self.resize();

            backgroundview.clearBackground();
            self.$el.fadeIn(function(){
                self.$('.portfoliothumbnailscontainerscrollable').scrollLeft(0);
            });
            self.showImages();
            NProgress.inc();
            NProgress.done();
            return this.$el;
        },

        resize : function(event){
            var self=this;
            var widthtotal = 0;
            this.$('.thumbnailcontainer').each(function(index,elem){
                widthtotal+=$(this).data('width')+1;
            });
            widthtotal+=70;

            this.$('.portfoliothumbnailscontainer').width(widthtotal);

            var height = $(window).height()-$('#menucontainer').height()-$('.portfoliotop').height()-52-10-28;
            this.$('.portfolioimagedisplaycontainer,.portfolioimagecontainer').height(height);

            this.$('.portfoliocontentcontent').each(function(index,elem){
                var currentElem = $(elem);
                var containerHeight = height;
                var elemCaption = currentElem.find('.captioninnercontent');

                if (elemCaption.length>0) containerHeight=containerHeight-19-14;

                var elemCon = currentElem.find('.innercontent');

                var nHeight = elemCon.data('height');
                var nWidth = elemCon.data('width');
                if (nHeight > containerHeight){
                    nHeight = containerHeight;
                    nWidth = containerHeight/elemCon.data('height')*elemCon.data('width');
                }



                elemCon.width(nWidth).height(nHeight);
                elemCaption.width(nWidth);
            });

        },

        mouseenterimage : function(event){
            var element = $(event.currentTarget);
            element.addClass('modelimageover');
        },

        mouseleaveimage : function(event){

            var element = $(event.currentTarget);
            element.removeClass('modelimageover');
        },

        showImages:function(){
            var self = this;
            this.$('.modelimage').each(function(index,elem){
                var that = $(this);
                $('<img>').load(function(){
                    that.fadeIn(function(){that.parent().addClass('containermodelimageloaded');});

                }).attr('src',that.attr('src'));
            });

        },

        imageModelClick : function(event){
            event.preventDefault();
            router.navigate($(event.currentTarget).data('slug'),{trigger:true});
        },


        close : function(){
            this.$el.remove();

        },

        show : function() {

        }
    })

    return ListCategoryView;

});

