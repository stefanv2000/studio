define([
    'jquery',
    'underscore',
    'backbone',
    'models/artists/portfolioContentCollection'
],function($,_,Backbone,PortfolioContentMain){

    var GalleryView = Backbone.View.extend({
        className:'mainportfoliocontainer',
        id:"mainportfoliocontainer",
        initialize : function(options){
            this.options = options;
            this.template = Templates.getTemplate("template-artists-gallery");

            $(window).on('resize',this.resize);

            var portfoliomodel = new PortfolioContentMain({path:Backbone.history.getFragment().split('/').slice(0).join('/')});

            this.options.link = Backbone.history.getFragment().split('/').slice(0,1).join('/');


            var self = this;
            portfoliomodel.fetch({
                success : function(model){
                    self.model = model;
                    self.collection = model.get('content');
                    self.path= model.get('path');
                    self.render();

                }
            });
        },
        events:{
            "click .thumbnailimage" : "thumbnailClick",
            "click #backtotoplink" : "scrollToTop"
        },
        scrollToTop : function(event){
            event.preventDefault();
            $("html, body").animate({ scrollTop: 0 });
        },
        render : function(){
            var self = this;
            self.$el.html(self.template({
                'con' : self.collection.toJSON(),
            }));


            self.populateContent();

            self.resize();

            self.$el.fadeIn(function(){
                self.resize();
            });


            return this.$el;
        },

        populateContent:function(){
            var self=this;
            this.$('.maingalleryimagecontainer').each(function(index,elem){
                var content = self.collection.at(index);

                var contentLink = content.get('linkpath')+'/_images1/'+content.get('name');
                var contentType = content.get('contentType');
                var imageid = content.get('id');

                var newElem = $(this);




                if (contentType=='video'){
                    contentLink = content.get('linkpath')+'/_media/'+content.get('name');
                    var videoElem = $('<video class="mejs-portfolioplayer" controls preload="none" data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" src="'+contentLink+'" width="'
                        +content.get('imageWidth')+'" height="'+content.get('imageHeight')+'" id="innercontentvideo'+imageid+'" style="width: 100%; height: 100%;" ></video>');
                    var coverlink = content.get('linkpath')+'/_media/'+content.get('originalName');
                    $('<img>').load(function(){
                        videoElem.attr('poster',coverlink);
                    }).attr('src',coverlink);

                    newElem.html($('<div id="innercontent'+imageid+'" class="innercontent videoplayer1" data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" ></div>').html(videoElem));
                    newElem.data('height',content.get('imageHeight'));
                    newElem.data('width',content.get('imageWidth'));


                } else
                if (contentType == 'vimeo') {
                    var videoElem = $('<iframe data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" class="innercontent" id="innercontent'+imageid+'" src="//player.vimeo.com/video/'+content.get('link')+'" width="'+content.get('imageWidth')+'" height="'+content.get('imageHeight')+'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
                    newElem.html(videoElem);
                    newElem.data('height',content.get('imageHeight'));
                    newElem.data('width',content.get('imageWidth'));
                } else
                if (contentType == 'youtube') {
                    var videoElem = $(' <iframe data-width="'+content.get('imageWidth')+'" data-height="'+content.get('imageHeight')+'" class="innercontent" id="innercontent'+imageid+'" width="'+content.get('imageWidth')+'" height="'+content.get('imageHeight')+'" src="http://www.youtube.com/embed/'+content.get('link')+'"></iframe>');
                    newElem.html(videoElem);
                    newElem.data('height',content.get('imageHeight'));
                    newElem.data('width',content.get('imageWidth'));
                } else
                {
                    var imageElem = $('<img src="'+contentLink+'" data-width="'
                        +content.get('imageWidth1')+'" data-height="'+content.get('imageHeight1')+'" id="innercontent'+imageid+'" class="imageportfoliobig innercontent"/>');
                    newElem.html(imageElem);

                    $('<img>').load(function(){
                        imageElem.animate({'opacity' : 1});
                    }).attr('src',imageElem.attr('src'));
                }

                if (content.get('caption1')!=''){
                    var captionElem = $('<div class="captioninnercontent" data-id="'+imageid+'">'+content.get('caption1')+'</div>');
                    newElem.append(captionElem);
                }





            });
        },

        resize : function(event){






            var self=this;
            var width = $(window).width()-40;
            var height = $(window).height();
            var orientation = 'vertical';

            if ($(window).width()>$(window).height()) orientation = 'horizontal';


            this.$('.innercontent').each(function(index,elem){
                var element = $(this);

                var nWidth = element.data('width');
                var nHeight = element.data('height');

                if (nWidth>width) {
                    nHeight = Math.floor(width/nWidth*nHeight);
                    nWidth = width;
                }
                if (orientation==='horizontal') {
                    var nvheight = height-30;
                    if (nHeight > nvheight ) {

                        nWidth=Math.floor(nvheight /element.data('height')*element.data('width'));
                        nHeight = nvheight ;
                    }
                }

                element.width(nWidth).height(nHeight);

            });


        },

        close : function(){
            $(window).off('resize',this.resize);
            this.$el.remove();

        },

        show : function() {

        }
    })

    return GalleryView;

});

